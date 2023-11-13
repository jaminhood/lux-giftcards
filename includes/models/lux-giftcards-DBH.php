<?php

// To deny anyone access to this file directly
if (!defined('ABSPATH')) exit;
global $jal_db_version;

$jal_db_version = '1.0';

// Create the Giftcard Orders Tables
function lux_giftcard_create_giftcard_orders_table()
{
  global $wpdb;
  global $jal_db_version;

  $table_name = $wpdb->prefix . 'lux_giftcard_orders';

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
		id int NOT NULL AUTO_INCREMENT,
		customer_id tinytext NOT NULL,
    time_stamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    asset_id tinyint NOT NULL,
    quantity decimal(20,7) NOT NULL,
    price decimal(20,2) NOT NULL,
    order_status tinyint DEFAULT '1',
    card_picture int NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);

  add_option('jal_db_version', $jal_db_version);
}

// Giftcard Orders
function lux_giftcard_get_customer_data_name($customer_id)
{
  $user = get_userdata($customer_id);
  return $user->display_name . " " . ucfirst($user->user_nicename);
}

function lux_giftcard_get_all_orders()
{
  global $wpdb;

  $table_name = $wpdb->prefix . 'lux_giftcard_orders';

  $result = $wpdb->get_results("SELECT * FROM $table_name");

  return $result;
}

function lux_giftcard_get_all_customer_orders($id)
{
  global $wpdb;

  $table_name = $wpdb->prefix . 'lux_giftcard_orders';

  $result = $wpdb->get_results("SELECT * FROM $table_name WHERE customer_id = '$id' ORDER BY time_stamp DESC");

  return $result;
}

function lux_giftcard_delete_order($order_id)
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'lux_giftcard_orders';
  $wpdb->query("DELETE FROM $table_name WHERE id='$order_id'");
}

function lux_giftcard_get_order_data($order_id)
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'lux_giftcard_orders';
  $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id='$order_id'");
  return $result[0];
}

function lux_giftcard_get_data($asset_id)
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'em_giftcard_sub_categories';
  $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id='$asset_id'");

  if (empty($result)) {
    throw new Exception("giftcard not found", 1);
  }

  return $result[0];
}

function lux_giftcard_create_new_order($data)
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'lux_giftcard_orders';
  $wpdb->insert(
    $table_name,
    $data
  );

  try {

    $customer = get_userdata($data["customer_id"]);
    $email = $customer->user_email;
    $name = $customer->display_name;
    $asset_type = "Giftcard";
    $asset = lux_giftcard_get_data($data['asset_id']);
    $asset_name = $asset->name;;
    $qty = $data["quantity"];
    $fee = $data["price"];

    $message_body = "Greetings $name,\n\nYou're recieving this eMail Notification because your Giftcard Order was placed successfully and is pending review.\n\nBelow are some of the order details\nAsset Type : $asset_type\nAsset : $asset_name\nQuantity : $qty\nAmount you get : $fee\n\nKindly return to Luxtrade and sign into your dashboard to continue trading Crypto and other digital assets.\n\nCheers!!!\nLuxtrade - Admin";

    wp_mail(
      $email,
      'LuxTrade Alert !!! Giftcard Order Created Successfully',
      $message_body
    );

    $name = lux_giftcard_get_customer_data_name($data["customer_id"]);

    $message_body = "Greetings,\n\nYou're recieving this eMail Notification because a customer by the name $name just made a Giftcard Order and is pending review.\n\nBelow are some of the order details\nAsset Type : $asset_type\nAsset : $asset_name\nQuantity : $qty\nFee : # $fee\n\nKindly return to Luxtrade and sign into WP Admin to view and update the order.\n\nCheers!!!\nLuxtrade - Admin";

    wp_mail(
      get_option('business_email'),
      'LuxTrade Alert !!! You have a new Buy Order',
      $message_body
    );
  } catch (\Throwable $th) {

    write_log($th);
  }
}

function lux_giftcard_mark_giftcard_as_declined($where)
{

  global $wpdb;
  $table_name = $wpdb->prefix . 'lux_giftcard_orders';

  $data = array(
    'order_status' => 0
  );

  $wpdb->update(
    $table_name,
    $data,
    $where
  );
}

function lux_giftcard_mark_giftcard_as_pending($where)
{

  global $wpdb;
  $table_name = $wpdb->prefix . 'lux_giftcard_orders';

  $data = array(
    'order_status' => 1
  );

  $wpdb->update(
    $table_name,
    $data,
    $where
  );
}

function lux_giftcard_mark_giftcard_as_approve($where)
{

  global $wpdb;
  $table_name = $wpdb->prefix . 'lux_giftcard_orders';

  $data = array(
    'order_status' => 2
  );

  $wpdb->update(
    $table_name,
    $data,
    $where
  );
}
