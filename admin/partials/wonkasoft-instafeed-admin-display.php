<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/admin/partials
 */

defined( 'ABSPATH' ) || die;

if ( is_admin() ) {

	if ( isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ) {

		global $wonkasoft_instafeed_page;

		$WS_OPTIONS = new Wonkasoft_Instafeed_Admin( WONKASOFT_INSTAFEED_SLUG, WONKASOFT_INSTAFEED_VERSION );

		$instafeed_view_layout = array(
			'0'   => 'Select view',
			'1'   => 'Carousel View',
			'2'   => 'List View',
		);

		$instafeed_view_layout_selected = ( ! empty( $WS_OPTIONS->get_instafeed_shop_view() ) ) ? $WS_OPTIONS->get_instafeed_shop_view() : 0;

		$instafeed_posts_to_show = ( ! empty( $WS_OPTIONS->get_instafeed_posts_limit() ) ) ? $WS_OPTIONS->get_instafeed_posts_limit() : '';

		$instafeed_posts_approval_email = ( ! empty( $WS_OPTIONS->get_instafeed_posts_approval_email() ) ) ? $WS_OPTIONS->get_instafeed_posts_approval_email() : '';

		$instafeed_client_id = ( ! empty( $WS_OPTIONS->get_instafeed_client_id() ) ) ? $WS_OPTIONS->get_instafeed_client_id() : '';

		$instafeed_access_token = ( ! empty( $WS_OPTIONS->get_instafeed_access_token() ) ) ? $WS_OPTIONS->get_instafeed_access_token() : '';

		$page = $_GET['page'];

		if ( $page === $wonkasoft_instafeed_page || $page === 'wonkasoft_instafeed_settings_display' ) :

			$wksa_tabs = array(

				'general'   => __( 'General Settings' ),

			);

			$current_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );

			$pid = empty( $_GET['pid'] ) ? '' : '&pid=' . $_GET['pid'];

			echo '<div class="wrap insta">';

			echo '<nav class="nav-tab-wrapper">';

			foreach ( $wksa_tabs as $name => $label ) {

				echo '<a href="' . admin_url( 'admin.php?page=insta-setting' . $pid . '&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';

			}

			echo '</nav>';

			do_action( 'insta_' . $current_tab );

			echo '</div>';


	  endif;

	}

	?>
	<h2 class="hndle ui-sortable-handle"><span><?php echo __( 'Instagram Settings', 'insta_feed' ); ?></span></h2>

				  <div id="wrapper">

					<div id="dashboard_right_now" class="formcontainer instagram instagram-settings">

						<form id="instafeed_shop_options" method="post" action="options.php">

						  <?php settings_fields( 'insta-settings-group' ); ?>

						  <?php do_settings_sections( 'insta-settings-group' ); ?>

							<div class="inside">

								<div class="main">

									<div class="options_group">
									  <div class="form-row form-row-full">
										<h3> <?php echo __( 'Shop Page Defaults', 'insta_feed' ); ?></h3>
										<span class="description">These options can be overridden by shortcode attributes ex. <code>[wonkasoft_instafeed_feed posts_to_show="10" view_layout="<?php echo $instafeed_view_layout[ $instafeed_view_layout_selected ]; ?>"]</code></span>
									  </div>
										<?php

										$WS_OPTIONS->wc_wonkasoft_instafeed_field(
											array(

												'id'                => '_instafeed_shop_view',
												'label'             => __( 'Feed view', 'Wonkasoft_Instafeed' ),
												'value'             => $instafeed_view_layout_selected,
												'options'           => $instafeed_view_layout,
												'type'                  => 'select',
												'desc_tip'          => true,
												'desc_help'       => __( 'Select View for instagram feeds on shop page.', 'Wonkasoft_Instafeed' ),
												'description'       => __( 'Select View for instagram feeds on shop page.', 'Wonkasoft_Instafeed' ),
												'wrapper_class'     => 'form-row form-row-full',
											)
										);

										$WS_OPTIONS->wc_wonkasoft_instafeed_field(
											array(

												'id'                => '_instafeed_posts_limit',
												'label'             => __( 'Feed posts to show', 'Wonkasoft_Instafeed' ),
												'value'             => $instafeed_posts_to_show,
												'type'                  => 'text',
												'placeholder'       => 'blank is set to 10',
												'desc_tip'          => true,
												'desc_help'       => __( 'Set Defult posts count for instagram feeds on shop page.', 'Wonkasoft_Instafeed' ),
												'description'       => __( 'Set Defult posts count for instagram feeds on shop page.', 'Wonkasoft_Instafeed' ),
												'wrapper_class'     => 'form-row form-row-full',
											)
										);

										$WS_OPTIONS->wc_wonkasoft_instafeed_field(
											array(

												'id'                => '_instafeed_posts_approval_email',
												'label'             => __( 'Feed posts approval email', 'Wonkasoft_Instafeed' ),
												'value'             => $instafeed_posts_approval_email,
												'type'                  => 'email',
												'placeholder'       => 'email of who will approve posts.',
												'desc_tip'          => true,
												'desc_help'       => __( 'Set the email/emails that are allowed to approve posts to feed. ( must have at least the role of editor )', 'Wonkasoft_Instafeed' ),
												'description'       => __( 'Set the email/emails that are allowed to approve posts to feed. ( must have at least the role of editor )', 'Wonkasoft_Instafeed' ),
												'wrapper_class'     => 'form-row form-row-full',
											)
										);

										$WS_OPTIONS->wc_wonkasoft_instafeed_field(
											array(

												'id'                => '_instafeed_client_id',
												'label'             => __( 'Wonkasoft-Feed Client ID', 'Wonkasoft_Instafeed' ),
												'value'             => $instafeed_client_id,
												'type'                  => 'password',
												'placeholder'       => 'Place Wonkasoft-Feed Client ID.',
												'desc_tip'          => true,
												'desc_help'       => __( 'Place Wonkasoft-Feed Client ID to allow access to retrieve your access token.', 'Wonkasoft_Instafeed' ),
												'description'       => __( 'Place Wonkasoft-Feed Client ID to allow access to retrieve your access token.', 'Wonkasoft_Instafeed' ),
												'wrapper_class'     => 'form-row form-row-full',
											)
										);

										$WS_OPTIONS->wc_wonkasoft_instafeed_field(
											array(

												'id'                => '_instafeed_access_token',
												'label'             => __( 'Feed Access Token', 'Wonkasoft_Instafeed' ),
												'value'             => $instafeed_access_token,
												'type'                  => 'password',
												'placeholder'       => 'Place Instagram access token here.',
												'desc_tip'          => true,
												'desc_help'          => __( 'Place your instagram access token here.', 'Wonkasoft_Instafeed' ),
												'description'       => __( 'Click to get <a id="get_instagram_access" href="#" data-redirect="' . get_site_url() . '" data-client="' . $instafeed_client_id . '">access token</a> to allow access to your Instagram Feed.<span id="msg-onclick" style="color: red;"></span>', 'Wonkasoft_Instafeed' ),
												'wrapper_class'     => 'form-row form-row-full',
											)
										);

										?>

									</div>

								</div>

							</div>

							<div class="submitter">

											  <?php submit_button( 'Save Settings' ); ?>

							</div>

						</form>

				  </div>
	<?php
}
