<?php

/**
 * @package Insta_Feed_Install_Schema
 * @author Wonkasoft
 */

if (! class_exists('Insta_Feed_Install_Schema')) {
    /**
     *
     */
    class Insta_Feed_Install_Schema
    {

        public function __construct()
        {

            $this->create_pages();

        }

        public function create_pages() {

           register_post_type( 'instagram_tags' );

           $pages = apply_filters( 'instagram_create_pages',array(
               'instagram' => array(
                   'name'=>_x( 'instagram-feeds','Page slug', 'insta_feed' ),
                   'title'=> _x( 'Instagram feeds','Page title', 'insta_feed' ),
                   'content' => '[' . apply_filters( 'instagram_tags_content', 'instagram_shop_feed' ).']')
               )
           );
           foreach ( $pages as $key => $page ){
               $this->insta_page_creation( esc_sql( $page['name'] ), 'instagram_' . $key . '_page_id', $page['title'], $page['content'] );
           }
       }

       /**create pages*/
        public function insta_page_creation( $slug, $option = '', $page_title = '', $page_content = ''){

            global $wpdb;
            $option_value = get_option( $option );
            if ( $option_value > 0 && get_post( $option_value ) )
            return -1;
            $page_found = null;
            if ( strlen( $page_content ) > 0 ) 	{
            // Search for an existing page with the specified page content (typically a shortcode)
            $page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_type='instagram_tags' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
            }
            else
            {
            // Search for an existing page with the specified page slug
            $page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_type='instagram_tags' AND post_name = %s LIMIT 1;", $slug ) );
            }
            if ( $page_found ) {
                if ( ! $option_value )
                    update_option( $option, $page_found );
                    return $page_found;
                }
            $user_id=is_user_logged_in();
            $mp_post_type=$page_title=='Instagram feeds'?'page':'instagram_tags';
            $page_data = array(
                'post_status'       => 'publish',
                'post_type'         => $mp_post_type,
                'post_author'       => $user_id,
                'post_name'         => $slug,
                'post_title'        => $page_title,
                'post_content'      => $page_content,
                'post_parent'       => $post_parent,
                'comment_status'    => 'closed'
            );
            $page_id = wp_insert_post( $page_data );
            if ( $option )
                update_option( $option, $page_id );

            return $page_id;
        }

        function insta_feed_create_tables()
        {
            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $ws_insta_tags = $wpdb->prefix.'ws_instagram_tags';

            if ( $wpdb->get_var('SHOW TABLES LIKE ' . $ws_insta_tags ) != $ws_insta_tags ) :

            $tag_table = "CREATE TABLE IF NOT EXISTS ".$ws_insta_tags." (id int(11) NOT NULL AUTO_INCREMENT, tag_name varchar(255) NOT NULL, linked_products longtext NOT NULL, visiblity longtext NOT NULL, priority int(10) NOT NULL, status boolean NOT NULL, PRIMARY KEY (id)) $charset_collate;";

            dbDelta( $tag_table );

            $ws_inst_tag_media = $wpdb->prefix.'ws_instagram_tags_media';

            $inst_tag_media_table = "CREATE TABLE IF NOT EXISTS ".$ws_inst_tag_media." (id int(11) NOT NULL AUTO_INCREMENT, tag_id int(11) NOT NULL, image_id varchar(255) NOT NULL, insta_username varchar(255) NOT NULL, images longtext NOT NULL, insta_message longtext NOT NULL, priority int(10) NOT NULL, visiblity longtext NOT NULL, status boolean NOT NULL, PRIMARY KEY (id)) $charset_collate;";

            dbDelta( $inst_tag_media_table ;

            update_option( 'wonkasoft_instafeed_database_version', '1.0.0' );

        else: 

            $ws_insta_tags = $wpdb->prefix.'ws_instagram_tags';

            if ( $wpdb->get_var('SHOW TABLES LIKE ' . $ws_insta_tags ) != $table_name ) :
            $tag_table = "CREATE TABLE IF NOT EXISTS ".$ws_insta_tags." (id int(11) NOT NULL AUTO_INCREMENT, tag_name varchar(255) NOT NULL, linked_products longtext NOT NULL, visiblity longtext NOT NULL, priority int(10) NOT NULL, status boolean NOT NULL, PRIMARY KEY (id)) $charset_collate;";

            dbDelta($tag_table);

            $inst_tag_media = $wpdb->prefix.'ws_instagram_tags_media';

            $inst_tag_media_table = "CREATE TABLE IF NOT EXISTS ".$inst_tag_media." (id int(11) NOT NULL AUTO_INCREMENT, tag_id int(11) NOT NULL, image_id varchar(255) NOT NULL, insta_username varchar(255) NOT NULL, images longtext NOT NULL, insta_message longtext NOT NULL, priority int(10) NOT NULL, visiblity longtext NOT NULL, status boolean NOT NULL, PRIMARY KEY (id)) $charset_collate;";

            dbDelta( $inst_tag_media_table );

            update_option( 'wonkasoft_instafeed_database_version', '1.0.0' );

        }
    }

}
