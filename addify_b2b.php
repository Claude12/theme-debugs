<?php
/**
 * Plugin Name:       B2B for WooCommerce
 * Plugin URI:        https://addify.store/product/b2b-for-woocommerce/
 * Description:       WooCommerce B2B plugin offers merchants a complete wholesale solution to optimize their website for both B2B & B2C customers. (PLEASE TAKE BACKUP BEFORE UPDATING THE PLUGIN).
 * Version:           3.5.0
 * Author:            Addify
 * Developed By:      Addify
 * Author URI:        https://addify.store/
 * Support:           https://addify.store/
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path:       /languages
 * Text Domain:       addify_b2b
 * Requires Plugins: woocommerce
 * WC requires at least: 4.0
 * WC tested up to: 10.*.*
 * Requires at least: 6.5
 * Tested up to: 6.*.*
 * Requires PHP: 7.4
 */
// Exit if accessed directly
if (! defined('ABSPATH') ) {
	exit;
}

if (!class_exists('Addify_B2B_Plugin') ) {

	class Addify_B2B_Plugin {
	

		public function __construct() {


			$this->afb2b_global_constents_vars();
			
			register_activation_hook(__FILE__, array( $this, 'afb2b_add_prod_visbility_page' ));

			add_action('wp_loaded', array( $this, 'afb2b_init' ));

			add_action('init', array( $this, 'afb2b_custom_post_types' ));


			if ( extension_loaded('soap') ) {
				require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
			} else {
				add_action('admin_notices', array( $this, 'add_admin_notice_for_soap' ) );
			}
			include_once AFB2B_PLUGIN_DIR . 'additional_classes/afb2b_role_front_ajax_controller.php';

			include_once AFB2B_PLUGIN_DIR . 'woocommerce-request-a-quote/class-addify-request-for-quote.php';
			include_once AFB2B_PLUGIN_DIR . 'products-visibility-by-user-roles/addify_product_visibility.php';
			include_once AFB2B_PLUGIN_DIR . 'addify-order-restrictions/addify-order-restrictions.php';
			include_once AFB2B_PLUGIN_DIR . 'addify-cart-based-discount/class-discount-cart-main.php';
			include_once AFB2B_PLUGIN_DIR . 'woocommerce-tax-exempt-plugin/addify_tax_exempt.php';
			include_once AFB2B_PLUGIN_DIR . 'user-registration-plugin-for-woocommerce/addify-registration-addon-main.php';
			include_once AFB2B_PLUGIN_DIR . 'addify-user-role-editor/af-user-role-editor.php';

			if (is_admin() ) {
				include_once AFB2B_PLUGIN_DIR . 'class_afb2b_admin.php';
				include_once AFB2B_PLUGIN_DIR . 'additional_classes/class_afb2b_role_based_pricing_admin.php';
				include_once AFB2B_PLUGIN_DIR . 'additional_classes/class-afb2b-shipping-front.php';
			} else {
	
				
				include_once AFB2B_PLUGIN_DIR . 'additional_classes/class_af_tax_display.php';
				include_once AFB2B_PLUGIN_DIR . 'additional_classes/class_afb2b_role_based_pricing_front.php';
				include_once AFB2B_PLUGIN_DIR . 'additional_classes/class-afb2b-shipping-front.php';
				include_once AFB2B_PLUGIN_DIR . 'additional_classes/class-afb2b-payments-front.php';
			}

			//HOPS compatibility
			add_action('before_woocommerce_init', array( $this, 'afb2b_HOPS_Compatibility' ));

			add_action( 'plugins_loaded', array( $this, 'afb2b_checks' ) ); 

			//Change menu-order
			//add_filter( 'custom_menu_order', array( $this, 'afb2b_submenu_order' ));
		}

		// public function afb2b_submenu_order( $menu_ord ) {
		//  global $submenu;

		//  $af_sub = $submenu;

		//  //echo $submenu['addify-b2b'];

		//  // Enable the next line to see all menu orders
		//  // echo '<pre>'.print_r($submenu,true).'</pre>';
		//  // exit;

		//  $arr                  = array();
		//  $arr[]                = $af_sub['addify-b2b'][6]; //Registration Fields
		//  $arr[]                = $af_sub['addify-b2b'][4]; //All Quotes
		//  $arr[]                = $af_sub['addify-b2b'][2]; //Quote Rules
		//  $arr[]                = $af_sub['addify-b2b'][3]; //Quote Fields
		//  $arr[]                = $af_sub['addify-b2b'][1]; //Role Base Pricing
		//  $arr[]                = $af_sub['addify-b2b'][8]; //Import Role Base Pricing
		//  $arr[]                = $af_sub['addify-b2b'][0]; //Order Restriction   
		//  $arr[]                = $af_sub['addify-b2b'][5]; //Cart Discounts
		//  $arr[]                = $af_sub['addify-b2b'][7]; //User Role Manager
		//  $arr[]                = $af_sub['addify-b2b'][9]; //Settings
		//  $af_sub['addify-b2b'] = $arr;

		//  return $menu_ord;
		// }

		public function afb2b_checks() {

			// Check for multisite.
			if ( ! is_multisite() && ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

				add_action( 'admin_notices', array( $this, 'afb2b_admin_notice' ));
			} 
		}


		public function afb2b_admin_notice() {

			$afb2b_allowed_tags = array(
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

			// Deactivate the plugin
			deactivate_plugins(__FILE__);

			$afb2b_woo_check = '<div id="message" class="error">
				<p><strong>B2B for WooCommerce Plugin is inactive.</strong> The <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce plugin</a> must be active for this plugin to work. Please install &amp; activate WooCommerce »</p></div>';
			echo wp_kses(__($afb2b_woo_check, 'addify_b2b'), $afb2b_allowed_tags);
		}


		public function afb2b_HOPS_Compatibility() {

			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		}
		
		
		public function add_admin_notice_for_soap() {
			?>
			<div id="message" class="error">
				<p>
					<a href="https://woocommerce.com/products/b2b-for-woocommerce/">
						<?php esc_html_e('B2B for WooComerce:', 'addify_b2b'); ?>
					</a>
					<?php esc_html_e(' Kindly activate soap application from your server to validate VIES VAT number validation.', 'addify_b2b'); ?>
				</p>
			</div>
			<?php
		}



		public function afb2b_global_constents_vars() {

			if (!defined('AFB2B_URL') ) {
				define('AFB2B_URL', plugin_dir_url(__FILE__));
			}

			if (!defined('AFB2B_BASENAME') ) {
				define('AFB2B_BASENAME', plugin_basename(__FILE__));
			}

			if (! defined('AFB2B_PLUGIN_DIR') ) {
				define('AFB2B_PLUGIN_DIR', plugin_dir_path(__FILE__));
			}
		}

		public function afb2b_init() {

			if (function_exists('load_plugin_textdomain') ) {
				load_plugin_textdomain('addify_b2b', false, dirname(plugin_basename(__FILE__)) . '/languages/');
			}
		}

		public function afb2b_add_prod_visbility_page() {

			$upload_url = wp_upload_dir();


			if (!is_dir($upload_url['basedir'] . '/addify_registration_uploads')) {
				mkdir($upload_url['basedir'] . '/addify_registration_uploads', 0777, true);
			}

			//Product Visibility error page
			if (null == get_page_by_path('af-product-visibility')) {

				$new_page = array(
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'post_author'    => 1,
					'post_name'      => esc_html__('af-product-visibility', 'addify_b2b'),
					'post_title'     => esc_html__('Products Visibility', 'addify_b2b'),
					'post_content'   => '[addify-product-visibility-page]',
					'post_parent'    => 0,
					'comment_status' => 'closed',
				);

				$page_id = wp_insert_post($new_page);

				update_option('addify_pvu_page_id', $page_id);
			} else {
				$page_id = get_page_by_path('af-product-visibility');
				update_option('addify_pvu_page_id', $page_id->ID);
			}


			//Customer User Registration settings.
			$this->afreg_insert_default_fields();
			$this->afreg_insert_emails_default_text();

			//Tax Exempt default settings.
			$this->aftax_installation();

			//Request a Quote default settings.
			$this->afrfq_register_settings();

			//role based pricing design templates settings
			$this->afb2b_role_pricing_template_settings();
		}

		public function afreg_insert_default_fields() {

			//First Name
			$first_name_posts = get_page_by_path( 'first_name', OBJECT, 'def_reg_fields' );
			if ('' == $first_name_posts) {
				$first_name_post = array(
					'post_title'  => 'First Name',
					'post_name'   => 'first_name',
					'post_type'   => 'def_reg_fields',
					'post_status' => 'unpublish',
					'menu_order'  => 1,        
				);
				$first_name_id   = wp_insert_post($first_name_post);
				update_post_meta($first_name_id, 'placeholder', 'Enter your first name');
				update_post_meta($first_name_id, 'is_required', 1);
				update_post_meta($first_name_id, 'width', 'half');
				update_post_meta($first_name_id, 'type', 'text');
				update_post_meta($first_name_id, 'message', '');
			}

			//Last Name
			$last_name_posts = get_page_by_path( 'last_name', OBJECT, 'def_reg_fields' );
			if ('' == $last_name_posts) {
				$last_name_post = array(
					'post_title'  => 'Last Name',
					'post_name'   => 'last_name',
					'post_type'   => 'def_reg_fields',
					'post_status' => 'unpublish',
					'menu_order'  => 2,        
				);
				$last_name_id   = wp_insert_post($last_name_post);
				update_post_meta($last_name_id, 'placeholder', 'Enter your last name');
				update_post_meta($last_name_id, 'is_required', 1);
				update_post_meta($last_name_id, 'width', 'half');
				update_post_meta($last_name_id, 'type', 'text');
				update_post_meta($last_name_id, 'message', '');
			}

			//Company
			$company_posts = get_page_by_path( 'billing_company', OBJECT, 'def_reg_fields' );
			if ('' == $company_posts) {
				$company_post = array(
					'post_title'  => 'Company',
					'post_name'   => 'billing_company',
					'post_type'   => 'def_reg_fields',
					'post_status' => 'unpublish',
					'menu_order'  => 3,       
				);
				$company_id   = wp_insert_post($company_post);
				update_post_meta($company_id, 'placeholder', 'Enter your company');
				update_post_meta($company_id, 'is_required', 0);
				update_post_meta($company_id, 'width', 'full');
				update_post_meta($company_id, 'type', 'text');
				update_post_meta($company_id, 'message', '');
			}


			//Country
			$country_posts = get_page_by_path( 'billing_country', OBJECT, 'def_reg_fields' );
			if ('' == $country_posts) {
				$country_post = array(
					'post_title'  => 'Country',
					'post_name'   => 'billing_country',
					'post_type'   => 'def_reg_fields',
					'post_status' => 'unpublish',
					'menu_order'  => 4,       
				);
				$country_id   = wp_insert_post($country_post);
				update_post_meta($country_id, 'placeholder', 'Select your country');
				update_post_meta($country_id, 'is_required', 1);
				update_post_meta($country_id, 'width', 'full');
				update_post_meta($country_id, 'type', 'select');
				update_post_meta($country_id, 'message', '');
			}

			//Address Line 1
			$address_1_posts = get_page_by_path( 'billing_address_1', OBJECT, 'def_reg_fields' );
			if ('' == $address_1_posts) {
				$address_1_post = array(
					'post_title'  => 'Street Address',
					'post_name'   => 'billing_address_1',
					'post_type'   => 'def_reg_fields',
					'post_status' => 'unpublish',
					'menu_order'  => 5,       
				);
				$address_1_id   = wp_insert_post($address_1_post);
				update_post_meta($address_1_id, 'placeholder', 'House number and street name');
				update_post_meta($address_1_id, 'is_required', 1);
				update_post_meta($address_1_id, 'width', 'full');
				update_post_meta($address_1_id, 'type', 'text');
				update_post_meta($address_1_id, 'message', '');
			}


			//Address Line 2
			$address_2_posts = get_page_by_path( 'billing_address_2', OBJECT, 'def_reg_fields' );
			if ('' == $address_2_posts) {
				$address_2_post = array(
					'post_title'  => 'Address 2',
					'post_name'   => 'billing_address_2',
					'post_type'   => 'def_reg_fields',
					'post_status' => 'unpublish',
					'menu_order'  => 6,       
				);
				$address_2_id   = wp_insert_post($address_2_post);
				update_post_meta($address_2_id, 'placeholder', 'Apartment, suite, unit etc. (optional)');
				update_post_meta($address_2_id, 'is_required', 0);
				update_post_meta($address_2_id, 'width', 'full');
				update_post_meta($address_2_id, 'type', 'text');
				update_post_meta($address_2_id, 'message', '');
			}

			//State
			$state_posts = get_page_by_path( 'billing_state', OBJECT, 'def_reg_fields' );
			if ('' == $state_posts) {
				$state_post = array(
					'post_title'  => 'State / County',
					'post_name'   => 'billing_state',
					'post_type'   => 'def_reg_fields',
					'post_status' => 'unpublish',
					'menu_order'  => 7,       
				);
				$state_id   = wp_insert_post($state_post);
				update_post_meta($state_id, 'placeholder', 'Select your state / county');
				update_post_meta($state_id, 'is_required', 1);
				update_post_meta($state_id, 'width', 'full');
				update_post_meta($state_id, 'type', 'select');
				update_post_meta($state_id, 'message', '');
			}


			//City
			$city_posts = get_page_by_path( 'billing_city', OBJECT, 'def_reg_fields' );
			if ('' == $city_posts) {
				$city_post = array(
					'post_title'  => 'Town / City',
					'post_name'   => 'billing_city',
					'post_type'   => 'def_reg_fields',
					'post_status' => 'unpublish',
					'menu_order'  => 8,       
				);
				$city_id   = wp_insert_post($city_post);
				update_post_meta($city_id, 'placeholder', 'Enter your city');
				update_post_meta($city_id, 'is_required', 1);
				update_post_meta($city_id, 'width', 'half');
				update_post_meta($city_id, 'type', 'text');
				update_post_meta($city_id, 'message', '');
			}


			//Post Code
			$postcode_posts = get_page_by_path( 'billing_postcode', OBJECT, 'def_reg_fields' );
			if ('' == $postcode_posts) {
				$postcode_post = array(
					'post_title'  => 'Postcode / Zip',
					'post_name'   => 'billing_postcode',
					'post_type'   => 'def_reg_fields',
					'post_status' => 'unpublish',
					'menu_order'  => 9,      
				);
				$postcode_id   = wp_insert_post($postcode_post);
				update_post_meta($postcode_id, 'placeholder', 'Enter your postcode / zip');
				update_post_meta($postcode_id, 'is_required', 1);
				update_post_meta($postcode_id, 'width', 'half');
				update_post_meta($postcode_id, 'type', 'text');
				update_post_meta($postcode_id, 'message', '');
			}

			//Phone
			$phone_posts = get_page_by_path( 'billing_phone', OBJECT, 'def_reg_fields' );
			if ('' == $phone_posts) {
				$phone_post = array(
					'post_title'  => 'Phone',
					'post_name'   => 'billing_phone',
					'post_type'   => 'def_reg_fields',
					'post_status' => 'unpublish',
					'menu_order'  => 10,      
				);
				$phone_id   = wp_insert_post($phone_post);
				update_post_meta($phone_id, 'placeholder', 'Enter your phone');
				update_post_meta($phone_id, 'is_required', 1);
				update_post_meta($phone_id, 'width', 'full');
				update_post_meta($phone_id, 'type', 'tel');
				update_post_meta($phone_id, 'message', '');
			}
		}

		public function aftax_installation() {

			update_option('af_tax_flush_rules', 'yes' );

			//Default Email subject and messages
			//Tax information submission message to admin
			if (empty(get_option('aftax_admin_email_message'))) {

				$info_msg_admin = '
				<p>{user_name} have submitted tax exemption form with the following details.</p>
				<p>{form_data}</p>
				<p>Approve Exemption Request:<br />
				{approve_link}
				</p>
				<p>Disapprove Exemption Request:<br />
				{disapprove_link}
				</p>
				';
				update_option('aftax_admin_email_message', $info_msg_admin);

			}

			if (empty(get_option('aftax_customer_email_message'))) {

				$info_msg_user = '
				<p>Hi {user_name},</p>
				<p>Thank you for submitted the tax exemption form. We have received your tax exemption request with the following details,</p>
				<p>{form_data}</p>
				
				';
				update_option('aftax_customer_email_message', $info_msg_user);

			}

			if (empty(get_option('aftax_admin_approve_tax_info_email_message'))) {

				$info_approve_msg_admin = '
				<p>The tax exemption has been approved for the following user,</p>
				<p>
				{user_name}<br />
				{customer_email}<br />
				{form_data}
				</p>
				
				';
				update_option('aftax_admin_approve_tax_info_email_message', $info_approve_msg_admin);

			}

			if (empty(get_option('aftax_approve_tax_info_email_message'))) {

				$info_approve_msg_user = '
				<p>Hi {user_name},</p>
				<p>Your tax exemption request has been approved. Below are the details of your tax exemption request,</p>
				<p>{form_data}</p>
				
				';
				update_option('aftax_approve_tax_info_email_message', $info_approve_msg_user);

			}

			if (empty(get_option('aftax_disapprove_tax_info_email_message'))) {

				$info_disapprove_msg_user = '
				<p>Hi {user_name},</p>
				<p>We regret to inform you that your tax exemption request has been declined. Below are the are details of your tax exemption request. If you feel this is mistake, please feel free to reach out to us.</p>
				<p>{form_data}</p>
				
				';
				update_option('aftax_disapprove_tax_info_email_message', $info_disapprove_msg_user);

			}

			if (empty(get_option('aftax_expire_tax_info_email_message'))) {

				$info_expire_msg_admin = '
				<p>Tax exemption has expired for {user_name}. Below are the details of the users tax exemption form,</p>
				<p>{form_data}</p>
				
				';
				update_option('aftax_expire_tax_info_email_message', $info_expire_msg_admin);

			}

			if (empty(get_option('aftax_customer_expire_tax_info_email_message'))) {

				$info_expire_msg_user = '
				<p>Hi {user_name},</p>
				<p>Your tax exemption has expired. Please provide the upload the updated tax exemption details from My Account > Tax Exemption. Below are the details of your existing tax exemption request. If you believe it’s a mistake, please reach out to us.</p>
				<p>{form_data}</p>
				
				';
				update_option('aftax_customer_expire_tax_info_email_message', $info_expire_msg_user);

			}
		}

		public function afrfq_register_settings() {

			if ( null === get_page_by_path( 'request-a-quote' ) ) {

				$new_page = array(
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'post_author'    => 1,
					'post_name'      => esc_html__( 'request-a-quote', 'addify_rfq' ),
					'post_title'     => esc_html__( 'Request a Quote', 'addify_rfq' ),
					'post_content'   => '[addify-quote-request-page]',
					'post_parent'    => 0,
					'comment_status' => 'closed',
				);

				$page_id = wp_insert_post( $new_page );

				update_option( 'addify_atq_page_id', $page_id );
			} else {
				$page_id = get_page_by_path( 'request-a-quote' );
				update_option( 'addify_atq_page_id', $page_id );
			}


			$quote_fields_obj = new AF_R_F_Q_Quote_Fields();
			$quote_fields_obj->afrfq_migrate_fields_enabled_to_rules();

			if ( empty( get_option( 'afrfq_admin_email' ) ) ) {
				update_option( 'afrfq_admin_email', get_option( 'admin_email' ), true );
			}

			if ( empty( get_option( 'afrfq_emails' ) ) ) {
				// update_option( 'afrfq_emails', $quote_emails, true );
				$this->afrfq_email_default_values();
			}

			if ( empty( get_option( 'afrfq_success_message' ) ) ) {
				update_option( 'afrfq_success_message', 'Your Quote Submitted Successfully.', true );
			}

			if ( empty( get_option( 'afrfq_pro_success_message' ) ) ) {
				update_option( 'afrfq_pro_success_message', 'Product Added to Quote Successfully.', true );
			}

			if ( empty( get_option( 'afrfq_view_button_message' ) ) ) {
				update_option( 'afrfq_view_button_message', 'View Quote', true );
			}

			if ( empty( get_option( 'afrfq_basket_option' ) ) ) {
				update_option( 'afrfq_basket_option', 'dropdown', true );
			}

			if ( empty( get_option( 'afrfq_backrgound_color' ) ) ) {
				update_option( 'afrfq_backrgound_color', '#d6e4f1', true );
			}

			if ( empty( get_option( 'afrfq_backrgound_color' ) ) ) {
				update_option( 'afrfq_backrgound_color', '#000000', true );
			}
			if ( empty( get_option( 'afrfq_table_header_color' ) ) ) {
				update_option( 'afrfq_table_header_color', '#ffffff', true );
			}
			if ( empty( get_option( 'afrfq_submit_button_fg_color' ) ) ) {
				update_option( 'afrfq_submit_button_fg_color', '#ffffff', true );
			}
		}


		public function afb2b_custom_post_types() {

			//Role Based Pricing Custom Post Type

			$labels2 = array(
				'name'                => __('Role Based Pricing Rules', 'addify_b2b'),
				'singular_name'       => __('Role Based Pricing Rules', 'addify_b2b'),
				'add_new'             => __('Add New Rule', 'addify_b2b'),
				'add_new_item'        => __('Add Rule', 'addify_b2b'),
				'edit_item'           => __('Edit Rule', 'addify_b2b'),
				'new_item'            => __('New Rule', 'addify_b2b'),
				'view_item'           => __('View Rule', 'addify_b2b'),
				'search_items'        => __('Search Rule', 'addify_b2b'),
				'exclude_from_search' => true,
				'not_found'           => __('No rule found', 'addify_b2b'),
				'not_found_in_trash'  => __('No rule found in trash', 'addify_b2b'),
				'parent_item_colon'   => '',
				'all_items'           => __('Role Based Pricing', 'addify_b2b'),
				'menu_name'           => __('Role Based Pricing', 'addify_b2b'),
			);
	
			$args2 = array(
				'labels'             => $labels2,
				'menu_icon'          => '',
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => 'addify-b2b',
				'query_var'          => true,
				'capability_type'    => 'page', 
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 90,
				'rewrite'            => array(
					'slug'       => 'csp-rule',
					'with_front' =>false,
				),
				'supports'           => array( 'title' ),
			);
	
			register_post_type('csp_rules', $args2);
		}



		public function afreg_insert_emails_default_text() {

			if (empty(get_option('afreg_admin_email_text'))) {

				$afreg_admin_email_text = '<p>Hi,</p>
<p>A new user has registered to your website. Here are the details,</p>
<p>{customer_details}</p>
<p><em>To disable this email, go to WooCommerce &gt; Settings &gt; Emails &gt; Addify Registration New User Email Admin.</em></p>';

				update_option('afreg_admin_email_text', $afreg_admin_email_text);

			}


			if (empty(get_option('afreg_update_user_admin_email_text'))) {

				$afreg_update_user_admin_email_text = '<p>Hi,</p>
<p>A new user has just updated their information from My Account Page.  Here are the details,</p>
<p>{customer_details}</p>
<p><em>To disable this email, go to WooCommerce &gt; Settings &gt; Emails &gt; Addify Registration Update User Email Admin.</em></p>';

				update_option('afreg_update_user_admin_email_text', $afreg_update_user_admin_email_text);

			}


			if (empty(get_option('afreg_user_email_text'))) {

				$afreg_user_email_text = '<p>Hi,</p>
<p>Thank you for creating new account. Here is glimpse of the details you have submitted.</p>
<p>{customer_details} </p>
<p>Thank you,</p>';

				update_option('afreg_user_email_text', $afreg_user_email_text);

			}



			if (empty(get_option('afreg_pending_approval_email_text'))) {

				$afreg_pending_approval_email_text = '<p>Hi,</p>
<p><br />Thank you for showing interest. To keep the community clean we like to review the new applicants. You will be notified VIA email about the approval status. Here is the glimpse of your details,</p>
<p>{customer_details}<br /><br />Thank you,</p>';

				update_option('afreg_pending_approval_email_text', $afreg_pending_approval_email_text);

			}



			if (empty(get_option('afreg_approved_email_text'))) {

				$afreg_approved_email_text = '<p>Hi,Thank you for showing patience. You account has been approved.</p>
<p>Thank you,</p>';

				update_option('afreg_approved_email_text', $afreg_approved_email_text);

			}



			if (empty(get_option('afreg_disapproved_email_text'))) {

				$afreg_disapproved_email_text = '<p>Hi,</p>
<p>We regret to inform you that your account has been disapproved. If you think its a mistake, please reach out to us.</p>
<p>Thank you,</p>';

				update_option('afreg_disapproved_email_text', $afreg_disapproved_email_text);

			}
		}

		public function afb2b_role_pricing_template_settings() {

			if (!get_option('csp_min_qty_error_msg')) {
				update_option( 'csp_min_qty_error_msg', 'Kindly enter quantity greater than %u' );
			}
			if (!get_option('csp_max_qty_error_msg')) {
				update_option( 'csp_max_qty_error_msg', 'Kindly enter quantity less than %u' );
			}
			if (!get_option('csp_update_cart_error_msg')) {
				update_option( 'csp_update_cart_error_msg', 'Kindly enter value between %min and  %max.' );
			}

			//general settings
			if (!get_option('afb2b_role_pricing_design_type')) {
				update_option('afb2b_role_pricing_design_type', 'default_template');
			}
			if (!get_option('afb2b_role_enable_template_heading')) {
				update_option('afb2b_role_enable_template_heading', 'yes');
			}
			if (!get_option('afb2b_role_template_heading_text')) {
				update_option('afb2b_role_template_heading_text', 'Select your Deal');
			}
			if (!get_option('afb2b_role_template_heading_text_font_size')) {
				update_option('afb2b_role_template_heading_text_font_size', '28');
			}
			if (!get_option('afb2b_role_enable_template_icon')) {
				update_option('afb2b_role_enable_template_icon', 'yes');
			}
			if (!get_option('afb2b_role_template_font_family')) {
				update_option('afb2b_role_template_font_family', '');
			}
			
			//default template settings

			if (!get_option('afb2b_role_default_template_text_color')) {
				update_option('afb2b_role_default_template_text_color', '#6d6d6d');
			}
			if (!get_option('afb2b_role_default_template_font_size')) {
				update_option('afb2b_role_default_template_font_size', '18');
			}
			
			//table settings
			if (!get_option('afb2b_role_table_header_color')) {
				update_option('afb2b_role_table_header_color', '#FFFFFF');
			}
			if (!get_option('afb2b_role_table_header_text_color')) {
				update_option('afb2b_role_table_header_text_color', '#000000');
			}
			if (!get_option('afb2b_role_table_odd_rows_color')) {
				update_option('afb2b_role_table_odd_rows_color', '#FFFFFF');
			}
			if (!get_option('afb2b_role_table_odd_rows_text_color')) {
				update_option('afb2b_role_table_odd_rows_text_color', '#000000');
			}
			if (!get_option('afb2b_role_table_even_rows_color')) {
				update_option('afb2b_role_table_even_rows_color', '#FFFFFF');
			}
			if (!get_option('afb2b_role_table_even_rows_text_color')) {
				update_option('afb2b_role_table_even_rows_text_color', '#000000');
			}
			if (!get_option('afb2b_role_enable_table_border')) {
				update_option('afb2b_role_enable_table_border', 'yes');
			}
			if (!get_option('afb2b_role_table_border_color')) {
				update_option('afb2b_role_table_border_color', '#CFCFCF');
			}
			if (!get_option('afb2b_role_table_header_font_size')) {
				update_option('afb2b_role_table_header_font_size', '18');
			}
			if (!get_option('afb2b_role_table_rows_font_size')) {
				update_option('afb2b_role_table_rows_font_size', '16');
			}
			
			// List settings
			if (!get_option('afb2b_role_list_border_color')) {
				update_option('afb2b_role_list_border_color', '#95B0EE');
			}
			if (!get_option('afb2b_role_list_background_color')) {
				update_option('afb2b_role_list_background_color', '#FFFFFF');
			}
			if (!get_option('afb2b_role_list_text_color')) {
				update_option('afb2b_role_list_text_color', '#000000');
			}
			if (!get_option('afb2b_role_selected_list_background_color')) {
				update_option('afb2b_role_selected_list_background_color', '#DFEBFF');
			}
			if (!get_option('afb2b_role_selected_list_text_color')) {
				update_option('afb2b_role_selected_list_text_color', '#000000');
			}
		
			//card settings
			if (!get_option('afb2b_role_card_border_color')) {
				update_option('afb2b_role_card_border_color', '#A3B39E');
			}
			if (!get_option('afb2b_role_card_background_color')) {
				update_option('afb2b_role_card_background_color', '#FFFFFF');
			}
			if (!get_option('afb2b_role_card_text_color')) {
				update_option('afb2b_role_card_text_color', '#000000');
			}
			if (!get_option('afb2b_role_selected_card_border_color')) {
				update_option('afb2b_role_selected_card_border_color', '#27CA34');
			}
			if (!get_option('afb2b_role_enable_card_sale_tag')) {
				update_option('afb2b_role_enable_card_sale_tag', 'yes');
			}
			if (!get_option('afb2b_role_sale_tag_background_color')) {
				update_option('afb2b_role_sale_tag_background_color', '#FF0000');
			}
			if (!get_option('afb2b_role_sale_tag_text_color')) {
				update_option('afb2b_role_sale_tag_text_color', '#FFFFFF');
			}
		}

		public function afrfq_email_default_values() {
			$emails = array(
				'af_admin' => array(
					'enable'  => '',
					'subject' => 'You’ve received a new quote request – #{quote_id}',
					'heading' => 'New Quote Received',
					'message' => 'Hi,<br><br>
									A new quote request has been submitted on your store.<br>
									Quote ID: {quote_id}<br>
									Customer Name: {user_name}<br><br>
									Please review this quote in your WooCommerce dashboard to take the next steps.<br>
									Thank you,<br><br>',
				),
			
				'af_pending' => array(
					'enable'  => '',
					'subject' => 'Your quote request has been received – #{quote_id}',
					'heading' => 'We’ve received your quote request',
					'message' => 'Hi {user_name},<br><br>
									Thank you for your interest in our products.<br>
									We’ve received your quote request (ID: {quote_id}) and our team will review it shortly.<br>
									You’ll be notified once it’s processed.<br><br>
									Best regards,<br><br>',
				),
			
				'af_in_process' => array(
					'enable'  => '',
					'subject' => 'Your quote #{quote_id} is being reviewed',
					'heading' => 'Quote Under Review',
					'message' => 'Hi {user_name},<br><br>
									Your quote request (ID: {quote_id}) is currently being reviewed by our team.<br>
									We’ll get back to you soon with a personalized offer.<br><br>
									Thank you for your patience!<br><br>',
				),
			
				'af_accepted' => array(
					'enable'  => '',
					'subject' => 'Your quote #{quote_id} has been accepted',
					'heading' => 'Quote Accepted',
					'message' => 'Hi {user_name},<br><br>
									Good news! Your quote request (ID: {quote_id}) has been accepted.<br>
									You can now proceed to confirm your order from your account dashboard.<br><br>',
				),
			
				'af_admin_conv' => array(
					'enable'  => '',
					'subject' => 'Quote #{quote_id} converted to an order',
					'heading' => 'Quote Converted to Order',
					'message' => 'Hi Admin,<br><br>
									The quote request #{quote_id} from {user_name} has been successfully converted to an order.<br>
									You can view the order details in your WooCommerce dashboard.<br><br>
									Regards,<br><br>',
				),
			
				'af_converted' => array(
					'enable'  => '',
					'subject' => 'Your quote #{quote_id} has been converted to an order',
					'heading' => 'Order Created from Your Quote',
					'message' => 'Hi {user_name},<br><br>
									Your quote request (ID: {quote_id}) has been successfully converted into an order.<br>
									You can complete the payment or track the order from your account dashboard.<br><br>',
				),
			
				'af_admin_converted_to_cart' => array(
					'enable'  => '',
					'subject' => 'Quote #{quote_id} converted to cart (Admin)',
					'heading' => 'Quote Converted to Cart',
					'message' => 'Hi Admin,<br><br>
									The quote #{quote_id} has been converted to a cart by {user_name}.<br>
									You can review the details in your WooCommerce dashboard.<br><br>',
				),
			
				'af_converted_to_cart' => array(
					'enable'  => '',
					'subject' => 'Quote #{quote_id} converted to cart',
					'heading' => 'Quote Converted to Cart',
					'message' => 'Hi {user_name},<br><br>
									Your quote request (ID: {quote_id}) has been converted to a shopping cart.<br>
									You can review the cart details and proceed to checkout using the button below.<br><br>',
				),
			
				'af_declined' => array(
					'enable'  => '',
					'subject' => 'Your quote #{quote_id} could not be processed',
					'heading' => 'Quote Declined',
					'message' => 'Hi {user_name},<br><br>
									We regret to inform you that your quote request (ID: {quote_id}) cannot be processed at this time due to the unavailability of one or more products.<br>
									You can contact our support team for alternatives or product recommendations.<br><br>
									Thank you for understanding,<br><br>',
				),
			
				'af_cancelled' => array(
					'enable'  => '',
					'subject' => 'Your quote #{quote_id} has been canceled',
					'heading' => 'Quote Canceled',
					'message' => 'Hi {user_name},<br><br>
									Your quote request (ID: {quote_id}) has been canceled.<br>
									If this was done by mistake or you’d like to request a new quote, please visit our store and submit a new one.<br><br>
									Thank you,<br><br>',
				),
			);
			
			update_option( 'afrfq_emails', $emails );
		}
	}

	new Addify_B2B_Plugin();

}
