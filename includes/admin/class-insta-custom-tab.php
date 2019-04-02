<?php

/**
 * @author Wonkasoft
 * @version 2.1.0
 * This file handles Custom Tab class.
 */

namespace Wc_Insta_Feed\Includes\Admin;

if (!defined('ABSPATH')) {
    exit;
}

if (! class_exists('Insta_Custom_Tab')) {
    /**
     *
     */
    class Insta_Custom_Tab
    {

        public $wpdb = '';
        public $table_name = '';
        public $table_media = '';

        public function __construct()
        {
            global $wpdb;

            //code
            $this->wpdb = $wpdb;

            $this->table_name = $this->wpdb->prefix.'instagram_tags';
            $this->table_media = $this->wpdb->prefix . 'instagram_tags_media';

            add_filter( 'woocommerce_product_data_tabs', array( $this, 'insta_custom_product_tabs' ) );
            add_action( 'woocommerce_product_data_panels', array( $this, 'insta_product_tab_content' ) );
            add_action( 'save_post', array( $this, 'save_insta_product_tab_content' ), 1 );

        }

        public function save_insta_product_tab_content( $post ) {

            if( !empty($post) && isset($_POST['img-visiblity']) && isset($_POST['media-status']) && isset($_POST['_insta_hastag'])) {

                $meta_value = array(
                    'visblity' => $_POST['img-visiblity'],
                    'status' => $_POST['media-status'],
                    'hashtag' => $_POST['_insta_hastag'],
                );

                update_post_meta($post, 'insta_product_meta', $meta_value );

            }

        }

        /**
         * Add a custom product tab.
         */
        function insta_custom_product_tabs( $tabs) {

            $tabs['instagram'] = array(
                'label'		=> __( 'Instagram', 'insta_feed' ),
                'target'	=> 'instagram_options',
                'class'		=> array( 'show_if_instagram'  ),
            );

            return $tabs;
        }

        function get_product_tag_media( $tag_id ) {

            $results = $this->wpdb->get_results( $this->wpdb->prepare("Select * from $this->table_media where tag_id=%d", $tag_id ) , ARRAY_A );

            if( !empty( $results ) ) {

                return $results;

            } else {

                return '';

            }

            return $tabs;
        }

        function get_product_visiblity_label( $visiblity_array, $image_id ){

			$label_arr = array(
				'0' => 'Home page',
				'1' => 'Product page',
				'2' => 'Dedicated page',
			);

			$content = '';

			$content .= "<select multiple name='img-visiblity[".$image_id."][]' class='img-visiblity'>";

			foreach ($label_arr as $key => $value) {

				if( !empty( $visiblity_array ) && in_array( $key, $visiblity_array )  ) {
					$selected = 'selected';
				} else {
					$selected = '';
				}

				$content .= "<option value='".$key."' ".$selected.">".$value."</option>";
			}

			$content .= "</select>";

			return $content;

        }

        function get_visiblity_label( $ids, $image_id ){

			$label_arr = array(
				'0' => 'Home page',
				'1' => 'Product page',
				'2' => 'Dedicated page',
			);

			$content = '';

			$content .= "<select multiple name='img-visiblity[".$image_id."][]' class='img-visiblity'>";

			foreach ($label_arr as $key => $value) {

				if( in_array( $key, $ids ) ) {
					$selected = 'selected';
				} else {
					$selected = '';
				}

				$content .= "<option value='".$key."' ".$selected.">".$value."</option>";
			}

			$content .= "</select>";

			return $content;

        }

        function get_product_status_label( $status_array, $image_id ) {

			$label_arr = array(
				'1' => 'Enabled',
				'0' => 'Disabled',
			);

			$content = '';

			$content .= "<select name='media-status[".$image_id."]'>";

            foreach ($label_arr as $key => $value) {

                if( $key == $status_array ) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }

                $content .= "<option value='".$key."' ".$selected.">".$value."</option>";
            }

			$content .= "</select>";

			return $content;


        }

        function get_status_label( $status, $image_id ) {

			$label_arr = array(
				'1' => 'Enabled',
				'0' => 'Disabled',
			);

			$content = '';

			$content .= "<select name='media-status[".$image_id."]'>";

            foreach ($label_arr as $key => $value) {

                if( $key == $status ) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }

                $content .= "<option value='".$key."' ".$selected.">".$value."</option>";
            }

			$content .= "</select>";

			return $content;


		}

        /**
		 * Contents of the instagram options product tab.
		 */

		function insta_product_tab_content() {

            global $post;

            $status_array = $visiblity_array = array();

            if( !empty($post ) ) {

                $tag_name = $tag_id = '';

                $linked_products = $this->wpdb->get_results("Select id, tag_name, linked_products from $this->table_name" );

                if( !empty( $linked_products ) ) {

                    foreach ($linked_products as $lkey => $lvalue) {

                        $link_products = !empty($lvalue->linked_products)?maybe_unserialize($lvalue->linked_products):'';

                        if( !empty($link_products) && in_array( $post->ID, $link_products)) {

                            $tag_name = $lvalue->tag_name;
                            $tag_id = $lvalue->id;

                            break;
                        }
                    }
                }
            }

			?>

			<div id='instagram_options' class='panel woocommerce_options_panel'>

				<div class='options_group'>

					<?php
          if( $tag_name ){

              woocommerce_wp_text_input( array(
                'id'			=> '_insta_hastag',
                'label'			=> __( 'Instagram hashtag', 'insta_feed' ),
                'desc_tip'		=> 'true',
                'description'	=> __( 'Instagram hashtag for the product', 'insta_feed' ),
                'type' 			=> 'text',
                'value'         => $tag_name,
                'custom_attributes' => array(
                  'readonly' => true
                )
              ) );
            }
					?>

				</div>

				<div class='options_group'>

 					<div class="field_wrapper">

						<div class="tt">

							<table class="datatable">

								<thead>

								    <tr>

                      <?php if($tag_name){ ?>

                      <th class="datath1"><label><b><?php echo _e('Preview','insta_feed'); ?></b></label></th>

								    	<th class="datath2"><label><b><?php echo _e('Author','insta_feed'); ?></b></label></th>

								    	<th class="datath3"><label><b><?php echo _e('Priority','insta_feed'); ?></b></label></th>

								    	<th class="datath4"><label><b><?php echo _e('Visiblity','insta_feed');?></b></label></th>

                      <th class="datath5"><label><b><?php echo _e('Status','insta_feed');?></b></label></th>

                    <?php } ?>

                    </tr>

								</thead>

								<tbody class="tbody">

								<?php
                  if($tag_name){
                  $custom_options = get_post_meta($post->ID,'insta_product_meta',true);

                  if( !empty( $custom_options )) {
                      $visiblity_array = $custom_options['visblity'];
                      $status_array = $custom_options['status'];
                      $hashtag = $custom_options['hashtag'];
                  }

                  $media_data = $this->get_product_tag_media( $tag_id );

									echo __("<label>Image settings</label>", "insta_feed");

									if(!empty($media_data)){

										foreach ($media_data as $tag) {

                                            $image_id  = $tag['image_id'];

                                            $author   = $tag['insta_username'];

                                            $images   = !empty( $tag['images'] ) ? maybe_unserialize( $tag['images'] ) : '';

                                            $image = isset( $images ) ? $images : '';

                                            $preview = !empty( $image ) ? '<img src="'.$image.'" alt="image" height="75px">' : 'N/A';

                                            $priority 	= $tag['priority'];

                                            $visiblity 	= !empty( $tag['visiblity'] ) ? maybe_unserialize($tag['visiblity']) : '';

                                            if( !empty( $visiblity_array ) ) {

                                                $visiblity_arr = !empty($visiblity_array) && isset($visiblity_array[$image_id]) ? $visiblity_array[$image_id]:'';

                                                $label = $this->get_product_visiblity_label( $visiblity_arr, $image_id );

                                            } else {

                                                $label = $this->get_visiblity_label( $visiblity, $image_id );

                                            }

                                            if( !empty($status_array)) {

                                                $status_arr = !empty($status_array) && isset($status_array[$image_id]) ? $status_array[$image_id]:'';

                                                $status_label = $this->get_product_status_label( $status_arr, $image_id );

                                            } else {

                                                $status_label = $this->get_status_label( $tag['status'], $image_id );

                                            }

                                            ?>

                                            <tr id="tr_<?php echo $image_id; ?>">

                                                <td class="datath1"><?php echo $preview; ?></td>

                                                <td class="datath2"><?php echo $author; ?></td>

                                                <td class="datath3"><?php echo $priority; ?></td>

                                                <td class="datath4"><?php echo $label; ?></td>

                                                <td class="datath5"><?php echo $status_label; ?></td>

                                            </tr>

                                                <?php
                                            }

                                         }
                      }else{
                        ?>
                        <tr>

                            <td class="datath1"><?php _e( 'No data found, please check and select a #tag. Go to <a href="'.admin_url('admin.php?page=woo-insta-tag').'">Settings</a>', 'insta_feed' ); ?></td>

                        </tr>
                        <?php

                      }
                      ?>


								</tbody>

							</table>

						</div>

					</div>

				</div>

			</div>

      <?php

		}

    }
}
