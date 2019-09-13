<?php
/**
 * The class Wonkasoft_Instagram_Tag functionality of the plugin.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/admin
 */

defined( 'ABSPATH' ) || die;

/**
 * Get Instagram tag data
 */
class Wonkasoft_Instagram_Tag {

	/**
	 * Will be set with array of tag data.
	 *
	 * @var array
	 */
	public $tag_data = array();

	/**
	 * Will be set with the tag ID.
	 *
	 * @var string
	 */
	public $tag_id = '';

	/**
	 * Will be set with the tag name.
	 *
	 * @var string
	 */
	public $tag = '';

	/**
	 * Will be set with the table name.
	 *
	 * @var string
	 */
	public $table_instagram_tags_media = '';

	/**
	 * Will be set with current user object.
	 *
	 * @var object
	 */
	public $current_user = null;

	/**
	 * Will be set with global $wpdb.
	 *
	 * @var object
	 */
	public $instadb = null;

	/**
	 * Will be set with table of products.
	 *
	 * @var object
	 */
	public $table_posts = null;

	/**
	 * Contains a list of statuses.
	 *
	 * @var array
	 */
	public $statuses_list = array(
		'0' => 'disable',
		'1' => 'enable',
	);

	/**
	 * Contains visibility list for options.
	 *
	 * @var array
	 */
	public $visibility_list = array(
		'1' => 'Shop page',
		'2' => 'Dedicated page',
		'3' => 'Product page',
	);


	/**
	 * The class contructor.
	 *
	 * @param string $tag_id should receive tag ID.
	 */
	public function __construct( $tag_id = null ) {
		global $wpdb;

		$this->instadb = $wpdb;
		$this->tag_id = $tag_id;
		$this->current_user = get_current_user_id();
		$this->table_posts = $this->instadb->prefix . 'posts';
		$this->table_instagram_tags_media = $this->instadb->prefix . 'instagram_tags_media';
	}

	/**
	 * This retrieves that posts from the database.
	 *
	 * @param  integer $per_page Sets the per page count.
	 * @return array            returns an array of the posts.
	 */
	public function insta_get_per_tag_data( $per_page = 10 ) {

		$results = $this->instadb->get_results( "SELECT * FROM $this->table_instagram_tags_media GROUP BY tag_id ORDER BY priority ASC", ARRAY_A );

		if ( ! empty( $results ) ) :
			$limited_posts = array();
			$limited_posts['count'] = count( (array) $results );
			for ( $i = 0; $i < $per_page; $i++ ) {
				if ( array_key_exists( $i, $results ) ) :
					$limited_posts['posts'][] = $results[ $i ];
				endif;
			}
		endif;

		if ( ! empty( $results ) && ! empty( $per_page ) ) {
			return $limited_posts;
		} elseif ( ! empty( $results ) ) {
			return $results;
		} else {
			return '';
		}

	}

	/**
	 * This function saves the instagram tags.
	 *
	 * @param string $tag_id contains the tag_id that is passed to this function.
	 * @param string $tag    contains the tag name that is passed to this function.
	 * @param array  $tag_data contains an array of the current tag to insert to db.
	 */
	public function insert_instagram_tag_records_to_db( $tag_id, $tag, $tag_data ) {
		$this->tag_id = $tag_id;
		$this->tag = $tag;
		$this->tag_data = $tag_data;
		$status = maybe_unserialize( get_post_meta( $this->tag_id, '_hashtag_staus', true ) );
		$format = array(
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%d',
			'%s',
		);

		$cleaning = $this->instadb->get_results( "SELECT * FROM $this->table_instagram_tags_media", ARRAY_A );
		foreach ( $cleaning as $key => $value ) {
			if ( false === get_post_status( $value['tag_id'] ) ) :
				if ( false !== get_post_status( $value['image_id'] ) ) :
					wp_delete_post( $value['image_id'], true );
				endif;
				$this->instadb->delete(
					$this->table_instagram_tags_media,
					array(
						'tag_id'    => $value['tag_id'],
					)
				);
			endif;
		}

		$this->instadb->delete(
			$this->table_instagram_tags_media,
			array(
				'tag_id'    => $this->tag_id,
			)
		);

		$args = array(
			'posts_per_page' => -1,
			'post_type'     => array( 'attachment', 'revision', 'instagram_tags' ),
			'post_status'   => array( 'inherit', 'auto-draft', 'publish' ),
		);

		$parent = new WP_Query( $args );

		if ( $parent->have_posts() ) :
			while ( $parent->have_posts() ) {
				$parent->the_post();
				$cur_post = get_post();
				$post_id = get_the_ID();
				if ( false === get_post_status( $cur_post->post_parent ) ) :
					wp_delete_post( $post_id, true );
				endif;
				if ( $this->tag_id == $cur_post->post_parent ) :
					wp_delete_post( $post_id, true );
				endif;
				if ( 'instagram_tags' === get_post_type( $post_id ) && 'auto-draft' === get_post_status( $post_id ) ) :
					wp_delete_post( $post_id, true );
				endif;
			}
		endif;

		foreach ( $this->tag_data->image_obj as $key => $value ) {
			$media_upload = $this->upload_image( $value->images->standard_resolution->url, $this->tag_id );
			$media_upload = json_decode( json_encode( $media_upload ) );

			$this->instadb->insert(
				$this->table_instagram_tags_media,
				array(
					'tag_id'            => $this->tag_id,
					'image_id'          => $media_upload->id,
					'insta_hashtag'     => $this->tag,
					'insta_image'       => $media_upload->url,
					'insta_message'     => $value->caption->text,
					'priority'          => $this->tag_data->priority,
					'visibility'         => $this->tag_data->visibility,
					'status'            => $status,
					'insta_image_obj'   => json_encode( $value ),
				),
				$format,
			);
		}

	}

	/**
	 * This is a private function that uploads the instagram images into the media center.
	 *
	 * @param  string $url     contains the image url.
	 * @param  string $tag_id contains the tag ID.
	 * @return string          returns
	 */
	private function upload_image( $url, $tag_id ) {
		$image = array();
		if ( '' !== $url ) {

			$attachment_id = media_sideload_image( $url, $tag_id, 'instagram image ' . $tag_id, 'id' );
			if ( is_wp_error( $attachment_id ) ) {
				$error = $attachment_id->get_error_messages();
				return $error;
			} else {
				$image['id'] = $attachment_id;
				$image['url'] = wp_get_attachment_url( $attachment_id );
			}
		}
		return $image;
	}

	/**
	 * This gets images by tag_id.
	 *
	 * @return array returns query array.
	 */
	public function insta_get_only_one_tag_media() {

		$results = $this->instadb->get_results( $this->instadb->prepare( "SELECT * FROM $this->table_instagram_tags_media WHERE tag_id = %d", $this->tag_id ), ARRAY_A );

		if ( ! empty( $results ) ) {

			return $results;

		} else {

			return '';

		}

	}

	/**
	 * This is to get tag data.
	 *
	 * @return array returns an array of results.
	 */
	public function insta_get_tag_limited_data() {

		$results = $this->instadb->get_results( "SELECT id, linked_products, visibility, status FROM $this->table_instagram_tags_media", ARRAY_A );

		if ( ! empty( $results ) ) {
			return $results;
		} else {
			return '';
		}

	}

	/**
	 * This function deletes instagram tags.
	 *
	 * @param  string $action passed action to complete.
	 * @param  string $tag_id Passed tag ID.
	 * @return array         returns the delete response or false on failure.
	 */
	public function delete_insta_tag_rows( $action, $tag_id ) {
		if ( ! empty( $action ) && 'delete' === $action ) {

			$response = $this->instadb->delete(
				$this->table_instagram_tags_media,
				array(
					'id' => $tag_id,
				),
				array(
					'%d',
				)
			);

			return $response;

		} else {

			return false;

		}

	}

	/**
	 * This will fetch the tags linked products.
	 *
	 * @return array returns an array of the tags linked products.
	 */
	public function insta_get_tag_linked_products() {
		$get_tag_options = $this->insta_get_tag_data_by_tag_id();
		$linked_products = maybe_unserialize( array_shift( $get_tag_options['linked_products'] ) );

		return $linked_products;
	}

	/**
	 * Get tag data by tag id.
	 *
	 * @return array         returns an array of data.
	 */
	public function insta_get_tag_data_by_tag_id() {

		$results = get_post_meta( $this->tag_id );

		if ( ! empty( $results ) ) {

			$new_arr = array(
				'hashtag'   => ( ! empty( $results['_insta_hashtag'] ) ) ? $results['_insta_hashtag'] : array( '' ),
				'priority'  => ( ! empty( $results['_hashtag_priority'] ) ) ? $results['_hashtag_priority'] : array( 10 ),
				'fetch_qty'  => ( ! empty( $results['_hashtag_image_qty'] ) ) ? $results['_hashtag_image_qty'] : array( 20 ),
				'visibility' => ( ! empty( $results['_hashtag_visibility'] ) ) ? maybe_unserialize( $results['_hashtag_visibility'] ) : array( '' ),
				'status'    => ( ! empty( $results['_hashtag_status'] ) ) ? $results['_hashtag_status'] : array( '0' ),
				'linked_products' => ( ! empty( $results['_insta_linked_products'] ) ) ? maybe_unserialize( $results['_insta_linked_products'] ) : array( '' ),
				'visibility_list'   => $this->visibility_list,
				'statuses_list' => $this->statuses_list,
			);

			return $new_arr;

		} else {

			return '';

		}

	}

	/**
	 * Get all products for selection list.
	 *
	 * @return array returns an array of product ids.
	 */
	public function get_all_products() {

		// Get 10 most recent product IDs in date descending order.
		$query = new WC_Product_Query(
			array(
				'limit' => -1,
				'post_status' => 'publish',
				'return' => 'ids',
			)
		);

		$products = $query->get_products();
		$products_array = array();
		foreach ( $products as $key => $value ) {
			$products_array[ $value ] = wc_get_product( $value )->get_name();
		}

		return $products_array;
	}
}
