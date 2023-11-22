<?php

// To deny anyone access to this file directly
if (!defined('ABSPATH')) exit;

// Create the Giftcard Orders Tables
function lux_giftcard_create_new_giftcard_order($data)
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'hid_ex_m_giftcard_orders';

  $wpdb->insert(
    $table_name,
    $data
  );

  try {
    $luxDBH = new LuxDBH;
    $customer = get_userdata($data["customer_id"]);
    $email = $customer->user_email;
    $name = $customer->display_name;
    $asset_type = "Giftcard";
    $asset = $luxDBH->lux_get_giftcard_sub_category_data($data['asset_id']);
    $asset_name = $asset->sub_category;
    $qty = $data["quantity"];
    $fee = $data["price"];

    $message_body = "Greetings $name,\n\nYou're recieving this eMail Notification because your Giftcard Order was placed successfully and is pending review.\n\nBelow are some of the order details\nAsset Type : $asset_type\nAsset : $asset_name\nQuantity : $qty\nAmount you get : $fee\n\nKindly return to Luxtrade and sign into your dashboard to continue trading Crypto and other digital assets.\n\nCheers!!!\nLuxtrade - Admin";

    wp_mail($email, 'LuxTrade Alert !!! Giftcard Order Created Successfully', $message_body);

    $name = hid_ex_m_get_customer_data_name($data["customer_id"]);

    $message_body = "Greetings,\n\nYou're recieving this eMail Notification because a customer by the name $name just made a Giftcard Order and is pending review.\n\nBelow are some of the order details\nAsset Type : $asset_type\nAsset : $asset_name\nQuantity : $qty\nFee : # $fee\n\nKindly return to Luxtrade and sign into WP Admin to view and update the order.\n\nCheers!!!\nLuxtrade - Admin";

    wp_mail(get_option('business_email'), 'LuxTrade Alert !!! You have a new Buy Order', $message_body);
  } catch (\Throwable $th) {
    write_log($th);
  }
}
