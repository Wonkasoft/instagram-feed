<?php

/**
 * @author Wonkasoft
 * @implements Assets_Interface
 */

namespace Wc_Insta_Feed\Includes\Front;

use Wc_Insta_Feed\Includes\Front\Util;
use Wc_Insta_Feed\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Instagram_Script_Loader' ) ) {

	class Instagram_Script_Loader implements Util\Assets_Interface {

		public function __construct() {

		}
		/**
		 *
		 */
		public function wkcInit() {
			add_action( 'wp_enqueue_scripts', array( $this, 'wkcEnqueueScripts_Public' ), 55 );
			add_action( 'admin_enqueue_scripts', array( $this, 'wkcEnqueueScripts_Admin' ) );
		}

		/**
		 * Front scripts and style enqueue
		 */
		public function wkcEnqueueScripts_Public() {
			global $wp_scripts;
			$slick_js_load = true;
			$slick_css_load = true;
			$slick_themecss_load = true;
			foreach ( $wp_scripts->queue as $script ) {
				if ( 'slick-js' === $script ) :
					$slick_js_load = false;
				endif;
			}
			global $wp_styles;
			foreach ( $wp_styles->queue as $style ) {
				if ( 'slick-js-style' === $style ) :
					$slick_css_load = false;
				endif;
				if ( 'slick-js-theme-style' === $style ) :
					$slick_themecss_load = false;
				endif;
			}
			if ( $slick_css_load ) {
				wp_enqueue_style( 'slick-js-style', str_replace( array( 'http:', 'https:' ), '', plugin_dir_url( __FILE__ ) . 'slick/slick.css' ) );
			}

			if ( $slick_themecss_load ) {
				wp_enqueue_style( 'slick-js-theme-style', str_replace( array( 'http:', 'https:' ), '', plugin_dir_url( __FILE__ ) . 'slick/slick-theme.css' ) );
			}

			wp_enqueue_style( 'insta-style', Insta_Feed_URL . 'assets/css/style.css' );

			if ( $slick_js_load ) {
				wp_enqueue_script( WONKA_INSTA_FEED_NAME . '-slick-js', str_replace( array( 'http:', 'https:' ), '', plugin_dir_url( __FILE__ ) . 'slick/slick.min.js' ), array( 'jquery' ), 'all', true );
			}

			if ( $slick_js_load ) :
				wp_enqueue_script( 'wonka-insta-script', Insta_Feed_URL . 'assets/js/plugin.js', array( 'jquery', WONKA_INSTA_FEED_NAME . '-slick-js' ), time(), true );
			else :
				wp_enqueue_script( 'wonka-insta-script', Insta_Feed_URL . 'assets/js/plugin.js', array( 'jquery', 'slick-js' ), time(), true );
			endif;

			wp_localize_script(
				'wonka-insta-script',
				'WONKA_INSTAGRAM_AJAX',
				array(
					'insta_admin_ajax' => admin_url( 'admin-ajax.php' ),
					'insta_api_nonce' => wp_create_nonce( 'insta-ajaxnonce' ),
				)
			);

		}

		/**
		 * Admin scripts and style enqueue
		 */
		public function wkcEnqueueScripts_Admin() {
			wp_enqueue_script( 'select2-js', plugins_url() . '/woocommerce/assets/js/select2/select2.min.js', array( 'jquery' ), time(), true );
			wp_enqueue_style( 'select2', plugins_url() . '/woocommerce/assets/css/select2.css' );
			wp_enqueue_style( 'admin-style', Insta_Feed_URL . 'assets/css/admin.css', Insta_Feed_SCRIPT_VERSION );
			wp_enqueue_script( 'admin-script', Insta_Feed_URL . 'assets/js/admin.js', Insta_Feed_SCRIPT_VERSION, time(), true );
			wp_localize_script(
				'admin-script',
				'WONKA_INSTAGRAM_AJAX',
				array(
					'insta_admin_ajax' => admin_url( 'admin-ajax.php' ),
					'insta_api_nonce' => wp_create_nonce( 'insta-ajaxnonce' ),
				)
			);

		}

	}
}
