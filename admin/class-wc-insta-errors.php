<?php

/**
 * The class Feed_List functionality of the plugin.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/admin
 */

defined( 'ABSPATH' ) or die;

/**
 * Save shipping area data
 */
class WC_Insta_Errors {

	public $error_code = 0;

	public function __construct( $error_code = '' ) {
		$this->error_code = $error_code;

	}

	public function set_error_code( $code ) {
		if ( ! empty( $code ) ) {

			$this->error_code = $code;

		}
	}

	public function get_error_code() {
		return $this->error_code;
	}

	public function insta_print_notification( $message, $top_margin = '' ) {
		if ( is_admin() ) {

			if ( $this->error_code === 0 ) {

				echo '<div class="notice notice-success ' . $top_margin . '">';
				echo '<p>' . $message . '<p>';
				echo '</div>';

			} else if ( $this->error_code === 1 ) {

				echo '<div class="notice notice-error">';
				echo '<p>' . $message . '<p>';
				echo '</div>';
			}
		} else {

			if ( $this->error_code == 0 ) {

				wc_print_notice( $message, 'success' );

			} else if ( $this->error_code == 1 ) {

				wc_print_notice( $message, 'error' );

			}
		}

	}

}
