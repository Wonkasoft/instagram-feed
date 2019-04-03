<?php

namespace Wc_Insta_Feed\Api;

/**
 * Instagram API class
 *
 * API Documentation: http://instagram.com/developer/
 *
 * @author Wonkasoft
 * @copyright Wonkasoft
 * @version 1.0
 */

class Instagram
{

    /**
     * Get info about a tag
     *
     * @param string $name Valid tag name
     *
     * @return mixed
     */
    public function getTag($name)
    {
        return $this->_makeCall('tags/' . $name);
    }

    /**
     * Get a recently tagged media.
     *
     * @param string $name Valid tag name
     * @param int $limit Limit of returned results
     *
     * @return mixed
     */
    public function getTagMedia( $name, $limit = 0 )
    {
        $params = array();

        if ($limit > 0) {
            $params['count'] = $limit;
        }

        return $this->_makeCall( $name );
    }

    /**
     * The call operator.
     *
     * @param string $function API resource path
     * @param bool $auth Whether the function requires an access token
     * @param array $params Additional request parameters
     * @param string $method Request type GET|POST
     *
     * @return mixed
     *
     * @throws \MetzWeb\Instagram\InstagramException
     */
    protected function _makeCall($function, $auth = false, $params = null, $method = 'GET')
    {

        $hashtag = trim( strtolower( $function ) );
        $url    = 'https://instagram.com/explore/tags/' . str_replace( '#', '', $hashtag );

        $insta_url = wp_remote_get( $url );

  			if ( is_wp_error( $insta_url ) ) {
  				return;
  			}

  			if ( 200 !== wp_remote_retrieve_response_code( $insta_url ) ) {
  				return;
  			}

  			$mix_content      = explode( 'window._sharedData = ', $insta_url['body'] );
  			$content_json  = explode( ';</script>', $mix_content[1] );
  			$content_array = json_decode( $content_json[0], true );

        if ( ! $content_array ) {
  				return;
  			}

  			if ( isset( $content_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
  				$insta_info = $content_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
  			} elseif ( isset( $content_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
  				$insta_info = $content_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
  			} else {
  				return;
  			}

  			if ( ! is_array( $insta_info ) ) {
  				return;
  			}

  			$instagram = array();
  			foreach ( $insta_info as $insta_value ) {

  				$id = '';
  				if ( ! empty( $insta_value['node']['id'] ) ) {
  					$id = $insta_value['node']['id'];
  				}

          $author = (isset($insta_value['node']['shortcode'])) ? $insta_value['node']['shortcode'] : '';

          $instagram[] = array(
  					'id' => $id,
            'author'=> $hashtag,
  					'url'=> preg_replace( '/^https?\:/i', '', $insta_value['node']['display_url'] ),
            'insta_message'=> $insta_value['node']['edge_media_to_caption']['edges'][0]['node']['text'],
  				);

  			}

        return json_encode($instagram);
    }
}
