<?php
/**
 * The class Wonkasoft_Insta_Feed functionality of the plugin.
 *
 * @link       https://wonkasoft.com
 * @since      1.0.0
 *
 * @package    Wonkasoft_Instafeed
 * @subpackage Wonkasoft_Instafeed/admin
 */

defined( 'ABSPATH' ) || die;

/**
 * Get Instagram tag data
 */
class Wonkasoft_Insta_Feed extends Wonkasoft_Instagram_Tag {

	/**
	 * Class to add to the element.
	 *
	 * @var string
	 */
	public $add_class = '';

	/**
	 * Will contain current feeds options.
	 *
	 * @var array
	 */
	public $tag_data = array();

	/**
	 * This will be set with tag id.
	 *
	 * @var string
	 */
	public $tag_id = '';

	/**
	 * To be set with table name.
	 *
	 * @var string
	 */
	public $table_name = '';

	/**
	 * Will be set with current hashtag.
	 *
	 * @var string
	 */
	public $insta_hashtag = '';

	/**
	 * Will contain list of product ids linked to tag.
	 *
	 * @var string
	 */
	public $linked_products = '';

	/**
	 * Where this tag feed will be visible.
	 *
	 * @var string
	 */
	public $hashtag_visibility = '';

	/**
	 * The priority set for current tag.
	 *
	 * @var string
	 */
	public $hashtag_priority = '';

	/**
	 * Will be set with enable or disable tag.
	 *
	 * @var string
	 */
	public $status = '';

	/**
	 * Will be set to 20 by default.
	 *
	 * @var int
	 */
	public $fetch_qty = 20;

	/**
	 * Will be loaded with the tag table data.
	 *
	 * @var object
	 */
	public $tag_images_table = null;

	/**
	 * Contains a list of statuses.
	 *
	 * @var array
	 */
	public $hashtag_statuses = array(
		'0' => 'disable',
		'1' => 'enable',
	);

	/**
	 * This will be set with the list of products for options.
	 *
	 * @var array
	 */
	public $linked_products_list = array();

	/**
	 * Contains visibility list for options.
	 *
	 * @var array
	 */
	public $hashtag_visibility_list = array(
		'1' => 'Shop page',
		'2' => 'Dedicated page',
		'3' => 'Product page',
	);

	/**
	 * The Contructor for the Wonkasoft_Insta_Feed class.
	 *
	 * @param string $tag_id contains the tag_id passed in.
	 */
	public function __construct( $tag_id = null ) {

		parent::__construct();
		$this->tag_id = $tag_id;

		$this->linked_products_list = $this->get_all_products();
		if ( ! empty( $this->tag_id ) ) {

			$this->tag_data = $this->insta_get_tag_data_by_tag_id();

			if ( ! empty( $this->tag_data ) ) {
				foreach ( $this->tag_data as $key => $value ) {
					if ( is_array( $value ) ) :
						$this->tag_data[ $key ] = array_shift( $value );
						endif;
				}
				$this->insta_hashtag = $this->tag_data['hashtag'];
				$this->linked_products = maybe_unserialize( $this->tag_data['linked_products'] );
				$this->hashtag_visibility = maybe_unserialize( $this->tag_data['visibility'] );
				$this->hashtag_priority = $this->tag_data['priority'];
				$this->fetch_qty = $this->tag_data['fetch_qty'];
				$this->status = maybe_unserialize( $this->tag_data['status'] );

			}
		}

		if ( is_admin() ) {

			$this->add_class = 'hpadmin_end';

		}

	}

	/**
	 * This is for the display of the edit screen for post type instagram_tags.
	 */
	public function get_insta_add_tag_template() {
		?>

		<div id="wrapper">
		<?php wp_nonce_field( 'wonkasoft_nonce', 'wonkasoft_intagram_tags_nonce' ); ?>
			<div id="instafeed_post_settings" class="formcontainer instagram instagram-settings <?php echo esc_attr( $this->add_class ); ?>">
					<div class="inside">

						<div class="main">
							<div class="instagram-wrap">
							<?php

							$this->ws_insta_text_input(
								array(
									'id'          => '_insta_hashtag',
									'value'             => ( ! empty( $this->insta_hashtag ) ) ? $this->insta_hashtag : '',
									'label'       => __( 'Instagram Hash tag ', 'Wonkasoft_Instafeed' ) . '<abbr class="required" title="required">*</abbr>',
									'desc_tip'    => true,
									'description' => __( 'Enter instagram hash tag.', 'Wonkasoft_Instafeed' ),
									'placeholder' => 'Hashtag here...',
									'class'       => 'api_wrapper',
								)
							);

							?>

							</div>

							<div class="options_group">

								<?php
								$this->ws_insta_select(
									array(
										'id'                => '_insta_linked_products',
										'label'         => __( 'Instagram linked products ', 'Wonkasoft_Instafeed' ) . '<abbr class="required" title="required">*</abbr>',
										'value'         => ( ! empty( $this->linked_products ) ) ? $this->linked_products : '',
										'options'       => $this->linked_products_list,
										'multi_select'  => true,
										'desc_tip'      => true,
										'description'   => __( 'Select which products are linked to this hashtag.', 'Wonkasoft_Instafeed' ),
										'wrapper_class' => 'form-row form-row-full',
									)
								);
								?>
							</div>

							<div class="options_group">

									<?php
									$this->ws_insta_select(
										array(
											'id'                => '_hashtag_visibility',
											'label'         => __( 'Hashtag Visibility ', 'Wonkasoft_Instafeed' ) . '<abbr class="required" title="required">*</abbr>',
											'value'         => ( ! empty( $this->hashtag_visibility ) ) ? $this->hashtag_visibility : '',
											'options'       => $this->hashtag_visibility_list,
											'multi_select'  => true,
											'desc_tip'      => true,
											'description'   => __( 'Select where this hashtag will be visible.', 'Wonkasoft_Instafeed' ),
											'wrapper_class' => 'form-row form-row-full',
										)
									);
									?>

							</div>

							<div class="options_group">

									<?php

									$this->ws_insta_text_input(
										array(
											'id'                => '_hashtag_priority',
											'value'             => ( ! empty( $this->hashtag_priority ) ) ? $this->hashtag_priority : '',
											'label'             => __( 'Hashtag Priority ', 'Wonkasoft_Instafeed' ) . '<abbr class="required" title="required">*</abbr>',
											'desc_tip'          => true,
											'description'       => __( 'Hash tag priority.', 'Wonkasoft_Instafeed' ),
											'type'              => 'number',
										)
									);

									?>

							</div>

							<div class="options_group">

								<?php
								$this->ws_insta_text_input(
									array(
										'id'                => '_hashtag_image_qty',
										'value'             => ( ! empty( $this->fetch_qty ) ) ? $this->fetch_qty : '',
										'label'             => __( 'Qty to fetch', 'Wonkasoft_Instafeed' ),
										'desc_tip'          => true,
										'description'       => __( 'Quantity of images to fetch. ( Blank sets 20 )', 'Wonkasoft_Instafeed' ),
										'type'              => 'number',
									)
								);

								?>

							</div>

							<div class="options_group">

								<?php
								$this->ws_insta_select(
									array(
										'id'                => '_hashtag_status',
										'label'         => __( 'Status ', 'Wonkasoft_Instafeed' ) . '<abbr class="required" title="required">*</abbr>',
										'value'         => ( ! empty( $this->status ) ) ? $this->status : '0',
										'options'       => $this->hashtag_statuses,
										'desc_tip'      => true,
										'description'   => __( 'Select Status for the shipping area.', 'Wonkasoft_Instafeed' ),
										'wrapper_class' => 'form-row form-row-full',
									)
								);
								?>

						</div>


					</div>

				</div>

				<div class="submitter">

						<?php

						if ( ! empty( $this->tag_data['hashtag'] ) ) :

							?>
							<?php submit_button( 'Update Hashtag' ); ?>

						<?php else : ?>

							<?php submit_button( 'Save Hashtag' ); ?>

					<?php endif; ?>


				</div>

		
		

			<?php

			$this->configure_instagram_account();
	}

	/**
	 * THis configures the start of the instagram api call.
	 */
	public function configure_instagram_account() {

		$tag = $this->insta_hashtag;

		if ( ! empty( $tag ) ) {
			?>
				<div class="wonkasoft-tag-images-wrap">
				 <p>Images fetched from instagram posted with tag <b><?php echo wp_kses_post( $tag ); ?></b></p>

			<?php
			add_thickbox();

			echo wp_kses(
				sprintf( '<a href="#TB_inline?width=800&height=550&inlineId=instagram-thickbox" id="instagram-thickbox-btn" data-tag-id="%d" data-tag="%s" title="Instagram images" class="thickbox button-primary">%s</a>', esc_attr( $this->tag_id ), esc_attr( $tag ), _x( 'Fetch Images', 'Wonkasoft_Instafeed' ) ),
				array(
					'a' => array(
						'href'  => array(),
						'id'    => array(),
						'data-tag-id'   => array(),
						'data-tag'  => array(),
						'title' => array(),
						'class' => array(),
					),
				)
			);

			$this->get_thickbox_template_wrap();

			$this->generate_wp_list_table_template();

		}

	}

	/**
	 * This generates the wp list table template.
	 */
	public function generate_wp_list_table_template() {

		$this->tag_images_table = new Wonkasoft_List_Instatag_Data( $this->tag_id );
		?>
				<h1>Wonkasoft Tag Images</h1>
								<?php
								$this->tag_images_table->prepare_items();
								$this->tag_images_table->display();
								?>
			</div>
			</div>
			</div>
		<?php
	}

	/**
	 * This creates the thickbox template.
	 */
	public function get_thickbox_template_wrap() {
		?>

	  <div id="instagram-thickbox" style="display:none;">

		<div class="thickbox-header">
			<div class="profile-info-container">
			</div>
			<button class="button button-primary import-images"><?php echo esc_html__( 'Update Images', 'Wonkasoft_Instafeed' ); ?></button>

		</div>
		
		<div class="thickbox-body-wrap pre-loader">
			<div class="thickbox-body" data-tag="<?php echo esc_attr( $this->insta_hashtag ); ?>" data-tag_id="<?php echo esc_attr( $this->tag_id ); ?>" data-priority="<?php echo esc_attr( $this->hashtag_priority ); ?>" data-visibility="<?php echo esc_attr( json_encode( $this->hashtag_visibility ) ); ?>" data-status="<?php echo esc_attr( json_encode( $this->status ) ); ?>">
			</div>
		</div>

	  </div>

		<?php
	}

	/**
	 * This loads the contents of the instagram import box.
	 */
	public function get_thickbox_template() {

		$insta = new Instagram();

		$result = $insta->set_the_tag(
			array(
				'tag'   => $this->insta_hashtag,
			)
		);

		$result = $insta->get_the_tag_media( $this->fetch_qty );

		if ( empty( $result ) ) {

			$message = __( 'Wrong Credentials used please check the credentials again.', 'Wonkasoft_Instafeed' );

			$error_obj->set_error_code( 1 );

			$error_obj->insta_print_notification( $message );

			die;

		} else {

			$this->show_results( json_decode( $result ) );

		}

	}

	/**
	 * Output a text input box.
	 *
	 * @param array $field contains the field args.
	 */
	public function ws_insta_text_input( $field ) {

		$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : '';
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
		$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
		$data_type              = empty( $field['data_type'] ) ? '' : $field['data_type'];

		switch ( $data_type ) {
			case 'price':
				$field['class'] .= ' wc_input_price';
				$field['value']  = wc_format_localized_price( $field['value'] );
				break;
			case 'decimal':
				$field['class'] .= ' wc_input_decimal';
				$field['value']  = wc_format_localized_decimal( $field['value'] );
				break;
			case 'stock':
				$field['class'] .= ' wc_input_stock';
				$field['value']  = wc_stock_amount( $field['value'] );
				break;
			case 'url':
				$field['class'] .= ' wc_input_url';
				$field['value']  = esc_url( $field['value'] );
				break;

			default:
				break;
		}

		// Custom attribute handling.
		$custom_attributes = array();

		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

			foreach ( $field['custom_attributes'] as $attribute => $value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}

		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
			<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . ' <i class="fa fa-question-circle" tool-tip="toggle" title="' . esc_attr( $field['description'] ) . '"></i></label>';

		echo '<input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . esc_attr( implode( ' ', $custom_attributes ) ) . ' /> ';

		if ( ! empty( $field['description'] ) ) {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

		echo '</p>';
	}

	/**
	 * Output a hidden input box.
	 *
	 * @param array $field contains the field args.
	 */
	public function ws_insta_hidden_input( $field ) {

		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['value'] = isset( $field['value'] ) ? $field['value'] : '';
		$field['class'] = isset( $field['class'] ) ? $field['class'] : '';

		echo '<input type="hidden" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" /> ';
	}

	/**
	 * Output a textarea input box.
	 *
	 * @param array $field contains the field args.
	 */
	public function ws_insta_textarea_input( $field ) {

		$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : '';
		$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['rows']          = isset( $field['rows'] ) ? $field['rows'] : 2;
		$field['cols']          = isset( $field['cols'] ) ? $field['cols'] : 20;

		// Custom attribute handling.
		$custom_attributes = array();

		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

			foreach ( $field['custom_attributes'] as $attribute => $value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}

		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
			<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . ' <i class="fa fa-question-circle" tool-tip="toggle" title="' . esc_attr( $field['description'] ) . '"></i></label>';

		echo '<textarea class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '"  name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="' . esc_attr( $field['rows'] ) . '" cols="' . esc_attr( $field['cols'] ) . '" ' . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $field['value'] ) . '</textarea> ';

		if ( ! empty( $field['description'] ) ) {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

		echo '</p>';
	}

	/**
	 * Output a checkbox input box.
	 *
	 * @param array $field contains the field args.
	 */
	public function ws_insta_checkbox( $field ) {

		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'checkbox';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : '';
		$field['cbvalue']       = isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes';
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

		// Custom attribute handling.
		$custom_attributes = array();

		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

			foreach ( $field['custom_attributes'] as $attribute => $value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}

		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
			<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . ' <i class="fa fa-question-circle" tool-tip="toggle" title="' . esc_attr( $field['description'] ) . '"></i></label>';

		echo '<input type="checkbox" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['cbvalue'] ) . '" ' . checked( $field['value'], $field['cbvalue'], false ) . '  ' . implode( ' ', $custom_attributes ) . '/> ';

		if ( ! empty( $field['description'] ) ) {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

		echo '</p>';
	}

	/**
	 * Output a select input box.
	 *
	 * @param array $field contains the field args.
	 */
	public function ws_insta_select( $field ) {

		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : '';
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
		$field['multi_select']  = isset( $field['multi_select'] ) ? $field['multi_select'] : false;

		$multi_attr = '';
		if ( $field['multi_select'] ) :
			$multi_attr = 'multiple';
		endif;

		// Custom attribute handling.
		$custom_attributes = array();

		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

			foreach ( $field['custom_attributes'] as $attribute => $value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}

		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
			<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . ' <i class="fa fa-question-circle" tool-tip="toggle" title="' . esc_attr( $field['description'] ) . '"></i></label>';

		echo '<select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '[]" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" ' . implode( ' ', $custom_attributes ) . ' ' . $multi_attr . '>';
		foreach ( $field['options'] as $key => $value ) {
			if ( in_array( $key, $field['value'] ) ) :
				echo '<option value="' . esc_attr( $key ) . '" selected>' . esc_html( $value ) . '</option>';
			else :
				echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
			endif;
		}

		echo '</select> ';

		if ( ! empty( $field['description'] ) ) {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

		echo '</p>';
	}

	/**

	 * Output a radio input box.
	 *
	 * @param array $field contains the field args.
	 */
	public function ws_insta_radio( $field ) {

		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
		$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : '';
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

		echo '<label>' . wp_kses_post( $field['label'] ) . '</label>';

		if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
			echo wc_help_tip( $field['description'] );
		}

		echo '<ul class="wc-radios">';

		foreach ( $field['options'] as $key => $value ) {

			echo '<li><input
					name="' . esc_attr( $field['name'] ) . '"
					value="' . esc_attr( $key ) . '"
					type="radio"
					class="' . esc_attr( $field['class'] ) . '"
					style="' . esc_attr( $field['style'] ) . '"
					' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
					/> ' . esc_html( $value ) . '</li>';
		}

		echo '</ul>';

		if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

	}
}
