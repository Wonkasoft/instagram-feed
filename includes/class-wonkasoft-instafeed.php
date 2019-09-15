<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/includes
 * @author     Wonkasoft <support@wonkasoft.com>
 */
class Wonkasoft_Instafeed {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wonkasoft_Instafeed_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WONKASOFT_INSTAFEED_VERSION' ) ) {
			$this->version = WONKASOFT_INSTAFEED_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wonkasoft-instafeed';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wonkasoft_Instafeed_Loader. Orchestrates the hooks of the plugin.
	 * - Wonkasoft_Instafeed_i18n. Defines internationalization functionality.
	 * - Wonkasoft_Instafeed_Admin. Defines all hooks for the admin area.
	 * - Wonkasoft_Instafeed_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wonkasoft-instafeed-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wonkasoft-instafeed-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wonkasoft-instafeed-admin.php';

		/**
		 * The class responsible for getting all instafeed errors.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wc-insta-errors.php';

		/**
		 * The class responsible for getting instagram settings.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-instagram-settings.php';

		/**
		 * The class responsible for getting all instafeed tags.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wonkasoft-instagram-tag.php';

		/**
		 * The class responsible for building the tag data table.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wonkasoft-list-instatag-data.php';

		/**
		 * The class responsible for getting all instafeed insta tags.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wonkasoft-insta-feed.php';

		/**
		 * The class responsible for feed list.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-feed-list.php';

		/**
		 * The class responsible for admin ajax requests.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wonkasoft-admin-ajax-functions.php';

		/**
		 * The class responsible for instagram api actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'api/class-instagram.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wonkasoft-instafeed-public.php';

		/**
		 * The class responsible for public ajax requests.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wonkasoft-public-ajax-functions.php';

		$this->loader = new Wonkasoft_Instafeed_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wonkasoft_Instafeed_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wonkasoft_Instafeed_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wonkasoft_Instafeed_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin_ajax = new Wonkasoft_Admin_Ajax_Functions( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wonkasoft_instafeed_admin_menu' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wonkasoft_instafeed_add_action_links' );
		$this->loader->add_action( 'init', $plugin_admin, 'wonkasoft_instafeed_init_loader' );
		$this->loader->add_filter( 'manage_edit-instagram_tags_columns', $plugin_admin, 'wonkasoft_instafeed_table_columns' );
		$this->loader->add_action( 'manage_instagram_tags_posts_custom_column', $plugin_admin, 'wonkasoft_instafeed_table_columns_default', 10, 2 );
		$this->loader->add_filter( 'manage_edit-instagram_tags_sortable_columns', $plugin_admin, 'wonkasoft_instafeed_table_sortable_columns' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'wonkasoft_instafeed_instagram_tag_save_post', 10 );
		$this->loader->add_action( 'wp_trash_post', $plugin_admin, 'wonkasoft_instagram_tags_posts_trashed', 10 );
		$this->loader->add_action( 'delete_post', $plugin_admin, 'wonkasoft_instagram_tags_posts_clean', 10 );
		$this->loader->add_action( 'wp_ajax_nopriv_get_instagram_images', $plugin_admin_ajax, 'get_instagram_images' );
		$this->loader->add_action( 'wp_ajax_get_instagram_images', $plugin_admin_ajax, 'get_instagram_images' );
		$this->loader->add_action( 'wp_ajax_nopriv_import_selected_insta_images', $plugin_admin_ajax, 'import_selected_insta_images' );
		$this->loader->add_action( 'wp_ajax_import_selected_insta_images', $plugin_admin_ajax, 'import_selected_insta_images' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wonkasoft_Instafeed_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_public_ajax = new Wonkasoft_Public_Ajax_Functions( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_nopriv_insta_images_by_tag_id', $plugin_public_ajax, 'insta_images_by_tag_id' );
		$this->loader->add_action( 'wp_ajax_insta_images_by_tag_id', $plugin_public_ajax, 'insta_images_by_tag_id' );
		$this->loader->add_action( 'wp_ajax_nopriv_insta_load_more_images', $plugin_public_ajax, 'insta_load_more_images' );
		$this->loader->add_action( 'wp_ajax_insta_load_more_images', $plugin_public_ajax, 'insta_load_more_images' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wonkasoft_Instafeed_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
