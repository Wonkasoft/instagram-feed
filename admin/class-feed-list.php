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

defined( 'ABSPATH' ) || die;

/**
 * Class Feed_List.
 */
class Feed_List extends Wonkasoft_Instagram_Tag {

	/**
	 * Feed status.
	 *
	 * @var string
	 */
	public $status = '';

	/**
	 * Products linked to this feed.
	 *
	 * @var array
	 */
	public $linked_products = array();

	public $tag_data = '';

	public $insta_hashtag = '';

	public $hashtag_priority = '';

	public $fetch_qty = 20;

	public $hashtag_visibility = '';

	public $error_obj = '';

	public $view = '';

	public $instadb = '';

	public $linked_products_list = array();

	public function __construct( $view = '' ) {

		parent::__construct();

		$this->error_obj = new WC_Insta_Errors();

		$this->view = $view;

		$this->lightbox_container();

	}

	public function validate_tag_results( $results ) {

		if ( empty( $results ) ) {
			return false;
		} else {
			return true;
		}

	}

	public function setup_class_tag_data( $results, $atts ) {

		$data = array();

		foreach ( $results['posts'] as $key => $result ) {

			if ( $atts['posts_per_page'] === $key ) :
				break;
			endif;

			if ( ! empty( $result ) ) {

				if ( '1' !== $result['status'] ) {
					continue;
				}

				$check = $this->insta_check_visibility( $result['visibility'] );

				if ( ! $check ) {
					continue;
				}

				if ( ! empty( $result ) ) {

					$count = $results['count'];

					$image_id = $result['image_id'];

					$hashtag = $result['insta_hashtag'];

					$insta_message = $result['insta_message'];

					$images = ! empty( $result['insta_image'] ) ? maybe_unserialize( $result['insta_image'] ) : '';

					$image = isset( $images ) ? $images : '';

					$preview = ! empty( $image ) ? '<img src="' . $image . '">' : 'N/A';

					array_push(
						$data,
						array(

							'image_id'      => $image_id,

							'preview'       => $preview,

							'insta_message' => $insta_message,

							'count'         => $count,

							'tag_id'        => $result['tag_id'],

							'tag_name'      => $hashtag,

						)
					);

				}
			}
		}

		if ( ! empty( $data ) ) {

			$shop_view = get_option( '_insta_shop_view' );

			if ( $shop_view === '0' && ! empty( $this->view ) && $this->view == 'shop' ) {

				echo "<div class='slider-wrapper " . $this->view . "'>";

				foreach ( $data as $pdata ) {

					$this->generate_media_list_html( $pdata );

				}

				echo '</div>';

			} else {

				foreach ( $data as $pdata ) {

					$this->generate_media_list_html( $pdata );

				}
			}
		}
	}

	/**
	 * This begins the shortcode build.
	 *
	 * @param  array $atts contains an array of the atts passed in.
	 */
	public function get_insta_tag_template( $atts ) {

		$atts = shortcode_atts(
			array(
				'posts_per_page' => 10,
			),
			$atts
		);

		$posts_per_page = ( ! empty( $atts ) ) ? $atts['posts_per_page'] : '';
		if ( ! is_front_page() || ! is_home() ) :
			?>
		<div class="header-tag-container">
			<h2 class="instafeed-title">#Aperabags</h2>
		</div>
			<?php
		endif;
		if ( ! is_front_page() || ! is_home() ) :
			?>
			<div id="wrapper" class="insta-shop-page">
		<?php else : ?>
			<div id="wrapper">
		<?php endif; ?>

				<div id="wonkasoft-instafeed-feed" class="instagram instagram-feeds <?php echo $this->view; ?>" data-view="<?php echo $this->view; ?>">

					<div class="inside">

						<div class="instagram-wrap row wonka-insta-row">

							<?php

							$results = $this->insta_get_per_tag_data( $posts_per_page );

							$bool = $this->validate_tag_results( $results );

							if ( $bool ) {

								$this->setup_class_tag_data( $results, $atts );

							} else {

								$message = __( 'No Instagram tags have been set!', 'Wonkasoft_Instafeed' );
								$this->error_obj->set_error_code( 1 );
								$this->error_obj->insta_print_notification( $message );
							}

							?>

						</div>

					<?php

					if ( ! empty( $results ) || ! is_front_page() || ! is_home() ) :
						if ( $atts['posts_per_page'] < $results['count'] ) :
							?>
							<div class="instagram-wrap row wonka-insta-row"> 
								<button type="button" class="fetch-more-posts"><i class="fas fa-plus"></i></button>
							</div>
							<?php
						endif;
					endif;
					?>
					</div>
				</div>
			</div>
		<?php
	}

	public function generate_media_list_html( $data ) {

		$output = '';

		$output .= '<div class="insta-box wonka-insta-box" id="wonka-box-' . $data['tag_id'] . '" data-tag-id="' . $data['tag_id'] . '" data-image-id="' . $data['image_id'] . '">';
		$output .= '<div class="img-wrap">';
		$output .= $data['preview'];
		$output .= '<div class="box-head">';
		$output .= '</div>';
		$output .= '</div><!-- .img-wrap -->';
		$output .= '</div>';

		echo wp_kses(
			$output,
			array(
				'div'  => array(
					'class'         => array(),
					'id'            => array(),
					'data_tag_id'   => array(),
					'data_image_id' => array(),
				),
				'span' => array(
					'class' => array(),
					'title' => array(),
				),
				'img'  => array(
					'src' => array(),
				),
			)
		);
	}

	/**
	 * THis checks the visibility of the tag.
	 *
	 * @param  string $visibility [description]
	 * @return [type]             [description]
	 */
	public function insta_check_visibility( $visibility = '' ) {

		if ( ! empty( $this->view ) && $this->view == 'shop' ) {
			$viewer = 1;
		} else {
			$viewer = 2;
		}

		if ( ! empty( $visibility ) ) {

			if ( ! empty( $visibility ) && in_array( $viewer, array( $visibility ) ) ) {
				return true;
			} else {
				return false;
			}
		} else {

			if ( ! empty( $this->hashtag_visibility ) && in_array( $viewer, $this->hashtag_visibility ) ) {

				return true;
			} else {
				return false;
			}
		}

	}

	public function lightbox_container() {

		?>

		<template id="screenSliderTemplate">
			<div class="screens-template">
				<div class="screen-template-wrap" active-client="{{data-tag-id}}">
					<span class="close-icon"><i class="fas fa-times"></i></span>
					<div class="content">
						<div class="wsgrid-squeezy">
							<div class="ws-loader"></div>
							<div class="inner-content">
								<div class="slider-part">
									<div class="insta-modal slider-wrapper"></div>
								</div>

								<div class="info-part">
									<div class="insta-tag-products"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</template>

		<div id="sliderHolder"></div>

		<?php
	}
}
