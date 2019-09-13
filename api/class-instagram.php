<?php
/**
 * Instagram API class
 *
 * API Documentation: http://instagram.com/developer/
 *
 * @package Wonkasoft_Instafeed
 * @author Wonkasoft
 * @copyright Wonkasoft
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * This is the Instagram API class for fetching feeds.
 */
class Instagram extends Wonkasoft_Instafeed_Admin {

	/**
	 * Will be set with access token.
	 *
	 * @var string
	 */
	public $access_token = '';

	/**
	 * Will be set with api params.
	 *
	 * @var string
	 */
	public $params = '';

	/**
	 * This is the contructor of the class.
	 */
	public function __construct() {
		parent::__construct();
		$this->access_token = $this->get_instafeed_access_token();
		$this->params = array(
			'count' => 10,
			'tag' => null,
		);
	}

	/**
	 * Get info about a tag
	 *
	 * @param array $data contains tag data.
	 * @return null
	 */
	public function set_the_tag( $data ) {
		$this->params['tag'] = preg_replace( '/([#])/', '', $data['tag'] );
		$this->params['tag_id'] = $data['tag_id'];
		$this->params['priority'] = $data['priority'];
		$this->params['visibility'] = $data['visibility'];
		$this->params['status'] = $data['status'];
		return;
	}

	/**
	 * Get info about a tag
	 *
	 * @param array $ids contains array of ids to setup.
	 *
	 * @return
	 */
	public function set_ids_array( $ids ) {
		if ( ! empty( $ids ) ) :
			$this->params['ids'] = $ids;
		endif;
		return;
	}

	/**
	 * Get a recently tagged media.
	 *
	 * @param string $name Valid tag name
	 * @param int    $limit Limit of returned results
	 *
	 * @return mixed
	 */
	public function get_the_tag_media( $limit = 20 ) {

		if ( $limit > 0 ) {
			$this->params['count'] = $limit;
		}

		return $this->_makeCall();
	}

	/**
	 * The call operator.
	 *
	 * @return mixed
	 */
	protected function _makeCall() {

		$url    = 'https://api.instagram.com/v1/users/self/media/recent?access_token=' . $this->access_token;

		$args = array(
			'Content-Type' => 'application/json; charset=utf-8',
		);

		$insta_url = wp_safe_remote_get( $url, $args );

		if ( is_wp_error( $insta_url ) ) {
			return;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $insta_url ) ) {
			return;
		}

		$ig_data = json_decode( wp_remote_retrieve_body( $insta_url ) );

		$instagram = array(
			'full_name' => $ig_data->data[0]->user->full_name,
			'profile_picture_link'  => $ig_data->data[0]->user->profile_picture,
			'image_obj'    => array(),
		);

		if ( array_key_exists( 'tag', $this->params ) ) :
			foreach ( $this->params as $key => $value ) {
				if ( 'tag' === $key ) :
					$instagram['tag'] = $value;
				endif;
				if ( 'priority' === $key ) :
					$instagram['priority'] = $value;
				endif;

				if ( 'visibility' === $key ) :
					$instagram['visibility'] = $value;
				endif;

				if ( 'status' === $key ) :
					$instagram['status'] = $value;
				endif;
			}
		endif;

		if ( ! empty( $this->params['ids'] ) ) :
			for ( $i = 0; $i < count( (array) $ig_data->data ); $i++ ) {
				if ( in_array( $ig_data->data[ $i ]->id, $this->params['ids'] ) ) :
					array_push( $instagram['image_obj'], $ig_data->data[ $i ] );
				endif;
			}

			$instagram = json_decode( json_encode( $instagram ), true );

			return json_encode( $instagram );
			else :
				for ( $i = 0; $i < count( (array) $ig_data->data ); $i++ ) {
					if ( in_array( $this->params['tag'], $ig_data->data[ $i ]->tags ) ) :
						array_push( $instagram['image_obj'], $ig_data->data[ $i ] );
				  endif;
				}

				$instagram = json_decode( json_encode( $instagram ), true );

				wp_send_json_success( $instagram );
			endif;

	}
}
