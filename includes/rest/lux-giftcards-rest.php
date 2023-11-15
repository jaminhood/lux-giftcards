<?php

# === To deny anyone access to this file directly
if (!defined("ABSPATH")) die("Direct access forbidden");

require_once(ABSPATH . 'wp-admin/includes/user.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

# === Check if LuxGiftcardsRest is defined
if (!class_exists('LuxGiftcardsRest')) :
  # === Declear class if class does not exists
  class LuxGiftcardsRest
  {
    # === Constructor method
    public function __construct()
    {
      # === Call rest api init method
      $this->rest_api_init();
    }

    # === Rest api init method
    private function rest_api_init()
    {
      # === Call routes method
      add_action('rest_api_init', array($this, 'create_rest_routes'));
    }

    # === Routes method
    public function create_rest_routes()
    {
      # === Giftcard get routes
      register_rest_route('l-card/v1', 'get', [
        'methods'  => 'GET',
        'callback' => array($this, 'get_giftcards')
      ]);
      # === Giftcard set routes
      register_rest_route('l-card/v1', 'set', [
        'methods'  => 'POST',
        'callback' => array($this, 'set_giftcards')
      ]);
      # === Giftcard get customer routes
      register_rest_route('l-card/v1', 'get-cards', [
        'methods'  => 'GET',
        'callback' => array($this, 'get_customer_giftcards'),
        'permission_callback' => 'hid_ex_m_rest_permit_customers'
      ]);
      # === Giftcard sell routes
      register_rest_route('l-card/v1', 'sell', [
        'methods'  => 'POST',
        'callback' => array($this, 'set_customer_giftcards'),
        'permission_callback' => 'hid_ex_m_rest_permit_customers'
      ]);
    }

    # === Get giftcard orders method
    public function get_giftcards()
    {
      # === Try to execute method's actions
      try {
        # === Instantiate lux database handler
        $luxDBH = new LuxDBH;
        # === Get all giftcards from wp database
        $all_giftcards = hid_ex_m_get_all_giftcard_orders();

        # === Check if giftcard isn't empty
        if (!(empty($all_giftcards))) {
          # === Loop through giftcards data
          foreach ($all_giftcards as $giftcard) {
            # === Get customer info
            $customer_data = hid_ex_m_get_customer_data($giftcard->customer_id);
            # === Set customer name
            $asset = $luxDBH->lux_get_giftcard_sub_category_data($giftcard->asset_id);
            $giftcard->asset_img = $asset['icon'];
            $giftcard->asset_name = $asset['sub_category'];
            $giftcard->customer_name = ucfirst($customer_data[0]->data->display_name) . ' ' . ucfirst($customer_data[0]->data->user_nicename);
            # === Set giftcard image from wp images
            $giftcard->proof = wp_get_attachment_url($giftcard->card_picture);
            # === Remove unnecessary infos from array
            unset($giftcard->customer_id);
            unset($giftcard->asset_id);
            unset($giftcard->card_picture);
          }
        }

        # === Set response to get all giftcards
        $response = new WP_REST_Response($all_giftcards);
        # === Set response status to 200
        $response->set_status(200);
        # === Return response object
        return $response;
      } catch (\Throwable $th) {
        # === Return error message
        return new WP_Error(
          'unknown error occured', // code
          'Error occured while trying to fetch giftcards', // data
          array('status' => 400) // status
        );
      }
    }

    # === Set giftcard orders method
    public function set_giftcards($request)
    {
      # === Check if all params are inputed
      if (!isset($request['id']) && !isset($request['order_id'])) {
        # === Return error if not
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting giftcard', // data
          array('status' => 400) // status
        );
      }

      try {
        # === Assign requests to variables
        $id = $request['id'];
        $order_id = $request['order_id'];

        # === Check if id is numeric
        if (!is_numeric($id)) {
          # === Return error if not
          return new WP_Error(
            'ID is not a number', # code
            'This id you sent is not a numerical value', # message
            array('status' => 400) # status
          );
        }

        # === Get transactions from orders table
        $transaction = hid_ex_m_get_giftcard_order_data($id);
        # === Assign transaction data to variables
        $customer = $transaction->customer_id;
        $old_balance = hid_ex_m_get_account_balance($customer);
        $where = array('id' => $id);
        $price = $transaction->price;
        $status = $transaction->order_status;

        # === Conditional for order_id
        switch ($order_id):
            # === Check for 0
          case '0':
            # === Check if status is approved
            if ($status == 2) {
              # === Debit wallet
              $new_balance = $old_balance - $price;
              update_user_meta($customer, 'account_balance', $new_balance);
            }
            # === Decline giftcard
            hid_ex_m_mark_giftcard_as_declined($where);
            break;
            # === Check for 1
          case '1':
            # === Check if status is approved
            if ($status == 2) {
              # === Debit wallet
              $new_balance = $old_balance - $price;
              update_user_meta($customer, 'account_balance', $new_balance);
            }
            # === Set giftcard to Pending
            hid_ex_m_mark_giftcard_as_pending($where);
            break;
            # === Check for 2
          case '2':
            # === Check if status is not approved
            if ($status != 2) {
              # === Creadit wallet
              $new_balance = $old_balance + $price;
              update_user_meta($customer, 'account_balance', $new_balance);
            }
            # === Set giftcard to Approved
            hid_ex_m_mark_giftcard_as_approve($where);
            break;
        endswitch;

        # === Set response message
        $response = new WP_REST_Response("Status Updated Successfully");
        # === Set response status to 200
        $response->set_status(200);
        # === Return response object
        return $response;
      } catch (\Throwable $th) {
        # === Return error message
        return new WP_Error(
          'unknown error occured', // code
          'Error occured while trying to set giftcards', // data
          array('status' => 400) // status
        );
      }
    }

    # === Get giftcard orders method
    public function get_customer_giftcards()
    {
      # === Try to execute method's actions
      try {
        # === Instantiate lux database handler
        $luxDBH = new LuxDBH;
        $customer_id = get_current_user_id();
        $output_data = hid_ex_m_get_all_customer_giftcard_orders($customer_id);

        if (!empty($output_data)) :
          foreach ($output_data as $single) :
            unset($single->id);
            unset($single->customer_id);

            # === Set customer name
            $asset = $luxDBH->lux_get_giftcard_sub_category_data($single->asset_id);

            $single->asset = $asset->sub_category;
            unset($single->asset_id);

            $single->snapshot = wp_get_attachment_url($single->card_picture);
            unset($single->card_picture);

            $single->status = hid_ex_m_get_order_status($single->order_status);
            unset($single->order_status);
          endforeach;
        else :
          # === Assign res to response message
          $res = array("message"   => "No history found");

          # === Set response message
          $response = new WP_REST_Response($res);
          # === Set response status to 200
          $response->set_status(200);
          # === Return response object
          return $response;
        endif;

        # === Set response to get all customer giftcards
        $response = new WP_REST_Response($output_data);
        # === Set response status to 200
        $response->set_status(200);
        # === Return response object
        return $response;

        # === Instantiate lux database handler
        $luxDBH = new LuxDBH;
        # === Get all giftcards from wp database
        $all_giftcards = hid_ex_m_get_all_giftcard_orders();

        # === Check if giftcard isn't empty
        if (!(empty($all_giftcards))) {
          # === Loop through giftcards data
          foreach ($all_giftcards as $giftcard) {
            # === Get customer info
            $customer_data = hid_ex_m_get_customer_data($giftcard->customer_id);
            # === Set customer name
            $asset = $luxDBH->lux_get_giftcard_sub_category_data($giftcard->asset_id);
            $giftcard->asset_img = $asset['icon'];
            $giftcard->asset_name = $asset['sub_category'];
            $giftcard->customer_name = ucfirst($customer_data[0]->data->display_name) . ' ' . ucfirst($customer_data[0]->data->user_nicename);
            # === Set giftcard image from wp images
            $giftcard->proof = wp_get_attachment_url($giftcard->card_picture);
            # === Remove unnecessary infos from array
            unset($giftcard->customer_id);
            unset($giftcard->asset_id);
            unset($giftcard->card_picture);
          }
        }
      } catch (\Throwable $th) {
        # === Return error message
        return new WP_Error(
          'unknown error occured', // code
          'Error occured while trying to fetch giftcards', // data
          array('status' => 400) // status
        );
      }
    }

    # === Set giftcard orders method
    public function set_customer_giftcards($request)
    {
      # === Check if all params are inputed
      if (!isset($request['asset_id']) && !isset($request['quantity']) && !isset($_FILES['file'])) {
        # === Return error if not
        return new WP_Error(
          'incomplete fields', // code
          'incomplete fields were submitted for setting giftcard', // data
          array('status' => 400) // status
        );
      }

      try {
        $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');

        if (!in_array($_FILES['file']['type'], $arr_img_ext)) {
          return new WP_Error(
            'error processing order', // code
            "error processing image", // data
            array('status' => 400) // status
          );
        }

        $upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));
        $type = '';

        if (!empty($upload['type'])) {
          $type = $upload['type'];
        } else {
          $mime = wp_check_filetype($upload['file']);
          if ($mime) {
            $type = $mime['type'];
          }
        }

        $attachment = array('post_title' => basename($upload['file']), 'post_content' => '', 'post_type' => 'attachment', 'post_mime_type' => $type, 'guid' => $upload['url']);
        $data = wp_insert_attachment($attachment, $upload['file']);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        wp_update_attachment_metadata($data, wp_generate_attachment_metadata($data, $upload['file']));

        $luxDBH = new LuxDBH;
        $id = $request['asset_id'];
        $asset = $luxDBH->lux_get_giftcard_sub_category_data($id);
        $quantity_san = $request['quantity'];
        $price = intval($quantity_san) * intval($asset->rate);

        $input_data = array(
          'customer_id' => get_current_user_id(),
          'asset_id'      => $id,
          'quantity' => $quantity_san,
          'price' => $price,
          'card_picture' => $data,
          'order_status'  => 1
        );

        hid_ex_m_create_new_giftcard_order($input_data);

        $response = new WP_REST_Response('giftcard processed successfully');
        $response->set_status(200);
        return $response;
      } catch (\Throwable $th) {
        # === Return error message
        return new WP_Error(
          'unknown error occured', // code
          'Error occured while trying to set giftcards', // data
          array('status' => 400) // status
        );
      }
    }
  }
endif;
