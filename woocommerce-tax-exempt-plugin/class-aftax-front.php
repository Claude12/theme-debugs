<?php
if (!defined('WPINC')) {
	die;
}

if (!class_exists('Addify_Tax_Exempt_Front')) {

	class Addify_Tax_Exempt_Front {
	


		public function __construct() {

			$custom_plugins = get_option('active_plugins');

			add_action('wp_enqueue_scripts', array( $this, 'aftax_front_script' ));
			add_action('woocommerce_after_order_notes', array( $this, 'aftax_apply_tax' ), 10, 1);
			add_action('woocommerce_checkout_update_order_review', array( $this, 'taxexempt_checkout_update_order_review' ));
			add_action('init', array( $this, 'aftaxadd_endpoints' ), 0);
			add_filter('query_vars', array( $this, 'aftaxadd_query_vars' ), 0);
			add_action('woocommerce_before_account_navigation', array( $this, 'aftax_flush_rewrite_rule' ));
			add_filter('the_title', array( $this, 'aftaxendpoint_title' ), 10, 2);
			add_filter('woocommerce_account_menu_items', array( $this, 'aftaxnew_menu_items' ));
			add_action('woocommerce_account_tax-exempt_endpoint', array( $this, 'aftaxendpoint_content' ));

			add_action('woocommerce_init', array( $this, 'aftax_save_tax_info' ));

			add_filter('woocommerce_init', array( $this, 'aftax_auto_exemption' ), PHP_INT_MAX, 1);

			add_action('woocommerce_checkout_update_order_meta', array( $this, 'aftax_save_extra_checkout_fields' ), 10, 2);
			add_action('woocommerce_thankyou', array( $this, 'aftax_display_order_data' ), 20);
			add_action('woocommerce_view_order', array( $this, 'aftax_display_order_data' ), 20);
			add_filter('woocommerce_email_order_meta_fields', array( $this, 'aftax_email_order_meta_fields' ), 10, 3);

			// Yith Custom My Account Page Compatibility.
			add_filter('yith_wcmap_after_endpoints_items', array( $this, 'yith_myaccount_menu_callback' ));

			//Ava Tax Compatibility 
			if (in_array('woocommerce-avatax/woocommerce-avatax.php', $custom_plugins)) {

				add_filter('pre_option_wc_avatax_enable_tax_calculation', array( $this, 'aftax_wc_diff_rate_for_user' ), PHP_INT_MAX, 2);
				add_filter('woocommerce_before_shipping_calculator', array( $this, 'aftax_wc_diff_rate_for_user' ), PHP_INT_MAX, 2);
				add_filter('woocommerce_before_checkout_billing_form', array( $this, 'aftax_wc_diff_rate_for_user' ), PHP_INT_MAX, 2);
				add_filter('woocommerce_product_get_tax_class', array( $this, 'aftax_wc_diff_rate_for_user' ), PHP_INT_MAX, 2);
				add_filter('woocommerce_product_variation_get_tax_class', array( $this, 'aftax_wc_diff_rate_for_user' ), PHP_INT_MAX, 2);
			}

			add_action('wp_loaded', array( $this, 'aftax_download' ));
		}



		public function aftax_download() {

			if (isset($_GET['download'])) {

				$user_id                = get_current_user_id();
				$user_info              = get_userdata($user_id);
				$aftax_fileupload_field = $user_info->aftax_fileupload_field;

				$file_path = AFTAX_MEDIA_PATH . $aftax_fileupload_field;

				af_te_download_file($file_path, $aftax_fileupload_field);

				die();
			}


			if (isset($_GET['download']) && isset($_GET['order_id'])) {

				$order                  = wc_get_order(intval($_GET['order_id']));
				$aftax_fileupload_field = $order->get_meta('aftax_fileupload_field', true);

				$file_path = AFTAX_MEDIA_PATH . $aftax_fileupload_field;
				af_te_download_file($file_path, $aftax_fileupload_field);

				die();
			}
		}

		public function aftax_flush_rewrite_rule() {
			flush_rewrite_rules();
		}
		public function ness() {

			echo "<li><a href=''><i class='fas fa-file-invoice'></i><span>i am here</span></a></li>";
		}


		public function aftax_auto_exemption() {

			global $woocommerce;

			if (!isset($woocommerce->customer) || empty($woocommerce->customer)) {
				return;
			}

			if ('yes' !== get_option('aftax_enable_auto_tax_exempttion')) {

				return;
			}

			if (!is_user_logged_in()) {

				$aftax_exempted_user_roles = (array) maybe_unserialize(get_option('aftax_exempted_user_roles'));

				if (in_array('guest', (array) $aftax_exempted_user_roles)) {

					$woocommerce->customer->set_is_vat_exempt(true);
					return;
				}

			} elseif (is_user_logged_in()) {

				$user                      = wp_get_current_user();
				$user_info                 = get_userdata($user->ID);
				$role                      = (array) $user->roles;
				$afuserroles               = (array) maybe_unserialize(get_option('aftax_requested_roles'));
				$afcustomers               = (array) maybe_unserialize(get_option('aftax_exempted_customers'));
				$aftax_exempted_user_roles = (array) maybe_unserialize(get_option('aftax_exempted_user_roles'));

				if (!empty($afcustomers)) {
					$exm_customers = $afcustomers;
				} else {
					$exm_customers = array();
				}

				if (!empty($aftax_exempted_user_roles)) {

					$exm_roles = $aftax_exempted_user_roles;

				} else {

					$exm_roles = array();

				}

				$aftax_tax_expire_date = get_user_meta($user->ID, 'aftax_tax_expire_date', true);
				$current_date          = gmdate('Y-m-d');

				if (!empty($aftax_tax_expire_date)) {

					$exp_date = $aftax_tax_expire_date;

				} else {

					$exp_date = '';
				}

				$aftax_tax_exemption_status = get_user_meta($user->ID, 'aftax_tax_exemption_status', true);

				if ('approved' == $aftax_tax_exemption_status) {

					if ($current_date <= $exp_date) {

						$woocommerce->customer->set_is_vat_exempt(true);

					} elseif (empty($aftax_tax_expire_date)) {

						$woocommerce->customer->set_is_vat_exempt(true);

					}

				} elseif (in_array($user->ID, $exm_customers) || in_array($role[0], $exm_roles)) {

					$woocommerce->customer->set_is_vat_exempt(true);

				} else {

					$woocommerce->customer->set_is_vat_exempt(false);
				}
			}
		}

		public function aftax_save_tax_info() {

			if (!empty($_POST['save_tax'])) {

				if (!function_exists('wp_verify_nonce')) {
					include_once ABSPATH . 'wp-includes/pluggable.php';
				}

				$retrieved_nonce = !empty($_REQUEST['aftax_nonce_field']) ? sanitize_text_field($_REQUEST['aftax_nonce_field']) : '';

				if (!wp_verify_nonce($retrieved_nonce, 'aftax_nonce_action')) {
					die(esc_html__('Failed security check', 'addify_b2b'));
				}

				if (isset($_POST['action']) && 'SubmitTaxForm' == $_POST['action']) {
					$this->aftax_submit_tax_form();
				}
			}
		}

		public function aftax_front_script() {

			wp_enqueue_style('aftax-frontc', plugins_url('/assets/css/aftax_front.css', __FILE__), '', '1.0', false);
			wp_enqueue_script('jquery');
			wp_enqueue_script('aftax-frontj', plugins_url('/assets/js/aftax_front.js', __FILE__), '', '1.0', false);
		}

		public function aftax_apply_tax( $checkout ) {

			wp_nonce_field('aftax_nonce_action', 'aftax_nonce_field');


			if (is_user_logged_in()) {

				$user                      = wp_get_current_user();
				$user_info                 = get_userdata($user->ID);
				$role                      = (array) $user->roles;
				$afuserroles               = (array) maybe_unserialize(get_option('aftax_requested_roles'));
				$afcustomers               = (array) maybe_unserialize(get_option('aftax_exempted_customers'));
				$aftax_exempted_user_roles = (array) maybe_unserialize(get_option('aftax_exempted_user_roles'));
				if (!empty($afcustomers)) {
					$exm_customers = $afcustomers;
				} else {
					$exm_customers = array();
				}

				if (!empty($aftax_exempted_user_roles)) {
					$exm_roles = $aftax_exempted_user_roles;
				} else {
					$exm_roles = array();
				}
				$aftax_tax_expire_date = get_user_meta($user->ID, 'aftax_tax_expire_date', true);
				$current_date          = gmdate('Y-m-d');

				if (!empty($aftax_tax_expire_date)) {

					$exp_date = $aftax_tax_expire_date;
				} else {

					$exp_date = '';
				}

				$aftax_tax_exemption_status = get_user_meta($user->ID, 'aftax_tax_exemption_status', true);

				$aftax_checkbox_status = false;
				$aftax_message_status  = false;

				if ('yes' == esc_attr(get_option('aftax_enable_tax_exm_msg')) && in_array($role[0], $afuserroles)) {
					if (!( in_array($role[0], $exm_roles) || in_array($user->ID, $exm_customers) || 'approved' == $aftax_tax_exemption_status )) {
						$aftax_message_status = true;
					}
				}

				if ('yes' != get_option('aftax_enable_auto_tax_exempttion')) {
					if (in_array($role[0], $exm_roles) || in_array($user->ID, $exm_customers) || 'approved' == $aftax_tax_exemption_status) {

						$aftax_checkbox_status = true;
					}
				}

				if ($aftax_checkbox_status) {
					?>
					<div id="tax_exemption_checkbox_div">
						<h3><?php echo esc_html__('Tax Exemption', 'addify_b2b'); ?></h3>
						<p class="form-row form-row-wide create-account woocommerce-validated">
							<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
								<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
									id="tax_exemption_checkbox" type="checkbox" name="tax_exemption_checkbox" value="yes"
									onclick="afUpOrder()">
								<span><?php echo esc_html__('Do you want to include tax exemption?', 'addify_b2b'); ?></span>
							</label>
						</p>
					</div>

					<?php

				} elseif ($aftax_message_status) {
					?>
					<div id="tax_exemption_checkbox_div">
						<h3><?php echo esc_html__('Tax Exemption', 'addify_b2b'); ?></h3>
						<p class="form-row form-row-wide create-account woocommerce-validated">
							<?php echo wp_kses(__(get_option('aftax_role_message_text'), 'addify_b2b'), ''); ?>
						</p>
					</div>
					<?php
				}
			} elseif ('yes' != get_option('aftax_enable_auto_tax_exempttion')) {
				$aftax_exempted_user_roles = (array) maybe_unserialize(get_option('aftax_exempted_user_roles'));
				if (in_array('guest', $aftax_exempted_user_roles)) {
					?>
					<div id="tax_exemption_checkbox_div">
						<h3><?php echo esc_html__('Tax Exemption', 'addify_b2b'); ?></h3>
						<p class="form-row form-row-wide create-account woocommerce-validated">
							<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
								<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
									id="tax_exemption_checkbox" type="checkbox" name="tax_exemption_checkbox" value="yes"
									onclick="afUpOrder()">
								<span><?php echo esc_html__('Do you want to include tax exemption?', 'addify_b2b'); ?></span>
							</label>
						</p>
					</div>

					<?php

				} elseif ('yes' == esc_attr(get_option('aftax_enable_guest_message'))) {
					?>
					<div id="tax_exemption_checkbox_div">
						<h3><?php echo esc_html__('Tax Exemption', 'addify_b2b'); ?></h3>
						<p class="form-row form-row-wide create-account woocommerce-validated">
							<?php echo wp_kses(__(get_option('aftax_guest_message_text'), 'addify_b2b'), ''); ?>
						</p>
					</div>
					<?php
				}

			} elseif ('yes' == esc_attr(get_option('aftax_enable_guest_message'))) {
				$aftax_exempted_user_roles = (array) maybe_unserialize(get_option('aftax_exempted_user_roles'));
				if (in_array('guest', (array) $aftax_exempted_user_roles)) {
					return;
				}
				?>
				<div id="tax_exemption_checkbox_div">
					<h3><?php echo esc_html__('Tax Exemption', 'addify_b2b'); ?></h3>
					<p class="form-row form-row-wide create-account woocommerce-validated">
						<?php echo wp_kses(__(get_option('aftax_guest_message_text'), 'addify_b2b'), ''); ?>
					</p>
				</div>
				<?php

			}
		}

		public function taxexempt_checkout_update_order_review( $post_data ) {
			global $woocommerce;

			check_ajax_referer('update-order-review', 'security');

			if (isset($_POST['post_data'])) {
				parse_str(sanitize_text_field($_POST['post_data']), $post_data);
			} else {
				$post_data = $_POST; // fallback for final checkout (non-ajax)
			}


			if (isset($post_data['tax_exemption_checkbox']) && 'yes' == $post_data['tax_exemption_checkbox']) {

				$woocommerce->customer->set_is_vat_exempt(true);
				wc_add_notice(esc_html__('Tax exempted', 'addify_b2b'), 'notice');
				return;

			} elseif ('yes' === get_option('aftax_enable_auto_tax_exempttion')) {

				if (is_user_logged_in()) {

					$user                      = wp_get_current_user();
					$user_info                 = get_userdata($user->ID);
					$role                      = (array) $user->roles;
					$afuserroles               = (array) maybe_unserialize(get_option('aftax_requested_roles'));
					$afcustomers               = (array) maybe_unserialize(get_option('aftax_exempted_customers'));
					$aftax_exempted_user_roles = (array) maybe_unserialize(get_option('aftax_exempted_user_roles'));
					if (!empty($afcustomers)) {
						$exm_customers = $afcustomers;
					} else {
						$exm_customers = array();
					}

					if (!empty($aftax_exempted_user_roles)) {
						$exm_roles = $aftax_exempted_user_roles;
					} else {
						$exm_roles = array();
					}

					$aftax_tax_expire_date = get_user_meta($user->ID, 'aftax_tax_expire_date', true);
					$current_date          = gmdate('Y-m-d');

					if (!empty($aftax_tax_expire_date)) {

						$exp_date = $aftax_tax_expire_date;
					} else {

						$exp_date = '';
					}

					$aftax_tax_exemption_status = get_user_meta($user->ID, 'aftax_tax_exemption_status', true);

					if ('approved' == $aftax_tax_exemption_status) {

						if ($current_date <= $exp_date) {
							$woocommerce->customer->set_is_vat_exempt(true);
							return;
						} elseif (empty($aftax_tax_expire_date)) {
							$woocommerce->customer->set_is_vat_exempt(true);
							return;
						}

					} elseif (in_array($user->ID, $exm_customers) || in_array($role[0], $exm_roles)) {

						$woocommerce->customer->set_is_vat_exempt(true);
						return;
					} else {

						$woocommerce->customer->set_is_vat_exempt(false);
					}
				} else {
					$aftax_exempted_user_roles = (array) maybe_unserialize(get_option('aftax_exempted_user_roles'));

					if (!empty($aftax_exempted_user_roles)) {
						$exm_roles = $aftax_exempted_user_roles;
					} else {
						$exm_roles = array();
					}
					if (in_array('guest', $exm_roles)) {

						$woocommerce->customer->set_is_vat_exempt(true);
						return;
					}
				}

			} else {
				$woocommerce->customer->set_is_vat_exempt(false);
				return;
			}
		}

		public function aftax_wc_diff_rate_for_user( $tax_class ) {

			if (is_user_logged_in()) {
				$user                      = wp_get_current_user();
				$role                      = (array) $user->roles;
				$afcustomers               = (array) maybe_unserialize(get_option('aftax_exempted_customers'));
				$aftax_exempted_user_roles = (array) maybe_unserialize(get_option('aftax_exempted_user_roles'));
				if (!empty($afcustomers)) {

					if (in_array($user->ID, $afcustomers)) {
						$tax_class = 'Zero Rate';
					}
				}

				if (!empty($aftax_exempted_user_roles)) {

					if (in_array($role[0], $aftax_exempted_user_roles)) {
						$tax_class = 'Zero Rate';
					}

				}
				$aftax_tax_expire_date = get_user_meta($user->ID, 'aftax_tax_expire_date', true);
				$current_date          = gmdate('Y-m-d');

				if (!empty($aftax_tax_expire_date)) {

					$exp_date = $aftax_tax_expire_date;
				} else {

					$exp_date = '';
				}

				$aftax_tax_exemption_status = get_user_meta($user->ID, 'aftax_tax_exemption_status', true);

				if ('approved' == $aftax_tax_exemption_status) {

					if ($current_date <= $exp_date) {
						$tax_class = 'Zero Rate';

					}
				}
			}
			return $tax_class;
		}

		public function aftaxadd_endpoints() {

			add_rewrite_endpoint('tax-exempt', EP_ROOT | EP_PAGES);

			if ('yes' == get_option('af_tax_flush_rules')) {
				flush_rewrite_rules();
				update_option('af_tax_flush_rules', 'no');
			}
		}

		public function aftaxadd_query_vars( $vars ) {
			$vars[] = 'tax-exempt';
			return $vars;
		}

		public function aftaxendpoint_title( $title, $id ) {
			global $wp_query;
			$is_endpoint = isset($wp_query->query_vars['tax-exempt']);
			if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
				// New page title.

				$title = esc_html__('Tax Exemption', 'addify_b2b');

				remove_filter('the_title', array( $this, 'aftaxendpoint_title' ));
			}

			return $title;
		}



		public function aftaxnew_menu_items( $items ) {

			$user        = wp_get_current_user();
			$afuserroles = (array) maybe_unserialize(get_option('aftax_requested_roles'));
			// Remove the logout menu item.
			$logout = $items['customer-logout'];
			unset($items['customer-logout']);
			// Insert your custom endpoint.

			$cur_role = current(wp_get_current_user()->roles);


			if (!empty($afuserroles) && in_array($cur_role, $afuserroles)) {
				$items['tax-exempt'] = esc_html__('Tax Exemption', 'addify_b2b');
			}

			// Insert back the logout item.
			$items['customer-logout'] = $logout;
			return $items;
		}

		public function aftaxendpoint_content() {

			global $wp_query;

			$user_id   = get_current_user_id();
			$user_info = get_userdata($user_id);

			$this->aftax_show_error_messages();
			$aftax_text_field           = $user_info->aftax_text_field;
			$aftax_textarea_field       = $user_info->aftax_textarea_field;
			$aftax_fileupload_field     = $user_info->aftax_fileupload_field;
			$aftax_tax_exemption_status = $user_info->aftax_tax_exemption_status;

			// fields
			$text_enable       = (array) maybe_unserialize(get_option('aftax_enable_text_field'));
			$textarea_enable   = (array) maybe_unserialize(get_option('aftax_enable_textarea_field'));
			$fileupload_enable = (array) maybe_unserialize(get_option('aftax_enable_fileupload_field'));

			$afcustomers               = (array) maybe_unserialize(get_option('aftax_exempted_customers'));
			$aftax_exempted_user_roles = (array) maybe_unserialize(get_option('aftax_exempted_user_roles'));
			$user                      = wp_get_current_user();
			$role                      = current(wp_get_current_user()->roles);

			if (!empty($afcustomers)) {
				$exm_customers = $afcustomers;
			} else {
				$exm_customers = array();
			}

			if (!empty($aftax_exempted_user_roles)) {
				$exm_roles = $aftax_exempted_user_roles;
			} else {
				$exm_roles = array();
			}

			$aftax_tax_expire_date = $user_info->aftax_tax_expire_date;



			$aftax_status = $user_info->aftax_tax_exemption_status;


			if (in_array($user_id, $exm_customers) || in_array($role, $exm_roles)) {
				?>

				<p><b><?php echo esc_html__('You are exempted from tax.', 'addify_b2b'); ?></b></p>

			<?php } elseif (in_array('enable', $text_enable) || in_array('enable', $textarea_enable) || in_array('enable', $fileupload_enable)) { ?>
				<form method="post" enctype="multipart/form-data">

					<?php wp_nonce_field('aftax_nonce_action', 'aftax_nonce_field'); ?>

					<p id="aftax_status_field" class="form-row afform-row">
						<label for="aftax_first_field"><b><?php echo esc_html__('Tax Exemption Status', 'addify_b2b'); ?></b>
						</label>


						<?php if ('approved' == $aftax_status) { ?>

							<span class="aftax_approved">
								<?php echo esc_attr($aftax_status); ?>
							</span>

						<?php } elseif ('disapproved' == $aftax_status) { ?>

							<span class="aftax_disapproved">
								<?php echo esc_attr($aftax_status); ?>
							</span>

						<?php } elseif ('expired' == $aftax_status) { ?>

							<span class="aftax_expired">
								<?php echo esc_attr($aftax_status); ?>
							</span>

						<?php } elseif ('pending' == $aftax_status) { ?>

							<span class="aftax_pending">
								<?php echo esc_attr($aftax_status); ?>
							</span>

							<?php
						} else {

							echo esc_html__('No information submitted', 'addify_b2b');
						}
						?>
					</p>

					<p id="aftax_expire_date" class="form-row afform-row">
						<label for="aftax_first_field"><b><?php echo esc_html__('Tax Exempt Expiry Date', 'addify_b2b'); ?></b>
						</label>
						<?php
						if (!empty($aftax_tax_expire_date) && ( 'approved' == $aftax_status || 'expired' == $aftax_status )) {
							echo esc_attr(gmdate('d F, Y', strtotime($aftax_tax_expire_date)));
						} else {
							echo esc_html__('No Expiry', 'addify_b2b');
						}
						?>
					</p>

					<?php
					// TAx JAr
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
							get_current_user_id()
						);

						$aftax_exemption_type = get_user_meta(get_current_user_id(), 'tax_exemption_type', true);
						?>
						<p id="aftax_exemption_type" class="form-row afform-row">
							<label for="aftax_first_field"><b><?php echo esc_html__('Tax Exempt Type', 'addify_b2b'); ?></b>
							</label>
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

						</p>
						<?php
					}
					?>

					<?php
					if (in_array('enable', $text_enable)) {
						?>

						<p id="aftax_first_field" class="form-row afform-row">
							<label
								for="aftax_first_field"><b><?php echo esc_html__(get_option('aftax_text_field_label'), 'addify_b2b'); ?></b>
							</label>
							<input value="<?php echo esc_attr($aftax_text_field); ?>" type="text"
								class="woocommerce-Input woocommerce-Input--text input-text" id="aftax_text_field" name="aftax_text_field"
								<?php
								if (in_array('required', $text_enable)) {
									echo 'required';
								}
								?>
								/>
						</p>
						<?php
					}
					?>

					<?php
					if (in_array('enable', $textarea_enable)) {
						?>
						<p id="aftax_second_field" class="form-row afform-row">
							<label
								for="aftax_second_field"><b><?php echo esc_html__(get_option('aftax_textarea_field_label'), 'addify_b2b'); ?></b>
							</label>
							<textarea name="aftax_textarea_field" id="aftax_textarea_field" rows="7" 
							<?php
							if (in_array('required', $textarea_enable)) {
								echo 'required';
							}
							?>
							><?php echo esc_attr($aftax_textarea_field); ?></textarea>

						</p>
						<?php
					}
					?>

					<?php
					if (in_array('enable', $fileupload_enable)) {
						?>
						<p id="aftax_third_field" class="form-row afform-row">
							<label
								for="aftax_third_field"><b><?php echo esc_html__(get_option('aftax_fileupload_field_label'), 'addify_b2b'); ?></b>
							</label>
							<input type="file" name="aftax_fileupload_field" id="aftax_fileupload_field" 
							<?php
							if (in_array('required', $fileupload_enable)) {
								echo 'required';
							}
							?>
							>
							<small><?php echo esc_html__('Allowed file types:', 'addify_b2b') . ' ' . esc_attr(get_option('aftax_allowed_file_types')); ?></small>
							<input type="hidden" name="aftax_current_file" id="aftax_current_file"
								value="<?php echo esc_attr($aftax_fileupload_field); ?>">

						</p>
					<?php } ?>

					<?php
					if (!empty($aftax_fileupload_field)) {
						?>

						<p id="aftax_file_link" class="form-row afform-row">
							<label
								for="aftax_third_field"><b><?php echo esc_html__(get_option('aftax_fileupload_field_label') . ' Link', 'addify_b2b'); ?></b>
							</label>
							<span>
								<a
									href="<?php echo esc_url($wp_query->query_vars['tax-exempt']); ?>?download"><?php echo esc_html__('Click here to download', 'addify_b2b'); ?></a>
							</span>

						</p>

					<?php } ?>

					<input type="hidden" name="action" value="SubmitTaxForm" />
					<input type="hidden" name="user_id" value="<?php echo intval($user_id); ?>">

					<p>
						<input type="submit" value="<?php echo esc_html__('Submit Tax Info', 'addify_b2b'); ?>" name="save_tax"
							class="button">
					</p>

				</form>
				<?php
			} else {
				?>
				<p id="aftax_status_field" class="form-row afform-row">
					<label for="aftax_first_field"><b><?php echo esc_html__('Tax Exemption Status', 'addify_b2b'); ?></b>
					</label>


					<?php if ('approved' == $aftax_status) { ?>

						<span class="aftax_approved">
							<?php echo esc_attr($aftax_status); ?>
						</span>

					<?php } elseif ('disapproved' == $aftax_status) { ?>

						<span class="aftax_disapproved">
							<?php echo esc_attr($aftax_status); ?>
						</span>

					<?php } elseif ('expired' == $aftax_status) { ?>

						<span class="aftax_expired">
							<?php echo esc_attr($aftax_status); ?>
						</span>

					<?php } elseif ('pending' == $aftax_status) { ?>

						<span class="aftax_pending">
							<?php echo esc_attr($aftax_status); ?>
						</span>

						<?php
					} else {

						echo esc_html__('No information submitted', 'addify_b2b');
					}
					?>


				</p>

				<p id="aftax_expire_date" class="form-row afform-row">
					<label for="aftax_first_field"><b><?php echo esc_html__('Tax Exempt Expiry Date', 'addify_b2b'); ?></b>
					</label>
					<?php
					if (!empty($aftax_tax_expire_date) && ( 'approved' == $aftax_status || 'expired' == $aftax_status )) {
						echo esc_attr(gmdate('d F, Y', strtotime($aftax_tax_expire_date)));
					} else {
						echo esc_html__('No Expiry', 'addify_b2b');
					}
					?>
				</p>
				<?php
			}
		}

		public function aftax_submit_tax_form() {

			wc()->mailer();
			$aftax_file          = '';
			$aftax_taxarea_field = '';
			$aftax_field         = '';

			$aftax_auto_approve = get_option('aftax_enable_auto_tax_exempt');

			include_once ABSPATH . 'wp-includes/pluggable.php';

			if (!empty($_REQUEST['aftax_nonce_field'])) {

				$retrieved_nonce = sanitize_text_field($_REQUEST['aftax_nonce_field']);
			} else {
				$retrieved_nonce = 0;
			}

			if (isset($_POST['user_id']) && '' != $_POST['user_id']) {
				$user_id = intval($_POST['user_id']);
			} else {
				$user_id = 0;
			}

			if (isset($_POST['aftax_text_field']) && '' != $_POST['aftax_text_field']) {

				if (!wp_verify_nonce($retrieved_nonce, 'aftax_nonce_action')) {
					die('Failed security check');
				}

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

					wc_add_notice(esc_html__('This file type is not allowed.', 'addify_b2b'), $notice_type = 'error');

					return;
				}
			} else {

				if (!empty($_POST['aftax_current_file'])) {
					$curr_file = sanitize_text_field($_POST['aftax_current_file']);
				} else {
					$curr_file = '';
				}

				update_metadata('user', $user_id, 'aftax_fileupload_field', $curr_file, '');

				$aftax_file = $curr_file;

			}

			if (!empty($aftax_auto_approve) && 'yes' == $aftax_auto_approve) {

				update_metadata('user', $user_id, 'aftax_tax_exemption_status', 'approved', '');
				//Send email to admin for auto approval
				do_action('aftax_approve_info_notification_admin', $user_id, $aftax_field, $aftax_taxarea_field, $aftax_file);
				//Send email to user for auto approval
				do_action('aftax_approve_info_notification_user', $user_id, $aftax_field, $aftax_taxarea_field, $aftax_file);
			} else {
				update_metadata('user', $user_id, 'aftax_tax_exemption_status', 'pending', '');
			}

			update_metadata('user', $user_id, 'aftax_tax_info_expire_email', '', '');

			// TaxJar
			if (isset($_POST['tax_exemption_type']) && '' != $_POST['tax_exemption_type']) {

				update_metadata('user', $user_id, 'tax_exemption_type', sanitize_text_field($_POST['tax_exemption_type']), '');

			} else {
				update_user_meta($user_id, 'tax_exemption_type', '');
			}


			//Email to admin
			do_action('aftax_info_notification_admin', $user_id, $aftax_field, $aftax_taxarea_field, $aftax_file);

			//Email to user
			do_action('aftax_info_notification_user', $user_id, $aftax_field, $aftax_taxarea_field, $aftax_file);

			wc_add_notice(esc_html__(get_option('aftax_add_tax_info_message')), $notice_type = 'success');
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

		public function aftax_save_extra_checkout_fields( $order_id, $posted ) {
			if (!empty($_REQUEST['aftax_nonce_field'])) {

				$retrieved_nonce = sanitize_text_field($_REQUEST['aftax_nonce_field']);
			} else {
				$retrieved_nonce = 0;
			}

			$user                       = wp_get_current_user();
			$user_id                    = $user->ID;
			$user_info                  = get_userdata($user_id);
			$aftax_text_field           = $user_info->aftax_text_field;
			$aftax_textarea_field       = $user_info->aftax_textarea_field;
			$aftax_fileupload_field     = $user_info->aftax_fileupload_field;
			$aftax_tax_exemption_status = $user_info->aftax_tax_exemption_status;
			$aftax_tax_expire_date      = $user_info->aftax_tax_expire_date;

			$order = wc_get_order($order_id);

			if (isset($_POST['tax_exemption_checkbox']) && 'yes' == $_POST['tax_exemption_checkbox']) {
				if (!wp_verify_nonce($retrieved_nonce, 'aftax_nonce_action')) {

					die('Failed security check');
				}
				$order->update_meta_data('tax_exemption_checkbox', 'Yes');
				$order->update_meta_data('aftax_text_field', esc_attr($aftax_text_field));
				$order->update_meta_data('aftax_textarea_field', esc_attr($aftax_textarea_field));
				$order->update_meta_data('aftax_fileupload_field', esc_attr($aftax_fileupload_field));
				$order->save();
			}
		}

		public function aftax_display_order_data( $order_id ) {
			global $wp;
			$current_url = home_url(add_query_arg(array(), $wp->request));
			$order       = wc_get_order($order_id);
			?>
			<?php
			if ('Yes' == esc_attr($order->get_meta('tax_exemption_checkbox', true))) {
				$uploaded_file = $order->get_meta('aftax_fileupload_field', true);
				?>
				<h2><?php echo esc_html__('Tax Exempt', 'addify_b2b'); ?></h2>
				<table class="shop_table shop_table_responsive additional_info">
					<tbody>
						<tr>
							<th><?php echo esc_html__('Is Tax Exempt?', 'addify_b2b'); ?></th>
							<td><?php echo esc_html__('Yes', 'addify_b2b'); ?></td>
						</tr>
						<tr>
							<th><?php echo esc_html__(get_option('aftax_text_field_label'), 'addify_b2b'); ?></th>
							<td><?php echo esc_attr($order->get_meta('aftax_text_field', true)); ?></td>
						</tr>
						<tr>
							<th><?php echo esc_html__(get_option('aftax_textarea_field_label'), 'addify_b2b'); ?></th>
							<td><?php echo esc_attr($order->get_meta('aftax_textarea_field', true)); ?></td>
						</tr>
						<tr>
							<th><?php echo esc_html__(get_option('aftax_fileupload_field_label') . ' Link', 'addify_b2b'); ?></th>
							<td>
								<?php
								if (!empty($uploaded_file)) {
									?>
									<a
										href="<?php echo esc_url($current_url); ?>?download&order_id=<?php echo intval($order_id); ?>"><?php echo esc_html__('Click here to download', 'addify_b2b'); ?></a>
									<?php
								} else {
									?>
									<p><?php echo esc_html__('No file has been uploaded', 'addify_b2b'); ?></p>
									<?php
								}
								?>
							</td>
						</tr>
					</tbody>
				</table>
				<?php
			}
		}

		public function aftax_email_order_meta_fields( $fields, $sent_to_admin, $order ) {

			if (esc_attr($order->get_meta('tax_exemption_checkbox', true)) == 'Yes') {
				$afim                           = AFTAX_MEDIA_URL . $order->get_meta('aftax_fileupload_field', true);
				$fields['is_tax_exempt']        = array(
					'label' => esc_html__('Is Tax Exempt?', 'addify_b2b'),
					'value' => esc_html__('Yes', 'addify_b2b'),
				);
				$fields['aftax_text_field']     = array(
					'label' => esc_html__(get_option('aftax_text_field_label'), 'addify_b2b'),
					'value' => esc_attr($order->get_meta('aftax_text_field', true)),
				);
				$fields['aftax_textarea_field'] = array(
					'label' => esc_html__(get_option('aftax_textarea_field_label'), 'addify_b2b'),
					'value' => esc_attr($order->get_meta('aftax_textarea_field', true)),
				);
				// $fields['aftax_fileupload_field'] = array(
				//  'label' => esc_html__( get_option( 'aftax_fileupload_field_label' ) . ' Link' , 'addify_b2b' ),
				//  'value' => '<a target="_blank" href="' . esc_url( $afim ) . '">' . esc_html__( 'Click here to view', 'addify_b2b' ) . '</a>',
				// );
			}
			return $fields;
		}

		public function yith_myaccount_menu_callback() {

			global $wp_query;
			$is_endpoint = isset($wp_query->query_vars['tax-exempt']);
			if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {

				$class = 'active';
			} else {

				$class = '';
			}

			?>
			<li class="<?php echo esc_attr($class); ?>">
				<a class="yith-my-wishlist"
					href="<?php echo esc_url(wc_get_endpoint_url('tax-exempt', '', get_permalink(wc_get_page_id('myaccount')))); ?>">
					<i class="fa fa-file"></i>
					<span><?php echo esc_html__('Tax Exemption', 'addify_b2b'); ?></span>
				</a>
			</li>
			<?php
		}
	}

	new Addify_Tax_Exempt_Front();

}
