<?php

  namespace Wc_Insta_Feed\Templates\Admin;

  use Wc_Insta_Feed\Helper;
  use Wc_Insta_Feed\Inc\WC_Insta_Errors;
  use Wc_Insta_Feed\Api\Instagram;


  if (!defined('ABSPATH')) {
      exit;
  }

  settings_errors();

  if (! class_exists('Insta_Tag')) {

      /**
       *
       */

	 class Settings extends Helper\Instagram_Settings
      {

        public $rate_limit = '';

        public $config = array();

        public $shop_view = '';

        public $_avl_views = array(
          '0'=>'Carousel View',
          '1'=>'List View',
        );

        public function __construct() {

          $this->shop_view = $this->get_insta_shop_view();

        }

		    public function get_insta_setting_template()
        {

          if(isset($_GET['page']) && !empty($_GET['page'])){

            $page = $_GET['page'];

            if( $page == 'insta-setting' ) :

            $wksa_tabs = array(

              'general'	=>	__('General Settings'),

            );

            $current_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );

              $pid = empty( $_GET['pid'] ) ? '' : '&pid='.$_GET['pid'];

              echo '<div class="wrap insta">';

              echo '<nav class="nav-tab-wrapper">';

              foreach ( $wksa_tabs as $name => $label ) {

                echo '<a href="'. admin_url( 'admin.php?page=insta-setting'.$pid.'&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';

              }

              echo '</nav>';

              do_action( 'insta_' . $current_tab );

              echo '</div>';


            endif;

          }
        }

        public function get_insta_general_setting_template()
        {
              ?>

                <h2 class="hndle ui-sortable-handle"><span><?php echo __( 'Instagram Settings', 'insta_feed' ); ?></span></h2>

                  <div id="wrapper">

                    <div id="dashboard_right_now" class="formcontainer instagram instagram-settings">

                        <form method="post" action="options.php">

                          <?php settings_fields( 'insta-settings-group' ); ?>

                          <?php do_settings_sections( 'insta-settings-group' ); ?>

                            <div class="inside">

                                <div class="main">

                                    <div class="options_group">

                                        <h3> <?php echo __( 'Shop Page', 'insta_feed' ); ?></h3>

                                        <?php

                                          wc_insta_select( array(
                                            'id'                => '_insta_shop_view',
                                            'label'         => __( 'Instagram feed view', 'insta_feed' ),
                                            'value'         => !empty($this->shop_view)?$this->shop_view:'',
                                            'options'       => $this->_avl_views,
                                            'desc_tip'      => true,
                                            'description'   => __( 'Select View for instagram feeds n shop page.', 'insta_feed' ),
                                            'wrapper_class' => 'form-row form-row-full'
                                            )
                                          );

                                        ?>

                                    </div>


                                </div>

                            </div>

                            <div class="submitter">

								              <?php submit_button('Save Settings'); ?>

                            </div>

                        </form>

                  </div>

           <?php

        }

  }
}
