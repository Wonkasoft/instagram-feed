<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wonkasoft.com
 * @since             1.0.0
 * @package           Wonkasoft_Instafeed
 *
 * @wordpress-plugin
 * Plugin Name:       Wonkasoft InstaFeed Extension for WooCommerce
 * Plugin URI:        https://wonkasoft.com/wonkasoft_instafeed.html
 * Description:       Instagram Feed Extension for WooCommerce will add instagram images on product shop and dedicated pages on woocommerce store by use of instagram hashtags.
 * Version:           1.0.0
 * Author:            Wonkasoft
 * Author URI:        https://wonkasoft.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wonkasoft-instafeed
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined( 'WPINC' ) or die;

/**
 * Currently plugin version.
 *
 * @since 1.0.0
 */
define( 'WONKASOFT_INSTAFEED_PATH', plugin_dir_path( __FILE__ ) );
define( 'WONKASOFT_INSTAFEED_URL', plugin_dir_url( __FILE__ ) );
define( 'WONKASOFT_INSTAFEED_SLUG', plugin_basename( dirname( __FILE__ ) ) );
define( 'WONKASOFT_INSTAFEED_NAME', 'Wonkasoft InstaFeed' );
define( 'WONKASOFT_INSTAFEED_BASENAME', plugin_basename( __FILE__ ) );
define( 'WONKASOFT_INSTAFEED_IMG_PATH', plugins_url( WONKASOFT_INSTAFEED_SLUG . '/admin/img' ) );
define( 'WONKASOFT_INSTAFEED_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wonkasoft-instafeed-activator.php
 */
function activate_wonkasoft_instafeed() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wonkasoft-instafeed-activator.php';
	Wonkasoft_Instafeed_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wonkasoft-instafeed-deactivator.php
 */
function deactivate_wonkasoft_instafeed() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wonkasoft-instafeed-deactivator.php';
	Wonkasoft_Instafeed_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wonkasoft_instafeed' );
register_deactivation_hook( __FILE__, 'deactivate_wonkasoft_instafeed' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wonkasoft-instafeed.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wonkasoft_instafeed() {
		$plugin = new Wonkasoft_Instafeed();
		$plugin->run();
}
run_wonkasoft_instafeed();
