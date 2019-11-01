<?php
/**
 * The post type list builder.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/admin
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {

	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

}

if ( ! class_exists( 'Wonkasoft_List_Instatag_Data' ) ) {
	/**
	 * This class builds the data table for the instagram_tags post type.
	 */
	class Wonkasoft_List_Instatag_Data extends WP_List_Table {

		/**
		 * The tag_id is the post id.
		 *
		 * @var string
		 */
		public $tag_id = '';

		/**
		 * Will be an object with the tag data.
		 *
		 * @var null
		 */
		public $tag_obj = null;

		/**
		 * Will contain any errors of the build.
		 *
		 * @var null
		 */
		public $error_obj = null;

		/**
		 * The contructor for the class
		 *
		 * @param string $tag_id post id that is passed in.
		 */
		public function __construct( $tag_id = null ) {

			$this->tag_id = ( ! empty( $tag_id ) ) ? $tag_id : null;
			parent::__construct(
				array(
					'singular' => __( 'Woocommerce Instagram Tag Data', 'Wonkasoft_Instafeed' ),
					'plural'   => __( 'Woocommerce Instagram Tags Data', 'Wonkasoft_Instafeed' ),
					'ajax'     => false,
					'screen'   => 'post_type=instagram_tags',
				)
			);

		}

		/**
		 * Retrieve tag data from the database
		 *
		 * @param int $per_page
		 * @param int $page_number
		 *
		 * @return mixed
		 */
		public function get_tag_images() {
			$get_tag_images = get_post_meta( $this->tag_id, 'tag_image_data', true );
			$get_tag_images = json_decode( json_encode( $get_tag_images ) );

			if ( ! empty( $get_tag_images ) ) :
				return $get_tag_images->image_obj;
			else :
				return;
			endif;
		}

		/**
		 * Delete a customer record.
		 *
		 * @param int $id tag image ID
		 */
		public function delete_tag_image( $id ) {
			$get_tag_images = get_post_meta( $this->tag_id, 'tag_image_data', true );
			$get_tag_images = json_decode( json_encode( $get_tag_images ) );
			foreach ( $get_tag_images->image_obj as $key => $value ) {
				if ( $id === $value->id ) :
					unset( $get_tag_images->image_obj[ $key ] );
					update_post_meta( $this->tag_id, 'tag_image_data', $get_tag_images );
				endif;
			}
		}

		/**
		 * Returns the count of records in the database.
		 *
		 * @return null|string
		 */
		public function record_count() {
			$get_tag_images = get_post_meta( $this->tag_id, 'tag_image_data', true );
			$get_tag_images = json_decode( json_encode( $get_tag_images ) );

			if ( ! empty( $get_tag_images ) ) :
				$count = count( (array) $get_tag_images->image_obj );
				return $count;
			else :
				return 0;
			endif;
		}

		/** Text displayed when no customer data is available */
		public function no_items() {
			_e( 'No images have been selected for this tag.', 'Wonkasoft_Instafeed' );
		}

		/**
		 * Render a column when no column specific method exists.
		 *
		 * @param array  $item
		 * @param string $column_name
		 *
		 * @return mixed
		 */
		public function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'preview':
					echo '<div class="insta-preview-image" data-id="' . $item->id . '"><img src="' . wp_get_attachment_image_src( $item->images->thumbnail->id, 'thumbnail', true ) . '" srcset="' . wp_get_attachment_image_srcset( $item->images->thumbnail->id, 'thumbnail', true ) . '" /></div>';
					break;
				case 'insta_message':
					echo '<span class="insta-preview-msg">' . $item->caption->text . '</span>';
					break;
				case 'likes':
					echo '<span class="insta-likes">' . $item->likes->count . '</span>';
					break;
				default:
					return print_r( $item, true ); // Show the whole array for troubleshooting purposes
			}
		}

		/**
		 * Define the columns that are going to be used in the table
		 *
		 * @return array $columns, the array of columns to use with the table
		 */
		public function get_columns() {
			return $columns = array(

				'preview'       => __( 'Preview', 'Wonkasoft_Instafeed' ),

				'insta_message' => __( 'Post Message', 'Wonkasoft_Instafeed' ),

				'likes'         => __( 'Likes', 'Wonkasoft_Instafeed' ),

			);

		}

		/**
		 * Columns to make sortable.
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			return $sortable_columns = array(
				'likes' => array( 'likes', true ),
			);
		}

		/**
		 * Handles data query and filter, sorting, and pagination.
		 */
		public function prepare_items() {

			$this->_column_headers = $this->get_column_info();

			$per_page     = $this->get_items_per_page( 'insta_tag_images_per_page', 10 );
			$current_page = $this->get_pagenum();
			$total_items  = self::record_count();

			$this->set_pagination_args(
				[
					'total_items' => $total_items, // WE have to calculate the total number of items
					'per_page'    => $per_page, // WE have to determine how many items to show on a page
				]
			);

			$this->items = self::get_tag_images();
		}

		/**
		 * This overrides the unwanted add of an extra _wpnonce field to my meta box.
		 */
		public function display_tablenav( $which ) {

		}
	}
}
