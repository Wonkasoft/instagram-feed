<?php

/**
 * @author Webkul
 * @version 2.0.0
 * This file handles Instagram tag Data
 */

namespace Wc_Insta_Feed\Helper\Tag;
use Wc_Insta_Feed\Inc\WC_Insta_Errors;

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Insta_Save_Tag')) {
    /**
     *Save Instagram tag data
     */
    class Insta_Save_Tag extends WC_Insta_Errors
    {
        public $instagram_data = '';
        public $table_name = '';
        public $current_user = '';
        public $wpdb= '';

        public function __construct($data)
        {
            global $wpdb;

            $this->wpdb = $wpdb;
            $this->current_user = get_current_user_id();
            $this->table_name = $this->wpdb->prefix.'instagram_tags';
            $this->instagram_data = $data;
            $this->insta_feed_save_tag_data();
        }

        public function insta_feed_save_tag_data()
        {
            $insta_hashtag = sanitize_text_field( $this->instagram_data['_insta_hashtag'] );
            $linked_products = isset( $this->instagram_data['_insta_linked_products'] ) ? $this->instagram_data['_insta_linked_products'] : '';
            $hashtag_visiblity = isset( $this->instagram_data['_hashtag_visiblity'] ) ? $this->instagram_data['_hashtag_visiblity'] : '';
            $hashtag_priority = filter_var( $this->instagram_data['_hashtag_priority'], FILTER_SANITIZE_NUMBER_INT );
            $status = filter_var( $this->instagram_data['_hashtag_status'], FILTER_SANITIZE_NUMBER_INT );


            if( empty( $insta_hashtag ) ) {
                $message = __( 'Instagram hashtag is missing ', 'insta_feed' );
                parent::set_error_code(1);
                parent::insta_print_notification($message);
            }

            if( empty( $linked_products ) ) {

                $message = __( 'Linked products are missing ', 'insta_feed' );
                parent::set_error_code(1);
                parent::insta_print_notification($message);
            }

            if( empty( $hashtag_visiblity ) ) {

                $message = __( 'Hashtag for product visisblity option is missing ', 'insta_feed' );
                parent::set_error_code(1);
                parent::insta_print_notification($message);
            }

            if( empty( $hashtag_priority ) ) {

                $message = __( 'Hashtag priority option is missing ', 'insta_feed' );
                parent::set_error_code(1);
                parent::insta_print_notification($message);
            }


            if( $status < 0 ) {
                $message = __( 'Instagram tag status is empty ', 'insta_feed' );
                parent::set_error_code(1);
                parent::insta_print_notification($message);
            }

            if( parent::get_error_code() == 0 ) {

                if( isset( $this->instagram_data['save-hashtag']) ) {

                    $this->wpdb->insert(
                        $this->table_name, 
                        array(
                            'tag_name' => $insta_hashtag,
                            'linked_products' => maybe_serialize( $linked_products ),
                            'visiblity' => maybe_serialize( $hashtag_visiblity ),
                            'priority' => $hashtag_priority,
                            'status' => $status
                        ),
                        array(
                            '%s',
                            '%s',
                            '%s',
                            '%d',
                            '%d'
                        )
                    );

                    $message = __( 'Instagram tag created successfully', 'insta_feed' );
                    parent::insta_print_notification($message);

                } elseif ( isset( $this->instagram_data['update-hashtag']) && !empty( $this->instagram_data['_tag_id']) ) {

                    $this->wpdb->update(
                        $this->table_name,
                        array(
                            'tag_name' => $insta_hashtag,
                            'linked_products' => maybe_serialize($linked_products),
                            'visiblity' => maybe_serialize($hashtag_visiblity),
                            'priority' => $hashtag_priority,
                            'status' => $status
                        ),
                        array(
                            'id' => intval( $this->instagram_data['_tag_id'] )
                        ),
                        array(
                            '%s',
                            '%s',
                            '%s',
                            '%d',
                            '%d'
                        ),
                        array(
                            '%d'
                        )
                    );

                    $message = __( 'Instagram tag updated successfully', 'insta_feed' );
                    parent::insta_print_notification($message);

                }

            } else {

                $message = __( 'Please fill up all the required fields ', 'insta_feed' );
                parent::insta_print_notification($message);

            }


        }

    }
}
