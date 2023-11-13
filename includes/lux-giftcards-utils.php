<?php

# === To deny anyone access to this file directly
if (!defined("ABSPATH")) die("Direct access forbidden");

# === Check if LuxGiftcardsUtils is defined
if (!class_exists('LuxGiftcardsUtils')) :
  # === Declear class if class does not exists
  class LuxGiftcardsUtils
  {
    # === Load assets class
    public function __construct()
    {
      add_action('admin_enqueue_scripts', array($this, 'load_assets'));
      add_action('after_setup_theme', array($this, 'add_support'));
      add_action('admin_menu', array($this, 'load_menu'));
    }

    # === Load plugin menu
    public function load_menu()
    {
      $home = new LuxGiftcardsHome;
      # === adding plugin in menu
      add_menu_page(
        'Giftcards', //page title
        'Giftcards', //menu title
        'manage_options', //capabilities
        'giftcards', //menu slug
        array($home, 'render'), //function
        'dashicons-index-card', // Icon
        10 // Position
      );
    }

    # === Load assets
    public function load_assets()
    {
      wp_enqueue_script('giftcards-js', LGURL . '/build/index.js', array('wp-element'), '1.0', true);
      wp_register_style('giftcards-css', LGURL . '/build/index.css', array(), time());
      wp_enqueue_style('giftcards-css');
    }

    # === Add supports
    public function add_support()
    {
      add_theme_support('title-tag');
      add_theme_support('post-thumbnails');
    }
  }
endif;
