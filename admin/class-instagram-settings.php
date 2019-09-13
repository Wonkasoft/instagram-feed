<?php
/**
 * The class Instagram_Settings functionality of the plugin.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/admin
 */

defined( 'ABSPATH' ) || die;

/**
 * Get Instagram settings data
 */
class Instagram_Settings {

	/**
	 * Will be set to the table name.
	 *
	 * @var string
	 */
	protected $insta_tags_table = '';

	/**
	 * Will be set to the global $wpdb.
	 *
	 * @var string
	 */
	protected $instadb = '';

	/**
	 * The constructor for this class.
	 */
	public function __construct() {
		global $wpdb;
		$this->instadb = $wpdb;
		$this->insta_tags_table = $wpdb->prefix . 'instagram_tags';
	}

	/**
	 * Get limit on
	 *
	 * @return $ratelimit
	 */
	public function get_insta_rate_limit() {
		return get_option( '_hashtag_rate_limit' );
	}

	/**
	 * Get shop view.
	 *
	 * @return $shopview
	 */
	public function get_insta_shop_view() {
		 return get_option( '_insta_shop_view' );
	}

	/**
	 * Get tag id by tag name.
	 *
	 * @param string $tag contains the tag name of which to get and return the id of.
	 * @return     This returns the tag id of the tag name that was passed in.
	 */
	public function get_tag_id_by_tag_name( $tag ) {

		$tag_id = $this->instadb->get_row( $this->instadb->prepare( "SELECT id FROM $this->insta_tags_table WHERE tag_name = %s", $tag ) );

		return $tag_id;
	}

}
