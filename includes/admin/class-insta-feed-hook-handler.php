<?php

/**
 * @author Webkul
 * @version 2.1.0
 * This file handles all admin end actions.
 */

namespace Wc_Insta_Feed\Includes\Admin;

use Wc_Insta_Feed\Includes\Admin;

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Insta_Feed_Hook_Handler')) {
    /**
     *
     */
    class Insta_Feed_Hook_Handler
    {
        public function __construct()
        {
            $function_handler = new Admin\Insta_Feed_Function_Handler;

            add_action('insta_general',  array( $function_handler, 'insta_general_settings' ), 10, 1 );

            // settings
            add_action('admin_menu', array( $function_handler, 'insta_feed_add_dashboard_menu'), 99 );

            add_action('admin_init', array( $function_handler, 'insta_register_settings' ) );

            add_action('woocommerce_add_instagram_hashtag', array( $function_handler, 'instagram_save_hashtag' ), 1 );

        }
    }
}
