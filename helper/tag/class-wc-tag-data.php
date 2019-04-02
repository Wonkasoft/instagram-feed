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

if (! class_exists('Wc_Tag_Data')) {
    
    /**
     *get Instagram tag data
     */

    class Wc_Tag_Data
    {
        public $tag_data = '';
        public $table_name = '';
        public $tag_id = '';
        public $wpdb= '';
        public $table_media = ''; 
        

        public function __construct($tag_id)
        {
            global $wpdb;
            
            $this->wpdb = $wpdb;

            $this->current_user = get_current_user_id();
            
            $this->tag_id = $tag_id;

            $this->table_media = $this->wpdb->prefix . 'instagram_tags_media';
        }

        public function insta_get_tag_media($per_page, $offset)
        {       

            $results = $this->wpdb->get_results( $this->wpdb->prepare("Select * from $this->table_media where tag_id=%d LIMIT $per_page OFFSET $offset", $this->tag_id) , ARRAY_A);

            if( !empty( $results ) ) {

                return $results;

            } else {
                
                return '';

            }
            
        }

        public function insta_get_only_one_tag_media()
        {       

            $results = $this->wpdb->get_row( $this->wpdb->prepare("Select * from $this->table_media where tag_id=%d AND status='1'", $this->tag_id) , ARRAY_A);

            if( !empty( $results ) ) {

                return $results;

            } else {
                
                return '';

            }
            
        }

        public function insta_tag_media_count() {

            $results = $this->wpdb->get_row( $this->wpdb->prepare("Select count(*) as count from $this->table_media where tag_id=%d AND status='1'", $this->tag_id) , ARRAY_A);

            if( !empty( $results ) ) {

                return $results;

            } else {
                
                return '';

            }
        }

        public function delete_insta_tag_rows( $action, $image_id )
        {        

            if( !empty( $action ) && $action == 'delete' ) {

                $response = $this->wpdb->delete(
                    $this->table_media,
                    array(
                    'image_id' => $image_id,
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

        public function update_insta_tag_status( $action, $image_id ) {
             
            
            switch ($action) {
              case 'enable':
                # code...
                $status = 1;
                break;
  
              case 'disable':
                # code...
                $status = 0;
                break;
     
              default:
                # code...
                $status = 0;
                break;
            }
  
            $response = $this->wpdb->update(
              $this->table_media,
              array(
                'status' => $status,
              ),
              array(
                'image_id' => $image_id,
              ),
              array(
                '%d',
              ),
              array(
                '%s',
              )
            );
            
            return $response;
        }

        public function insta_get_tag_media_by_search( $search_query) {
 
            $results = $this->wpdb->get_results( $this->wpdb->prepare( "Select * from $this->table_media where insta_username like %s AND tag_id=%d ", '%' . sanitize_title_for_query( $search_query ) . '%', $this->tag_id ), ARRAY_A );
           

            return $results;
        }
        
        public function insta_get_tag_products()
        {       
 
            $this->table_name = $this->wpdb->prefix.'instagram_tags';
            
            $result = $this->wpdb->get_row( $this->wpdb->prepare( "Select linked_products from $this->table_name where id=%d", intval( $this->tag_id ) ) );
          
            if( !empty( $result ) ) {
                
                return $result;

            } else {
                
                return '';

            }
            
        }

        public function insta_get_tag_name_by_tag_id()
        {       
 
            $this->table_name = $this->wpdb->prefix.'instagram_tags';
             
            $results = $this->wpdb->get_row( $this->wpdb->prepare( "Select tag_name from $this->table_name where id=%d", intval( $this->tag_id ) ) );

            if( !empty( $results ) ) {
                
                return $results;

            } else {
                
                return '';

            }
            
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
 
  
    }
}
