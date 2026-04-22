<?php
if (! defined('WPINC') ) {
	die;
}

if (!class_exists('Addify_B2B_Plugin_Admin') ) {

	class Addify_B2B_Plugin_Admin extends Addify_B2B_Plugin {
	

		public function __construct() {

			add_action('admin_menu', array( $this, 'afb2b_custom_menu_admin' ));
			add_action('admin_enqueue_scripts', array( $this, 'afb2b_admin_scripts' ));
			add_action('admin_init', array( $this, 'afb2b_options' ));

			add_action('wp_loaded', array( $this, 'afrolebase_import_prices' ) );
		}

		public function afb2b_admin_scripts() {

			$screen = get_current_screen();


			if ('toplevel_page_addify-b2b' == $screen->id || 'b2b_page_addify-import-rolebase-prices' == $screen->id) {

				wp_enqueue_style('thickbox');
				wp_enqueue_script('thickbox');
				wp_enqueue_script('media-upload');
				wp_enqueue_media();
				wp_enqueue_style('afb2b-admin-css', plugins_url('assets/css/afb2b_admin.css', __FILE__), '', '1.0');
				wp_enqueue_script('jquery-js', '//code.jquery.com/jquery-1.12.4.js', false, '1.0');
				wp_enqueue_script('jquery-ui', '//code.jquery.com/ui/1.12.1/jquery-ui.js', false, '1.0');
				wp_enqueue_script('afb2b-admin-js', plugins_url('assets/js/afb2b_admin.js', __FILE__), false, '1.0');
				$afpvu_data = array(
					'admin_url' => admin_url('admin-ajax.php'),
					'nonce'     => wp_create_nonce('afb2b-ajax-nonce'),
				);


				
				wp_localize_script('afb2b-admin-js', 'afb2b_php_vars', $afpvu_data);
				
				wp_enqueue_style('select2', plugins_url('/assets/css/select2.css', __FILE__), false, '1.0');
				wp_enqueue_script('select2', plugins_url('/assets/js/select2.js', __FILE__), false, '1.0');
			}

			if ('b2b_page_addify-import-rolebase-prices' == $screen->id) {

				wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', false, '1.0');
			}
		}



		public function afb2b_custom_menu_admin() {

			add_menu_page(
				esc_html__('B2B', 'addify_b2b'), // page title 
				esc_html__('B2B', 'addify_b2b'), // menu title
				'manage_options', // capability
				'addify-b2b',  // menu-slug
				array( $this, 'afb2b_module_settings' ),   // callback function
				'dashicons-groups',   //menu icon
				17    // menu position
			);

			

			//add_submenu_page('addify-b2b', esc_html__('All Submitted Quotes', 'addify_b2b'), esc_html__('All Quotes', 'addify_b2b'), 'manage_options', 'edit.php?post_type=addify_quote', '', 20 );

			//add_submenu_page('addify-b2b', esc_html__('Quote Rules', 'addify_b2b'), esc_html__('Quote Rules', 'addify_b2b'), 'manage_options', 'edit.php?post_type=addify_rfq', '', 30 );

			//add_submenu_page('addify-b2b', esc_html__('Quote Fields', 'addify_b2b'), esc_html__('Quote Fields', 'addify_b2b'), 'manage_options', 'edit.php?post_type=addify_rfq_fields', '', 40 );

			//add_submenu_page('addify-b2b', esc_html__('Registration Fields', 'addify_b2b'), esc_html__('Registration Fields', 'addify_b2b'), 'manage_options', 'edit.php?post_type=afreg_fields', '', 50 );

			//add_submenu_page('addify-b2b', esc_html__('Role Based Pricing', 'addify_b2b'), esc_html__('Role Based Pricing', 'addify_b2b'), 'manage_options', 'edit.php?post_type=csp_rules', '', 60 );

			add_submenu_page('addify-b2b', esc_html__('Import Role Base Prices', 'addify_b2b'), esc_html__('Import Role Base Prices', 'addify_b2b'), 'manage_options', 'addify-import-rolebase-prices', array( $this, 'afb2b_import_prices' ), 100);

			add_submenu_page('addify-b2b', esc_html__('User Role Manager', 'addify_user_role_editor'), esc_html__('User Role Manager', 'addify_user_role_editor'), 'manage_options', 'af-ure', array( $this, 'af_b2b_ure_tab_callback' ), 120);

			add_submenu_page('addify-b2b', esc_html__('B2B Settings', 'addify_b2b'), esc_html__('Settings', 'addify_b2b'), 'manage_options', 'addify-b2b', array( $this, 'afb2b_module_settings' ), 110 );
		}

		public function af_b2b_ure_tab_callback() {

			?>


			<div class="wrap">
				<h2>
					<?php echo esc_html__('User Role Manager', 'addify_user_role_editor'); ?>
					<?php settings_errors(); ?>

				</h2>
				<span class="clear"></span>
			</div>

			<span class="clear"></span>
			<div class="loco-content">
				<?php

				
				include AF_URE_PLUGIN_DIR . 'includes/admin/view/add-new-user-role.php';
				
				?>
			</div>
			<?php
		}

		public function afrolebase_import_success_notice() {
			?>
			<div class="updated notice notice-success is-dismissible">
				<p><?php echo esc_html__('Prices imported successfully.', 'addify_b2b'); ?></p>
			</div>
			<?php
		}

		public function afrolebase_import_prices() {

			if ( !empty( $_POST['afb2b_import_prices'] ) ) {

				$retrieved_nonce = isset( $_REQUEST['afroleprice_import_nonce_field'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['afroleprice_import_nonce_field'] ) ) : '';

				if ( ! wp_verify_nonce( $retrieved_nonce, 'afroleprice_import_action' ) ) {
					die( esc_html__('Security Violated.', 'addify_b2b') );
				}

				$response = include_once AFB2B_PLUGIN_DIR . 'includes/role-based/import_prices_csv_function.php';

				if ( $response ) {
					add_action('admin_notices', array( $this, 'afrolebase_import_success_notice' ) );
				}
			}
		}

		public function afb2b_import_prices() {
			include_once AFB2B_PLUGIN_DIR . 'includes/role-based/import_prices_csv.php';
		}

		public function afb2b_module_settings() {

			if (isset($_GET['tab']) ) {  
				$active_tab = sanitize_text_field($_GET['tab']);  
			} else {
				$active_tab = 'tab_one';
			}

			if (isset($_GET['subtab_products_visibility']) ) {  
				$active_tab_products_visibility = sanitize_text_field($_GET['subtab_products_visibility']);  
			} else {
				$active_tab_products_visibility = 'one_products_visibility';
			}

			if (isset($_GET['subtab_rfq']) ) {  
				$active_tab_rfq = sanitize_text_field($_GET['subtab_rfq']);  
			} else {
				$active_tab_rfq = 'general';
			}

			if (isset($_GET['subtab_afreg']) ) {  
				$active_tab_afreg = sanitize_text_field($_GET['subtab_afreg']);  
			} else {
				$active_tab_afreg = 'one_afreg';
			}

			if (isset($_GET['subtab_tax']) ) {  
				$active_tab_tax = sanitize_text_field($_GET['subtab_tax']);  
			} else {
				$active_tab_tax = 'general';
			}

			if (isset($_GET['subtab_afrolebase']) ) {  
				$active_tab_afrolebase = sanitize_text_field($_GET['subtab_afrolebase']);  
			} else {
				$active_tab_afrolebase = 'one_afrolebase';
			}


			?>
				<div class="wrap">
					<h2><?php echo esc_html__('Settings', 'addify_b2b'); ?></h2>
			<?php settings_errors(); ?> 

					<h2 class="nav-tab-wrapper">  
					
						<a href="?page=addify-b2b&tab=tab_one" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_one' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Products Visibility', 'addify_b2b'); ?></a>

						<a href="?page=addify-b2b&tab=tab_two" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_two' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Request a Quote', 'addify_b2b'); ?></a>

						<a href="?page=addify-b2b&tab=tab_three" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_three' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('B2B Registration', 'addify_b2b'); ?></a> 

						<a href="?page=addify-b2b&tab=tab_four" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_four' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Role Based Pricing', 'addify_b2b'); ?></a>
						<a href="?page=addify-b2b&tab=tax" class="nav-tab <?php echo esc_attr($active_tab) == 'tax' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Tax', 'addify_b2b'); ?></a>
						<a href="?page=addify-b2b&tab=tab_seven" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_seven' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Tax-Exempt', 'addify_b2b'); ?></a> 
						<a href="?page=addify-b2b&tab=tab_five" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_five' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Shipping', 'addify_b2b'); ?></a>
						<a href="?page=addify-b2b&tab=tab_six" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_six' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Payments', 'addify_b2b'); ?></a>
						<a href="?page=addify-b2b&tab=tab_eight" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_eight' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Cart Discounts', 'addify_b2b'); ?></a>

						<a href="?page=addify-b2b&tab=tab_nine" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_nine' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Order Restriction', 'addify_b2b'); ?></a>
					</h2>

					<form method="post" action="options.php" class="afb2b_options_form"> 
			<?php if ('tab_one' == $active_tab ) { ?>

							<ul class="subsubsub">
								<li>
									<a href="?page=addify-b2b&tab=tab_one&subtab_products_visibility=three_products_visibility" class="<?php echo esc_attr($active_tab_products_visibility) == 'three_products_visibility' ? 'current' : ''; ?>"><?php echo esc_html__('General Settings', 'addify_b2b'); ?></a>
									|
								</li>
								<li>
									<a href="?page=addify-b2b&tab=tab_one&subtab_products_visibility=one_products_visibility" class="<?php echo esc_attr($active_tab_products_visibility) == 'one_products_visibility' ? 'current' : ''; ?>"><?php echo esc_html__('Global Visibility', 'addify_b2b'); ?></a>
									|
								</li>
								<li>
									<a href="?page=addify-b2b&tab=tab_one&subtab_products_visibility=two_products_visibility" class="<?php echo esc_attr($active_tab_products_visibility) == 'two_products_visibility' ? 'current' : ''; ?>"><?php echo esc_html__('Visibility by User Roles', 'addify_b2b'); ?></a>
								</li>
								
							</ul>

				<?php
				if ('one_products_visibility' == $active_tab_products_visibility ) {

					settings_fields('setting-group-1');
					do_settings_sections('addify-products-visibility-1');

				}

				if ('two_products_visibility' == $active_tab_products_visibility ) {

					settings_fields('setting-group-2');
					do_settings_sections('addify-products-visibility-2');

				}

				if ('three_products_visibility' == $active_tab_products_visibility ) {

					settings_fields('setting-group-3');
					do_settings_sections('addify-products-visibility-3');

				}
				?>

			<?php } ?>


			<?php if ('tab_two' == $active_tab ) { ?>

					<ul class="subsubsub">
						<li>
							<a href="?page=addify-b2b&tab=tab_two&subtab_rfq=general" class="<?php echo esc_attr( $active_tab_rfq ) === 'general' ? 'current' : ''; ?>"><?php echo esc_html__( 'General', 'addify_b2b' ); ?>
							</a>
						</li>|
						<li>
						<a href="?page=addify-b2b&tab=tab_two&subtab_rfq=convert_to_cart_restrict" class=" <?php echo esc_attr( $active_tab_rfq ) === 'convert_to_cart_restrict' ? 'current' : ''; ?>"><?php echo esc_html__( 'Convert to Cart Restriction', 'addify_b2b' ); ?>
						</a>|
					</li>
						<li>
							<a href="?page=addify-b2b&tab=tab_two&subtab_rfq=messages" class="<?php echo esc_attr( $active_tab_rfq ) === 'messages' ? 'current' : ''; ?>"><?php echo esc_html__( 'Custom Messages', 'addify_b2b' ); ?>
						</a>
						</li>|
						<li>
							<a href="?page=addify-b2b&tab=tab_two&subtab_rfq=emails" class="<?php echo esc_attr( $active_tab_rfq ) === 'emails' ? 'current' : ''; ?>"><?php echo esc_html__( 'Emails', 'addify_b2b' ); ?>
							</a>
						</li>|
						<li>
							<a href="?page=addify-b2b&tab=tab_two&subtab_rfq=captcha" class="<?php echo esc_attr( $active_tab_rfq ) === 'captcha' ? 'current' : ''; ?>"><?php echo esc_html__( 'Google Captcha', 'addify_b2b' ); ?>
							</a>
						</li>|
						<li>
							<a href="?page=addify-b2b&tab=tab_two&subtab_rfq=editors" class="<?php echo esc_attr( $active_tab_rfq ) === 'editors' ? 'current' : ''; ?>"><?php echo esc_html__( 'Page builders', 'addify_b2b' ); ?>
						</a>
						</li>|
						<li>
							<a href="?page=addify-b2b&tab=tab_two&subtab_rfq=attributes" class="<?php echo esc_attr( $active_tab_rfq ) === 'attributes' ? 'current' : ''; ?>"><?php echo esc_html__( 'Quote Attributes', 'addify_b2b' ); ?>
							</a>
						</li>|
						<li>
							<a href="?page=addify-b2b&tab=tab_two&subtab_rfq=button" class=" <?php echo esc_attr( $active_tab_rfq ) === 'button' ? 'current' : ''; ?>"><?php echo esc_html__( 'Quote Page Customization', 'addify_b2b' ); ?>
							</a>|
						</li>
						<li>
							<a href="?page=addify-b2b&tab=tab_two&subtab_rfq=popup_design" class=" <?php echo esc_attr( $active_tab_rfq ) === 'popup_design' ? 'current' : ''; ?>"><?php echo esc_html__( 'Quote Popup Customization', 'addify_b2b' ); ?>
							</a>|
						</li>
						<li>
							<a href="?page=addify-b2b&tab=tab_two&subtab_rfq=quote_btn_shortcode" class=" <?php echo esc_attr( $active_tab_rfq ) === 'quote_btn_shortcode' ? 'current' : ''; ?>"><?php echo esc_html__( 'Quote Button Shortcode', 'addify_b2b' ); ?>
							</a>|
						</li>
						
						<li>
							<a href="?page=addify-b2b&tab=tab_two&subtab_rfq=pdf_layouts" class="<?php echo esc_attr( $active_tab_rfq ) === 'pdf_layouts' ? 'current' : ''; ?>"><?php echo esc_html__( 'PDF Settings', 'addify_b2b' ); ?>
							</a>
						</li>
						
					</ul>

				<?php
				
				if ( 'general' === $active_tab_rfq ) {
					settings_fields( 'afrfq_general_setting_fields' );
					do_settings_sections( 'afrfq_general_setting_section' );
				} elseif ( 'convert_to_cart_restrict' === $active_tab_rfq ) {
					settings_fields( 'afrfq_convert_to_cart_restrict_fields' );
					do_settings_sections( 'afrfq_convert_to_cart_restrict_section' );
				} elseif ( 'messages' === $active_tab_rfq ) {
					settings_fields( 'afrfq_messages_fields' );
					do_settings_sections( 'afrfq_messages_section' );
				} elseif ( 'emails' === $active_tab_rfq ) {
					settings_fields( 'afrfq_emails_fields' );
					do_settings_sections( 'afrfq_emails_section' );
				} elseif ( 'captcha' === $active_tab_rfq ) {
					settings_fields( 'afrfq_captcha_fields' );
					do_settings_sections( 'afrfq_captcha_section' );
				} elseif ( 'editors' === $active_tab_rfq ) {
					settings_fields( 'afrfq_editors_fields' );
					do_settings_sections( 'afrfq_editors_section' );
				} elseif ( 'attributes' === $active_tab_rfq ) {
					settings_fields( 'afrfq_attributes_fields' );
					do_settings_sections( 'afrfq_attributes_section' );
				} elseif ( 'button' === $active_tab_rfq ) {
					settings_fields( 'afrfq_button_setting_fields' );
					do_settings_sections( 'afrfq_button_setting_section' );
				} elseif ( 'popup_design' === $active_tab_rfq ) {
					settings_fields( 'afrfq_popup_setting_fields' );
					do_settings_sections( 'afrfq_popup_design_setting_section' );
				} elseif ( 'quote_btn_shortcode' === $active_tab_rfq ) {
					settings_fields( 'afrfq_quote_btn_shortcode_fields' );
					do_settings_sections( 'afrfq_quote_btn_shortcode_setting_section' );
				} elseif ( 'pdf_layouts' === $active_tab_rfq ) {
					settings_fields( 'afrfq_pdflayout_setting_fields' );
					do_settings_sections( 'afrfq_pdflayout_setting_section' );
				}


							
				
							
				?>

			<?php } ?>


			<?php if ('tab_three' == $active_tab ) { ?>

							<ul class="subsubsub">
								
								<li>
									<a href="?page=addify-b2b&tab=tab_three&subtab_afreg=one_afreg" class="<?php echo esc_attr($active_tab_afreg) == 'one_afreg' ? 'current' : ''; ?>"><?php echo esc_html__('General Settings', 'addify_b2b'); ?></a>
									|
								</li>

								<li>
									<a href="?page=addify-b2b&tab=tab_three&subtab_afreg=five_afreg" class="<?php echo esc_attr($active_tab_afreg) == 'five_afreg' ? 'current' : ''; ?>"><?php echo esc_html__('Enable Default Fields', 'addify_b2b'); ?></a>
									|
								</li>

								<li>
									<a href="?page=addify-b2b&tab=tab_three&subtab_afreg=two_afreg" class="<?php echo esc_attr($active_tab_afreg) == 'two_afreg' ? 'current' : ''; ?>"><?php echo esc_html__('User Role Settings', 'addify_b2b'); ?></a>
									|
								</li>

								<li>
									<a href="?page=addify-b2b&tab=tab_three&subtab_afreg=three_afreg" class="<?php echo esc_attr($active_tab_afreg) == 'three_afreg' ? 'current' : ''; ?>"><?php echo esc_html__('Approve New User Settings', 'addify_b2b'); ?></a>
									|
								</li>

								<li>
									<a href="?page=addify-b2b&tab=tab_three&subtab_afreg=four_afreg" class="<?php echo esc_attr($active_tab_afreg) == 'four_afreg' ? 'current' : ''; ?>"><?php echo esc_html__('Email Settings', 'addify_b2b'); ?></a>
									|
								</li>

								<li>
									<a href="edit.php?post_type=afreg_fields"><?php echo esc_html__('All Registration Fields', 'addify_b2b'); ?></a>
								</li>
								
							</ul>

				<?php
				if ('one_afreg' == $active_tab_afreg ) {

					settings_fields('afreg_setting-group-1');
					do_settings_sections('addify-afreg-1');

				}

				if ('two_afreg' == $active_tab_afreg ) {

					settings_fields('afreg_setting-group-2');
					do_settings_sections('addify-afreg-2');

				}

				if ('three_afreg' == $active_tab_afreg ) {

					settings_fields('afreg_setting-group-3');
					do_settings_sections('addify-afreg-3');

				}

				if ('four_afreg' == $active_tab_afreg ) {

					settings_fields('afreg_setting-group-4');
					do_settings_sections('addify-afreg-4');

				}

				if ('five_afreg' == $active_tab_afreg ) {

					settings_fields('afreg_setting-group-5');
					do_settings_sections('addify-afreg-5');

				}

							
				?>

			<?php } ?>

			<?php if ('tab_four' == $active_tab ) { ?>


					<ul class="subsubsub">
						
						<li>
							<a href="?page=addify-b2b&tab=tab_four&subtab_afrolebase=one_afrolebase" class="<?php echo esc_attr($active_tab_afrolebase) == 'one_afrolebase' ? 'current' : ''; ?>"><?php echo esc_html__('General Settings', 'addify_b2b'); ?></a>
							|
						</li>
						<li>
							<a href="?page=addify-b2b&tab=tab_four&subtab_afrolebase=pricing_template_settings" class="<?php echo esc_attr($active_tab_afrolebase) == 'pricing_template_settings' ? 'current' : ''; ?>"><?php echo esc_html__('Price Template Settings', 'addify_b2b'); ?></a>
							|
						</li>
						<li>
							<a href="?page=addify-b2b&tab=tab_four&subtab_afrolebase=discount" class="<?php echo esc_attr($active_tab_afrolebase) == 'discount' ? 'current' : ''; ?>"><?php echo esc_html__('Price for Discount', 'addify_b2b'); ?></a>
							|
						</li>

						

						<li>
							<a href="edit.php?post_type=csp_rules"><?php echo esc_html__('All Role Based Pricing Rules', 'addify_b2b'); ?></a>
						</li>
						
					</ul>

				<?php
				if ('one_afrolebase' == $active_tab_afrolebase ) {

					settings_fields('afrolebased_setting-group-1');
					do_settings_sections('addify-role-pricing-1');

				}

				if ('pricing_template_settings' == $active_tab_afrolebase ) {

					settings_fields('afrolebased_setting_pricing_template_settings');
					do_settings_sections('addify-role-pricing-template-settings');
					
				}

				if ('discount' == $active_tab_afrolebase ) {

					settings_fields('afrolebased_setting_discount');
					do_settings_sections('addify-role-pricing-discount');

				}

							
				?>


			<?php } ?>

			<?php 
			if ('tab_five' == $active_tab ) { 

				settings_fields('afrolebased_shipping_setting');
				do_settings_sections('addify-role-based-shipping');

			}

			?>

			<?php 
			if ('tab_six' == $active_tab ) {

				settings_fields('afrolebased_payments_setting');
				do_settings_sections('addify-role-based-payments');
			} 
			
			if ( 'tax' === $active_tab ) {
				settings_fields('af_tax_diaplay_fields');
				do_settings_sections('af_tax_diaplay_section');
			}

			if ('tab_seven' == $active_tab ) {
				?>
					<ul class="subsubsub">
					
					<li>
						<a href="?page=addify-b2b&tab=tab_seven&subtab_tax=general" class="<?php echo esc_attr($active_tab_tax) == 'general' ? 'current' : ''; ?>"><?php echo esc_html__('General', 'addify_b2b'); ?></a>
						|
					</li>

					<li>
						<a href="?page=addify-b2b&tab=tab_seven&subtab_tax=exempt_customers_roles" class="<?php echo esc_attr($active_tab_tax) == 'exempt_customers_roles' ? 'current' : ''; ?>"><?php echo esc_html__('Customers and Roles', 'addify_b2b'); ?></a>
						|
					</li>

					<li>
						<a href="?page=addify-b2b&tab=tab_seven&subtab_tax=exempt_request" class="<?php echo esc_attr($active_tab_tax) == 'exempt_request' ? 'current' : ''; ?>"><?php echo esc_html__('Exemption Request', 'addify_b2b'); ?></a>
						|
					</li>

					<li>
						<a href="?page=addify-b2b&tab=tab_seven&subtab_tax=email_notification" class="<?php echo esc_attr($active_tab_tax) == 'email_notification' ? 'current' : ''; ?>"><?php echo esc_html__('Email & Notification', 'addify_b2b'); ?></a>
						|
					</li>

					<li>
						<a href="?page=addify-b2b&tab=tab_seven&subtab_tax=guest_user" class="<?php echo esc_attr($active_tab_tax) == 'guest_user' ? 'current' : ''; ?>"><?php echo esc_html__('Guest Users', 'addify_b2b'); ?></a>
						
					</li>
					
				</ul>
				<?php

				if ('general' == $active_tab_tax ) {

					settings_fields('aftax_general_setting_fields');
					do_settings_sections('aftax_general_setting_section');

				}

				if ('exempt_customers_roles' == $active_tab_tax ) {

					settings_fields('aftax_exempted_customer_roles_setting_fields');
					do_settings_sections('aftax_exempted_customer_roles_setting_section');

				}

				if ('exempt_request' == $active_tab_tax ) {

					settings_fields('aftax_request_setting_fields');
					do_settings_sections('aftax_request_setting_section');

				}

				if ('email_notification' == $active_tab_tax ) {

					settings_fields( 'aftax_email_messages_setting_fields' );
						do_settings_sections( 'aftax_email_messages_setting_section' );
						do_settings_sections( 'aftax_add_update_info_setting_section' );
						do_settings_sections( 'aftax_approve_info_setting_section' );
						do_settings_sections( 'aftax_disapprove_info_setting_section' );
						do_settings_sections( 'aftax_expire_info_setting_section' );

				}

				if ('guest_user' == $active_tab_tax ) {

					settings_fields('aftax_guest_setting_fields');
					do_settings_sections('aftax_guest_setting_section');

				}
			}
			?>

			<?php 
			if ('tab_eight' == $active_tab ) { 

				settings_fields('afcart_discount_setting');
				do_settings_sections('addify-cart-based-discount');

			}

			?>

			<?php 
			if ('tab_nine' == $active_tab ) { 

				settings_fields('addify-order-restrictions-fields');
				do_settings_sections('addify-or-general');

			}

			?>

						<div class="submit_b2b_settings">
			<?php submit_button(); ?>
						</div>
					</form> 

				</div>
			<?php 
		}

		public function afb2b_options() {

			include_once AFB2B_PLUGIN_DIR . 'includes/afb2b_registration_settings.php';
			// Role based pricing
			include_once AFB2B_PLUGIN_DIR . 'includes/afb2b_role_based_pricing_settings.php';
			include_once AFB2B_PLUGIN_DIR . 'includes/role-based/discount-setting.php';
			include_once AFB2B_PLUGIN_DIR . 'includes/role-based/pricing-template-settings.php';
			
			//Default Fields
			include_once AFB2B_PLUGIN_DIR . 'includes/afreg_def_fields.php';
			

			//Payments
			include_once AFB2B_PLUGIN_DIR . 'includes/payments/addify-payments-by-user-roles.php';
			//Shipping
			include_once AFB2B_PLUGIN_DIR . 'includes/shipping/addify-shipping-by-user-roles-settings.php';
			//Tax
			include_once AFB2B_PLUGIN_DIR . 'includes/tax/tax-settings.php';
			//Tax-Exempt
			include_once AFB2B_PLUGIN_DIR . 'woocommerce-tax-exempt-plugin/settings/general.php';
			include_once AFB2B_PLUGIN_DIR . 'woocommerce-tax-exempt-plugin/settings/exempted_customer_roles.php';
			include_once AFB2B_PLUGIN_DIR . 'woocommerce-tax-exempt-plugin/settings/request.php';
			include_once AFB2B_PLUGIN_DIR . 'woocommerce-tax-exempt-plugin/settings/email_messages.php';
			include_once AFB2B_PLUGIN_DIR . 'woocommerce-tax-exempt-plugin/settings/guest.php';
			//Cart Discount
			include_once AFB2B_PLUGIN_DIR . 'includes/cart-based-discount/cart-based-discount-settings.php';
			//Order Restrictions
			include_once AFB2B_PLUGIN_DIR . 'addify-order-restrictions/includes/admin/settings/general.php';
		}


		public function afpvusearchProducts() {

			

			if (isset($_POST['nonce']) && '' != $_POST['nonce']) {

				$nonce = sanitize_text_field($_POST['nonce']);
			} else {
				$nonce = 0;
			}

			if (isset($_POST['q']) && '' != $_POST['q']) {

				if (! wp_verify_nonce($nonce, 'afb2b-ajax-nonce') ) {

					die('Failed ajax security check!');
				}
				

				$pro = sanitize_text_field($_POST['q']);

			} else {

				$pro = '';

			}


			$data_array = array();
			$args       = array(
				'post_type'   => 'product',
				'post_status' => 'publish',
				'numberposts' => -1,
				's'           =>  $pro,
			);
			$pros       = get_posts($args);

			if (!empty($pros)) {

				foreach ($pros as $proo) {

					$title        = ( mb_strlen($proo->post_title) > 50 ) ? mb_substr($proo->post_title, 0, 49) . '...' : $proo->post_title;
					$data_array[] = array( $proo->ID, $title ); // array( Post ID, Post Title )
				}
			}
			
			echo json_encode($data_array);

			die();
		}
	}

	new Addify_B2B_Plugin_Admin();

}
