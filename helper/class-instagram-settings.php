<?php

/**
 * @author Webkul
 * @version 2.1.0
 * This file handles settings data class.
 */

namespace Wc_Insta_Feed\Helper;

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Instagram_Settings')) {
    /**
     *
     */
    class Instagram_Settings implements Util\Settings_Interface
    {
        public function __construct()
        {
            //code
        }

        /**
         * @return $ratelimit
         */
        public function get_insta_rate_limit()
        {
            return get_option('_hashtag_rate_limit' );
        }

        /**
         * @return $shopview
         */
        public function get_insta_shop_view()
        {
            return get_option('_insta_shop_view' );
        }

        /**
         * @return $authorname
         */
        public function get_tag_id_by_tag_name( $tag)
        {
            global $wpdb;

            $insta_tags = $wpdb->prefix.'instagram_tags';

            $tag_id = $wpdb->get_row( $wpdb->prepare("Select id from $insta_tags where tag_name=%s", $tag));

            return $tag_id;
        }



    }
}
