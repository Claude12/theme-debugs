<?php
if (!defined('WPINC')) {
	die;
}

if (!class_exists('Addify_Tax_Exempt_Admin')) {

	class Addify_Tax_Exempt_Admin extends Addify_Tax_Exempt {
	



		public function __construct() {

			add_action('admin_enqueue_scripts', array( $this, 'aftax_admin_assets' ));

			add_action('admin_init', array( $this, 'aftax_add_setting_files' ), 10);

			add_filter('manage_users_columns', array( $this, 'aftax_new_modify_user_table' ));
			add_filter('manage_users_custom_column', array( $this, 'aftax_new_modify_user_table_row' ), 10, 3);
			add_action('show_user_profile', array( $this, 'aftax_show_status_field_profile' ));
			add_action('edit_user_profile', array( $this, 'aftax_show_status_field_profile' ));
			add_action('personal_options_update', array( $this, 'aftax_save_status_field_profile' ));
			add_action('edit_user_profile_update', array( $this, 'aftax_save_status_field_profile' ));
			add_action('woocommerce_admin_order_data_after_order_details', array( $this, 'aftax_display_order_data_in_admin' ));

			add_action('wp_ajax_aftaxsearchUsers', array( $this, 'aftaxsearchUsers' ));
		}

		public function aftax_admin_assets() {

			$screen = get_current_screen();



			if ('toplevel_page_addify-b2b' == $screen->id) {
				wp_enqueue_style('thickbox');
				wp_enqueue_script('thickbox');
				wp_enqueue_script('media-upload');

				wp_enqueue_media();


				wp_enqueue_style('select2-css', plugins_url('assets/css/select2.css', WC_PLUGIN_FILE), array(), '1.0.0');

				wp_enqueue_script('select2-js', plugins_url('assets/js/select2/select2.min.js', WC_PLUGIN_FILE), array( 'jquery' ), '1.0.0', true);
			}

			if ('toplevel_page_addify-b2b' == $screen->id || 'user-edit' == $screen->id || 'users' == $screen->id) {

				wp_enqueue_style('aftax_adminc', plugins_url('/assets/css/aftax_admin.css', __FILE__), array(), '1.1.0');
				wp_enqueue_script('aftax-adminj', plugins_url('/assets/js/aftax_admin.js', __FILE__), array(), '1.1.0');
				$aftax_data = array(
					'admin_url' => admin_url('admin-ajax.php'),
					'nonce'     => wp_create_nonce('aftax-ajax-nonce'),

				);
				wp_localize_script('aftax-adminj', 'aftax_php_vars', $aftax_data);
			}
		}


		public function aftax_add_setting_files() {

			if (isset($_GET['download']) && isset($_GET['user_id'])) {

				$user_info              = get_userdata(intval($_GET['user_id']));
				$aftax_fileupload_field = $user_info->aftax_fileupload_field;
				$file_path              = AFTAX_MEDIA_PATH . $aftax_fileupload_field;
				af_te_download_file($file_path, $aftax_fileupload_field);

			}


			if (isset($_GET['download']) && isset($_GET['order_id'])) {

				$order                  = wc_get_order(intval($_GET['order_id']));
				$aftax_fileupload_field = $order->get_meta('aftax_fileupload_field', true);

				$file_path = AFTAX_MEDIA_PATH . $aftax_fileupload_field;
				af_te_download_file($file_path, $aftax_fileupload_field);
			}
		}




		public function aftax_new_modify_user_table( $column ) {

			$column['tax_exemption_status'] = esc_html__('Tax Exemption Status', 'addify_b2b');
			return $column;
		}

		public function aftax_new_modify_user_table_row( $val, $column_name, $user_id ) {
			$tax_status            = get_the_author_meta('aftax_tax_exemption_status', $user_id);
			$user_info             = get_userdata($user_id);
			$aftax_tax_expire_date = $user_info->aftax_tax_expire_date;
			$current_date          = gmdate('Y-m-d');


			$tax_status = $user_info->aftax_tax_exemption_status;


			switch ($column_name) {
				case 'tax_exemption_status':
					if (!empty(get_the_author_meta('aftax_tax_exemption_status', $user_id))) {
						if ('pending' == $tax_status) {
							return "<span class='aftax_pending'>" . esc_attr($tax_status) . '</span>';
						} elseif ('approved' == $tax_status) {
							return "<span class='aftax_approved'>" . esc_attr($tax_status) . '</span>';
						} elseif ('disapproved' == $tax_status) {
							return "<span class='aftax_disapproved'>" . esc_attr($tax_status) . '</span>';
						} elseif ('expired' == $tax_status) {
							return "<span class='aftax_expired'>" . esc_attr($tax_status) . '</span>';
						}
					}

				// no break
				default:
			}
			return $val;
		}

		public function aftax_show_status_field_profile( $user ) {
			wp_nonce_field('aftax_nonce_action', 'aftax_nonce_field');
			$user_info                  = get_userdata($user->ID);
			$user_id                    = $user->ID;
			$aftax_text_field           = $user_info->aftax_text_field;
			$aftax_textarea_field       = $user_info->aftax_textarea_field;
			$aftax_fileupload_field     = $user_info->aftax_fileupload_field;
			$aftax_tax_exemption_status = $user_info->aftax_tax_exemption_status;
			$aftax_tax_expire_date      = $user_info->aftax_tax_expire_date;
			wc()->mailer();

			// fields
			$text_enable       = (array) maybe_unserialize(get_option('aftax_enable_text_field'));
			$textarea_enable   = (array) maybe_unserialize(get_option('aftax_enable_textarea_field'));
			$fileupload_enable = (array) maybe_unserialize(get_option('aftax_enable_fileupload_field'));

			if (isset($_GET['action']) && ( 'pending' == $user_info->aftax_tax_exemption_status || empty($user_info->aftax_tax_exemption_status) )) {
				update_metadata('user', $user->ID, 'aftax_tax_exemption_status', sanitize_text_field($_GET['action']), '');

				if ('approved' == $_GET['action']) {

					//Approved Email to admin
					do_action('aftax_approve_info_notification_admin', $user->ID, $aftax_text_field, $aftax_textarea_field, $aftax_fileupload_field);
					//Approved email to user
					do_action('aftax_approve_info_notification_user', $user->ID, $aftax_text_field, $aftax_textarea_field, $aftax_fileupload_field);

				} elseif ('disapproved' == $_GET['action']) {

					//Disapproved email to user
					do_action('aftax_disapprove_info_notification', $user->ID, $aftax_text_field, $aftax_textarea_field, $aftax_fileupload_field);
				}

				update_metadata('user', $user->ID, 'aftax_tax_info_expire_email', '', '');

			}

			?>
			<h2><?php echo esc_html__('Tax Exempt', 'addify_b2b'); ?></h2>
			<table class="form-table">

				<?php if (in_array('enable', $text_enable)) { ?>

					<tr>
						<th><label
								for="anu_additional_info"><?php echo esc_html__(get_option('aftax_text_field_label'), 'addify_b2b'); ?></label>
						</th>
						<td>

							<input type="text" name="aftax_text_field" id="aftax_text_field" class="regular-text"
								value="<?php echo esc_attr($aftax_text_field); ?>">


						</td>
					</tr>

				<?php } ?>

				<?php if (in_array('enable', $textarea_enable)) { ?>

					<tr>
						<th><label
								for="anu_additional_info"><?php echo esc_html__(get_option('aftax_textarea_field_label'), 'addify_b2b'); ?></label>
						</th>
						<td>
							<textarea name="aftax_textarea_field" id="aftax_textarea_field" class="input-text" cols="5"
								rows="5"><?php echo esc_attr($aftax_textarea_field); ?></textarea>
						</td>
					</tr>

				<?php } ?>

				<?php
				if (class_exists('WC_Taxjar')) {
					$aftax_exemption_type_options = apply_filters(
						'aftax_exemption_type_options',
						array(
							'wholesale'   => __('Wholesale', 'addify_b2b'),
							'government'  => __('Government', 'addify_b2b'),
							'marketplace' => __('Marketplace', 'addify_b2b'),
							'other'       => __('Other', 'addify_b2b'),
							'non_exempt'  => __('Non Exempt', 'addify_b2b'),
						),
						$user_id
					);

					$aftax_exemption_type = get_user_meta($user->ID, 'tax_exemption_type', true);
					?>
					<tr>
						<th><label for="aftax_first_field"><b><?php echo esc_html__('Tax Exempt Type', 'addify_b2b'); ?></b>

							</label>
						</th>
						<td>
							<select id="aftax_exemption_type" name="tax_exemption_type" class="aftax_exemption_type" required>
								<option value=""><?php echo esc_html__('Select Tax Exempt Type', 'addify_b2b'); ?></option>
								<?php

								foreach ($aftax_exemption_type_options as $key => $value) {
									?>
									<option value="<?php echo esc_attr($key); ?>" <?php echo selected($key, $aftax_exemption_type); ?>>
										<?php echo esc_attr($value); ?>
									</option>
									<?php
								}
								?>
							</select>
						</td>

						<?php
				}
				?>

					<?php if (in_array('enable', $fileupload_enable)) { ?>

					<tr>
						<th><label
								for="anu_additional_info"><?php echo esc_html__(get_option('aftax_fileupload_field_label'), 'addify_b2b'); ?></label>
						</th>
						<td>
							<input type="file" name="aftax_fileupload_field" id="aftax_fileupload_field">
							<small><?php echo esc_html__('Allowed file types:', 'addify_b2b') . ' ' . esc_attr(get_option('aftax_allowed_file_types')); ?></small>
							<input type="hidden" name="aftax_current_file" id="aftax_current_file"
								value="<?php echo esc_attr($aftax_fileupload_field); ?>">
						</td>
					</tr>

				<?php } ?>

				<?php if (!empty($aftax_fileupload_field)) { ?>

					<tr>
						<th>
							<label
								for="anu_additional_info"><?php echo esc_html__(get_option('aftax_fileupload_field_label') . ' Link', 'addify_b2b'); ?></label>
						</th>
						<td>
							<span>
								<a target="_blank"
									href="<?php echo esc_url(admin_url()); ?>profile.php?download&user_id=<?php echo intval($user->ID); ?>"><?php echo esc_html__('Click here to download', 'addify_b2b'); ?></a>
							</span>
						</td>
					</tr>

				<?php } ?>

				<tr>
					<th><label for="anu_new_user_status"><?php echo esc_html__('Tax Exemption Status', 'addify_b2b'); ?></label>
					</th>
					<td>
						<select id="aftax_tax_exemption_status" name="aftax_tax_exemption_status">
							<option value=""><?php echo esc_html__('Select Tax Exemption Status', 'addify_b2b'); ?></option>
							<option value="approved" <?php echo selected('approved', esc_attr($aftax_tax_exemption_status)); ?>>
								<?php echo esc_html__('Approved', 'addify_b2b'); ?>
							</option>
							<option value="disapproved" <?php echo selected('disapproved', esc_attr($aftax_tax_exemption_status)); ?>><?php echo esc_html__('Disapproved', 'addify_b2b'); ?></option>
							<option value="expired" <?php echo selected('expired', esc_attr($aftax_tax_exemption_status)); ?>>
								<?php echo esc_html__('Expired', 'addify_b2b'); ?>
							</option>
						</select>

					</td>
				</tr>

				<tr>
					<th><label
							for="anu_new_user_status"><?php echo esc_html__('Tax Exemption Expire Date', 'addify_b2b'); ?></label>
					</th>
					<td>
						<input type="date" name="aftax_tax_expire_date" id="aftax_tax_expire_date"
							value="<?php echo esc_attr($aftax_tax_expire_date); ?>" />
					</td>
				</tr>

			</table>
			<?php
		}

		public function aftax_save_status_field_profile( $user_id ) {

			if (!current_user_can('edit_user', $user_id)) {
				return false;
			}

			$retrieved_nonce = !empty($_REQUEST['aftax_nonce_field']) ? sanitize_text_field($_REQUEST['aftax_nonce_field']) : 0;

			if (!wp_verify_nonce($retrieved_nonce, 'aftax_nonce_action')) {

				die(esc_html__('Failed security check', 'addify_b2b'));
			}

			if (isset($_POST['aftax_text_field']) && '' != $_POST['aftax_text_field']) {

				update_metadata('user', $user_id, 'aftax_text_field', sanitize_text_field($_POST['aftax_text_field']), '');
				$aftax_field = sanitize_text_field($_POST['aftax_text_field']);
			}

			if (isset($_POST['aftax_textarea_field']) && '' != $_POST['aftax_textarea_field']) {

				update_metadata('user', $user_id, 'aftax_textarea_field', sanitize_text_field($_POST['aftax_textarea_field']), '');
				$aftax_taxarea_field = sanitize_text_field($_POST['aftax_textarea_field']);

			}
			if (isset($_FILES['aftax_fileupload_field']['name']) && '' != $_FILES['aftax_fileupload_field']['name']) {
				if (!empty($_FILES['aftax_fileupload_field']['name'])) {
					$ffname = sanitize_text_field($_FILES['aftax_fileupload_field']['name']);
				} else {
					$ffname = '';
				}

				if (!empty($_FILES['aftax_fileupload_field']['tmp_name'])) {
					$ftempname = sanitize_text_field($_FILES['aftax_fileupload_field']['tmp_name']);
				} else {
					$ftempname = '';
				}

				$file          = time() . $ffname;
				$target_path   = AFTAX_MEDIA_PATH . $file;
				$allowed_types = explode(',', esc_attr(get_option('aftax_allowed_file_types')));
				$file_type     = wp_check_filetype_and_ext($target_path, basename(sanitize_text_field($_FILES['aftax_fileupload_field']['name'])));

				$ext = isset($file_type['ext']) ? $file_type['ext'] : '';

				if (in_array($ext, $allowed_types)) {
					$temp = move_uploaded_file($ftempname, $target_path);

					update_metadata('user', $user_id, 'aftax_fileupload_field', $file, '');
					$aftax_file = $file;

				} else {

					$this->aftax_errors()->add('aftax_fileupload_field_error', esc_html__('This file type is not allowed.', 'addify_b2b'));

					return;
				}
			} else {

				if (!empty($_POST['aftax_current_file'])) {
					$curr_fiel = sanitize_text_field($_POST['aftax_current_file']);
				} else {
					$curr_fiel = '';
				}

				update_metadata('user', $user_id, 'aftax_fileupload_field', $curr_fiel, '');
				$aftax_file = $curr_fiel;
			}

			$user_info                  = get_userdata($user_id);
			$aftax_tax_exemption_status = $user_info->aftax_tax_exemption_status;

			if (!empty($_POST['aftax_tax_exemption_status']) && $aftax_tax_exemption_status != $_POST['aftax_tax_exemption_status']) {

				wc()->mailer();

				if ('approved' == $_POST['aftax_tax_exemption_status']) {


					// Send email to admin to inform that tax status is approved.
					do_action('aftax_approve_info_notification_admin', $user_id, $aftax_field, $aftax_taxarea_field, $aftax_file);
					// Send email to user to inform that tax status is approved.
					do_action('aftax_approve_info_notification_user', $user_id, $aftax_field, $aftax_taxarea_field, $aftax_file);
					update_metadata('user', $user_id, 'aftax_tax_info_expire_email', '', '');



				}

				if ('disapproved' == $_POST['aftax_tax_exemption_status']) {

					// Send email to user to inform that tax status is disapproved.
					do_action('aftax_disapprove_info_notification', $user_id, $aftax_field, $aftax_taxarea_field, $aftax_file);
					update_metadata('user', $user_id, 'aftax_tax_info_expire_email', '', '');

				}

				if ('expired' == $_POST['aftax_tax_exemption_status']) {

					//Email to admin
					do_action('aftax_expire_info_notification_admin', $user_id, $aftax_field, $aftax_taxarea_field, $aftax_file);

					//Email to user
					do_action('aftax_expire_info_notification_user', $user_id, $aftax_field, $aftax_taxarea_field, $aftax_file);

					update_metadata('user', $user_id, 'aftax_tax_info_expire_email', 'sent', '');
				}
			}

			if (!empty($_POST['aftax_tax_exemption_status'])) {
				update_metadata('user', $user_id, 'aftax_tax_exemption_status', sanitize_text_field($_POST['aftax_tax_exemption_status']), '');
			} else {
				update_metadata('user', $user_id, 'aftax_tax_exemption_status', '', '');
			}

			if (!empty($_POST['aftax_tax_expire_date'])) {
				update_metadata('user', $user_id, 'aftax_tax_expire_date', sanitize_text_field($_POST['aftax_tax_expire_date']), '');
			} else {
				update_metadata('user', $user_id, 'aftax_tax_expire_date', '', '');
			}
		}

		public function aftax_errors() {
			static $wp_error; // Will hold global variable safely

			$wp_error = new WP_Error(null, null, null);

			return $wp_error;
		}

		public function aftax_show_error_messages() {
			$codess = $this->aftax_errors();
			if (!empty($codess)) {

				$codes = $codess->get_error_codes();
			} else {
				$codes = '';
			}
			if (!empty($codes)) {
				// Loop error codes and display errors
				foreach ($codes as $code) {
					if ('aftax_success_m' == $code) {
						echo '<ul class="woocommerce-info">';
						$message = $this->aftax_errors()->get_error_message($code);
						echo '<li>' . esc_attr($message) . '</li>';
						echo '</ul>';

					} else {
						echo '<ul class="woocommerce-error">';
						$message = $this->aftax_errors()->get_error_message($code);
						echo '<li>' . esc_attr($message) . '</li>';
						echo '</ul>';

					}
				}
			}
		}

		public function aftax_display_order_data_in_admin( $order ) {
			?>
			<?php
			if ('Yes' == esc_attr($order->get_meta('tax_exemption_checkbox', true))) {
				$uploaded_file = $order->get_meta('aftax_fileupload_field', true);
				?>
				<div class="order_data_column">
					<h4><?php echo esc_html__('Tax Exempt', 'addify_b2b'); ?></h4>
					<div class="address aftax_order_details">
						<p>
							<strong><?php echo esc_html__('Is Tax Exempt?', 'addify_b2b'); ?></strong>
							<?php echo esc_html__('Yes ', 'addify_b2b'); ?>
						</p>
						<p>
							<strong><?php echo esc_html__(get_option('aftax_text_field_label'), 'addify_b2b'); ?></strong>
							<?php echo esc_attr($order->get_meta('aftax_text_field', true)); ?>
						</p>
						<p>
							<strong><?php echo esc_html__(get_option('aftax_textarea_field_label'), 'addify_b2b'); ?></strong>
							<?php echo esc_attr($order->get_meta('aftax_textarea_field', true)); ?>
						</p>
						<p>
							<strong><?php echo esc_html__(get_option('aftax_fileupload_field_label') . ' Link', 'addify_b2b'); ?></strong>
							<?php
							if (!empty($uploaded_file)) {
								?>
								<a
									href="<?php echo esc_url(admin_url()); ?>/admin.php?download&page=wc-orders&action=edit&order_id=<?php echo intval($order->get_id()); ?>"><?php echo esc_html__('Click here to download', 'addify_b2b'); ?></a>
								<?php
							} else {
								?>
							<p><?php echo esc_html__('No file has been uploaded', 'addify_b2b'); ?></p>
							<?php
							}
							?>
						</p>
					</div>

				</div>
				<?php
			}
		}


		public function aftaxsearchUsers() {

			$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

			if (!wp_verify_nonce($nonce, 'aftax-ajax-nonce')) {
				die('Failed ajax security check!');
			}

			$search = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : 0;

			$data_array  = array();
			$users       = new WP_User_Query(
				array(
					'search'         => '*' . esc_attr($search) . '*',
					'search_columns' => array(
						'user_login',
						'user_nicename',
						'user_email',
						'user_url',
					),
				)
			);
			$users_found = $users->get_results();

			if (!empty($users_found)) {

				foreach ($users_found as $user) {

					$title        = $user->display_name . '(' . $user->user_email . ')';
					$data_array[] = array( $user->ID, $title ); // array( User ID, User name and email )
				}
			}

			echo wp_json_encode($data_array);

			die();
		}
	}

	new Addify_Tax_Exempt_Admin();
}
