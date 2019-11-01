<?php
/**
 * The admin ajax request class.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/public
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class handles all the admin ajax requests.
 */
class Wonkasoft_Public_Ajax_Functions {

	/**
	 * Get instagram images by tag id
	 */
	public function insta_images_by_tag_id() {

		check_ajax_referer( 'insta-ajaxnonce', 'security', true ) || die( 'your nonce failed!' );

		$tag_id = isset( $_GET['tag'] ) ? intval( $_GET['tag'] ) : '';

		$data            = array();
		$product_content = '';

			$media_obj = new Wonkasoft_Instagram_Tag( $tag_id );

			$tag_medias = $media_obj->insta_get_only_one_tag_media();

			$tag_products = $media_obj->insta_get_tag_linked_products();

		if ( ! empty( $tag_medias ) ) {

			foreach ( $tag_medias as $tkey => $tag_media ) {

				$image_id = $tag_media['image_id'];

				$insta_hashtag = $tag_media['insta_hashtag'];

				$images = ! empty( $tag_media['insta_image'] ) ? maybe_unserialize( $tag_media['insta_image'] ) : '';

				$image = isset( $images ) ? $images : '';

				$insta_message = ( ! empty( $tag_media['insta_message'] ) ) ? $tag_media['insta_message'] : '';

				$preview = ! empty( $image ) ? '<div class="item screens"><div class="box-head"><span class="insta-tag" title="' . $insta_hashtag . '"></span></div><img data-message="' . esc_html( $insta_message ) . '" srcset="' . esc_attr( wp_get_attachment_image_srcset( $image_id, 'wonkasoft_instafeed_size' ) ) . '" /></div>' : 'N/A';

				$data['insta_pic'][] = array(

					'image_id'      => $image_id,

					'preview'       => $preview,

					'insta_message' => $insta_message,

					'insta_hashtag' => $insta_hashtag,

					'tag_id'        => $tag_id,

				);

			}
		}

		if ( ! empty( $tag_products ) ) {

			$args = array(
				'post_type'      => 'product',
				'post__in'       => $tag_products,
				'post_status'    => 'publish',
				'posts_per_page' => 4,
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {

				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					global $product;

					$logo      = get_site_icon_url();
					$site_name = strtolower( preg_replace( '/\s+/', '', get_bloginfo( 'name' ) ) );

					// Ensure visibility.
					if ( empty( $product ) || ! $product->is_visible() ) {
						return;
					}
					$product_content .= '<div class="wonka-insta-row wonka-insta-site-logo">';
					$product_content .= '<div class="col-12">';
					$product_content .= '<img class="wonka-insta-logo-img" src="' . $logo . '" />';
					$product_content .= '<div class="wonka-insta-site-info">@' . $site_name . '</div>';
					$product_content .= '</div>';
					$product_content .= '</div>';
					$product_content .= '<div class="wonka-insta-row wonka-insta-message">';
					foreach ( $data['insta_pic'] as $insta_img ) :
						$product_content .= '<div class="wonka-insta-message-container" data-image-id="' . $insta_img['image_id'] . '"><p>';
						$product_content .= $insta_img['insta_message'];
						$product_content .= '</p></div>';
					endforeach;
					$product_content .= '</div>';

					$product_content .= '<div class="wonka-insta-row wonka-insta-link">';
					$product_content .= '<div class="col-12">';

					$product_title = get_the_title( $product->get_id() );
					$url           = get_permalink( $product->get_id(), false );

					$product_content .= '<h4 class="wonka-insta-title">' . $product_title . '</h4>';
					$product_content .= '<a href="' . $url . '" class="wonka-btn">';
					$product_content .= __( 'Shop This Bag', 'wonkasoft_instafeed' );
					$product_content .= '</a>';
				}
				$product_content .= '</div>';
				$product_content .= '</div>';

				$data['insta_products'] = $product_content;
			}
		}

		if ( ! empty( $data ) ) {

			$response = array(
				'error' => false,
				'data'  => $data,
			);

		} else {

			$message = __( 'No media found regarding this tag', 'Wonkasoft_Instafeed' );

			$response = array(
				'error' => true,
				'data'  => $data,
			);

		}

		wp_send_json( $response );
		wp_die();

	}

	/**
	 * This functions loads more images to the instagram shop page.
	 */
	public function insta_load_more_images() {

		check_ajax_referer( 'insta-ajaxnonce', 'security', true ) || die( 'your nonce failed!' );

		$posts_per_page = ( isset( $_GET['posts_per_page'] ) ) ? array( 'posts_per_page' => wp_kses_post( wp_unslash( $_GET['posts_per_page'] ) ) ) : 10;
		$view           = ( isset( $_GET['view'] ) ) ? wp_kses_post( wp_unslash( $_GET['view'] ) ) : 'insta_feed';

		$response = array(
			'data' => '',
		);

		ob_start();
		$new_feed_list    = new Feed_List( $view );
		$data             = $new_feed_list->get_insta_tag_template( $posts_per_page );
		$response['data'] = trim( ob_get_clean(), "\n\t" );

		wp_send_json( $response );
		wp_die();
	}
}
