<?php 
if ( ! defined( 'WPINC' ) ) {
	die; 
}

if ( !class_exists( 'Addify_Registration_Fields_Addon_Admin' ) ) { 

	class Addify_Registration_Fields_Addon_Admin extends Addify_Registration_Fields_Addon {

		public function __construct() {
			
			add_action( 'admin_enqueue_scripts', array( $this, 'afreg_admin_scripts' ) );
			//Custom meta boxes
			add_action( 'admin_init', array( $this, 'afreg_register_metaboxes' ), 10 );
			add_action( 'save_post_afreg_fields', array( $this, 'afreg_meta_box_save' ));
			add_filter( 'manage_afreg_fields_posts_columns', array( $this, 'afreg_custom_columns' ) );
			add_action( 'manage_afreg_fields_posts_custom_column' , array( $this, 'afreg_custom_column' ), 10, 2 );
			add_filter('bulk_actions-edit-afreg_fields', array( $this, 'afreg_bulk_action' ));
			add_filter( 'handle_bulk_actions-edit-afreg_fields', array( $this, 'afreg_bulk_action_handler' ), 10, 3 );
			add_action( 'admin_notices', array( $this, 'afreg_bulk_action_admin_notice' ) );
			add_action( 'edit_user_profile', array( $this, 'afreg_profile_fields' ));
			add_action( 'edit_user_profile_update', array( $this, 'afreg_update_profile_fields' ));

			add_filter( 'manage_users_columns', array( $this, 'afreg_modify_user_table' ));
			add_filter( 'manage_users_custom_column', array( $this, 'afreg_modify_user_table_row' ), 10, 3 );
			add_filter( 'user_row_actions', array( $this, 'afreg_user_row_actions' ), 10, 2 );
			add_action( 'load-users.php', array( $this, 'afreg_update_action' ) );
			add_action( 'restrict_manage_users', array( $this, 'afreg_status_filter' ), 10, 1 );
			add_action( 'pre_user_query', array( $this, 'afreg_filter_user_by_status' ) );
			add_action( 'admin_footer-users.php', array( $this, 'afreg_admin_footer' ) );
			add_action( 'load-users.php', array( $this, 'afreg_bulk_action_user' ) );

			add_action('wp_ajax_afreg_save_df_form', array( $this, 'afreg_save_df_form' ));
			add_action('wp_ajax_nopriv_afreg_save_df_form', array( $this, 'afreg_save_df_form' ));

			add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'afreg_custom_checkout_field_display_admin_order_meta' ), 10, 1 );
		}

		public function afreg_admin_scripts() { 

			$screen = get_current_screen();


			if ( !in_array( $screen->id, array( 'edit-afreg_fields', 'afreg_fields', 'toplevel_page_addify-b2b' ) ) ) {
				return;
			}

			wp_enqueue_style( 'select2', plugins_url( 'assets/css/select2.css', WC_PLUGIN_FILE ), array(), '5.7.2' );

			wp_enqueue_script( 'select2', plugins_url( 'assets/js/select2/select2.min.js', WC_PLUGIN_FILE ), array( 'jquery' ), '4.0.3', true );

			
			wp_enqueue_script( 'color-spectrum-js', plugins_url( '/js/afreg_color_spectrum.js', __FILE__ ), false, '1.0' );
			wp_enqueue_style( 'color-spectrum-css', plugins_url( '/css/afreg_color_spectrum.css', __FILE__ ), false, '1.0' );

			wp_enqueue_style( 'afreg-admin-css', plugins_url( '/css/afreg_admin.css', __FILE__ ), false, '1.0' );
			wp_enqueue_script( 'afreg-admin-js', plugins_url( '/js/afreg_admin.js', __FILE__ ), false, '1.0.0' );
			$current_link = '';
			$afreg_data   = array(
				'admin_url' => admin_url('admin-ajax.php'),
				'nonce'     => wp_create_nonce('afreg-ajax-nonce'),
				'url'       => $current_link,
				
			);
			wp_localize_script( 'afreg-admin-js', 'afreg_php_vars', $afreg_data );
		}

		public function afreg_custom_checkout_field_display_admin_order_meta( $order ) { 

			

			$afreg_args = array( 
				'posts_per_page' => -1,
				'post_type'      => 'afreg_fields',
				'post_status'    => 'publish',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			);
			

			$afreg_extra_fields = get_posts($afreg_args);

			foreach ($afreg_extra_fields as $afreg_field) {

				$afreg_field_type          = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
				$afreg_field_order_details = get_post_meta( intval($afreg_field->ID), 'afreg_field_order_details', true );
				$afregcheck                = get_user_meta( $order->get_customer_id(), 'afreg_additional_' . intval($afreg_field->ID), true );

				if (!empty($afregcheck) && 'on' == $afreg_field_order_details) { 

					$value = get_user_meta( $order->get_customer_id(), 'afreg_additional_' . intval($afreg_field->ID), true );
					
					if ( 'checkbox' == $afreg_field_type) {
						if ('yes' == $value) {
							echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_b2b') . '</b>' . esc_html__('Yes', 'addify_b2b') . '</p>';
						} else {
							echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_b2b') . '</b>' . esc_html__('No', 'addify_b2b') . '</p>';
						}

					} elseif ( 'fileupload' == $afreg_field_type) {


						$upload_url = wp_upload_dir();

						$current_file = '';

						$curr_image_new_folder = $upload_url['basedir'] . '/addify_registration_uploads/' . $value;

						$curr_image = esc_url(AFREG_URL . 'uploaded_files/' . $value);

						if (file_exists($curr_image_new_folder)) {

							$current_file = esc_url($upload_url['baseurl'] . '/addify_registration_uploads/' . $value);

						} elseif (file_exists($curr_image)) {

							$current_file = esc_url(AFREG_URL . 'uploaded_files/' . $value);

						}

						
						echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_b2b') . '</b><a href=' . esc_url($current_file) . '>' . esc_html__('Click here to View', 'addify_b2b') . '</a></p>';

					} elseif ( in_array( $afreg_field_type , array( 'multiselect', 'multi_checkbox', 'select', 'radio' ) ) ) {
						$val_array           = explode(', ' , $value );
						$afreg_field_options = unserialize(get_post_meta(  intval($afreg_field->ID) , 'afreg_field_option', true )); 
						$value               = '';
						foreach ( $val_array as $option_val ) {
							foreach ($afreg_field_options as $afreg_field_option ) { 
								if ( esc_attr( $option_val ) == $afreg_field_option['field_value'] ) {
									$value .=  $afreg_field_option['field_text'] . ', ';
								}
							}
						}

						echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_b2b') . '</b>' . esc_attr($value) . '</p>';
					} elseif ('timepicker' == $afreg_field_type) {

						echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_b2b') . '</b><input type="time" value="' . esc_attr($value) . '" readonly="readonly"></p>';

					} else {
						echo '<p><b>' . esc_html__($afreg_field->post_title . ': ', 'addify_b2b') . '</b>' . esc_attr($value) . '</p>';
					}

				}
			}
		}

		public function afreg_register_metaboxes() {

			add_meta_box( 'afreg_field_details', esc_html__( 'Field Details', 'addify_b2b' ), array( $this, 'afreg_field_details_callback' ), 'afreg_fields', 'normal', 'high' );
			add_meta_box( 'afreg_field_formating', esc_html__( 'Field Formating', 'addify_b2b' ), array( $this, 'afreg_field_formating_callback' ), 'afreg_fields', 'normal', 'high' );
			add_meta_box( 'afreg_field_user_role', esc_html__( 'Dependency', 'addify_b2b' ), array( $this, 'afreg_field_user_role_callback' ), 'afreg_fields', 'normal', 'high' );
			add_meta_box( 'afreg_field_status', esc_html__( 'Field Status', 'addify_b2b' ), array( $this, 'afreg_field_status_callback' ), 'afreg_fields', 'side', 'high' );
		}

		public function afreg_field_details_callback() {
			global $post;
			wp_nonce_field( 'afreg_nonce_action', 'afreg_nonce_field' );
			$afreg_field_type      = get_post_meta( $post->ID, 'afreg_field_type', true );
			$afreg_field_options   = unserialize(get_post_meta( $post->ID, 'afreg_field_option', true )); 
			$afreg_field_file_size = get_post_meta( $post->ID, 'afreg_field_file_size', true );
			$afreg_field_file_type = get_post_meta( $post->ID, 'afreg_field_file_type', true );

			$afreg_vat_validation = get_post_meta( $post->ID, 'afreg_vat_validation', true );
			$afreg_vat_length     = get_post_meta( $post->ID, 'afreg_vat_length', true );
			
			?>
			<div class="addify_reg">
				<div class="meta_field_full">
					<label for="afreg_field_label"><?php echo esc_html__('Field Label', 'addify_b2b'); ?></label>
					<p class="afreg_field_label_msg"><?php echo esc_html__( 'Enter the text in above title field, that will become field label.', 'addify_b2b' ); ?></p>
				</div>

				<div class="meta_field_full">
					<label for="afreg_field_type"><?php echo esc_html__('Field Type', 'addify_b2b'); ?></label>
					<select name="afreg_field_type" id="afreg_field_type" class="afreg_field_select" onchange="afreg_show_options(this.value)">
						<option value="text" <?php echo selected(esc_attr($afreg_field_type), 'text'); ?>><?php echo esc_html__('Text', 'addify_b2b'); ?></option>
						<option value="textarea" <?php echo selected(esc_attr($afreg_field_type), 'textarea'); ?>><?php echo esc_html__('Textarea', 'addify_b2b'); ?></option>
						<option value="email" <?php echo selected(esc_attr($afreg_field_type), 'email'); ?>><?php echo esc_html__('Email', 'addify_b2b'); ?></option>
						<option value="select" <?php echo selected(esc_attr($afreg_field_type), 'select'); ?>><?php echo esc_html__('Selectbox', 'addify_b2b'); ?></option>
						<option value="multiselect" <?php echo selected(esc_attr($afreg_field_type), 'multiselect'); ?>><?php echo esc_html__('Multi Selectbox', 'addify_b2b'); ?></option>
						<option value="checkbox" <?php echo selected(esc_attr($afreg_field_type), 'checkbox'); ?>><?php echo esc_html__('Checkbox', 'addify_b2b'); ?></option>
						<option value="multi_checkbox" <?php echo selected(esc_attr($afreg_field_type), 'multi_checkbox'); ?>><?php echo esc_html__('Multi Checkbox', 'addify_b2b'); ?></option>
						<option value="radio" <?php echo selected(esc_attr($afreg_field_type), 'radio'); ?>><?php echo esc_html__('Radio Button', 'addify_b2b'); ?></option>
						<option value="number" <?php echo selected(esc_attr($afreg_field_type), 'number'); ?>><?php echo esc_html__('Number', 'addify_b2b'); ?></option>
						<option value="password" <?php echo selected(esc_attr($afreg_field_type), 'password'); ?>><?php echo esc_html__('Password', 'addify_b2b'); ?></option>
						<option value="fileupload" <?php echo selected(esc_attr($afreg_field_type), 'fileupload'); ?>><?php echo esc_html__('File Upload (Supports my account registration page only)', 'addify_b2b'); ?></option>
						<option value="color" <?php echo selected(esc_attr($afreg_field_type), 'color'); ?>><?php echo esc_html__('Color Picker', 'addify_b2b'); ?></option>
						<option value="datepicker" <?php echo selected(esc_attr($afreg_field_type), 'datepicker'); ?>><?php echo esc_html__('Date Picker', 'addify_b2b'); ?></option>
						<option value="timepicker" <?php echo selected(esc_attr($afreg_field_type), 'timepicker'); ?>><?php echo esc_html__('Time Picker', 'addify_b2b'); ?></option>
						<option value="vat" <?php echo selected(esc_attr($afreg_field_type), 'vat'); ?>><?php echo esc_html__('Tax/Vat', 'addify_b2b'); ?></option>
						<option value="googlecaptcha" <?php echo selected(esc_attr($afreg_field_type), 'googlecaptcha'); ?>><?php echo esc_html__('Google reCAPTCHA (Supports my account registration page only', 'addify_b2b'); ?></option>
						<option value="heading" <?php echo selected(esc_attr($afreg_field_type), 'heading'); ?>><?php echo esc_html__('Heading', 'addify_b2b'); ?></option>
						<option value="description" <?php echo selected(esc_attr($afreg_field_type), 'description'); ?>><?php echo esc_html__('Description', 'addify_b2b'); ?></option>
					</select>
				</div>

				<div class="meta_field_full afreg_vat">
					<label for="afreg_field_file_size"><?php echo esc_html__('Validation Type', 'addify_b2b'); ?></label>
					<select name="afreg_vat_validation" id="afreg_vat_validation" class="afreg_vat_validation">
						<option value="none" <?php echo selected(esc_attr($afreg_vat_validation), 'none'); ?>><?php echo esc_html__('None', 'addify_b2b'); ?></option>
						<option value="length" <?php echo selected(esc_attr($afreg_vat_validation), 'length'); ?>><?php echo esc_html__('Length', 'addify_b2b'); ?></option>
						<option value="vies" <?php echo selected(esc_attr($afreg_vat_validation), 'vies'); ?>><?php echo esc_html__('VIES Validation', 'addify_b2b'); ?></option>
					</select>

					<div id="afreg_vat_length">
						<label for="afreg_field_file_size"><?php echo esc_html__('Length', 'addify_b2b'); ?></label>
						<input type="number" name="afreg_vat_length" value="<?php echo intval( $afreg_vat_length ); ?>">
					</div>
				</div>

				<div id="afreg_recaptcha" class="meta_field_full">
					<p class="afreg_field_label_msg"><?php echo esc_html__( 'For google reCaptcha field you must enter correct site key and secret key in our module settings. Without these keys google reCaptcha will not work.', 'addify_b2b' ); ?></p>
				</div>

				<div class="meta_field_full afreg_fileupload">
					<label for="afreg_field_file_size"><?php echo esc_html__('File Upload Size(MB)', 'addify_b2b'); ?></label>
					<input type="number" name="afreg_field_file_size" id="afreg_field_file_size" class="" value="<?php echo esc_attr($afreg_field_file_size); ?>" />
				</div>

				<div class="meta_field_full afreg_fileupload">
					<label for="afreg_field_file_type"><?php echo esc_html__('Allowed File Types(Add Comma(,) separated types. e.g png,jpg,gif)', 'addify_b2b'); ?></label>
					<input type="text" name="afreg_field_file_type" id="afreg_field_file_type" class="afreg_field_text" value="<?php echo esc_attr($afreg_field_file_type); ?>" />
				</div>

				<div class="meta_field_full" id="afreg_field_options">
					<label for="afreg_field_options"><?php echo esc_html__('Field Options', 'addify_b2b'); ?></label>
					<div class="afreg_field_options">
						<table cellspacing="0" cellpadding="0" border="1" width="100%">
							<thead>
								<tr>
									<th><?php echo esc_html__('Option Value', 'addify_b2b'); ?></th>
									<th><?php echo esc_html__('Field Label/Text', 'addify_b2b'); ?></th>
									<th><?php echo esc_html__('Action', 'addify_b2b'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$afreg_a = 0;
								if (!empty($afreg_field_options)) {
									foreach ($afreg_field_options as $afreg_field_option) { 
										?>
										<tr>
											<td>
												<input type="text" name="afreg_field_option[<?php echo intval($afreg_a); ?>][field_value]" id="afreg_field_option_value<?php echo intval($afreg_a); ?>" class="option_field" value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" />
											</td>
											<td>
												<input type="text" name="afreg_field_option[<?php echo intval($afreg_a); ?>][field_text]" id="afreg_field_option_value<?php echo intval($afreg_a); ?>" class="option_field" value="<?php echo esc_attr($afreg_field_option['field_text']); ?>" />
											</td>
											<td><button type="button" class="button button-danger" onclick="jQuery(this).closest('tr').remove();"><?php echo esc_html__('Remove Option', 'addify_b2b'); ?></button></td>
										</tr>
										<?php ++$afreg_a; } } ?>
									</tbody>
									<tfoot>
										<tr id="NewField"></tr>
									</tfoot>

								</table>

								<div class="afreg_addbt"><button type="button" class="button-primary" onclick="afreg_add_option()"><?php echo esc_html__('Add New Option', 'addify_b2b'); ?></button></div>
							</div>
						</div>

					</div>

					<?php 
		}

		public function afreg_field_formating_callback() {
			global $post;

			$afreg_field_required                  = get_post_meta( $post->ID, 'afreg_field_required', true );
			$afreg_field_show_in_registration_form = get_post_meta( $post->ID, 'afreg_field_show_in_registration_form', true );
			$afreg_field_show_in_my_account        = get_post_meta( $post->ID, 'afreg_field_show_in_my_account', true );
			$afreg_field_read_only                 = get_post_meta( $post->ID, 'afreg_field_read_only', true );
			$afreg_field_order_details             = get_post_meta( $post->ID, 'afreg_field_order_details', true );
			$afreg_field_width                     = get_post_meta( $post->ID, 'afreg_field_width', true );
			$afreg_field_placeholder               = get_post_meta( $post->ID, 'afreg_field_placeholder', true );
			$afreg_field_description               = get_post_meta( $post->ID, 'afreg_field_description', true );
			$afreg_field_css                       = get_post_meta( $post->ID, 'afreg_field_css', true );

			$afreg_field_heading_type      = get_post_meta( $post->ID, 'afreg_field_heading_type', true );
			$afreg_field_description_field = get_post_meta( $post->ID, 'afreg_field_description_field', true );

			if (empty($afreg_field_show_in_registration_form )) {

				$afreg_field_show_in_registration_form = 'on';
			}

			if (empty($afreg_field_show_in_my_account )) {

				$afreg_field_show_in_my_account = 'on';
			}

			?>
					<div class="addify_reg">
						<div class="meta_field_formating afreg_recaptchahide heading_hide">
							<label for="afreg_field_required"><?php echo esc_html__('Required Field', 'addify_b2b'); ?></label>
							<input type="checkbox" name="afreg_field_required" id="afreg_field_required" <?php echo checked(esc_attr($afreg_field_required), 'on'); ?> />
						</div>

						<div class="meta_field_formating afreg_recaptchahide heading_show">
							<label for="afreg_field_show_in_registration_form"><?php echo esc_html__('Show in WooCommerce Registration Form', 'addify_b2b'); ?></label>
							<input type="checkbox" name="afreg_field_show_in_registration_form" id="afreg_field_show_in_registration_form" <?php echo checked(esc_attr($afreg_field_show_in_registration_form), 'on'); ?> />
						</div>

						<div class="meta_field_formating afreg_recaptchahide heading_show">
							<label for="afreg_field_show_in_my_account"><?php echo esc_html__('Show in WooCommerce My Account', 'addify_b2b'); ?></label>
							<input type="checkbox" name="afreg_field_show_in_my_account" id="afreg_field_show_in_my_account" <?php echo checked(esc_attr($afreg_field_show_in_my_account), 'on'); ?> />
						</div>

						<!-- Description -->
						<div class="meta_field_formating afreg_recaptchahide description_show">
							<label for="afreg_field_description_field"><?php echo esc_html__('Description Field', 'addify_b2b'); ?></label>
							<textarea name="afreg_field_description_field" id="afreg_field_description_field" rows="7" cols="106"><?php echo wp_kses_post($afreg_field_description_field); ?></textarea>
						</div>
						<!-- Description -->

						<!-- Heading -->
						<div class="meta_field_formating afreg_recaptchahide heading_type_show">
							<label for="afreg_field_heading_type"><?php echo esc_html__('Heading Format', 'addify_b2b'); ?></label>
							<select name="afreg_field_heading_type" id="afreg_field_heading_type">
								<option value="h1" <?php echo selected(esc_attr($afreg_field_heading_type), 'h1'); ?>><?php echo esc_html__('H1', 'addify_b2b'); ?></option>
								<option value="h2" <?php echo selected(esc_attr($afreg_field_heading_type), 'h2'); ?>><?php echo esc_html__('H2', 'addify_b2b'); ?></option>
								<option value="h3" <?php echo selected(esc_attr($afreg_field_heading_type), 'h3'); ?>><?php echo esc_html__('H3', 'addify_b2b'); ?></option>
								<option value="h4" <?php echo selected(esc_attr($afreg_field_heading_type), 'h4'); ?>><?php echo esc_html__('H4', 'addify_b2b'); ?></option>
								<option value="h5" <?php echo selected(esc_attr($afreg_field_heading_type), 'h5'); ?>><?php echo esc_html__('H5', 'addify_b2b'); ?></option>
								<option value="h6" <?php echo selected(esc_attr($afreg_field_heading_type), 'h6'); ?>><?php echo esc_html__('H6', 'addify_b2b'); ?></option>
							</select>
						</div>
						<!-- Heading -->

						<div class="meta_field_formating afreg_recaptchahide heading_hide">
							<label for="afreg_field_read_only"><?php echo esc_html__('Read Only Field(Customer can not update this from My Account page)', 'addify_b2b'); ?></label>
							<input type="checkbox" name="afreg_field_read_only" id="afreg_field_read_only" <?php echo checked(esc_attr($afreg_field_read_only), 'on'); ?> />
						</div>

						<div class="meta_field_formating afreg_recaptchahide heading_hide">
							<label for="afreg_field_order_details"><?php echo esc_html__('Show in admin order detail page and order email', 'addify_b2b'); ?></label>
							<input type="checkbox" name="afreg_field_order_details" id="afreg_field_order_details" <?php echo checked(esc_attr($afreg_field_order_details), 'on'); ?> />
						</div>

						<div class="meta_field_formating afreg_recaptchahide heading_hide">
							<label for="afreg_field_width"><?php echo esc_html__('Field Width', 'addify_b2b'); ?></label>
							<select name="afreg_field_width" id="afreg_field_width">
								<option value="full" <?php echo selected(esc_attr($afreg_field_width), 'full'); ?>><?php echo esc_html__('Full Width', 'addify_b2b'); ?></option>
								<option value="half" <?php echo selected(esc_attr($afreg_field_width), 'half'); ?>><?php echo esc_html__('Half Width', 'addify_b2b'); ?></option>
							</select>

						</div>

						<div class="meta_field_full afreg_recaptchahide heading_hide">
							<label for="afreg_field_placeholder"><?php echo esc_html__('Field Placeholder Text', 'addify_b2b'); ?></label>
							<input type="text" name="afreg_field_placeholder" id="afreg_field_placeholder" class="afreg_field_text" value="<?php echo esc_attr($afreg_field_placeholder); ?>" />
						</div>

						<div class="meta_field_full heading_hide gshow">
							<label for="afreg_field_description"><?php echo esc_html__('Field Description', 'addify_b2b'); ?></label>
							<input type="text" name="afreg_field_description" id="afreg_field_description" class="afreg_field_text" value='<?php echo wp_kses_post($afreg_field_description); ?>' />
							<p><?php echo esc_html__('HTML tags are allowd.', 'addify_b2b'); ?></p>
						</div>

						<div class="meta_field_full afreg_recaptchahide heading_show">
							<label for="afreg_field_css"><?php echo esc_html__('Field Custom Css Class', 'addify_b2b'); ?></label>
							<input type="text" name="afreg_field_css" id="afreg_field_css" class="afreg_field_text" value="<?php echo esc_attr($afreg_field_css); ?>" />
						</div>

					</div>

					<?php 
		}

		public function afreg_field_user_role_callback() {

			global $post, $wp_roles;

			$depFieldsAllowd = array( 'select', 'multiselect', 'checkbox', 'multi_checkbox', 'radio' );

			$afreg_field_user_roles = (array) get_post_meta( $post->ID, 'afreg_field_user_roles', true );

			$afreg_is_dependable = get_post_meta( $post->ID, 'afreg_is_dependable', true );

			$get_all_post = get_posts( array(
				'post_type'      => 'afreg_fields',
				'fields'         =>'ids',
				'posts_per_page' =>-1,
				'post_status'    => 'publish',
			) );

			//$check_index_of_current_post = array_search(get_the_ID(), $get_all_post);

			//unset( $get_all_post[ $check_index_of_current_post ] );

			?>
					<table class="wp-list-table widefat fixed striped table-view-list">
						<tbody>
							<tr class="afreg_recaptchahide heading_show">
								<th>
									<label for="afreg_field_css"><?php echo esc_html__('is Dependable on User Role ?', 'addify_b2b'); ?></label>
								</th>
								<td>
									<input type="checkbox" name="afreg_is_dependable" id="afreg_is_dependable" <?php echo checked(esc_attr($afreg_is_dependable), 'on'); ?> />
								</td>
							</tr>

							

							<tr class="afreg_recaptchahide heading_show afuserroledep">
								<th>
									<label for="afreg_field_required"><?php echo esc_html__('Select User Roles', 'addify_b2b'); ?></label>

								</th>

								<td>
									<select style="width: 50%;" multiple class="parent af_reg_live_search" name="afreg_field_user_roles[]" id="afreg_field_user_roles">
								<?php 
								foreach ($wp_roles->get_names() as $key => $value) { 
									if ( 'administrator' != $key) { 
										?>
												<option value="<?php echo esc_attr( $key ); ?>" <?php if ( in_array( $key , $afreg_field_user_roles) ) : ?>
												selected
												<?php endif ?> >
												<?php echo esc_attr($value); ?>

											</option>
											<?php 
									}
								}
								?>

								</select>
								<p class="description afreg_enable_user_role"><?php echo esc_html__('Select user roles on which you want to show this field, leave empty for show in all.', 'addify_b2b'); ?></p>

							</td>
						</tr>

						<?php 
						if ( count( $get_all_post)  >= 1  ) :

							$dependable_detail       = (array) get_post_meta( get_the_ID(), 'afreg_field_dependable_on', true ); 
							$dependable_field_id     = isset($dependable_detail['dependable_field_id']) ? $dependable_detail['dependable_field_id'] : '' ;
							$dependable_field_option = isset($dependable_detail['dependable_field_option']) ? $dependable_detail['dependable_field_option'] : '' ;
							?>

							<tr class="afreg_recaptchahide heading_show afregvatFielddependable">
								<th>
									<label for="afreg_field_css"><?php echo esc_html__('is Dependable on Fields ?', 'addify_b2b'); ?></label>
								</th>
								<td>
									<input type="checkbox" name="afreg_field_dependable_on[checkbox]" value="yes" class="afreg_field_dependable_on_checkbox" <?php if ( isset( $dependable_detail['checkbox'] ) && ! empty( $dependable_detail['checkbox'] )  ) : ?>
										checked
									<?php endif ?> />
								</td>
							</tr>


							<tr class="afregvatFielddependable">
								<th>
									<label for="afreg_field_css"><?php echo esc_html__('Select Fields', 'addify_b2b'); ?></label>
									<br />
									<span class="afreg_infomsg"><?php echo esc_html__('(Selectbox, Multi Selectbox, Checkbox, Multi Checkbox and Radio Button field types are allowed)', 'addify_b2b'); ?></span>
								</th>
								<td>
									<select name="afreg_field_dependable_on[dependable_field_id]" class="afreg_field_dependable_on_dependable_field_id">
										<option value="0"><?php echo esc_html__('Select Dependable Field', 'addify_b2b'); ?></option>

										<?php foreach ($get_all_post as $rule_id) : ?>
											<?php

											$depFieldType = get_post_meta($rule_id, 'afreg_field_type', true);
											if (in_array($depFieldType, $depFieldsAllowd)) {
												?>
												<option value="<?php echo esc_attr( $rule_id ); ?>" <?php if ( $rule_id == $dependable_field_id ) : ?>
												selected
													<?php endif ?> data-field_type="<?php echo esc_attr( get_post_meta($rule_id, 'afreg_field_type', true) ); ?>" >
													<?php echo esc_attr( get_the_title( $rule_id ) ); ?>
												</option>
											<?php } ?>
									<?php endforeach ?>

								</select>
							</td>
						</tr>

						<tr class="afregvatFielddependable">
							<th>
								<label for="afreg_field_css"><?php echo esc_html__('Enter Field Values', 'addify_b2b'); ?></label>

							</th>
							<td>
								<input type="text" name="afreg_field_dependable_on[dependable_field_option]" value="<?php echo esc_attr( $dependable_field_option ); ?>" class="afreg_field_dependable_on_dependable_field_option">
								<p><?php echo esc_html__('Enter multiple option value with comma seprated.', 'addify_b2b'); ?></p>


							</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
			<?php
		}

		public function afreg_field_status_callback() {

			global $post;

			?>
			<div class="addify_reg">

				<div class="meta_field_full">
					<label for="afreg_field_sort_order"><?php echo esc_html__('Field Sort Order', 'addify_b2b'); ?></label>
					<input type="number" min="0" name="afreg_field_sort_order" id="afreg_field_sort_order" value="<?php echo esc_attr($post->menu_order); ?>" />
				</div>

				<div class="meta_field_formating">
					<label for="afreg_field_status"><?php echo esc_html__('Field Status', 'addify_b2b'); ?></label>
					<select name="afreg_field_status" id="afreg_field_status">
						<option value="publish" <?php echo selected(esc_attr($post->post_status), 'publish'); ?>><?php echo esc_html__('Active', 'addify_b2b'); ?></option>
						<option value="draft" <?php echo selected(esc_attr($post->post_status), 'draft'); ?>><?php echo esc_html__('Inactive', 'addify_b2b'); ?></option>
					</select>
				</div>
			</div>
			<?php
		}

		public function afreg_meta_box_save( $post_id ) {

			//For custom post type:
			$exclude_statuses = array(
				'auto-draft',
				'trash',
			);

			$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

			if ( in_array( get_post_status($post_id), $exclude_statuses ) || is_ajax() || 'untrash' === $action ) {
				return;
			}

			if ( empty( $_POST['afreg_nonce_field'] ) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['afreg_nonce_field'])), 'afreg_nonce_action')) {

				wp_die( esc_html__('Security Violated', 'addify_b2b') );
			}


			if ( isset( $_POST['afreg_field_type'] ) ) { 

				update_post_meta( intval($post_id), 'afreg_field_type', sanitize_text_field( $_POST['afreg_field_type'] ) );
			}

			remove_action( 'save_post_afreg_fields', array( $this, 'afreg_meta_box_save' ));

			if ( isset($_POST['afreg_field_status']) ) {
				wp_update_post( array(
					'ID'          => intval($post_id),
					'post_status' => sanitize_text_field($_POST['afreg_field_status']),
				) );
			}

			if ( isset($_POST['afreg_field_sort_order']) ) {
				wp_update_post( array(
					'ID'         => intval($post_id),
					'menu_order' => sanitize_text_field($_POST['afreg_field_sort_order']),
				) );
			}

			add_action( 'save_post_afreg_fields', array( $this, 'afreg_meta_box_save' ));

			if ( isset( $_POST['afreg_field_option'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_option', serialize(sanitize_meta( '', $_POST['afreg_field_option'], '')));
			} else {
				delete_post_meta( intval($post_id), 'afreg_field_option' );
			}


			if ( isset( $_POST['afreg_vat_validation'] ) ) {
				update_post_meta( intval($post_id), 'afreg_vat_validation', sanitize_text_field( $_POST['afreg_vat_validation'] ) );
			}

			if ( isset( $_POST['afreg_vat_length'] ) ) {
				update_post_meta( intval($post_id), 'afreg_vat_length', sanitize_text_field( $_POST['afreg_vat_length'] ) );
			}



			if ( isset( $_POST['afreg_field_required'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_required', sanitize_text_field( $_POST['afreg_field_required'] ) );
			} else {
				update_post_meta( intval($post_id), 'afreg_field_required', 'off' );    
			}


			if ( isset( $_POST['afreg_field_show_in_registration_form'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_show_in_registration_form', sanitize_text_field( $_POST['afreg_field_show_in_registration_form'] ) );
			} else {
				update_post_meta( intval($post_id), 'afreg_field_show_in_registration_form', 'off' );   
			}


			if ( isset( $_POST['afreg_field_show_in_my_account'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_show_in_my_account', sanitize_text_field( $_POST['afreg_field_show_in_my_account'] ) );
			} else {
				update_post_meta( intval($post_id), 'afreg_field_show_in_my_account', 'off' );  
			}


			if ( isset( $_POST['afreg_field_read_only'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_read_only', sanitize_text_field( $_POST['afreg_field_read_only'] ) );
			} else {
				update_post_meta( intval($post_id), 'afreg_field_read_only', 'off' );
			}

			if ( isset( $_POST['afreg_field_order_details'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_order_details', sanitize_text_field( $_POST['afreg_field_order_details'] ) );
			} else {
				update_post_meta( intval($post_id), 'afreg_field_order_details', 'off' );
			}

			if ( isset( $_POST['afreg_field_width'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_width', sanitize_text_field( $_POST['afreg_field_width'] ) );
			}

			if ( isset( $_POST['afreg_field_placeholder'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_placeholder', sanitize_text_field( $_POST['afreg_field_placeholder'] ) );
			}

			if ( isset( $_POST['afreg_field_description'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_description', sanitize_meta('', $_POST['afreg_field_description'], '' ) );
			}

			if ( isset( $_POST['afreg_field_css'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_css', sanitize_text_field( $_POST['afreg_field_css'] ) );
			}

			if ( isset( $_POST['afreg_field_file_size'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_file_size', sanitize_text_field( $_POST['afreg_field_file_size'] ) );
			}

			if ( isset( $_POST['afreg_field_file_type'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_file_type', sanitize_text_field( $_POST['afreg_field_file_type'] ) );
			}

			if ( isset( $_POST['afreg_field_user_roles'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_user_roles', sanitize_meta( '', $_POST['afreg_field_user_roles'], ''));
			} else {

				update_post_meta( intval($post_id), 'afreg_field_user_roles', array() );
			}

			if ( isset( $_POST['afreg_is_dependable'] ) ) {
				update_post_meta( intval($post_id), 'afreg_is_dependable', sanitize_text_field( $_POST['afreg_is_dependable'] ) );
			} else {
				update_post_meta( intval($post_id), 'afreg_is_dependable', 'off' );
			}


			$dependable_detail = isset($_POST['afreg_field_dependable_on']) ? sanitize_meta( '', $_POST['afreg_field_dependable_on'] , '' ) : array() ;

			$dependable_detail['dependable_field_option'] = isset($dependable_detail['dependable_field_option']) ? preg_replace( '/\s+/', ' ', trim( $dependable_detail['dependable_field_option'] ) ) : '' ;

			update_post_meta( intval($post_id), 'afreg_field_dependable_on', $dependable_detail );


			if ( isset( $_POST['afreg_field_heading_type'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_heading_type', sanitize_text_field( $_POST['afreg_field_heading_type'] ) );
			} else {
				update_post_meta( intval($post_id), 'afreg_field_heading_type', '' );
			}

			if ( isset( $_POST['afreg_field_description_field'] ) ) {
				update_post_meta( intval($post_id), 'afreg_field_description_field', sanitize_meta( '', $_POST['afreg_field_description_field'], ''));
			} else {

				update_post_meta( intval($post_id), 'afreg_field_description_field', '' );
			}
		}


		public function afreg_custom_columns( $columns ) {

			unset($columns['date']);
			$columns['afreg_field_type']       = esc_html__( 'Field Type', 'addify_b2b' );
			$columns['afreg_field_status']     = esc_html__( 'Status', 'addify_b2b' );
			$columns['afreg_field_sort_order'] = esc_html__( 'Sort Order', 'addify_b2b' );


			return $columns;
		}

		public function afreg_custom_column( $column, $post_id ) {
			$afreg_post = get_post($post_id);
			switch ( $column ) {
				case 'afreg_field_type':
				echo esc_attr(ucwords(str_replace('_', ' ', get_post_meta($post_id, 'afreg_field_type', true))));
					break;

				case 'afreg_field_status':
					if ('publish' == $afreg_post->post_status) {
						echo esc_html__( 'Active', 'addify_b2b' );
					} else {
						esc_html__( 'Inactive', 'addify_b2b' );
					}
					break;

				case 'afreg_field_sort_order':
				echo esc_attr($afreg_post->menu_order);
					break;

			}
		}

		public function afreg_bulk_action( $bulk_actions ) {
			$bulk_actions['afreg_active']   = esc_html__( 'Active', 'addify_b2b' );
			$bulk_actions['afreg_inactive'] = esc_html__( 'Inactive', 'addify_b2b' );
			return $bulk_actions;
		}

		public function afreg_bulk_action_handler( $redirect_to, $action_name, $post_ids ) {

			if ( 'afreg_active' === $action_name ) {

				foreach ( $post_ids as $post_id ) { 
					wp_update_post( array(
						'ID'          => intval($post_id),
						'post_status' => 'publish',
					) );
				} 

				$redirect_to = add_query_arg( 'afreg_active', count( $post_ids ), $redirect_to ); 
				return $redirect_to; 

			} elseif ( 'afreg_inactive' === $action_name ) {

				foreach ( $post_ids as $post_id ) { 
					wp_update_post( array(
						'ID'          => intval($post_id),
						'post_status' => 'draft',
					) );
				} 

				$redirect_to = add_query_arg( 'afreg_inactive', count( $post_ids ), $redirect_to ); 
				return $redirect_to;
			} else {
				return $redirect_to;
			}
		} 

		public function afreg_bulk_action_admin_notice() { 

			$afreg_allowed_tags = array(
				'a'      => array(
					'class' => array(),
					'href'  => array(),
					'rel'   => array(),
					'title' => array(),
				),
				'b'      => array(),

				'div'    => array(
					'class' => array(),
					'title' => array(),
					'style' => array(),
				),
				'p'      => array(
					'class' => array(),
				),
				'strong' => array(),

			);

			if ( ! empty( $_REQUEST['afreg_active'] ) ) { 
				$posts_count     = intval( $_REQUEST['afreg_active'] ); 
				$afreg_woo_check = '<div id="message" class="updated notice notice-success is-dismissible"><p>' . $posts_count . ' field(s) are set to active.</p><button type="button" class="notice-dismiss"></button></div>';
				echo wp_kses( __( $afreg_woo_check, 'addify_b2b' ), $afreg_allowed_tags);

			} elseif (! empty( $_REQUEST['afreg_inactive'] ) ) {
				$posts_count     = intval( $_REQUEST['afreg_inactive'] ); 
				$afreg_woo_check = '<div id="message" class="updated notice notice-success is-dismissible"><p>' . $posts_count . ' field(s) are set to inactive.</p><button type="button" class="notice-dismiss"></button></div>';
				echo wp_kses( __( $afreg_woo_check, 'addify_b2b' ), $afreg_allowed_tags);
			}
		} 




		public function afreg_profile_fields() {

			if ( isset( $_GET['user_id'])) {

				$user_id = intval($_GET['user_id']);

			} else {

				$user_id = '';
			}

			wp_nonce_field( 'afreg_nonce_action', 'afreg_nonce_field' );
			?>

			<h3><?php echo esc_html__(get_option('afreg_additional_fields_section_title'), 'addify_b2b'); ?></h3>
			<div class="afreg_extra_fields">
				<table class="form-table">

					<?php if (!empty( get_option('afreg_enable_approve_user')) && 'yes' == get_option('afreg_enable_approve_user')) { ?>

						<tr>
							<th><label><?php echo esc_html__('User Status', 'addify_b2b'); ?></label></th>
							<td>
								<?php
								$user_status = get_user_meta( $user_id, 'afreg_new_user_status', true);
								?>
								<select name="afreg_new_user_status">
									<option value=""><?php echo esc_html__('Select Status', 'addify_b2b'); ?></option>
									<?php 
									if ('approved' == $user_status || 'disapproved' == $user_status || '' == $user_status) {
										echo '';
									} else { 
										?>
										<option value="pending" <?php echo selected('pending', $user_status); ?>><?php echo esc_html__('Pending', 'addify_b2b'); ?></option>
									<?php } ?>
									<option value="approved" <?php echo selected('approved', $user_status); ?>><?php echo esc_html__('Approved', 'addify_b2b'); ?></option>
									<option value="approve_without_email" <?php echo selected('approve_without_email', $user_status); ?>><?php echo esc_html__('Approve Without Email', 'addify_b2b'); ?></option>
									<option value="disapproved" <?php echo selected('disapproved', $user_status); ?>><?php echo esc_html__('Disapproved', 'addify_b2b'); ?></option>
								</select>
							</td>
						</tr>
					<?php } ?>
					<?php 

					$afreg_args = array( 
						'posts_per_page'   => -1,
						'post_type'        => 'afreg_fields',
						'post_status'      => 'publish',
						'orderby'          => 'menu_order',
						'order'            => 'ASC',
						'suppress_filters' => false,

					);
					$afreg_extra_fields = get_posts($afreg_args);
					if (!empty($afreg_extra_fields)) {

						foreach ($afreg_extra_fields as $afreg_field) {

							$afreg_field_type        = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
							$afreg_field_options     = unserialize(get_post_meta( intval($afreg_field->ID), 'afreg_field_option', true )); 
							$afreg_field_placeholder = get_post_meta( intval($afreg_field->ID), 'afreg_field_placeholder', true );
							$afreg_field_description = get_post_meta( intval($afreg_field->ID), 'afreg_field_description', true );

							if ( isset( $_GET['user_id'])) {

								$value = get_user_meta( intval($_GET['user_id']), 'afreg_additional_' . intval($afreg_field->ID), true );   
							} else {
								$value = '';
							}

							if (!empty(get_post_meta( intval($afreg_field->ID), 'afreg_is_dependable', true ))) {

								$afreg_is_dependable = get_post_meta( intval($afreg_field->ID), 'afreg_is_dependable', true );
							} else {
								$afreg_is_dependable = 'off';
							}

							$afreg_field_user_roles = get_post_meta( $afreg_field->ID, 'afreg_field_user_roles', true );
							$field_roles            = maybe_unserialize($afreg_field_user_roles);


							if ('text' == $afreg_field_type || 'vat' == $afreg_field_type ) { 
								?>
								<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
									<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
										<?php 
										if (!empty($afreg_field->post_title)) {
											echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
										?>
										</label></th>
										<td>
											<input type="text" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
											<br>
											<span class="description"></span>
											<?php if (!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
											<?php } ?>
										</td>
									</tr>
								<?php } elseif ( 'textarea' == $afreg_field_type) { ?>

									<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
										<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
											<?php 
											if (!empty($afreg_field->post_title)) {
												echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
											?>
											</label></th>
											<td>
												<textarea class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php echo esc_attr($value); ?></textarea>
												<br>
												<span class="description"></span>
												<?php if (!empty($afreg_field_description)) { ?>
													<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
												<?php } ?>
											</td>
										</tr>

									<?php } elseif ( 'email' == $afreg_field_type) { ?>

										<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
											<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
												<?php 
												if (!empty($afreg_field->post_title)) {
													echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
												?>
												</label></th>
												<td>
													<input type="email" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
													<br>
													<span class="description"></span>
													<?php if (!empty($afreg_field_description)) { ?>
														<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
													<?php } ?>
												</td>
											</tr>

										<?php } elseif ( 'select' == $afreg_field_type) { ?>

											<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
												<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
													<?php 
													if (!empty($afreg_field->post_title)) {
														echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
													?>
													</label></th>
													<td>
														<select class="input-select " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
															<?php foreach ($afreg_field_options as $afreg_field_option) { ?>
																<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php echo selected(esc_attr($value), esc_attr($afreg_field_option['field_value'])); ?>>
																	<?php 
																	if (!empty($afreg_field_option['field_text'])) {
																		echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_b2b');} 
																	?>
																	</option>
																<?php } ?>
															</select>
															<br>
															<span class="description"></span>
															<?php if (!empty($afreg_field_description)) { ?>
																<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
															<?php } ?>
														</td>
													</tr>

												<?php } elseif ( 'multiselect' == $afreg_field_type) { ?>

													<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
														<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
															<?php 
															if (!empty($afreg_field->post_title)) {
																echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
															?>
															</label></th>
															<td>
																<select class="input-select " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>[]" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" multiple>
																	<?php 
																	foreach ($afreg_field_options as $afreg_field_option) {

																		$db_values = explode(', ', $value);

																		if (!empty($db_values)) { 
																			?>
																			<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" 
																				<?php 
																				if (in_array(esc_attr($afreg_field_option['field_value']), $db_values)) {
																					echo 'selected';} 
																				?>
																					>
																					<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_b2b'); ?>
																				<?php } else { ?>
																					<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>">
																						<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_b2b'); ?>
																					</option>
																				<?php } } ?>
																			</select>
																			<br>
																			<span class="description"></span>
																			<?php if (!empty($afreg_field_description)) { ?>
																				<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
																			<?php } ?>
																		</td>
																	</tr>

																<?php } elseif ( 'multi_checkbox' == $afreg_field_type) { ?>

																	<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
																		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																			<?php 
																			if (!empty($afreg_field->post_title)) {
																				echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
																			?>
																			</label></th>
																			<td>
																				<?php 
																				foreach ($afreg_field_options as $afreg_field_option) {
																					$db_values = explode(', ', $value);
																					?>
																					<input type="checkbox" class="input-checkbox " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>[]" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($afreg_field_option['field_value']); ?>"
																					<?php
																					if (in_array(esc_attr($afreg_field_option['field_value']), $db_values)) {
																						echo 'checked';
																					}
																					?>
																					/>
																					<span class="afreg_radio">
																						<?php 
																						if (!empty($afreg_field_option['field_text'])) {
																							echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_b2b');} 
																						?>
																						</span>
																					<?php } ?>
																					<br>
																					<span class="description"></span>
																					<?php if (!empty($afreg_field_description)) { ?>
																						<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
																					<?php } ?>
																				</td>
																			</tr>

																		<?php } elseif ( 'checkbox' == $afreg_field_type) { ?>

																			<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
																				<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																					<?php 
																					if (!empty($afreg_field->post_title)) {
																						echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
																					?>
																					</label></th>
																					<td>
																						<input type="checkbox" class="input-checkbox " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="yes" <?php echo checked('yes', esc_attr($value)); ?>  />
																						<br>
																						<span class="description"></span>
																						<?php if (!empty($afreg_field_description)) { ?>
																							<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
																						<?php } ?>
																					</td>
																				</tr>

																			<?php } elseif ( 'radio' == $afreg_field_type) { ?>

																				<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
																					<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																						<?php 
																						if (!empty($afreg_field->post_title)) {
																							echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
																						?>
																						</label></th>
																						<td>
																							<?php foreach ($afreg_field_options as $afreg_field_option) { ?>
																								<input type="radio" class="input-radio " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php echo checked(esc_attr($value), esc_attr($afreg_field_option['field_value'])); ?>  />
																								<span class="afreg_radio">
																									<?php 
																									if (!empty($afreg_field_option['field_text'])) {
																										echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_b2b');} 
																									?>
																									</span>
																								<?php } ?>
																								<br>
																								<span class="description"></span>
																								<?php if (!empty($afreg_field_description)) { ?>
																									<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
																								<?php } ?>
																							</td>
																						</tr>

																					<?php } elseif ( 'number' == $afreg_field_type) { ?>

																						<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
																							<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																								<?php 
																								if (!empty($afreg_field->post_title)) {
																									echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
																								?>
																								</label></th>
																								<td>
																									<input type="number" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																									<br>
																									<span class="description"></span>
																									<?php if (!empty($afreg_field_description)) { ?>
																										<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
																									<?php } ?>
																								</td>
																							</tr>

																						<?php } elseif ( 'password' == $afreg_field_type) { ?>

																							<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
																								<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																									<?php 
																									if (!empty($afreg_field->post_title)) {
																										echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
																									?>
																									</label></th>
																									<td>
																										<input type="password" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																										<br>
																										<span class="description"></span>
																										<?php if (!empty($afreg_field_description)) { ?>
																											<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
																										<?php } ?>
																									</td>
																								</tr>

																							<?php } elseif ( 'fileupload' == $afreg_field_type) { ?>

																								<tr class="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
																									<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php echo esc_html__('Current', 'addify_b2b'); ?> <?php 
																									if (!empty($afreg_field->post_title)) {
																										echo esc_html__($afreg_field->post_title , 'addify_b2b' );
																									} 
																									?>
																								</label></th>

																								<td>
																									<?php 


																									$upload_url = wp_upload_dir();

																									$current_file = '';

																									$curr_image_new_folder = $upload_url['basedir'] . '/addify_registration_uploads/' . $value;

																									$curr_image = esc_url(AFREG_URL . 'uploaded_files/' . $value);

																									if (file_exists($curr_image_new_folder)) {

																										$current_file = esc_url($upload_url['baseurl'] . '/addify_registration_uploads/' . $value);

																									} elseif (file_exists($curr_image)) {

																										$current_file = esc_url(AFREG_URL . 'uploaded_files/' . $value);

																									}


																									if (!empty($value)) {
																										$ext = pathinfo($current_file, PATHINFO_EXTENSION);
																										if ( 'jpg' == $ext || 'JPG' == $ext || 'jpeg' == $ext || 'JPEG' == $ext || 'png' == $ext || 'PNG' == $ext || 'gif' == $ext || 'GIF' == $ext || 'bmp' == $ext || 'BMP' == $ext) { 
																											?>
																											<a href="<?php echo esc_url($current_file); ?>" target="_blank">
																												<img src="<?php echo esc_url($current_file); ?>" width="150" height="150" />

																											</a>
																										<?php } else { ?>

																											<a href="<?php echo esc_url($current_file); ?>" target="_blank">
																												<img src="<?php echo esc_url(AFREG_URL); ?>images/file_icon.png" width="150" height="150" title="Click to View" />
																											</a>

																										<?php } } ?>
																									</td>


																								</tr>

																								<tr class="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
																									<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																										<?php 
																										if (!empty($afreg_field->post_title)) {
																											echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
																										?>
																										</label></th>
																										<td>
																											<input type="file" class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="" placeholder="
																											<?php 
																											if (!empty($afreg_field_placeholder)) {
																												echo esc_html__($afreg_field_placeholder , 'addify_b2b' );} 
																											?>
																												" />
																												<br>
																												<span class="description"></span>
																												<?php if (!empty($afreg_field_description)) { ?>
																													<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
																												<?php } ?>
																											</td>
																										</tr>

																									<?php } elseif ( 'color' == $afreg_field_type) { ?>

																										<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
																											<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																												<?php 
																												if (!empty($afreg_field->post_title)) {
																													echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
																												?>
																												</label></th>
																												<td>
																													<input type="color" class="input-text color_sepctrumm" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($value); ?>" placeholder="
																													<?php 
																													if (!empty($afreg_field_placeholder)) {
																														echo esc_html__($afreg_field_placeholder , 'addify_b2b' );} 
																													?>
																														" />
																														<br>
																														<span class="description"></span>
																														<?php if (!empty($afreg_field_description)) { ?>
																															<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
																														<?php } ?>

																														<script>

																															jQuery(".color_sepctrumm").spectrum({
																																color: "<?php echo esc_attr($value); ?>",
																																preferredFormat: "hex",
																															});

																														</script>
																													</td>
																												</tr>

																											<?php } elseif ( 'datepicker' == $afreg_field_type) { ?>

																												<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
																													<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																														<?php 
																														if (!empty($afreg_field->post_title)) {
																															echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
																														?>
																														</label></th>
																														<td>
																															<input type="date" class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($value); ?>" placeholder="
																															<?php 
																															if (!empty($afreg_field_placeholder)) {
																																echo esc_html__($afreg_field_placeholder , 'addify_b2b' );} 
																															?>
																																" />
																																<br>
																																<span class="description"></span>
																																<?php if (!empty($afreg_field_description)) { ?>
																																	<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
																																<?php } ?>
																															</td>
																														</tr>

																													<?php } elseif ( 'timepicker' == $afreg_field_type) { ?>

																														<tr id="afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>">
																															<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
																																<?php 
																																if (!empty($afreg_field->post_title)) {
																																	echo esc_html__($afreg_field->post_title , 'addify_b2b' );} 
																																?>
																																</label></th>
																																<td>
																																	<input type="time" class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($value); ?>" placeholder="
																																	<?php 
																																	if (!empty($afreg_field_placeholder)) {
																																		echo esc_html__($afreg_field_placeholder , 'addify_b2b' );} 
																																	?>
																																		" />
																																		<br>
																																		<span class="description"></span>
																																		<?php if (!empty($afreg_field_description)) { ?>
																																			<span class="description"><?php echo wp_kses_post($afreg_field_description, 'addify_b2b'); ?></span>
																																		<?php } ?>
																																	</td>
																																</tr>

																																<?php 
																													}
																													?>


																															<!-- Dependable -->
																															<?php if ('on' == $afreg_is_dependable && !empty($field_roles)) { ?>

																																<style>
																																	#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?> { display: none; }
																																	.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?> { display: none; }
																																</style>

																															<?php } ?>

																															<script>

																																jQuery(document).ready(function() {

																																	var val = jQuery('#role option:selected').val();
																																	var field_roles = new Array();
																																	var is_dependable = '<?php echo esc_attr($afreg_is_dependable); ?>';

																																	<?php if ( !empty($field_roles)) { ?>
																																		<?php foreach ($field_roles as $key => $value) { ?>

																																			field_roles.push('<?php echo esc_attr($value); ?>');

																																		<?php } ?>

																																		var match_val = field_roles.includes(val);

																																		if (match_val == true && is_dependable == 'on') {


																																			jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();
																																			jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();

																																		} else if (match_val == false && is_dependable == 'on') {

																																			jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').hide();
																																			jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').hide();
																																		} else {

																																			jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();
																																			jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();

																																		}

																																	<?php } ?>

																																});

																																jQuery(document).on('change', '#role', function() {

																																	var val = this.value;
																																	var field_roles = new Array();
																																	var is_dependable = '<?php echo esc_attr($afreg_is_dependable); ?>';

																																	<?php if ( !empty($field_roles)) { ?>
																																		<?php foreach ($field_roles as $key => $value) { ?>

																																			field_roles.push('<?php echo esc_attr($value); ?>');

																																		<?php } ?>

																																		var match_val = field_roles.includes(val);

																																		if (match_val == true && is_dependable == 'on') {


																																			jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();
																																			jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();


																																		} else if (match_val == false && is_dependable == 'on') {

																																			jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').hide();
																																			jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').hide();

																																		} else {

																																			jQuery('#afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();
																																			jQuery('.afreg_additionalshowhide_<?php echo intval($afreg_field->ID); ?>').show();


																																		}

																																	<?php } ?>


																																});

																															</script>

																															<?php 
						}
					}

					?>

																												</table>
																											</div>
																											<?php 
		}

		public function afreg_update_profile_fields( $customer_id ) {


			if ( empty( $_POST['afreg_nonce_field'] ) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['afreg_nonce_field'])), 'afreg_nonce_action')) {
				wp_die( esc_html__('Security Violated', 'addify_b2b') );
			}

			$user_info         = get_userdata( $customer_id );
			$afreg_user_status = $user_info->afreg_new_user_status;

			if ( ! empty( $_POST['afreg_new_user_status'] ) && $afreg_user_status != $_POST['afreg_new_user_status'] ) {

				if ('approved' == $_POST['afreg_new_user_status']) {

					$userStatus = 'approved';
				} elseif ('disapproved' == $_POST['afreg_new_user_status']) {

					$userStatus = 'disapproved';
				} elseif ('approve_without_email' == $_POST['afreg_new_user_status']) {

					$userStatus = 'approved';

				}


				update_user_meta( $customer_id, 'afreg_new_user_status', esc_attr($userStatus));





				if ( 'approved' == $_POST['afreg_new_user_status'] ) {


					//Send Message to user that his/her account is approved. 

					wc()->mailer()->emails['afreg_approved_user_email_user']->trigger( $customer_id );

				}

				if ( 'disapproved' == $_POST['afreg_new_user_status'] ) {


					//Send Message to user that their account is disapproved.  

					wc()->mailer()->emails['afreg_disapproved_user_email_user']->trigger( $customer_id );

				}
			}




			$afreg_args = array( 
				'posts_per_page' => -1,
				'post_type'      => 'afreg_fields',
				'post_status'    => 'publish',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			);

			$afreg_extra_fields = get_posts($afreg_args);

			if (!empty($afreg_extra_fields)) {


				foreach ($afreg_extra_fields as $afreg_field) {

					$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );

					if ( isset( $_POST[ 'afreg_additional_' . intval($afreg_field->ID) ] ) || isset( $_FILES[ 'afreg_additional_' . intval($afreg_field->ID) ] ) ) {

						if ( 'fileupload' == $afreg_field_type) {

							$upload_url = wp_upload_dir();

							if (isset($_FILES[ 'afreg_additional_' . intval($afreg_field->ID) ]['name']) && '' != $_FILES[ 'afreg_additional_' . intval($afreg_field->ID) ]['name']) { 

								if ( isset( $_FILES[ 'afreg_additional_' . intval($afreg_field->ID) ]['name'])) {
									$file = time() . sanitize_text_field($_FILES[ 'afreg_additional_' . intval($afreg_field->ID) ]['name']);
								} else {
									$file = '';
								}

								$target_path = $upload_url['basedir'] . '/addify_registration_uploads/';
								$target_path = $target_path . $file;
								if ( isset( $_FILES[ 'afreg_additional_' . intval($afreg_field->ID) ]['tmp_name'])) {
									$temp = move_uploaded_file(sanitize_text_field($_FILES[ 'afreg_additional_' . intval($afreg_field->ID) ]['tmp_name']), $target_path);
								} else {
									$temp = '';
								}

								update_user_meta($customer_id, 'afreg_additional_' . intval($afreg_field->ID), $file);

							}

						} elseif ( 'multiselect' == $afreg_field_type) { 
							$prefix   = '';
							$multival = '';
							foreach (sanitize_meta('', $_POST[ 'afreg_additional_' . intval($afreg_field->ID) ], '') as $value) {
								$multival .= $prefix . $value;
								$prefix    = ', ';
							}
							update_user_meta( $customer_id, 'afreg_additional_' . intval($afreg_field->ID), sanitize_text_field($multival) );

						} elseif ( 'multi_checkbox' == $afreg_field_type) { 
							$prefix   = '';
							$multival = '';
							foreach (sanitize_meta('', $_POST[ 'afreg_additional_' . intval($afreg_field->ID) ], '') as $value) {
								$multival .= $prefix . $value;
								$prefix    = ', ';
							}
							update_user_meta( $customer_id, 'afreg_additional_' . intval($afreg_field->ID), sanitize_text_field($multival) );

						} else {

							update_user_meta( $customer_id, 'afreg_additional_' . intval($afreg_field->ID), sanitize_text_field($_POST[ 'afreg_additional_' . intval($afreg_field->ID) ]));
						}

					} else {

						update_user_meta( $customer_id, 'afreg_additional_' . intval($afreg_field->ID), '');
					}
				}

			}
		}

		public function afreg_modify_user_table( $column ) {

			if (!empty( get_option('afreg_enable_approve_user')) && 'yes' == get_option('afreg_enable_approve_user')) {

				$column['user_status'] = esc_html__( 'User Status', 'addify_b2b' );
			}


			return $column;
		}

		public function afreg_modify_user_table_row( $val, $column_name, $user_id ) {
			switch ($column_name) {
				case 'user_status':
				$user_status = get_user_meta($user_id, 'afreg_new_user_status', true);
					return ucfirst($user_status);
				default:
			}
			return $val;
		}

		public function afreg_user_row_actions( $actions, $user ) {

			if ( get_current_user_id() == $user->ID ) {
				return $actions;
			}

			if ( is_super_admin( $user->ID ) ) {
				return $actions;
			}

			$approve_action = '';
			$deny_action    = '';

			$user_status = get_user_meta( $user->ID, 'afreg_new_user_status', true);

			$approve_link = add_query_arg( array(
				'action' => 'approved',
				'user'   => $user->ID,
			) );
			$approve_link = remove_query_arg( array( 'new_role' ), $approve_link );
			$approve_link = wp_nonce_url( $approve_link, 'addify-afreg-fields' );

			$deny_link = add_query_arg( array(
				'action' => 'disapproved',
				'user'   => $user->ID,
			) );
			$deny_link = remove_query_arg( array( 'new_role' ), $deny_link );
			$deny_link = wp_nonce_url( $deny_link, 'addify-afreg-fields' );

			if (!empty( get_option('afreg_enable_approve_user')) && 'yes' == get_option('afreg_enable_approve_user')) {

				$approve_action = '<a href="' . esc_url( $approve_link ) . '">' . esc_html__( 'Approve', 'addify_b2b' ) . '</a>';
				$deny_action    = '<a href="' . esc_url( $deny_link ) . '">' . esc_html__( 'Disapprove', 'addify_b2b' ) . '</a>';

			}

			if ( 'pending' == $user_status ) {
				$actions[] = $approve_action;
				$actions[] = $deny_action;
			} elseif ( 'approved' == $user_status ) {
				$actions[] = $deny_action;
			} elseif ( 'disapproved' == $user_status ) {
				$actions[] = $approve_action;
			}

			return $actions;
		}

		public function afreg_update_action() {

			//Email link approval
			if ( isset( $_GET['action_email'] ) && in_array( $_GET['action_email'], array( 'approved', 'disapproved' ) ) && !isset( $_GET['new_role'] ) ) {

				$sendback = remove_query_arg( array( 'approved', 'disapproved', 'deleted', 'ids', 'afreg-status-query-submit', 'new_role' ), wp_get_referer() );
				if ( !$sendback ) {
					$sendback = admin_url( 'users.php' );
				}

				$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
				$pagenum       = $wp_list_table->get_pagenum();
				$sendback      = add_query_arg( 'paged', $pagenum, $sendback );

				$status = sanitize_key( $_GET['action_email'] );

				if ( isset( $_GET['user'])) {
					$user = absint( $_GET['user'] );
				} else {
					$user = 0;
				}


				update_user_meta( $user, 'afreg_new_user_status', $status);



				if ( 'approved' == $_GET['action_email'] ) {

					//Send Message to user that their account is approved.

					wc()->mailer()->emails['afreg_approved_user_email_user']->trigger( $user );



					$sendback = add_query_arg( array(
						'approved' => 1,
						'ids'      => $user,
					), $sendback );


					?>
																													<script>
																														window.location = '<?php echo esc_url($sendback); ?>';
																													</script>
																													<?php

				} elseif ('disapproved' == $_GET['action_email']) {

					//Send Message to user that their account is disapproved.

					wc()->mailer()->emails['afreg_disapproved_user_email_user']->trigger( $user );
					$sendback = add_query_arg( array(
						'approved' => 1,
						'ids'      => $user,
					), $sendback );


					?>
																													<script>
																														window.location = '<?php echo esc_url($sendback); ?>';
																													</script>
																													<?php

				} 


			}


			if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'approved', 'disapproved' ) ) && !isset( $_GET['new_role'] ) ) {
				check_admin_referer( 'addify-afreg-fields' );

				$sendback = remove_query_arg( array( 'approved', 'disapproved', 'deleted', 'ids', 'afreg-status-query-submit', 'new_role' ), wp_get_referer() );
				if ( !$sendback ) {
					$sendback = admin_url( 'users.php' );
				}

				$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
				$pagenum       = $wp_list_table->get_pagenum();
				$sendback      = add_query_arg( 'paged', $pagenum, $sendback );

				$status = sanitize_key( $_GET['action'] );

				if ( isset( $_GET['user'])) {
					$user = absint( $_GET['user'] );
				} else {
					$user = 0;
				}


				update_user_meta( $user, 'afreg_new_user_status', $status);




				if ( 'approved' == $_GET['action'] ) {

					//Send Message to user that their account is approved.  
					wc()->mailer()->emails['afreg_approved_user_email_user']->trigger( $user );

					$sendback = add_query_arg( array(
						'approved' => 1,
						'ids'      => $user,
					), $sendback );




				} elseif ( 'disapproved' == $_GET['action'] ) {



					//Send Message to user that their account is disapproved.  
					wc()->mailer()->emails['afreg_disapproved_user_email_user']->trigger( $user );


					$sendback = add_query_arg( array(
						'disapproved' => 1,
						'ids'         => $user,
					), $sendback );

				}

				wp_safe_redirect( $sendback );
				exit;


			}
		}

		public function afreg_status_filter( $s_filter ) {

			$id = 'afreg_approve_new_user_filter-' . $s_filter;

			$f_button = submit_button( esc_html__( 'Filter', 'addify_b2b' ), 'button', 'afreg-status-query-submit', false, array( 'id' => 'afreg-status-query-submit' ) );
			$f_status = $this->changed_status();

			?>
																											<label class="screen-reader-text" for="<?php echo esc_attr($id); ?>"><?php echo esc_html__( 'View all users', 'addify_b2b' ); ?></label>
																											<select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" class="anusec">
																												<option value=""><?php echo esc_html__( 'View all users', 'addify_b2b' ); ?></option>
				<?php foreach ( $this->get_all_statuses() as $status ) { ?>
																													<option value="<?php echo esc_attr( $status ); ?>"<?php echo selected( $status, $f_status ); ?>>

																														<?php

																														if ( 'disapproved' == $status) {
																															echo esc_html__('Disapproved', 'addify_b2b');
																														} else {
																															echo esc_html__( ucfirst($status) );
																														}
																														?>

																													</option>
																												<?php } ?>
																											</select>
																											<?php echo esc_attr(apply_filters( 'afreg_approve_new_user_filter_button', $f_button )); ?>

																											<?php
		}

		public function changed_status() {
			if ( ! empty( $_REQUEST['afreg_approve_new_user_filter-top'] ) || ! empty( $_REQUEST['afreg_approve_new_user_filter-bottom'] ) ) {
				$aa =  esc_attr( ( ! empty( $_REQUEST['afreg_approve_new_user_filter-top'] ) ) ? sanitize_text_field($_REQUEST['afreg_approve_new_user_filter-top']) : sanitize_text_field($_REQUEST['afreg_approve_new_user_filter-bottom'] ));
			} else {
				$aa =  null;
			}
			return $aa;
		}

		public function get_all_statuses() {
			return array( 'pending', 'approved', 'disapproved' );
		}

		public function afreg_filter_user_by_status( $qry ) {

			global $wpdb;

			if ( !is_admin() ) {
				return;
			}


			if ( $this->changed_status() != null ) { 
				$filter = $this->changed_status();


				$qry->query_from .= " INNER JOIN {$wpdb->usermeta} ON ( {$wpdb->users}.ID = $wpdb->usermeta.user_id )";
				if ( 'approved' == $filter ) {
					$qry->query_fields = "DISTINCT SQL_CALC_FOUND_ROWS {$wpdb->users}.ID";
					$where             = $qry->query_from  .= " LEFT JOIN {$wpdb->usermeta} AS mt1 ON ({$wpdb->users}.ID = mt1.user_id AND mt1.meta_key = 'afreg_new_user_status')";

					$qry->query_where .= " AND ( ( $wpdb->usermeta.meta_key = 'afreg_new_user_status' AND CAST($wpdb->usermeta.meta_value AS CHAR) = 'approved' ) OR mt1.user_id IS NULL )";
				} else {
					$qry->query_where .= " AND ( ($wpdb->usermeta.meta_key = 'afreg_new_user_status' AND CAST($wpdb->usermeta.meta_value AS CHAR) = '{$filter}') )";
				}



			}
		}

		public function afreg_admin_footer() {
			$screen = get_current_screen();

			if ( 'users' == $screen->id ) { 
				if (!empty( get_option('afreg_enable_approve_user')) && 'yes' == get_option('afreg_enable_approve_user')) {
					?>
																													<script type="text/javascript">
																														jQuery(document).ready(function ($) {
																															$('<option>').val('approved').text('<?php echo esc_html__( 'Approve', 'addify_b2b' ); ?>').appendTo("select[name='action']");
																															$('<option>').val('approved').text('<?php echo esc_html__( 'Approve', 'addify_b2b' ); ?>').appendTo("select[name='action2']");

																															$('<option>').val('disapproved').text('<?php echo esc_html__( 'Disapprove', 'addify_b2b' ); ?>').appendTo("select[name='action']");
																															$('<option>').val('disapproved').text('<?php echo esc_html__( 'Disapprove', 'addify_b2b' ); ?>').appendTo("select[name='action2']");
																														});
																													</script>
																													<?php 
				}
			}
		}

		public function afreg_bulk_action_user() {
			$screen = get_current_screen();

			if ( 'users' == $screen->id ) {

				// get the action
				$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
				$action        = $wp_list_table->current_action();


				$allowed_actions = array( 'approved', 'disapproved' );
				if ( !in_array( $action, $allowed_actions ) ) {
					return;
				}




				// security check
				check_admin_referer( 'bulk-users' );

				// make sure ids are submitted
				if ( isset( $_REQUEST['users'] ) ) {
					$user_ids = array_map( 'intval', $_REQUEST['users'] );
				}

				if ( empty( $user_ids ) ) {
					return;
				}

				$sendback = remove_query_arg( array( 'approved', 'disapproved', 'deleted', 'ids', 'afreg_approve_new_user_filter', 'afreg_approve_new_user_filter2', 'afreg-status-query-submit', 'new_role' ), wp_get_referer() );
				if ( !$sendback ) {
					$sendback = admin_url( 'users.php' );
				}

				$pagenum  = $wp_list_table->get_pagenum();
				$sendback = add_query_arg( 'paged', $pagenum, $sendback );



				switch ( $action ) {
					case 'approved':
					$approved = 0;
						foreach ( $user_ids as $user_id ) {


							//Send Message to user that their account is approved.
																														wc()->mailer()->emails['afreg_approved_user_email_user']->trigger( $user_id );

																														update_user_meta( $user_id, 'afreg_new_user_status', 'approved');
																														++$approved;
						}

																													$sendback = add_query_arg( array(
																														'approved' => $approved,
																														'ids' => join( ',', $user_ids ),
																													), $sendback );
						break;

					case 'disapproved':
					$disapproved = 0;
						foreach ( $user_ids as $user_id ) {


							//Send Message to user that their account is disapproved.
																														wc()->mailer()->emails['afreg_disapproved_user_email_user']->trigger( $user_id );

																														update_user_meta( $user_id, 'afreg_new_user_status', 'disapproved');
																														++$disapproved;
						}

																													$sendback = add_query_arg( array(
																														'disapproved' => $disapproved,
																														'ids' => join( ',', $user_ids ),
																													), $sendback );
						break;

					default:
						return;
				}

				$sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ), $sendback );

				wp_safe_redirect( $sendback );
				exit();
			}
		}


		public function afreg_default_fields() {

			require AFREG_PLUGIN_DIR . 'admin/afreg_def_admin.php';
		}

		public function afreg_save_df_form() {



			if ( empty( $_POST['nonce'] ) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'afreg-ajax-nonce')) {
				wp_die( esc_html__('Security Violated', 'addify_b2b') );
			}


			if (isset($_POST['post_ids']) && '' != $_POST['post_ids']) {
				$post_ids = sanitize_meta('', $_POST['post_ids'], '');          
			} else {
				$post_ids = array(); }

			if (isset($_POST['field_label']) && '' != $_POST['field_label']) {
																											$field_label = sanitize_meta('', $_POST['field_label'], '');            
			} else {
																												$field_label = array(); }

			if (isset($_POST['field_placeholder']) && '' != $_POST['field_placeholder']) {
				$field_placeholder = sanitize_meta('', $_POST['field_placeholder'], '');            
			} else {
				$field_placeholder = array(); }

			if (isset($_POST['field_required']) && '' != $_POST['field_required']) {
				$field_required = sanitize_meta('', $_POST['field_required'], '');          
			} else {
				$field_required = array(); }

			if (isset($_POST['field_width']) && '' != $_POST['field_width']) {
				$field_width = sanitize_meta('', $_POST['field_width'], '');            
			} else {
				$field_width = array(); }

			if (isset($_POST['field_message']) && '' != $_POST['field_message']) {
				$field_message = sanitize_meta('', $_POST['field_message'], '');            
			} else {
				$field_message = array(); }

			if (isset($_POST['field_status']) && '' != $_POST['field_status']) {
				$field_status = sanitize_meta('', $_POST['field_status'], '');          
			} else {
				$field_status = array(); }

			if (isset($_POST['field_sort_order']) && '' != $_POST['field_sort_order']) {
				$field_sort_order = sanitize_meta('', $_POST['field_sort_order'], '');          
			} else {
				$field_sort_order = array(); }

																																			$full_array = array_map(function ( $a, $b, $c, $d, $e, $f, $g, $h ) { 
																																																				return $a . '-:-' . $b . '-:-' . $c . '-:-' . $d . '-:-' . $e . '-:-' . $f . '-:-' . $g . '-:-' . $h; 
																																			}, $post_ids, $field_label, $field_placeholder, $field_required, $field_width, $field_message, $field_status, $field_sort_order);

			if ('' != $full_array) {
				foreach ($full_array as $data) {

$value         = explode('-:-', $data);
$p_id          = intval($value[0]);
$f_label       = sanitize_text_field($value[1]);
$f_placeholder = sanitize_text_field($value[2]);
$f_required    = sanitize_text_field($value[3]);
$f_width       = sanitize_text_field($value[4]);
$f_message     = sanitize_text_field($value[5]);
$f_status      = sanitize_text_field($value[6]);
$f_sort_order  = sanitize_text_field($value[7]);



$af_post = array(
	'ID'          => $p_id,
	'post_title'  => $f_label,
	'post_status' => $f_status,
	'menu_order'  => $f_sort_order,
);

// Update the post and post meta into the database
wp_update_post( $af_post );

update_post_meta( $p_id, 'placeholder', $f_placeholder );
update_post_meta( $p_id, 'is_required', $f_required );
update_post_meta( $p_id, 'width', $f_width );
update_post_meta( $p_id, 'message', $f_message );

				}
			}

											echo 'success';

											die();
		}
	}

																																	new Addify_Registration_Fields_Addon_Admin();
}
