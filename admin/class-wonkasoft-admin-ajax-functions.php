<?php
/**
 * The admin ajax request class.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class handles all the admin ajax requests.
 */
class Wonkasoft_Admin_Ajax_Functions {

	/**
	 * This function fetches the instagram images.
	 *
	 * @sends object returns an json object of images data.
	 */
	public function get_instagram_images() {

		check_ajax_referer( 'insta-ajaxnonce', 'security', true ) || die( 'your nonce failed!' );

		$tag = ( isset( $_GET['tag'] ) ) ? wp_kses_post( wp_unslash( $_GET['tag'] ) ) : '';
		$tag_id = ( isset( $_GET['tag_id'] ) ) ? wp_kses_post( wp_unslash( $_GET['tag_id'] ) ) : '';
		$insta_tag = new Wonkasoft_Insta_Feed( $tag_id );
		$result = $insta_tag->get_thickbox_template();

		wp_send_json_success( $result );
	}

	/**
	 *  Import images in db
	 */
	public function import_selected_insta_images() {

		check_ajax_referer( 'insta-ajaxnonce', 'security', true ) || die( 'your nonce failed!' );

		$id_array = ( isset( $_POST['insta_id'] ) ) ? json_decode( wp_kses_post( wp_unslash( $_POST['insta_id'] ) ), true ) : '';

		$tag = ( isset( $_POST['tag'] ) ) ? wp_kses_post( wp_unslash( $_POST['tag'] ) ) : '';

		$tag_id = ( isset( $_POST['tag_id'] ) ) ? wp_kses_post( wp_unslash( $_POST['tag_id'] ) ) : '';

		$priority = ( isset( $_POST['priority'] ) ) ? wp_kses_post( wp_unslash( $_POST['priority'] ) ) : '';

		$visibility = ( isset( $_POST['visibility'] ) ) ? wp_kses_post( wp_unslash( $_POST['visibility'] ) ) : '';

		$status = ( isset( $_POST['status'] ) ) ? wp_kses_post( wp_unslash( $_POST['status'] ) ) : '';

		$tag_data = array(
			'tag_id'     => $tag_id,
			'tag'        => $tag,
			'priority'   => $priority,
			'visibility' => $visibility,
			'status'     => $status,
		);

		$add_tag_media = new Wonkasoft_Instagram_Tag();

		if ( ! empty( $tag ) ) {

			$setting = new Instagram_Settings();

			$insta = new Instagram();
			$insta->set_the_tag( $tag_data );
			if ( is_array( $id_array ) && array_key_exists( 0, $id_array ) ) :
				$ids = $id_array;
				$insta->set_ids_array( $ids );
				$results = $insta->get_the_tag_media( count( (array) $ids ) );
			else :
				$ids = array( 'not_set' );
				$results = $insta->get_the_tag_media();
			endif;

			if ( ! empty( $results ) ) {

				$results = json_decode( $results );

				$db_response = $add_tag_media->insert_instagram_tag_records_to_db( $tag_id, $tag, $results );
				update_post_meta( $tag_id, 'tag_image_data', $results );

				$message = __( 'Successfully saved selected images to database.', 'Wonkasoft_Instafeed' );

				$response = array(
					'error' => false,
					'message' => $message,
					'data_obj'  => $results,
				);

			} else {

				update_post_meta( $tag_id, 'tag_image_data', $results );

				$message = __( 'Successfully saved selected images to database.', 'Wonkasoft_Instafeed' );

				$response = array(
					'error' => false,
					'message' => $message,
					'data_obj'  => $results,
				);

			}
		}
		wp_send_json_success( $response );
		wp_die();
	}
}
