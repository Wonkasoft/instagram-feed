<?php

  namespace Wc_Insta_Feed\Templates\Admin\Tag;

  use Wc_Insta_Feed\Templates\Admin\Tag\Util;
  use Wc_Insta_Feed\Api;
  use Wc_Insta_Feed\Helper;
  use Wc_Insta_Feed\Helper\Instagram_Settings;
  use Wc_Insta_Feed\Inc\WC_Insta_Errors;
  use Wc_Insta_Feed\Helper\Tag\Wc_Tag;



  if (!defined('ABSPATH')) {
      exit;
  }

  if (! class_exists('Insta_Tag')) {
      /**
       *
       */
      class Insta_Tag extends Helper\Tag\Wc_Tag implements Util\Tag_Interface
      {

        public $status = '';

        public $linked_products = '';

        public $tag_data = '';

        public $insta_hashtag = '';

        public $hashtag_priority = '';

        public $hashtag_visiblity = '';

        public $tag_id = '';

        public $table_name = '';

        public $add_class = '';

        public $hashtag_status = array(
          '0'=>'disable',
          '1'=>'enable',
        );

        public $config = array();

        public $linked_products_list = array();

        public $hashtag_visiblity_list = array(
          '0'=>'Shop page',
          '1'=>'Dedicated page',
          '2'=>'Product page',
        );


        public function __construct( $tag_id = '' ) {

          global $wpdb;

          $this->wpdb = $wpdb;
          $this->tag_id = $tag_id;
          $this->linked_products_list = $this->get_all_products($this->tag_id);

          $this->verify_nonce();

          if( !empty( $this->tag_id ) ) {

            $this->tag_data = $this->insta_get_tag_data_by_tag_id( $this->tag_id );

            if( !empty( $this->tag_data ) ) {

              $this->insta_hashtag = $this->tag_data['hashtag'];
              $this->linked_products = $this->tag_data['linked_products'];

              $this->hashtag_priority = $this->tag_data['priority'];
              $this->status = $this->tag_data['status'];
              $this->hashtag_visiblity = $this->tag_data['visiblity'];

            }

          }

          if( is_admin() ) {

            $this->add_class = 'hpadmin_end';

          }

        }
          function get_insta_add_tag_template()
          {

              ?>

                <h2 class="hndle ui-sortable-handle"><span><?php echo __( 'Instagram Tag', 'insta_feed' ); ?></span></h2>

                  <div id="wrapper">

                    <div id="dashboard_right_now" class="formcontainer instagram instagram-settings <?php echo $this->add_class; ?>">

                        <form action="" method="post">

                            <?php wp_nonce_field( 'hashtag_nonce_action', 'hashtag_nonce_field' ); ?>

                            <div class="inside">

                                <div class="main">

                                    <div class="instagram-wrap">

                                      <?php

                                        wc_insta_text_input( array(
                                          'id'          => '_insta_hashtag',
                                          'value'             => ! empty( $this->insta_hashtag ) ? $this->insta_hashtag : '',
                                          'label'       => __( 'Instagram Hash tag', 'insta_feed' ) . ' <abbr class="required" title="required">*</abbr>',
                                          'desc_tip'    => true,
                                          'description' => __( 'Enter instagram hash tag.', 'insta_feed' ),
                                          'placeholder' => 'type hashtag',
                                          'class'       => 'api_wrapper'
                                        ) );

                                      ?>

                                    </div>

                                    <div class="options_group">

                                      <label for="_insta_linked_products"><?php echo __('Instagram linked products', 'insta_feed' ); ?> <abbr class="required" title="required">*</abbr></label>
                                      <select id="_insta_linked_products" name="_insta_linked_products[]" multiple="true">

                                      <?php

                                          foreach ($this->linked_products_list as $lkey => $lvalue) {

                                            if( in_array($lkey, $this->linked_products)) {

                                              $selected = 'selected';

                                            } else {

                                              $selected = '';

                                            }

                                            echo '<option value="'.$lkey.'"'.$selected.'>'.$lvalue.'</option>';

                                          }

                                      ?>

                                      </select>
                                    </div>

                                    <div class="options_group">

                                    <label for="_hashtag_visiblity"> <?php echo __('Hashtag Visiblity', 'insta_feed' ); ?> <abbr class="required" title="required">*</abbr></label>

                                      <select id="_hashtag_visiblity" name="_hashtag_visiblity[]" multiple="true">

                                      <?php

                                          foreach ($this->hashtag_visiblity_list as $lkey => $lvalue) {

                                            if( in_array($lkey, $this->hashtag_visiblity)) {

                                              $selected = 'selected';

                                            } else {

                                              $selected = '';

                                            }

                                            echo '<option value="'.$lkey.'"'.$selected.'>'.$lvalue.'</option>';

                                          }

                                      ?>

                                      </select>

                                    </div>

                                    <div class="options_group">

                                      <?php

                                        wc_insta_text_input( array(
                                          'id'                => '_hashtag_priority',
                                          'value'             => !empty($this->hashtag_priority)?$this->hashtag_priority:'',
                                          'label'             => __( 'Hashtag Priority', 'insta_feed' ).'<abbr class="required" title="required">*</abbr>',
                                          'desc_tip'          => true,
                                          'description'       => __( 'Hash tag priority.', 'insta_feed' ),
                                          'type'              => 'number',
                                          'custom_attributes' => array(
                                            'step' => 1,
                                            'min'  => 0,
                                          ),
                                        ) );

                                      ?>

                                    </div>

                                    <div class="options_group">

                                      <?php

                                        wc_insta_select( array(
                                          'id'                => '_hashtag_status',
                                          'label'         => __( 'Status ', 'insta_feed' ).'<abbr class="required" title="required">*</abbr>',
                                          'value'         => isset( $this->status ) && !empty( $this->status ) ? $this->status : 0,
                                          'options'       => $this->hashtag_status,
                                          'desc_tip'      => true,
                                          'description'   => __( 'Select Status for the shipping area.', 'insta_feed' ),
                                          'wrapper_class' => 'form-row form-row-full'
                                          )
                                        );
                                      ?>

                                    </div>


                                </div>

                            </div>

                            <div class="submitter">

                              <?php

                                if( isset($_GET['tag_id']) && !empty($_GET['tag_id']) ) :

                                ?>
                                    <input type="hidden" name="_tag_id" value="<?php echo $_GET['tag_id']; ?>">

                                    <button type="submit" name="update-hashtag" class="button button-primary" value="update area">Update Hashtag</button>

                                <?php else: ?>

                                    <button type="submit" name="save-hashtag" class="button button-primary" value="save hashtag">Save Hashtag</button>

                                <?php endif;  ?>


                            </div>

                        </form>

                      </div>

           <?php


          $this->configure_instagram_account();

        }

        public function configure_instagram_account(){

          $error_obj = new WC_Insta_Errors();

          $setting = new Helper\Instagram_Settings();

          $tag = $this->insta_hashtag;

          if( !empty( $tag ) ) {

              echo __( '<p>Images fetched from instagram posted with tag <b>'.$tag.'</b></p>', 'insta_feed' );

              add_thickbox();

              echo '<a href="#TB_inline?width=800&height=550&inlineId=instagram-thickbox" id="instagram-thickbox-btn" data-tag="'.$tag.'" title="Instagram images" class="thickbox button-primary">'.__('Fetch Images','insta_feed').'</a>';

              $this->get_thickbox_template_wrap();

              $this->generate_wp_list_table_template();


            }


        }

        public function generate_wp_list_table_template(){


          insta_get_template_part('admin/tag/list-tag-data');

        }

        public function get_thickbox_template_wrap(){
          ?>

          <div id="instagram-thickbox" style="display:none;">

            <div class="thickbox-header">

                <button class="button button-primary import-images"><?php echo __( 'Import', 'insta_feed' );?></button>

            </div>

            <div class="thickbox-body-wrap pre-loader">

            </div>

          </div>

          <?php
        }
        public function get_thickbox_template( $error_obj, $setting, $tag ){

          ?>

            <div class="thickbox-body" data-tag="<?php echo $tag; ?>">

              <?php

                $insta = new Api\Instagram();

                $result = $insta->getTagMedia( $tag, 20 );


                if( empty( $result ) ) {

                  $message = __( 'Wrong Credentials used please check the credentials again.', 'insta_feed' );

                  $error_obj->set_error_code(1);

                  $error_obj->insta_print_notification( $message );

                  die;

                } else {

                  $this->show_results( json_decode( $result ) );

                }

              ?>

            </div>


          <?php
        }

        public function show_results( $decoded_results ) {

          foreach($decoded_results as $item){

              $image_link = $item->url;

              $image_id = $item->id;

              echo '<div class="img-wrap"><img id="'.$image_id.'" src="'.$image_link.'" /></div>';
          }
          die;
        }

        public function verify_nonce() {

          if( isset($_POST['save-hashtag']) || isset( $_POST['update-hashtag'] ) ) {

            if ( ! isset( $_POST['hashtag_nonce_field'] ) || ! wp_verify_nonce( $_POST['hashtag_nonce_field'], 'hashtag_nonce_action' ) ) {

              print 'Sorry, your nonce did not verify.';
              exit;

            } else {

              do_action( 'woocommerce_add_instagram_hashtag',$_POST );

            }

          }

        }
  }
}
