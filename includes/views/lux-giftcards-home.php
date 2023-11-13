<?php

# === To deny anyone access to this file directly
if (!defined("ABSPATH")) die("Direct access forbidden");

# === Check if LuxGiftcardsHome is defined
if (!class_exists('LuxGiftcardsHome')) :
  # === Declear class if class does not exists
  class LuxGiftcardsHome
  {
    # === Load assets class
    public function render()
    {
      echo '<div class="wrap"><div id="lux-giftcard-home"></div></div>';
    }
  }
endif;
