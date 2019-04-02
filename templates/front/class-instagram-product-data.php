<?php

  namespace Wc_Insta_Feed\Templates\Front;
  use Wc_Insta_Feed\Helper;

  if (!defined('ABSPATH')) {
      exit;
  }


  if (! class_exists('Instagram_Product_Data')) {
      /**
       *
       */
      class Instagram_Product_Data extends Helper\Tag\Wc_Tag_Data
      {

        public $status = '';

        public $linked_products = '';


        public $linked_products_list = array();

        public function __construct() {

          global $wpdb;

          $this->wpdb = $wpdb;

          $this->lightbox_container();

        }

        public function validate_tag_results( $results ) {

            if( empty($results ) ) {

                $message = __( 'Instagrams tags are not available', 'insta_feed' );

                $this->error_obj->set_error_code(1);
                $this->error_obj->insta_print_notification($message);

                return false;

            } else {

                return true;

            }
        }

        public function setup_class_tag_data( $results ) {

            foreach ($results as $key => $value) {

                $this->tag_data = $this->insta_get_tag_data_by_tag_id( $value['id'] );

                if( !empty( $this->tag_data ) ) {

                    $this->insta_hashtag = $this->tag_data['hashtag'];
                    $this->linked_products = $this->tag_data['linked_products'];

                    $this->hashtag_priority = $this->tag_data['priority'];
                    $this->status = $this->tag_data['status'];
                    $this->hashtag_visiblity = $this->tag_data['visiblity'];

                    if( $this->status != '1' )
                        continue;

                    $check = $this->insta_check_visiblity();

                    if( !$check )
                        continue;

                    $media_obj = new Helper\Tag\Wc_Tag_Data( $value['id'] );

                    $tag_media = $media_obj->insta_get_only_one_tag_media();

                    if ( ! empty( $tag_media ) ) {

                        $count = $media_obj->insta_tag_media_count();

                        $image_id  = $tag_media['image_id'];

                        $author   = $tag_media['insta_username'];

                        $images   	= !empty( $tag_media['images'] ) ? maybe_unserialize( $tag_media['images'] ) : '';

                        $image = isset( $images ) ? $images : '';

                        $preview = !empty( $image ) ? '<img src="'.$image.'" alt="'.$author.'" >' : 'N/A';

                        $data = array(

                            'image_id' => $image_id,

                            'preview'  => $preview,

                            'author'   => $author,

                            'count'   => $count['count'],

                            'tag_id'   =>  $value['id']

                        );


                        if( !empty( $data ) ) {


                            $this->generate_media_list_html( $data );

                        }


                    }

                }
            }
        }

        function generate_instagram_product_template($product_id, $count, $media, $tag_name)
        {

            $custom_options = get_post_meta($product_id,'insta_product_meta',true);

            $visiblity_array = $status_array = array();

            if( !empty( $custom_options ) ) {

                if( isset( $custom_options['status'] ) ) {
                    $status_array = $custom_options['status'];
                }

                if( isset( $custom_options['visblity'] ) ) {
                    $visiblity_array = $custom_options['visblity'];
                }
            }

          ?>

            <div id="wrapper">

                <section class="instagram instagram-feeds insta-product-page">

                    <div class="insta-header">

                        <h2><?php echo __( 'Featured Post of buyers from Instagram','insta_feed'); ?></h2>

                        <p><?php echo __('Bought this product? Great post a snapshot on instagram with <b>'.$tag_name.'</b> to get a chance to be featured here', 'insta_feed' ); ?></p>

                    </div>
                    <div class="instagram-wrap">

                        <?php

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

                        ?>

                    </div>
                    <?php

                    if( $count > 0 )
                    {

                        ?>

                        <div class="instabox-footer">
                            <input type="hidden" value="<?php echo '1'; ?>" name="paged">
                            <button class="in-load-more" data-product-id="<?php echo $product_id;?>" id="<?php echo $mvalue['tag_id']; ?>"><?php echo __('View More', 'insta_feed'); ?></button>
                        </div>

                        <?php

                    }

                   ?>

                </section>

            </div>

           <?php


        }

        public function generate_media_list_html( $data ) {

            ?>

                <div class="insta-box" id="<?php echo $data['tag_id']; ?>" data-image-id="<?php echo $data['image_id']; ?>">

                    <?php echo $data['preview']; ?>

                    <div class="box-head">
                        <span class="count-pic"><?php echo $data['count']; ?></span>
                        <button class="popup-open"><?php echo __('View Products', 'insta_feed'); ?></button>
                    </div>
                </div>

            <?php

        }

        public function insta_check_visiblity( $visiblity = '' ) {

            if( !empty( $visiblity ) ) {

                if( !empty( $visiblity ) && in_array( '1', $visiblity ) ) {
                    return true;
                } else {
                    return false;
                }

            } else {

                if( !empty( $this->hashtag_visiblity ) && in_array( '1', $this->hashtag_visiblity ) ) {
                    return true;
                } else {
                    return false;
                }
            }

        }

        public function lightbox_container(){

            ?>

            <template id="screenSliderTemplate">
                <div class="screens-template">
                    <div class="screen-template-wrap" active-client="{{id}}">
                        <span class="close-icon"></span>
                        <div class="content">
                            <div class="wkgrid-squeezy">
                                <div class="wk-loader"></div>
                                <div class="inner-content">

                                    <div class="info-part">
                                        <h2><?php echo __('Products', 'insta_feed'); ?></h2>
                                        <div class="insta-tag-products">{{tagProducts}}</div>
                                    </div>

                                    <div class="slider-part">
                                        <div class="slider-wrapper owl-carousel">{{sliderItems}}</div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div id="sliderHolder"></div>

            <?php
        }



  }

}
