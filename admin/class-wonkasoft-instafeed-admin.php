<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/admin
 * @author     Wonkasoft, LLC <support@wonkasoft.com>
 */
class Wonkasoft_Instafeed_Admin {

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
	 * The db global.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $wonkadb    The global wpdb object.
	 */
	private $wonkadb;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name = WONKASOFT_INSTAFEED_SLUG, $version = WONKASOFT_INSTAFEED_VERSION ) {

		global $wpdb;
		$this->wonkadb = $wpdb;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
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
		wp_enqueue_style( 'select2', plugins_url() . '/woocommerce/assets/css/select2.css', array(), 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wonkasoft-instafeed-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( 'select2-js', plugins_url() . '/woocommerce/assets/js/select2/select2.min.js', array( 'jquery' ), time(), true );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wonkasoft-instafeed-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'WONKA_INSTAGRAM_AJAX',
			array(
				'insta_api_nonce' => wp_create_nonce( 'insta-ajaxnonce' ),
			)
		);
	}

	/**
	 * This sets up the admin menu for Wonkasoft Instafeed.
	 */
	public function wonkasoft_instafeed_admin_menu() {
		/**
		* This will check for Wonkasoft Tools Menu, if not found it will make it.
		*/
		global $wonkasoft_instafeed_page;
		if ( empty( $GLOBALS['admin_page_hooks']['wonkasoft_menu'] ) ) {
			$wonkasoft_instafeed_page = 'wonkasoft_menu';
			add_menu_page(
				'Wonkasoft',
				'Wonkasoft Tools',
				'manage_options',
				'wonkasoft_menu',
				array( $this, 'wonkasoft_instafeed_settings_display' ),
				WONKASOFT_INSTAFEED_IMG_PATH . '/wonka-logo-2.svg',
				100
			);

			add_submenu_page(
				'wonkasoft_menu',
				WONKASOFT_INSTAFEED_NAME,
				WONKASOFT_INSTAFEED_NAME,
				'manage_options',
				$wonkasoft_instafeed_page,
				array( $this, 'wonkasoft_instafeed_settings_display' )
			);
		} else {

			/**
			* This creates option page in the settings tab of admin menu
			*/
			$wonkasoft_instafeed_page = 'wonkasoft_instafeed_settings_display';
			add_submenu_page(
				'wonkasoft_menu',
				WONKASOFT_INSTAFEED_NAME,
				WONKASOFT_INSTAFEED_NAME,
				'manage_options',
				$wonkasoft_instafeed_page,
				array( $this, 'wonkasoft_instafeed_settings_display' )
			);
		}

		add_submenu_page(
			'edit.php?post_type=instagram_tags',
			'Settings',
			'Settings',
			'manage_options',
			$wonkasoft_instafeed_page,
			array( $this, 'wonkasoft_instafeed_settings_display' )
		);

		$this->insta_register_settings();

		if ( ! class_exists( 'WooCommerce' ) ) :
			deactivate_plugins( WONKASOFT_INSTAFEED_BASENAME );
		endif;
	}

	/**
	 * Register Option Settings
	 */
	public function insta_register_settings() {

		register_setting( 'insta-settings-group', '_instafeed_posts_limit' );
		register_setting( 'insta-settings-group', '_instafeed_shop_view' );
		register_setting( 'insta-settings-group', '_instafeed_posts_approval_email' );
		register_setting( 'insta-settings-group', '_instafeed_client_id' );
		register_setting( 'insta-settings-group', '_instafeed_access_token' );
	}

	/**
	 * This function returns the limt set on the settings page.
	 *
	 * @return $ratelimit
	 */
	public function get_instafeed_posts_limit() {
		return get_option( '_instafeed_posts_limit' );
	}

	/**
	 * This function returns the shop view set on the settings page.
	 *
	 * @return $shopview
	 */
	public function get_instafeed_shop_view() {
		return get_option( '_instafeed_shop_view' );
	}

	/**
	 * Get the admin email for approvals.
	 *
	 * @return $emails
	 */
	public function get_instafeed_posts_approval_email() {
		return get_option( '_instafeed_posts_approval_email' );
	}

	/**
	 * Get Wonkasoft-Feed client id.
	 *
	 * @return $client_id
	 */
	public function get_instafeed_client_id() {
		return get_option( '_instafeed_client_id' );
	}

	/**
	 * Get Instagram access token.
	 *
	 * @return $access_token
	 */
	public function get_instafeed_access_token() {
		return get_option( '_instafeed_access_token' );
	}

	/**
	 * Add the admin settings display.
	 */
	public function wonkasoft_instafeed_settings_display() {
		include_once( plugin_dir_path( __FILE__ ) . 'partials/wonkasoft-instafeed-admin-display.php' );
	}

	/**
	 * Wonkasoft_instafeed_add_action_links This adds the action links in the plugin area of the dashboard.
	 *
	 * @since 1.0.0 [<Adding of action links>]
	 */
	public function wonkasoft_instafeed_add_action_links() {
		include_once( plugin_dir_path( __FILE__ ) . 'partials/wonkasoft-instafeed-add-action-links.php' );
	}

	/**
	 * Output a select input box.
	 *
	 * @param array $field contains field args.
	 */
	public function wc_wonkasoft_instafeed_field( $field ) {

		$field['type']         = isset( $field['type'] ) ? $field['type'] : 'input';
		if ( 'select' === $field['type'] ) :
			$field['class']       = isset( $field['class'] ) ? $field['class'] : 'select form-control short';
		else :
			$field['class']       = isset( $field['class'] ) ? $field['class'] : 'form-control short';
		endif;
		$field['placeholder']   = isset( $field['placeholder'] ) ? ' placeholder="' . $field['placeholder'] . '" ' : '';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : '';
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
		$field['desc_help']      = isset( $field['desc_help'] ) ? $field['desc_help'] : false;
		$styles_set = ( ! empty( $field['style'] ) ) ? ' style="' . esc_attr( $field['style'] ) . '" ' : '';

		// Custom attribute handling.
		$custom_attributes = array();
		$output = '';

		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
			foreach ( $field['custom_attributes'] as $attribute => $value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}

		$output .= '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"> <label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . ' <i class="fa fa-question-circle" tool-tip="toggle" title="' . $field['desc_help'] . '"></i></label>';

		if ( 'select' === $field['type'] ) :
			$output .= '<select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" ' . implode( ' ', $custom_attributes ) . '>';
			foreach ( $field['options'] as $key => $value ) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
			}

			$output .= '</select> ';
		endif;

		if ( 'password' === $field['type'] ) :
			$output .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" ' . $styles_set . implode( ' ', $custom_attributes ) . ' value="' . esc_attr( $field['value'] ) . '"' . $field['placeholder'] . '/>';
		endif;

		if ( 'email' === $field['type'] ) :
			$output .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" ' . $styles_set . implode( ' ', $custom_attributes ) . ' value="' . esc_attr( $field['value'] ) . '"' . $field['placeholder'] . '/>';
		endif;

		if ( 'text' === $field['type'] ) :
			$output .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" ' . $styles_set . implode( ' ', $custom_attributes ) . ' value="' . esc_attr( $field['value'] ) . '"' . $field['placeholder'] . '/>';
		endif;

		if ( 'input' === $field['type'] ) :
			$output .= '<input id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" ' . $styles_set . implode( ' ', $custom_attributes ) . ' value="' . esc_attr( $field['value'] ) . '"' . $field['placeholder'] . '/>';
		endif;

		if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
			$output .= '<span class="description">' . wp_kses(
				$field['description'],
				array(
					'a' => array(
						'id'    => array(),
						'href'  => array(),
						'data-redirect' => array(),
						'data-client'   => array(),
					),
					'span'  => array(
						'id'    => array(),
						'style' => array(),
					),
				)
			) . '</span>';
		}

		$output .= '</p>';

		echo wp_kses(
			$output,
			array(
				'p' => array(
					'class' => array(),
				),
				'label' => array(
					'for'   => array(),
				),
				'i' => array(
					'class' => array(),
					'tool-tip'  => array(),
					'title' => array(),
				),
				'select' => array(
					'class' => array(),
					'id'  => array(),
					'name' => array(),
					'style' => array(),
					'value' => array(),
				),
				'option' => array(
					'value' => array(),
					'selected'  => array(),
				),
				'input' => array(
					'class' => array(),
					'id'  => array(),
					'name' => array(),
					'style' => array(),
					'type' => array(),
					'value' => array(),
				),
				'a' => array(
					'id'    => array(),
					'href'  => array(),
					'data-redirect' => array(),
					'data-client'   => array(),
				),
				'span'  => array(
					'id'    => array(),
					'style' => array(),
					'class' => array(),
				),
			)
		);

	}

	/**
	 * Adds the shortcode into wordpress.
	 *
	 * @since 1.0.0
	 */
	public function wonkasoft_instafeed_init_loader() {
		add_shortcode( 'wonkasoft_instafeed_feed', array( $this, 'add_wonkasoft_instagram_shop_feeds' ) );
		add_shortcode( 'wonkasoft_shop_feed', array( $this, 'add_wonkasoft_instagram_shop_feeds' ) );

		/**
		 * Registering Custom Post types.
		 */
		// Register Custom Post Type Members.
		$labels = array(
			'name'                  => _x( 'Instagram Shop Feeds', 'Wonkasoft_Instafeed' ),
			'singular_name'         => _x( 'InstaFeed', 'Wonkasoft_Instafeed' ),
			'menu_name'             => WONKASOFT_INSTAFEED_NAME,
			'name_admin_bar'        => WONKASOFT_INSTAFEED_NAME,
			'archives'              => __( 'Instafeed Archives', 'Wonkasoft_Instafeed' ),
			'attributes'            => __( 'Instafeed Attributes', 'Wonkasoft_Instafeed' ),
			'parent_item_colon'     => __( 'Parent Instafeed:', 'Wonkasoft_Instafeed' ),
			'all_items'             => __( 'Instagram Shop Feeds', 'Wonkasoft_Instafeed' ),
			'add_new_item'          => __( 'Add Instafeed', 'Wonkasoft_Instafeed' ),
			'add_new'               => __( 'New Instafeed', 'Wonkasoft_Instafeed' ),
			'new_item'              => __( 'New Instafeed', 'Wonkasoft_Instafeed' ),
			'edit_item'             => __( 'Edit Instafeed', 'Wonkasoft_Instafeed' ),
			'update_item'           => __( 'Update Instafeed', 'Wonkasoft_Instafeed' ),
			'view_item'             => __( 'View Instafeed', 'Wonkasoft_Instafeed' ),
			'view_items'            => __( 'View Instafeeds', 'Wonkasoft_Instafeed' ),
			'search_items'          => __( 'Search Instafeeds', 'Wonkasoft_Instafeed' ),
			'not_found'             => __( 'No Instafeeds found', 'Wonkasoft_Instafeed' ),
			'not_found_in_trash'    => __( 'No Instafeeds found in Trash', 'Wonkasoft_Instafeed' ),
			'featured_image'        => __( 'Instafeed Image', 'Wonkasoft_Instafeed' ),
			'set_featured_image'    => __( 'Set Instafeed image', 'Wonkasoft_Instafeed' ),
			'remove_featured_image' => __( 'Remove Instafeed image', 'Wonkasoft_Instafeed' ),
			'use_featured_image'    => __( 'Use as Instafeed image', 'Wonkasoft_Instafeed' ),
			'insert_into_item'      => __( 'Insert into Instafeed', 'Wonkasoft_Instafeed' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Instafeed', 'Wonkasoft_Instafeed' ),
			'items_list'            => __( 'Instafeeds list', 'Wonkasoft_Instafeed' ),
			'items_list_navigation' => __( 'Instafeeds list navigation', 'Wonkasoft_Instafeed' ),
			'filter_items_list'     => __( 'Filter Instafeeds list', 'Wonkasoft_Instafeed' ),
		);

		$args = array(
			'label'                 => __( 'Instafeed', 'Wonkasoft_Instafeed' ),
			'description'           => __( 'Instafeed information pages.', 'Wonkasoft_Instafeed' ),
			'labels'                => $labels,
			'supports'              => array( 'thumbnail', 'comments' ),
			'register_meta_box_cb'  => array( $this, 'add_instagram_tags_meta_boxes' ),
			'taxonomies'            => array( 'meta_data' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 150,
			'menu_icon'             => WONKASOFT_INSTAFEED_IMG_PATH . '/instagram.png',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'query_var'             => 'instagram_tags',
			'capability_type'       => 'post',
			'show_in_rest'          => true,
		);

		register_post_type( 'instagram_tags', $args );
	}

	/**
	 * This adds meta boxes for the instagram_tags post type.
	 *
	 * @since 1.0.0
	 */
	public function add_instagram_tags_meta_boxes() {

		add_meta_box( 'wsif_hashtag_setup', esc_html__( 'Hashtag Setup', 'Wonkasoft_Instafeed' ), array( $this, 'wsif_hashtag_setup_settings' ), 'instagram_tags', 'normal', 'high' );
	}

	/**
	 * This parses the instagram_tags post type hashtag settings.
	 */
	public function wsif_hashtag_setup_settings() {
		$post_id = get_the_ID();
		$post_tag_template = new Wonkasoft_Insta_Feed( $post_id );
		$post_tag_template->get_insta_add_tag_template();
	}

	/**
	 * Saving Current Tag post options.
	 *
	 * @param  string $post_id contains the surrent post_id.
	 * @return boolean          returns false if unsuccessful.
	 */
	public function wonkasoft_instafeed_instagram_tag_save_post( $post_id ) {

		if ( ! isset( $_POST['wonkasoft_intagram_tags_nonce'] ) || ! wp_verify_nonce( wp_kses_post( wp_unslash( $_POST['wonkasoft_intagram_tags_nonce'] ) ), 'wonkasoft_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$_insta_hashtag = ( isset( $_POST['_insta_hashtag'] ) ) ? wp_kses_post( wp_unslash( $_POST['_insta_hashtag'] ) ) : '';
		if ( ! empty( $_insta_hashtag ) ) :
			update_post_meta( $post_id, '_insta_hashtag', $_insta_hashtag );
		endif;

		$_insta_linked_products = ( isset( $_POST['_insta_linked_products'] ) ) ? wp_kses_post( wp_unslash( $_POST['_insta_linked_products'] ) ) : '';
		if ( ! empty( $_insta_linked_products ) ) :
			update_post_meta( $post_id, '_insta_linked_products', $_insta_linked_products );
		endif;

		$_hashtag_visibility = ( isset( $_POST['_hashtag_visibility'] ) ) ? wp_kses_post( wp_unslash( $_POST['_hashtag_visibility'] ) ) : '';
		if ( ! empty( $_hashtag_visibility ) ) :
			update_post_meta( $post_id, '_hashtag_visibility', $_hashtag_visibility );
		endif;

		$_hashtag_priority = ( isset( $_POST['_hashtag_priority'] ) ) ? wp_kses_post( wp_unslash( $_POST['_hashtag_priority'] ) ) : '';
		if ( ! empty( $_hashtag_priority ) ) :
			update_post_meta( $post_id, '_hashtag_priority', $_hashtag_priority );
		endif;

		$_hashtag_image_qty = ( isset( $_POST['_hashtag_image_qty'] ) ) ? wp_kses_post( wp_unslash( $_POST['_hashtag_image_qty'] ) ) : 20;
		if ( ! empty( $_hashtag_image_qty ) ) :
			update_post_meta( $post_id, '_hashtag_image_qty', $_hashtag_image_qty );
		endif;

		$_hashtag_status = ( isset( $_POST['_hashtag_status'] ) ) ? wp_kses_post( wp_unslash( $_POST['_hashtag_status'] ) ) : '0';
		if ( ! empty( $_hashtag_status ) ) :
			update_post_meta( $post_id, '_hashtag_status', $_hashtag_status );
			if ( is_array( $_hashtag_status ) ) :
				$str_status = maybe_unserialize( array_shift( $_hashtag_status ) );
				if ( is_array( $str_status ) ) :
					$str_status = array_shift( $str_status );
				endif;
			endif;
			$table = $this->wonkadb->prefix . 'instagram_tags_media';
			$this->wonkadb->update(
				$table,
				array(
					'status'        => $str_status,
				),
				array(
					'tag_id'        => $post_id,
				)
			);
		endif;
	}

	/**
	 * Builds the columns for the post type instagram_tags.
	 *
	 * @param  array $columns contains the instagram_tags columns.
	 * @return array          returns the instagram_tags columns.
	 */
	public function wonkasoft_instafeed_table_columns( $columns ) {

		return $columns = array(

			'cb'                            => '<input type="checkbox" />',

			'tag_id'                    => __( 'Tag ID', 'Wonkasoft_Instafeed' ),

			'tag_name'              => __( 'Tag Name', 'Wonkasoft_Instafeed' ),

			'linked_products'   => __( 'Linked Products', 'Wonkasoft_Instafeed' ),

			'hashtag_visibility'   => __( 'Hashtag Visibility', 'Wonkasoft_Instafeed' ),

			'priority'                  => __( 'Priority', 'Wonkasoft_Instafeed' ),

			'status'                    => __( 'Status', 'Wonkasoft_Instafeed' ),

		);
	}

	/**
	 * This set the data for the instagram_tags table.
	 *
	 * @param  string $column  contains the column slug.
	 * @param  string $post_id contains the post ID.
	 */
	public function wonkasoft_instafeed_table_columns_default( $column, $post_id ) {
		global $post;

		$tag_obj = new Wonkasoft_Instagram_Tag( $post_id );
		$error_obj = new WC_Insta_Errors();
		$results = $tag_obj->insta_get_tag_data_by_tag_id();

		$str_options = array(
			'hashtag',
			'priority',
			'fetch_qty',
		);
		$tagged_products = '';
		foreach ( $results as $key => $value ) {
			if ( in_array( $key, $str_options ) && is_array( $value ) ) :
				$results[ $key ] = array_shift( $value );
			endif;
			if ( 'linked_products' === $key && is_array( $value ) ) :
				$cur_value = maybe_unserialize( array_shift( $value ) );
				if ( is_array( $cur_value ) ) :
					foreach ( $cur_value as $product_id ) {
						if ( '' !== $product_id ) :
							$cur_product = wc_get_product( $product_id );

							if ( empty( $tagged_products ) ) :
								$tagged_products .= $cur_product->get_title();
							else :
								$tagged_products .= ', ' . $cur_product->get_title();
							endif;
						endif;
					}
				endif;
				$results[ $key ] = $tagged_products;
			endif;

			if ( 'visibility' === $key ) :
				$visibility_list = $results['visibility_list'];
				if ( is_array( $value ) ) :
					$cur_value = maybe_unserialize( array_shift( $value ) );
					if ( is_array( $cur_value ) ) :
						$cur_value = array_shift( $cur_value );
					endif;
					if ( '' !== $cur_value ) :
						$results[ $key ] = $visibility_list[ $cur_value ];
					else :
						$results[ $key ] = $cur_value;
					endif;
				endif;
			endif;

			if ( 'status' === $key ) :
				$statuses_list = $results['statuses_list'];
				if ( is_array( $value ) ) :
					$cur_value = maybe_unserialize( array_shift( $value ) );
					if ( is_array( $cur_value ) ) :
						$cur_value = array_shift( $cur_value );
					endif;
					if ( '' !== $cur_value ) :
						$results[ $key ] = $statuses_list[ $cur_value ];
					endif;
				endif;
			endif;
		}

		switch ( $column ) {
			case 'tag_id':
				$tag_id = $post_id;

				if ( empty( $tag_id ) ) {
					echo esc_html__( 'Unknown', 'Wonkasoft_Instafeed' );
				} else {
					printf( __( '%s', 'Wonkasoft_Instafeed' ), wp_kses_post( $tag_id ) );
				}

				$get_actions = new Wonkasoft_List_Instatag_Data();
				if ( 'trash' === $post->post_status ) :
					$actions = $get_actions->row_actions(
						array(
							'Restore'  => sprintf( '<a href="%s" aria-label="Restore “%s” from the Trash">Restore</a>', wp_nonce_url( admin_url( 'post.php?post=' . $post_id . '&action=untrash' ), 'untrash-post_' . $post_id ), $post_id ),
							'Delete Permanently' => sprintf( '<a href="%s" aria-label="Delete “%s” Permanently">Delete&nbsp;Permanently</a>', esc_url( get_delete_post_link( $post_id, '', true ) ), $post_id ),
						),
						false
					);
				else :
					$actions = $get_actions->row_actions(
						array(
							'Edit'  => sprintf( '<a href="%s" aria-label="Edit “%s”">Edit</a>', get_edit_post_link( $post_id, 'display' ), $post_id ),
							'Quick Edit'    => sprintf( '<button type="button" class="button-link editinline" aria-label="Quick edit “%s” inline" aria-expanded="false">Quick&nbsp;Edit</button>', $post_id ),
							'Trash' => sprintf( '<a href="%s" aria-label="Trash “%s”">Trash</a>', esc_url( get_delete_post_link( $post_id ) ), $post_id ),
						),
						false
					);
				endif;
				echo $actions;
				break;
			case 'tag_name':
				if ( empty( $post_id ) ) {
					echo esc_html__( 'Unknown', 'Wonkasoft_Instafeed' );
				} else {
					printf( __( '%s', 'Wonkasoft_Instafeed' ), $results['hashtag'] );
				}
				break;
			case 'linked_products':
				if ( empty( $post_id ) ) {
					echo esc_html__( 'Unknown', 'Wonkasoft_Instafeed' );
				} else {
					printf( __( '%s', 'Wonkasoft_Instafeed' ), $results['linked_products'] );
				}
				break;
			case 'hashtag_visibility':
				if ( empty( $post_id ) ) {
					echo esc_html__( 'Unknown', 'Wonkasoft_Instafeed' );
				} else {
					printf( __( '%s', 'Wonkasoft_Instafeed' ), $results['visibility'] );
				}
				break;
			case 'priority':
				if ( empty( $post_id ) ) {
					echo esc_html__( 'Unknown', 'Wonkasoft_Instafeed' );
				} else {
					printf( __( '%s', 'Wonkasoft_Instafeed' ), $results['priority'] );
				}
				break;
			case 'status':
				if ( empty( $post_id ) ) {
					echo esc_html__( 'Unknown', 'Wonkasoft_Instafeed' );
				} else {
					printf( __( '%s', 'Wonkasoft_Instafeed' ), $results['status'] );
				}
				break;

			default:
				break;
		}
	}

	/**
	 * This function will check to make sure that posts are only left in the database that have an existing parent post.
	 */
	public function wonkasoft_instagram_tags_posts_clean( $post_id ) {
		$table = $this->wonkadb->prefix . 'instagram_tags_media';
		$results = $this->wonkadb->get_results( "SELECT * FROM $table WHERE tag_id = $post_id", ARRAY_A );
		foreach ( $results as $key => $value ) {
			if ( false !== get_post_status( $value['image_id'] ) ) :
				wp_delete_post( $value['image_id'], true );
			endif;
			$this->wonkadb->delete(
				$table,
				array(
					'tag_id'    => $value['tag_id'],
				)
			);
		}
	}

	/**
	 * Decide which columns to activate the sorting functionality on
	 *
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	public function wonkasoft_instafeed_table_sortable_columns( $cols ) {

		return $cols = array(
			'tag_id'         => array( 'tag_id', true ),
			'tag_name'         => array( 'tag_name', true ),
			'priority'         => array( 'priority', true ),
			'status'           => array( 'status', true ),
		);
	}

	/**
	 * This is what builds the shortcode content.
	 */
	public function add_wonkasoft_instagram_shop_feeds( $atts ) {

		if ( is_shop() ) {
			$view = 'shop';
		} else {
			$view = 'insta_feed';
		}

		$obj = new Feed_List( $view );
		$obj->get_insta_tag_template( $atts );
	}
}
