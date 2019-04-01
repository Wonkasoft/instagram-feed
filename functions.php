<?php

/**
 * Plugin Name: Instagram Shop Feed Extension for WooCommerce
 * Plugin URI: https://store.webkul.com/instagram-shop-feed.html
 * Description: Instagram Shop Feed Extension for WooCommerce will add instagram images on product shop and dedicated pages on woocommerce store by use of instagram hashtags
 * Version: 1.0.0
 * Author: WebKul
 * Author URI: https://webkul.com
 * Domain Path: plugins/instagram-shop-feed
 * License URI: https://store.webkul.com/license.html
 * Text Domain: insta_feed
 * WC requires at least: 3.0.0
 * WC tested up to: 3.3.x
 */

if (!defined('ABSPATH')) {
    exit;
}

function define_constants(){

    ! defined('Insta_Feed_URL') && define('Insta_Feed_URL', plugin_dir_url(__FILE__));

    ! defined('Insta_Feed_FILE') && define('Insta_Feed_FILE', plugin_dir_path(__FILE__));

    !define( 'Insta_Feed_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

    ! defined('Insta_Feed_SCRIPT_VERSION') && define('Insta_Feed_SCRIPT_VERSION', '1.0.1');
}

define_constants();

if (! function_exists('insta_feed_install')) {

    function insta_feed_install()
    {
        if (! function_exists('WC')) {

            add_action('admin_notices', 'insta_feed_admin_notice');

        } else {

            new Woocommerce_Insta_Feed();
            do_action('insta_feed_init');

        }
    }

    add_action('plugins_loaded', 'insta_feed_install', 11);
}

/**
 * Admin notice function for Marketplace not found
 */
function insta_feed_admin_notice()
{
    ?>
    <div class="error">
        <p><?php _ex('Woocommerce Instagram Shop Feed Extension is enabled but not effective. It requires <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce Plugin</a> in order to work.', 'Alert Message: WooCommerce requires', 'mp_hyperlocal'); ?></p>
    </div>
    <?php
}

if (! function_exists('Insta_Feed_Install_Schema')) {
    /**
     * Schema install callback
     */
    function Insta_Feed_Install_Schema()
    {
        require_once(Insta_Feed_FILE . 'install.php');
        $obj = new Insta_Feed_Install_Schema();
        $obj->insta_feed_create_tables();
    }

    register_activation_hook( __FILE__, 'Insta_Feed_Install_Schema' );
}

if (!class_exists('Woocommerce_Insta_Feed')) {

    class Woocommerce_Insta_Feed
    {
        public function __construct()
        {

            add_action('insta_feed_init', array($this, 'insta_feed_includes'));
            load_plugin_textdomain( 'insta_feed', false, Insta_Feed_FILE . '/languages' );
        }

        public function insta_feed_includes()
        {
            require_once(Insta_Feed_FILE . 'includes/instagram-file-handler.php');
        }

    }
}
