<?php

use Wc_Insta_Feed\Helper\Tag;
use Wc_Insta_Feed\Inc;

if( ! defined ( 'ABSPATH' ) )

	exit;


if( !class_exists( 'WP_List_Table' ) ){

	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

}

if( ! class_exists ( 'WC_List_Insta_Tags' ) ){

	class WC_List_Insta_Tags extends WP_List_Table {

		public $tag_obj = '';

		public $error_obj = '';

		function __construct() {

			parent::__construct( array(

				'singular'  => 'Woocommerce Instagram Tag',
				'plural'    => 'Woocommerce Instagram Tags',
				'ajax'      => false

			) );

		}

		function prepare_items(){

			global $wpdb;

			$tag_obj = new Tag\WC_Tag();
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

			$perpage = $this->get_items_per_page( 'tags_per_page', 10 );

			$this->_column_headers = array( $columns, $hidden, $sortable );

			if( empty( $per_page  )  || $per_page < 1 ){

				$per_page = $screen->get_option( 'per_page', 'default' );

			}

			function usort_reorder( $a, $b ){

				$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'tag_id';

				$order = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc';

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

				'tag_id'    => __( 'Tag', 'insta_feed' ),

				'tag_name'    => __( 'Tag Name', 'insta_feed' ),

				'linked_products'    => __( 'Linked Products', 'insta_feed' ),

        		'priority'  => __( 'Priority', 'insta_feed' ),

        		'status'    => __( 'Status', 'insta_feed' )

			);

		}

		function column_default( $item, $column_name ){

			switch( $column_name ) {

				case 'tag_id':

				case 'tag_name':

				case 'linked_products':

        		case 'priority':

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

				 'tag_name' => array( 'tag_name', true ),
				 'priority' => array( 'priority', true ),
				 'status' => array( 'status', true ),
			 );

		 }
		 public function get_hidden_columns(){

			 return array();

		 }

		 function column_cb( $item ){

			 return sprintf('<input type="checkbox" id="tag_id_%s" name="tag_id[]" value="%s" />', $item[ 'tag_id' ], $item[ 'tag_id' ]);

		 }

		 private function table_data(){

			global $wpdb;

			$data = array();

			 if ( isset( $_POST['s'] ) && ! empty( $_POST['s'] ) ) {

				 $search_query = $_POST['s'];

				 $results = $this->tag_obj->insta_feed_get_tag_data_by_search($search_query);


			} else {

				$results = $this->tag_obj->insta_get_tag_data();

			}

			 if ( ! empty( $results ) ) {

				foreach ($results as $tag) {

					$tag_id  = $tag['id'];

					$tag_name   = $tag['tag_name'];

					$linked_products   	= !empty( $tag['linked_products'] ) ? maybe_unserialize( $tag['linked_products'] ) : '';
					$linked_products = !empty( $linked_products ) ? maybe_unserialize($linked_products) : '';
					$product_arr = $this->tag_obj->get_product_title_by_product_id( $linked_products );
					$priority 	= $tag['priority'];

					$visiblity 	= !empty( $tag['visiblity'] ) ? maybe_unserialize($tag['visiblity']) : '';
					$status  	= $tag['status']== 0 ? 'disabled' : 'enabled';

					$data[] = array(

						'tag_id'   => $tag_id,

						'tag_name'   => $tag_name,

						'linked_products' =>  !empty( $product_arr ) ? implode( ", ", $product_arr ) : '',

						'priority' =>  $priority,

						'status'   => $status,

					);

				 }
			 }

			 return $data;

		 }

		 function column_tag_id( $item ){

			 $actions = array(

				 'edit' =>  sprintf( '<a href="admin.php?page=woo-insta-tag&action=edit&tag_id=%d">Edit</a>', $item[ 'tag_id' ] ),
				 'delete' =>  sprintf( '<a href="admin.php?page=woo-insta-tag&action=delete&tag_id=%d">Delete</a>', $item[ 'tag_id' ] ),
			 );

			 return sprintf( '%1$s %2$s', $item[ 'tag_id' ], $this->row_actions( $actions ) );

		 }

		 /**
		 * Bulk actions on list.
		 */
		public function get_bulk_actions() {
			$actions = array(
				'activate'    => 'Activate',
				'deactivate'  => 'Deactivate',
				'delete'      => 'Delete',
			);
			return $actions;
		}

		/**
		 * Process bulk actions.
		 */
		public function process_bulk_action() {
			$count = 0;
			$delete = 0;

			if ( isset( $_GET['action'] ) &&  $_GET['action'] === 'delete' ) {

				if ( isset( $_GET['tag_id'] ) && ! empty( $_GET['tag_id'] ) ) {

					$tag_id = intval( $_GET['tag_id'] );

					$result = $this->tag_obj->delete_insta_tag_rows( $_GET['action'], $tag_id );

					if ( $result ) {

						$message = __( 'Instagram tag deleted successfully ', 'insta_feed' );
						$this->error_obj->set_error_code(0);
						$this->error_obj->insta_print_notification($message);
						wp_redirect('admin.php?page=insta-feed');
						exit();

					} else {

						wp_redirect('admin.php?page=insta-feed');
						exit();
					}
				}

			} else if ( $this->current_action() === 'activate' ) {

				if ( isset( $_POST['tag_id'] ) && ! empty( $_POST['tag_id'] ) ) {

					if ( is_array( $_POST['tag_id'] ) ) {

						$tag_ids = $_POST['tag_id'];

						foreach ( $tag_ids as $tag_id ) {

							$result = $this->tag_obj->update_insta_tag_status( $this->current_action(), $tag_id );

							if ( $result ) {

								$count++;

							}
						}

						if( $count > 0 ) {

							$message = __( $count . ' Instagram tags activated successfully ', 'insta_feed' );
							$this->error_obj->set_error_code(0);
							$this->error_obj->insta_print_notification($message);

						}
					}
				}

			} elseif ( $this->current_action() === 'deactivate' ) {

				if ( isset( $_POST['tag_id'] ) && ! empty( $_POST['tag_id'] ) ) {

					if ( is_array( $_POST['tag_id'] ) ) {

						$tag_ids = $_POST['tag_id'];

						$count = 0;

						foreach ( $tag_ids as $tag_id ) {

							$result = $this->tag_obj->update_insta_tag_status( $this->current_action(), $tag_id );

							if ( $result ) {
								$count++;
							}
						}

						if( $count > 0 ) {
							$message = __( $count . ' Instagram tags deactivated successfully ', 'insta_feed' );
							$this->error_obj->set_error_code(0);
							$this->error_obj->insta_print_notification($message);
						}

					}
				}

			} elseif ( $this->current_action() === 'delete' ) {

				if ( isset( $_POST['tag_id'] ) && ! empty( $_POST['tag_id'] ) ) {

					if ( is_array( $_POST['tag_id'] ) ) {

						$tag_ids = $_POST['tag_id'];

						$delete = 0;

						foreach ( $tag_ids as $tag_id ) {

							$result = $this->tag_obj->delete_insta_tag_rows( $this->current_action(), $tag_id );

							if ( $result ) {

								$delete++;

							}

							if( $count > 0 ) {
								$message = __( $count . ' Instagram tags deleted successfully ', 'insta_feed' );
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


$tag_list = new WC_List_Insta_Tags();

$tag_list->prepare_items();

?>
<div class="wrap">

	<h1>Instagram Tag List <a href="<?php echo admin_url() . 'admin.php?page=woo-insta-tag&action=add'; ?>" class="page-title-action"><?php echo __( 'Add New', 'insta_feed' ); ?></a></h1>

	<form method="POST">

		<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />

		<?php

			$tag_list->search_box( 'Search tag', 'search-id' );

			$tag_list->display();

		?>

	</form>

</div>
