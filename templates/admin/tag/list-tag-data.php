<?php

use Wc_Insta_Feed\Helper\Tag;
use Wc_Insta_Feed\Inc;

if( ! defined ( 'ABSPATH' ) )

	exit;


if( !class_exists( 'WP_List_Table' ) ){

	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

}

if( ! class_exists ( 'WC_List_Tag_Data' ) ){

	class WC_List_Tag_Data extends WP_List_Table {

		public $tag_obj = '';

		public $error_obj = '';

		function __construct() {

			parent::__construct( array(

				'singular'  => 'Woocommerce Instagram Tag Data',
				'plural'    => 'Woocommerce Instagram Tags Data',
				'ajax'      => false

			) );

		}

		function prepare_items(){

			global $wpdb;

			$tag_obj = new Tag\Wc_Tag_Data(intval($_GET['tag_id']));

			$error_obj = new Inc\WC_Insta_Errors();

			$this->tag_obj = $tag_obj;

			$this->error_obj = $error_obj;

			$columns = $this->get_columns();

			$sortable = $this->get_sortable_columns();

			$hidden = $this->get_hidden_columns();

			$this->process_bulk_action();

			$data = $this->table_data();

			$totalItems = count( $data );

			$user = get_current_user_ID();

			$screen = get_current_screen();

			$perpage = $this->get_items_per_page( 'option_per_page', 9 );

			$this->_column_headers = array( $columns, $hidden, $sortable );

			if( empty( $per_page  )  || $per_page < 1 ){

				$per_page = $screen->get_option( 'per_page', 'default' );

			}

			function usort_reorder( $a, $b ){

				$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'image_id';
				
				$order = ( !empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc';

				$result = strcmp( $a[$orderby], $b[$orderby] );

				return ( $order === 'asc' ) ? $result : -$result;

			}

			usort( $data, 'usort_reorder' );

			$totalPages = ceil( $totalItems / $perpage );

			$currentPage = $this->get_pagenum();

			$data = array_slice( $data, ( ( $currentPage - 1 ) * $perpage ), $perpage );

			$this->set_pagination_args( array(

				"total_items" => $totalItems,

				"total_pages" => $totalPages,

				"per_page"    => $perpage

			) );

			$this->items = $data;

		}


		/**
		 * Define the columns that are going to be used in the table
		 * @return array $columns, the array of columns to use with the table
		 */

		function get_columns(){

			return $columns = array(

				'cb'  => '<input type="checkbox" />',

				'preview'    => __( 'Preview', 'insta_feed' ),

				'post_message'    => __( 'Post Message', 'insta_feed' ),

				'status'    => __( 'Status', 'insta_feed' )

			);

		}

		function column_default( $item, $column_name ){

			switch( $column_name ){

				case 'preview':

				case 'post_message':

				case 'status':

				return $item[ $column_name ];

				default:

				return print_r( $item, true );

			}

		}

		/**
		 * Decide which columns to activate the sorting functionality on
		 * @return array $sortable, the array of columns that can be sorted by the user
		 */

		public function get_sortable_columns(){

			return $sortable = array(
				'status' => array( 'status', true ),
			);

		}
		public function get_hidden_columns(){

			return array();

		}

		function column_cb( $item ){

			return sprintf('<input type="checkbox" id="image_id_%s" name="image_id[]" value="%s" />', $item[ 'image_id' ], $item[ 'image_id' ]);

		}

		private function table_data(){

			global $wpdb;

			$data = array();

			$results = $this->tag_obj->insta_get_tag_media(300, 0);

			if ( ! empty( $results ) ) {

				foreach ($results as $tag) {

					$image_id  = $tag['image_id'];

					$images   = !empty( $tag['images'] ) ? maybe_unserialize( $tag['images'] ) : '';

					$image = isset( $images ) ? $images : '';

					$preview = !empty( $image ) ? '<img src="'.$image.'" alt="image" height="75px">' : 'N/A';

					$post_message = '';

					$status  	= $tag['status']== 0 ? 'disabled' : 'enabled';

					$data[] = array(

						'image_id'   => $image_id,

						'preview'   => $preview,

						'post_message'   => $post_message,

						'status'   => $status,

					);

				}
			}

			return $data;

		}

		 /**
		 * Bulk actions on list.
		 */
		 public function get_bulk_actions() {
		 	$actions = array(
		 		'enable'   => 'Enable',
		 		'disable'  => 'Disable',
		 		'delete'   => 'Delete',
		 	);
		 	return $actions;
		 }

		/**
		 * Process bulk actions.
		 */
		public function process_bulk_action() {
			$count = 0;
			$delete = 0;

			if ( $this->current_action() === 'enable' ) {

				if ( isset( $_POST['image_id'] ) && ! empty( $_POST['image_id'] ) ) {

					if ( is_array( $_POST['image_id'] ) ) {

						$image_ids = $_POST['image_id'];

						foreach ( $image_ids as $image_id ) {

							$result = $this->tag_obj->update_insta_tag_status( $this->current_action(), $image_id );

							if ( $result ) {

								$count++;

							}
						}

						if( $count > 0 ) {

							$message = __( $count . ' Instagram tag image(s) enabled successfully ', 'insta_feed' );
							$this->error_obj->set_error_code(0);
							$this->error_obj->insta_print_notification($message);

						}
					}
				}

			} elseif ( $this->current_action() === 'disable' ) {

				if ( isset( $_POST['image_id'] ) && ! empty( $_POST['image_id'] ) ) {

					if ( is_array( $_POST['image_id'] ) ) {

						$image_ids = $_POST['image_id'];

						$count = 0;

						foreach ( $image_ids as $image_id ) {

							$result = $this->tag_obj->update_insta_tag_status( $this->current_action(), $image_id );

							if ( $result ) {
								$count++;
							}
						}

						if( $count > 0 ) {
							$message = __( $count . ' Instagram tag image(s) disabled successfully ', 'insta_feed' );
							$this->error_obj->set_error_code(0);
							$this->error_obj->insta_print_notification($message);
						}

					}
				}

			} elseif ( $this->current_action() === 'delete' ) {

				if ( isset( $_POST['image_id'] ) && ! empty( $_POST['image_id'] ) ) {

					if ( is_array( $_POST['image_id'] ) ) {

						$image_ids = $_POST['image_id'];

						$delete = 0;

						foreach ( $image_ids as $image_id ) {

							$result = $this->tag_obj->delete_insta_tag_rows( $this->current_action(), $image_id );

							if ( $result ) {

								$delete++;

							}

							if( $count > 0 ) {
								$message = __( $count . ' Instagram tag image(s) deleted successfully ', 'insta_feed' );
								$this->error_obj->set_error_code(0);
								$this->error_obj->insta_print_notification($message);
							}
						}
					}
				}
			}
		}

	}

}


$tag_data_list = new WC_List_Tag_Data();

$tag_data_list->prepare_items();

?>
<div class="wrap">

	<form method="POST">

		<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />

		<?php

		$tag_data_list->display();

		?>

	</form>

</div>
