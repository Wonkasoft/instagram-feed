<?php

/**
 * @author Wonkasoft
 * @version 2.0.0
 * This file handles all front end action callbacks.
 */

namespace Wc_Insta_Feed\Includes\Front;

use Wc_Insta_Feed\Templates\Front;
use Wc_Insta_Feed\Helper\Tag;
use Wc_Insta_Feed\Inc\WC_Insta_Errors;



if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Insta_Front_Function_Handler')) {
    /**
     *
     */
    class Insta_Front_Function_Handler extends WC_Insta_Errors implements Util\Functions_Interface
    {

        public function __construct()
        {

            add_shortcode('instagram_shop_feed', array( $this, 'add_instagram_shop_feeds') );

            add_action('woocommerce_after_shop_loop', array( $this, 'add_instagram_shop_feeds') );

            add_action('woocommerce_after_single_product_summary', array( $this, 'instagram_feeds_single_product_page'), 80 );
        }


        /**
         * Initialize function
         */
        public function instagram_feeds_single_product_page()
        {
            global $product;
            $tag_obj = new Tag\Wc_Tag();
            $results = $tag_obj->insta_get_tag_limited_data();
            $bool = false;
            $tag_id = '';
            $product_id = $product->get_id();

            if( !empty( $results) ) {

                foreach ($results as $rkey => $rvalue) {

                    $visiblity = !empty($rvalue['visiblity'])? maybe_unserialize($rvalue['visiblity']):'';
                    $status = !empty($rvalue['status']) ? $rvalue['status'] : '';
                    $lproducts = isset( $rvalue['linked_products'] ) && !empty( $rvalue['linked_products'] ) ? maybe_unserialize($rvalue['linked_products']):'';
                    if( !empty($lproducts) && in_array($product_id, $lproducts ) ) {
                        if( !empty($visiblity) && in_array('2', $visiblity) ) {
                          if(!$status){
                            continue;
                          }
                          $bool = true;
                          $tag_id = $rvalue['id'];
                          break;

                        }
                    }
                }

            }

            if( $bool && !empty($tag_id)) {

                $tag_data = new Tag\Wc_Tag_Data($tag_id);

                $offset = 0;

                $count = $tag_data->insta_tag_media_count();

                $per_page = get_option('posts_per_page');

                $per_page = !empty($per_page)? $per_page:8;

                $limit = $offset * $per_page;

                $tag_name = $tag_data->insta_get_tag_name_by_tag_id();

                $media = $tag_data->insta_get_tag_media( $per_page, $limit);

                $pdata = new Front\Instagram_Product_Data();

                $pdata->generate_instagram_product_template($product_id, $per_page, $media, $tag_name->tag_name);
            }

        }

        /**
         * instgram pictures after product summary
         */
        public function insta_feed_initialize()
        {



        }

         /**
        * add instagram feeds via shortcode
        */
        public function add_instagram_shop_feeds()
        {
            if( is_shop() ) {
                $view = 'shop';
            } else {
                $view = 'insta_feed';
            }
            $obj = new Front\Feed_List($view);
            $obj->get_insta_tag_template();
        }



    }
}
