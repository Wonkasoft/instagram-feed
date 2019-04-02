<?php

/**
 * @author Wonkasoft
 * @implements Assets_Interface
 */

namespace Wc_Insta_Feed\Includes\Front;

use Wc_Insta_Feed\Includes\Front\Util;
use Wc_Insta_Feed\Helper;

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Instagram_Script_Loader')) {

    class Instagram_Script_Loader implements Util\Assets_Interface
    {
        public function __construct()
        {

        }
        /**
        *
        */
        public function wkcInit()
        {
            add_action('wp_enqueue_scripts', array($this, 'wkcEnqueueScripts_Public'));
            add_action('admin_enqueue_scripts', array($this, 'wkcEnqueueScripts_Admin'));
        }

        /**
        * Front scripts and style enqueue
        */
        public function wkcEnqueueScripts_Public()
        {
            wp_enqueue_style('insta-style', Insta_Feed_URL . 'assets/css/style.css');
            wp_enqueue_style('owl-style', Insta_Feed_URL . 'assets/css/owl.carousel.min.css');
            wp_enqueue_script('owl-script', Insta_Feed_URL . 'assets/js/owl.carousel.min.js', array( 'jquery' ), Insta_Feed_SCRIPT_VERSION);
            wp_enqueue_script('insta-script', Insta_Feed_URL . 'assets/js/plugin.js', array( 'jquery' ));
            wp_localize_script('insta-script', 'insta_script', array(
                'insta_admin_ajax' => admin_url('admin-ajax.php'),
                'insta_api_nonce' => wp_create_nonce('insta-ajaxnonce')
            ));

        }

        /**
        * Admin scripts and style enqueue
        */
        public function wkcEnqueueScripts_Admin()
        {
            wp_enqueue_script('select2-js', plugins_url().'/woocommerce/assets/js/select2/select2.min.js',array('jquery'));
	        wp_enqueue_style('select2', plugins_url().'/woocommerce/assets/css/select2.css');
            wp_enqueue_style('admin-style', Insta_Feed_URL . 'assets/css/admin.css', Insta_Feed_SCRIPT_VERSION);
            wp_enqueue_script('admin-script', Insta_Feed_URL . 'assets/js/admin.js', Insta_Feed_SCRIPT_VERSION);
            wp_localize_script( 'admin-script', 'insta_script', array( 'insta_admin_ajax' => admin_url( 'admin-ajax.php' ), 'insta_api_nonce' => wp_create_nonce('insta-ajaxnonce')) );

        }

    }
}
