<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/includes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/includes
 * @author     Wonkasoft <support@wonkasoft.com>
 */
class Wonkasoft_Instafeed_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) :
			$error = 'WooCommerce is required to be active in order to use Wonkasoft InstaFeed Extension for WooCommerce!';
			die( 'Plugin NOT activated: ' . wp_kses_post( $error ) );
		endif;

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$ws_inst_tag_media = $wpdb->prefix . 'instagram_tags_media';

		$inst_tag_media_table = "CREATE TABLE IF NOT EXISTS $ws_inst_tag_media (
			id int(11) NOT NULL AUTO_INCREMENT, 
			tag_id int(11) NOT NULL, 
			image_id varchar(255) NOT NULL, 
			insta_hashtag varchar(255) NOT NULL, 
			insta_image longtext NOT NULL, 
			insta_message longtext NOT NULL, 
			priority int(10) NOT NULL, 
			visiblity longtext NOT NULL, 
			status boolean NOT NULL, 
			insta_image_obj longtext NOT NULL, 
			PRIMARY KEY (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $inst_tag_media_table );

		update_option( 'wonkasoft_instafeed_database_version', WONKASOFT_INSTAFEED_VERSION );

	}

}
