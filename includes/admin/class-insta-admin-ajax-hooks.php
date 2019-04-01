<?php

/**
 * @author Webkul
 * @version 2.0.0
 * This file handles all admin end ajax actions.
 */

namespace Wc_Insta_Feed\Includes\Admin;

use Wc_Insta_Feed\Includes\Admin;

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Insta_Admin_Ajax_Hooks')) {
    /**
     *Ajax Hooks
     */
    class Insta_Admin_Ajax_Hooks
    {
        public function __construct()
        {
            $ajax_functions = new Admin\Insta_Admin_Ajax_Functions;

            add_action('wp_ajax_nopriv_import_selected_insta_images', array($ajax_functions, 'import_selected_insta_images'));

            add_action('wp_ajax_import_selected_insta_images', array($ajax_functions, 'import_selected_insta_images'));

            add_action('wp_ajax_nopriv_insta_images_by_tag_id', array($ajax_functions, 'insta_images_by_tag_id'));

            add_action('wp_ajax_insta_images_by_tag_id', array($ajax_functions, 'insta_images_by_tag_id'));

            add_action('wp_ajax_nopriv_insta_load_more_images', array($ajax_functions, 'insta_load_more_images'));

            add_action('wp_ajax_insta_load_more_images', array($ajax_functions, 'insta_load_more_images'));

            add_action('wp_ajax_nopriv_get_insta_images', array($ajax_functions, 'get_insta_images'));

            add_action('wp_ajax_get_insta_images', array($ajax_functions, 'get_insta_images'));


        }
    }
}
