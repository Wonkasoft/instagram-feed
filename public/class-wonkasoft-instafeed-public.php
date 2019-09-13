<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/public
 * @author     Wonkasoft <support@wonkasoft.com>
 */
class Wonkasoft_Instafeed_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wonkasoft_Instafeed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wonkasoft_Instafeed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $wp_styles;
		$slick_css_load = true;
		$slick_themecss_load = true;
		foreach ( $wp_styles->queue as $style ) {
			if ( 'slick-js-style' === $style ) :
				$slick_css_load = false;
			endif;
			if ( 'slick-js-theme-style' === $style ) :
				$slick_themecss_load = false;
			endif;
		}

		if ( $slick_css_load ) {
			wp_enqueue_style( 'slick-js-style', str_replace( array( 'http:', 'https:' ), '', WONKASOFT_INSTAFEED_URL . 'includes/slick/slick.css' ), array(), '1.8.0', 'all' );
		}

		if ( $slick_themecss_load ) {
			wp_enqueue_style( 'slick-js-theme-style', str_replace( array( 'http:', 'https:' ), '', WONKASOFT_INSTAFEED_URL . 'includes/slick/slick-theme.css' ), array(), '1.8.0', 'all' );
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wonkasoft-instafeed-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wonkasoft_Instafeed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wonkasoft_Instafeed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $wp_scripts;
		$slick_js_load = true;
		foreach ( $wp_scripts->queue as $script ) {
			if ( 'slick-js' === $script ) :
				$slick_js_load = false;
			endif;
		}

		if ( $slick_js_load ) {
			wp_enqueue_script( WONKASOFT_INSTAFEED_SLUG . '-slick-js', str_replace( array( 'http:', 'https:' ), '', WONKASOFT_INSTAFEED_URL . 'includes/slick/slick.min.js' ), array( 'jquery' ), $this->version, true );
		}

		if ( $slick_js_load ) :
				wp_enqueue_script( $this->plugin_name . '-public-js', str_replace( array( 'http:', 'https:' ), '', plugin_dir_url( __FILE__ ) . 'js/wonkasoft-instafeed-public.js' ), array( 'jquery', WONKASOFT_INSTAFEED_SLUG . '-slick-js' ), $this->version, true );
			else :
				wp_enqueue_script( $this->plugin_name . '-public-js', str_replace( array( 'http:', 'https:' ), '', plugin_dir_url( __FILE__ ) . 'js/wonkasoft-instafeed-public.js' ), array( 'jquery', 'slick-js' ), $this->version, true );
			endif;

			wp_localize_script(
				$this->plugin_name . '-public-js',
				'WONKA_INSTAGRAM_AJAX',
				array(
					'insta_admin_ajax' => admin_url( 'admin-ajax.php' ),
					'insta_api_nonce' => wp_create_nonce( 'insta-ajaxnonce' ),
				)
			);
	}

}
