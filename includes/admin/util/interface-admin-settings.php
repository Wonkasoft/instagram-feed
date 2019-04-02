<?php

/**
 * @author Wonkasoft
 * @version 2.1.0
 * This file handles admin settings interface.
 */

namespace Wc_Insta_Feed\Includes\Admin\Util;

if (!defined('ABSPATH')) {
    exit;
}

interface Admin_Settings_interface
{
   
    /**
     * Add Menu under MP menu
     */
    public function insta_feed_add_dashboard_menu();

    /**
     * Register Option Settings
     */
    public function insta_register_settings();

    /**
    * Save Shipping Area Details
     */
    public function instagram_save_hashtag($data);

      
}
