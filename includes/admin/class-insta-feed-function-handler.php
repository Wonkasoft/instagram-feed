<?php

/**
 * @author Wonkasoft
 * @version 2.1.0
 * This file handles all admin end action callbacks.
 */

namespace Wc_Insta_Feed\Includes\Admin;

use Wc_Insta_Feed\Templates\Admin;
use Wc_Insta_Feed\Helper\Tag\Insta_Save_Tag;

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Insta_Feed_Function_Handler')) {
    /**
     *
     */
    class Insta_Feed_Function_Handler
    {

        public $insta_code = '';

        public function __construct()
        {
          add_filter( 'set-screen-option' , array( $this , 'insta_set_pagination' ), 10, 3 );
            //
        }

        /**
         * Add Menu under MP menu
         */
        public function insta_feed_add_dashboard_menu()
        {
            $insta_hook = add_menu_page( __('Instagram Shop Feeds', 'insta_feed'), __('Instagram Shop Feeds', 'insta_feed'), 'manage_options', 'insta-feed', array( $this, 'insta_feed_tag_template'), Insta_Feed_URL.'assets/images/instagram.png', 55 );
            add_submenu_page( __('insta-feed', 'insta_feed'), __('Add Instagram Tags', 'insta_feed'), __('Add Instagram Tags', 'insta_feed'), 'manage_options', 'woo-insta-tag', array( $this, 'insta_feed_tag_template') );
            add_submenu_page( __('insta-feed', 'insta_feed'), __('Settings', 'insta_feed'), __('Settings', 'insta_feed'), 'manage_options', 'insta-setting', array($this, 'insta_feed_settings_template'), 1, 1);
            add_action( "load-$insta_hook", array( $this , 'insta_add_options' ) );
        }



        /**
         * Add template view for instagram tag
         */
        public function insta_feed_tag_template()
        {

            if( isset( $_GET['action'] ) && $_GET['action'] == 'add' ) {

                $tag = new Admin\Tag\Insta_Tag();
                $tag->get_insta_add_tag_template();

            } else if( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['tag_id'] ) ) {

                $tag = new Admin\Tag\Insta_Tag( intval( $_GET['tag_id'] ) );
                $tag->get_insta_add_tag_template();

            }  else if( ( ! isset( $_GET['action'] ) )  && ( isset( $_GET['page'] ) && $_GET['page'] == 'woo-insta-tag' ) ) {

                $tag = new Admin\Tag\Insta_Tag();
                $tag->get_insta_add_tag_template();

            } else {

                insta_get_template_part('admin/tag/list-insta-tag');

            }
        }

        public function insta_add_options(){

          $option = 'per_page';
          $args = array(
                 'label' => __('Number of items per page:','insta_feed'),
                 'default' => 10,
                 'option' => 'tags_per_page'
                );
          add_screen_option( $option, $args );

        }
        function insta_set_pagination($status, $option, $value) {
            return $value;
        }
        /**
         * Add template view account details
         */
        public function insta_general_settings()
        {
            $tagsettings = new Admin\Settings();
            $tagsettings->get_insta_general_setting_template();
        }

        /**
         * Add template view for ship rate
         */
        public function insta_feed_settings_template()
        {

            $tagsettings = new Admin\Settings();
            $tagsettings->get_insta_setting_template();
        }

        /**
         * Register Option Settings
         */
        public function insta_register_settings()
        {

            register_setting('insta-settings-group', '_hashtag_rate_limit');
            register_setting('insta-settings-group', '_insta_shop_view');
        }


        /**
        * Save Shipping Area Details
        */

        public function instagram_save_hashtag($data)
        {
            $result = new Insta_Save_Tag($data);

        }


    }
}
