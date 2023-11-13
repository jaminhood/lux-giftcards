<?php

/**
 * Plugin Name: Lux Giftcards
 * Plugin URI: https://github.com/jaminhood/exchange-manager
 * Version: 1.0.0
 * Description: This plugin will access all giftcard orders and allow you make changes to them
 * Author: JaminHood
 * Author URI: https://github.com/jaminhood
 * License: GPU
 * Text Domain: em
 */

# === To deny anyone access to this file directly
if (!defined("ABSPATH")) die("Direct access forbidden");

# === Plugin path
define("LGPATH", plugin_dir_path(__FILE__));
# === Plugin url
define("LGURL", plugin_dir_url(__FILE__));

if (!class_exists('LuxGiftcards')) {
  class LuxGiftcards
  {
    # === Constructor method
    public function __construct()
    {
      # === Requesting files from external scripts
      require_once(LGPATH . "includes/lux-giftcards-utils.php");
      require_once(LGPATH . "includes/models/lux-giftcards-DBH.php");
      require_once(LGPATH . "includes/views/lux-giftcards-home.php");
      require_once(LGPATH . "includes/rest/lux-giftcards-rest.php");

      # === Call class init method
      $this->init();
    }

    # === Class init method
    public function init()
    {
      register_activation_hook(__FILE__, array($this, 'activate'));
      register_deactivation_hook(__FILE__, array($this, 'deactivate'));

      # === Instantiate the assets class
      new LuxGiftcardsUtils;
      # === Instantiate the controller class
      new LuxGiftcardsRest;

      // $input_data = array(
      //   'customer_id' => 1,
      //   'asset_id'      => 8,
      //   'quantity' => 5,
      //   'price' => "78.99",
      //   'card_picture' => 8,
      //   'order_status'  => 1
      // );

      // global $wpdb;
      // $table_name = $wpdb->prefix . 'hid_ex_m_giftcard_orders';

      // $wpdb->insert($table_name, $input_data);
      // $orders = hid_ex_m_get_all_giftcard_orders();
      // print_r($orders);
    }

    # === On activation
    public function activate()
    {
    }

    # === On deactivation
    public function deactivate()
    {
      # === Flush everything
      flush_rewrite_rules(true);
    }
  }

  new LuxGiftcards;
}
