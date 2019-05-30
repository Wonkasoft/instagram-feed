<?php

/**
 * @author Wonkasoft
 * @version 2.0.0
 * This file handles Instagram tag Data
 */

namespace Wc_Insta_Feed\Helper\Tag;

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Wc_Tag')) {
    /**
     *get Instagram tag data
     */
    class Wc_Tag
    {
        public $tag_data = '';
        public $table_name = '';
        public $current_user = '';
        public $wpdb= '';
        public $table_products = '';


        public function __construct()
        {
            global $wpdb;

            $this->wpdb = $wpdb;
            $this->current_user = get_current_user_id();
            $this->table_products = $this->wpdb->prefix . 'posts';
        }

        public function insta_get_tag_data( $per_page = null )
        {

            $this->table_name = $this->wpdb->prefix . 'instagram_tags';

            $results = $this->wpdb->get_results( "SELECT * FROM $this->table_name ORDER BY priority ASC", ARRAY_A);

            if ( ! empty ( $per_page ) ) :
                $limited_posts = array();
                $limited_posts['count'] = count( (array)$results );
                for ($i=0; $i < $per_page; $i++) { 
                    $limited_posts[] = $results[$i];
                }
            endif;

            if ( ! empty ( $results ) && ! empty ( $per_page ) ) {
                return $limited_posts;
            } elseif ( ! empty ( $results ) ) {
                return $results;
            } else {
                return '';
            }

        }


        public function insta_get_tag_limited_data()
        {

            $this->table_name = $this->wpdb->prefix.'instagram_tags';

            $results = $this->wpdb->get_results( "Select id, linked_products, visiblity, status from $this->table_name", ARRAY_A);

            if( !empty( $results ) ) {
                return $results;
            } else {
                return '';
            }

        }

        public function delete_insta_tag_rows( $action, $tag_id )
        {

            $this->table_name = $this->wpdb->prefix.'instagram_tags';

            if( !empty( $action ) && $action == 'delete' ) {

                $response = $this->wpdb->delete(
                    $this->table_name,
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

        public function update_insta_tag_status( $action, $tag_id ){

            $this->table_name = $this->wpdb->prefix.'instagram_tags';

            switch ($action) {
              case 'activate':
                # code...
                $status = 1;
                break;

              case 'deactivate':
                # code...
                $status = 0;
                break;

              case 'delete':
                # code...
                $status = 0;
                break;

              default:
                # code...
                $status = 0;
                break;
            }

            $response = $this->wpdb->update(
              $this->table_name,
              array(
                'status' => $status,
              ),
              array(
                'id' => intval( $tag_id ),
              ),
              array(
                '%d',
              ),
              array(
                '%d',
              )
            );

            return $response;
        }

        public function insta_feed_get_tag_data_by_search( $search_query) {

            $new_arr = array();

            $this->table_name = $this->wpdb->prefix.'instagram_tags';

            $results = $this->wpdb->get_results( $this->wpdb->prepare( "Select * from $this->table_name where tag_name like %s", '%' . sanitize_title_for_query( $search_query ) . '%' ), ARRAY_A );

            if( !empty( $results ) ) {

                foreach ($results as $rkey => $rvalue) {
                    array_push( $new_arr, array(
                        'id'   => $rvalue['id'],
                        'tag_name'   => $rvalue['tag_name'],
                        'priority'  => $rvalue['priority'],
                        'visiblity' => !empty( $rvalue['visiblity'] ) ? maybe_unserialize($rvalue['visiblity']) : '',
                        'status'    => $rvalue['status'],
                        'linked_products' => !empty( $rvalue['linked_products'] ) ? maybe_unserialize($rvalue['linked_products']) : '',
                    ));
                }


            }

            return $new_arr;
        }

        public function insta_get_tag_data_by_tag_id( $tag_id )
        {

            $this->table_name = $this->wpdb->prefix.'instagram_tags';

            $results = $this->wpdb->get_row( $this->wpdb->prepare( "Select * from $this->table_name where id=%d", intval( $tag_id ) ) );

            if( !empty( $results ) ) {

                $new_arr = array(
                    'hashtag'   => $results->tag_name,
                    'priority'  => $results->priority,
                    'visiblity' => !empty( $results->visiblity ) ? maybe_unserialize($results->visiblity) : '',
                    'status'    => $results->status,
                    'linked_products' => !empty( $results->linked_products ) ? maybe_unserialize($results->linked_products) : '',
                );


                return $new_arr;

            } else {

                return '';

            }

        }

        public function get_all_products($tag_id)
        {

            $this->table_products = $this->wpdb->prefix . 'posts';
            $this->table_name = $this->wpdb->prefix.'instagram_tags';

            $results = $this->wpdb->get_results( "Select post_title, ID from $this->table_products where post_type='product' AND post_status='publish'", ARRAY_A);

            $linked_products = $this->wpdb->get_results( $this->wpdb->prepare( "Select id,linked_products from $this->table_name where id <> %d", intval( $this->tag_id ) ) );

            $new_arr = $arr =  $linked_arr = array();

            if( !empty( $results ) ) {

                // if( !empty( $linked_products ) ) {

                    // $linked_pro = wp_list_pluck( $linked_products, 'linked_products' );

                    foreach ($linked_pro as $lkey => $lvalue) {

                        if( !empty($lvalue)) {

                            array_push( $arr, maybe_unserialize($lvalue) );

                        }

                    }

                    $arr = !empty( $arr ) ? array_merge_recursive($arr):'';
                    $linked_arr = call_user_func_array('array_merge', $arr);

                // }

                foreach( $results as $key => $value ) {

                    if( ! in_array($value['ID'], $linked_arr) )
                        $new_arr[$value['ID']] = $value['post_title'];
                }

                return $new_arr;

            } else {

                return '';

            }

        }

        public function get_product_title_by_product_id($product_ids)
        {
            if( !empty( $product_ids ) ) {

                $args = array(
                    'post__in' => $product_ids,
                    'post_status' => 'publish',
                    'post_type' => 'product'
                );

                $posts = get_posts($args);

                $list = wp_list_pluck( $posts, 'post_title' );

                return $list;
            }

            return array();

        }


    }
}
