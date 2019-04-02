<?php

/**
 * @author Wonkasoft
 * @version 2.0.0
 * This file handles all admin end ajax callbacks.
 */

namespace Wc_Insta_Feed\Includes\Admin;

use Wc_Insta_Feed\Includes\Admin\Util;
use Wc_Insta_Feed\Helper;
use Wc_Insta_Feed\Api;
use Wc_Insta_Feed\Templates\Admin\Tag;
use Wc_Insta_Feed\Inc\WC_Insta_Errors;

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Insta_Admin_Ajax_Functions')) {
    /**
     *
     */
    class Insta_Admin_Ajax_Functions implements Util\Admin_Ajax_Functions_Interface
    {

        public $wpdb = '';

        public $insta_table = '';

        public $insta_table_meta = '';


        public function __construct()
        {
            global $wpdb;

            $this->wpdb = $wpdb;

            $this->insta_table = $this->wpdb->prefix . 'instagram_tags';

            $this->insta_table_meta = $this->wpdb->prefix . 'instagram_tags_media';

        }

        /**
         *  import images in db
        */

        public function import_selected_insta_images() {

            if( check_ajax_referer('insta-ajaxnonce', 'nonce', false)) {

                $id_array = $_POST['insta_id'];

                $tag = $_POST['tag'];

                if( !empty( $tag ) && !empty($id_array) ) {

                    $ids = json_decode(stripslashes($id_array), true);

                    $setting = new Helper\Instagram_Settings();

                    $insta = new Api\Instagram();


                    $result = $insta->getTagMedia( $tag, 20 );
                    

                    if( !empty( $result ) ) {

                        $result = json_decode($result);

                        foreach($result as $item){
                            $image_link = $item->url;
                            $insta_message = $item->insta_message;
                            $image_id = $item->id;
                            $author = $item->author;
                            if( !empty( $ids ) && in_array( $image_id, $ids ) ) {

                                $images = $this->object_to_array($item->url);

                                $insta_data[$image_id] =  array(

                                    'username'          => $author,
                                    'images'            => $images,
                                    'profile_picture'   => '',
                                    'profile_picture'   => '',
                                    'insta_message'     => $insta_message,
                                );

                            }
                        }

                        $tag_id = $setting->get_tag_id_by_tag_name( $tag );

                        if( !empty( $tag_id ) ) {

                            $res = $this->insert_insta_records_to_db( $insta_data, $tag_id->id );
                            if( $res ) {

                                $message = __( 'Successfully imported selected images', 'insta_feed' );

                                $response = array(
                                    'error' => false,
                                    'message' => $message,
                                );

                            } else {

                                $message = __( 'There is some issue while importing, please try again', 'insta_feed' );

                                $response = array(
                                    'error' => true,
                                    'message' => $message,
                                );
                            }

                        } else {

                            $message = __( 'Tag mismatched, please try again', 'insta_feed' );

                            $response = array(
                                'error' => true,
                                'message' => $message,
                            );
                        }
                    }
                }

                wp_send_json($response);
                wp_die();
            }

        }
        public function get_insta_images(){

          if( check_ajax_referer('insta-ajaxnonce', 'nonce', false) ) {

            $tag = ( isset( $_POST['tag'] ) ) ? $_POST['tag'] : '';
            $insta_Tag = new Tag\Insta_Tag();
            $result = $insta_Tag->get_thickbox_template( $error_obj = '', $setting = '', $tag );

            return $result;

          }

        }

        /**
         *  load more images instagram
        */

        public function insta_load_more_images() {

            if( check_ajax_referer('insta-ajaxnonce', 'nonce', false)) {

                $tag_id = isset($_GET['tag_id']) ? intval($_GET['tag_id']) : '';
                $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : '';
                $paged = isset($_GET['paged']) ? intval($_GET['paged']) : '';
                $custom_options = get_post_meta($product_id,'insta_product_meta',true);
                $visiblity_array = $status_array = array();

                if( !empty( $tag_id ) && !empty($product_id) && !empty($paged) ) {

                    $res = $this->is_tag_valid($tag_id);

                    if( $res ) {

                        $linked_product = !empty( $res['linked_products'] ) ? maybe_unserialize( $res['linked_products'] ) : '';

                        if( !empty( $linked_product ) && in_array( $product_id, $linked_product ) ) {

                            if( !empty( $custom_options ) ) {

                                if( isset( $custom_options['status'] ) ) {
                                    $status_array = $custom_options['status'];
                                }

                                if( isset( $custom_options['visblity'] ) ) {
                                    $visiblity_array = $custom_options['visblity'];
                                }
                            }

                            $tag_data = new Helper\Tag\Wc_Tag_Data($tag_id);

                            $offset = $paged;

                            $count = $tag_data->insta_tag_media_count();

                            $per_page = get_option('posts_per_page');

                            $per_page = !empty($per_page)? intval($per_page):8;

                            $limit = $offset * $per_page;

                            $media = $tag_data->insta_get_tag_media( $per_page, $limit);

                            if( !empty( $media ) ) {

                                ob_start();

                                foreach ($media as $mkey => $mvalue) {

                                    $image_id = !empty($mvalue['image_id']) ? $mvalue['image_id'] : '';

                                    // image is disbled
                                    if( !empty( $status_array ) && isset( $status_array[$image_id ] ) &&  $status_array[$image_id ] == '0' )  {
                                        continue;
                                    }

                                    if( !empty( $visiblity_array ) && isset( $visiblity_array[$image_id ] ) &&  !in_array( '1', $visiblity_array[$image_id ] ) )  {
                                        continue;
                                    }

                                    $images = !empty( $mvalue ) ? maybe_unserialize( $mvalue['images'] ) : '';

                                    $image = isset( $images ) ? $images : '';

                                    $author   = $mvalue['insta_username'];

                                    $preview = !empty( $image ) ? '<img src="'.$image.'" alt="'.$author.'" >' : 'N/A';


                                    ?>

                                    <div class="insta-box" id="<?php echo $mvalue['tag_id']; ?>" data-image-id="<?php echo $mvalue['image_id']; ?>">

                                        <?php echo $preview; ?>

                                        <div class="box-head">

                                            <span class="pic-author" title="publisher"><?php echo $author; ?></span>

                                        </div>

                                    </div>

                                    <?php

                                }

                                $list = ob_get_clean();

                                $response = array(
                                    'error' => false,
                                    'message' => $list,
                                );

                            } else {


                                $response = array(
                                    'error' => false,
                                    'message' => '',
                                );

                            }


                        } else {

                            $message = __( 'Tag mismatched, please try again', 'insta_feed' );

                            $response = array(
                                'error' => true,
                                'message' => $message,
                            );
                        }


                    } else {

                        $message = __( 'Tag mismatched, please try again', 'insta_feed' );

                        $response = array(
                            'error' => true,
                            'message' => $message,
                        );
                    }

                } else {

                    $message = __( 'Tag mismatched, please try again', 'insta_feed' );

                    $response = array(
                        'error' => true,
                        'message' => $message,
                    );
                }

                wp_send_json($response);
                wp_die();
            }

        }

         /**
         *  get instagram images by tag id
        */

        function insta_images_by_tag_id() {

            if( check_ajax_referer( 'insta-ajaxnonce', 'nonce', false ) ) {

                $tag_id = isset( $_GET['tag'] ) ? intval( $_GET['tag'] ): '';
                $is_exist = $this->is_tag_exist( $tag_id );
                $data = array();
                $product_content = '';
                if( $is_exist ) {

                    $media_obj = new Helper\Tag\Wc_Tag_Data( $tag_id );
                    $offset = 0;
                    $per_page = 100;

                    $tag_medias = $media_obj->insta_get_tag_media($per_page, $offset);

                    $tag_products = $media_obj->insta_get_tag_products();

                    $tag_products = !empty( $tag_products ) ? maybe_unserialize( $tag_products->linked_products ) : '';

                    if( !empty( $tag_products )) {

                        $args = array(
                            'post_type' => 'product',
                            'post__in' => $tag_products,
                            'post_status'=> 'publish',
                            'posts_per_page' => 4
                        );

                        $the_query = new \WP_Query($args);

                        if ( $the_query->have_posts() ) {

                            ob_start();

                            $product_content .=  woocommerce_product_loop_start();

                            while ( $the_query->have_posts() ) {

                                $the_query->the_post();

                                global $product;

                                // Ensure visibility.
                                if ( empty( $product ) || ! $product->is_visible() ) {
                                    return;
                                }

                                echo '<li class="type-product status-publish product">';

                                    /**
                                     * Hook: woocommerce_before_shop_loop_item.
                                     *
                                     * @hooked woocommerce_template_loop_product_link_open - 10
                                     */
                                    $product_content .= do_action( 'woocommerce_before_shop_loop_item' );

                                    /**
                                     * Hook: woocommerce_before_shop_loop_item_title.
                                     *
                                     * @hooked woocommerce_show_product_loop_sale_flash - 10
                                     * @hooked woocommerce_template_loop_product_thumbnail - 10
                                     */
                                    $product_content .= do_action( 'woocommerce_before_shop_loop_item_title' );

                                    /**
                                     * Hook: woocommerce_shop_loop_item_title.
                                     *
                                     * @hooked woocommerce_template_loop_product_title - 10
                                     */
                                    $product_content .= do_action( 'woocommerce_shop_loop_item_title' );

                                    /**
                                     * Hook: woocommerce_after_shop_loop_item_title.
                                     *
                                     * @hooked woocommerce_template_loop_rating - 5
                                     * @hooked woocommerce_template_loop_price - 10
                                     */
                                    $product_content .= do_action( 'woocommerce_after_shop_loop_item_title' );

                                    /**
                                     * Hook: woocommerce_after_shop_loop_item.
                                     *
                                     * @hooked woocommerce_template_loop_product_link_close - 5
                                     * @hooked woocommerce_template_loop_add_to_cart - 10
                                     */
                                    $product_content .= do_action( 'woocommerce_after_shop_loop_item' );

                                echo '</li>';

                            }
                            $product_content .= woocommerce_product_loop_end();

                            $data['insta_products'] = ob_get_clean();
                        }
                    }

                    if ( ! empty( $tag_medias ) ) {

                        $tagKey = 0;

                        foreach ($tag_medias as $tkey => $tag_media) {

                            if( isset( $tag_media['status'] )  &&  $tag_media['status'] == '0'){
                              continue;
                            }

                            $image_id = $tag_media['image_id'];

                            $author  = $tag_media['insta_username'];

                            $images  = !empty( $tag_media['images'] ) ? maybe_unserialize( $tag_media['images'] ) : '';

                            $image   = isset( $images ) ? $images : '';

                            $insta_message = ( ! empty( $tag_media['insta_message'] ) ) ? $tag_media['insta_message']: '';

                            $preview = !empty( $image ) ? '<div class="item screens"><div class="box-head"><span class="pic-author" title="publisher">'.$author.'</span></div><img src="'.$image.'" alt="'.$author.'" data-message="' . $insta_message . '" ></div>' : 'N/A';

                            $data['insta_pic'][$tagKey] = array(

                              'image_id' => $image_id,

                              'preview'  => $preview,

                              'insta_message'  => $insta_message,

                              'author'   => $author,

                              'tag_id'   =>  $tag_id

                            );

                            $tagKey++;

                        }

                    }

                }

                if( !empty( $data ) ) {

                    $response = array(
                        'error' => false,
                        'data' => $data,
                    );

                } else {

                    $message = __( 'No media found regarding this tag', 'insta_feed' );

                    $response = array(
                        'error' => true,
                        'data' => $data,
                    );

                }

                wp_send_json($response);
                wp_die();
            }

        }

        public function is_tag_exist( $tag_id ) {

            $this->insta_table = $this->wpdb->prefix . 'instagram_tags';

            $bool = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT id FROM $this->insta_table where id=%d", $tag_id ) , ARRAY_A );

            if( !empty( $bool ) ) {
                return true;
            } else {
                return false;
            }

        }

        public function is_tag_valid( $tag_id ) {

            $this->insta_table = $this->wpdb->prefix . 'instagram_tags';

            $bool = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT linked_products FROM $this->insta_table where id=%d", $tag_id ) , ARRAY_A );

            if( !empty( $bool ) ) {
                return $bool;
            } else {
                return false;
            }

        }

        public function insert_insta_records_to_db($insta_data, $tag) {

            $tag_id_array = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT image_id FROM $this->insta_table_meta where tag_id=%d", $tag ) , ARRAY_A );

            $tag_id_array = !empty( $tag_id_array ) ? wp_list_pluck( $tag_id_array, 'image_id' ) : '';

            $query = "INSERT INTO $this->insta_table_meta ( tag_id, image_id, insta_username, images, priority, visiblity, status ) VALUES ";

            $values = $tag_ids = $place_holders = array();

            foreach ( $insta_data as $kdata => $vdata ) {

                if( ( empty( $tag_id_array ) || ( ! empty( $tag_id_array ) && ! in_array( $kdata, $tag_id_array )) ) && ! in_array( $kdata, $tag_ids ) ) {

                    array_push( $values, $tag );

                    array_push( $values, $kdata );

                    array_push( $values, $vdata['username'] );

                    array_push( $values, maybe_serialize( $vdata['images'] ) );

                    array_push( $values, $vdata['insta_message'] );

                    array_push( $values, 1 );

                    array_push( $values, maybe_serialize( array(0,1,2) ) );

                    array_push( $values, 1 );

                    $place_holders[] = "( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%d' )";

                    $tag_ids[] = $kdata;

                }

            }


            if( ! empty( $values ) ) {

                $query .= implode(', ', $place_holders);

                $res = $this->wpdb->query( $this->wpdb->prepare( "$query ", $values ) );

                return true;

            } else {

                return false;
            }

        }

        public function object_to_array($obj) {

            $new = json_decode(json_encode($obj), true);

            return $new;

        }


    }
}
