<?php 
/**
 * Custom User Registration Fields for WooCommerce
 */

if (! defined('WPINC') ) {
	die;
}

defined( 'ABSPATH' ) || exit;
if (!class_exists('Addify_Registration_Fields_Addon') ) { 

	class Addify_Registration_Fields_Addon {

		public function __construct() {

			$this->afreg_global_constents_vars();

			add_action( 'init', array( $this, 'afreg_custom_post_type' ));
			

			include_once AFREG_PLUGIN_DIR . '/includes/post-types/class-af-reg-fields-controller.php';
			include_once AFREG_PLUGIN_DIR . '/includes/general-functions.php';

			if (is_admin() ) {
				include_once AFREG_PLUGIN_DIR . 'admin/class-afreg-fields-admin.php';
			} else {
				include_once AFREG_PLUGIN_DIR . 'front/class-afreg-fields-front.php';
			}

			add_action('wp_ajax_get_states', array( $this, 'get_states' ));
			add_action('wp_ajax_nopriv_get_states', array( $this, 'get_states' ));  

			add_filter( 'woocommerce_email_classes', array( $this, 'afreg_emails' ), 90, 1 );   

			add_action( 'init', array( $this, 'load_rest_api' ) );  
		}

		

		public function load_rest_api() {

			if ( defined('WC_PLUGIN_FILE') ) {
				require AFREG_PLUGIN_DIR . 'includes/rest-api/Server.php';
				\Addify\user_registration_fields\RestApi\Server::instance()->init();
			}
		}

		public function afreg_global_constents_vars() {
			
			if (!defined('AFREG_URL') ) {
				define('AFREG_URL', plugin_dir_url(__FILE__));
			}

			if (!defined('AFREG_BASENAME') ) {
				define('AFREG_BASENAME', plugin_basename(__FILE__));
			}

			if (! defined('AFREG_PLUGIN_DIR') ) {
				define('AFREG_PLUGIN_DIR', plugin_dir_path(__FILE__));
			}
		}
		

		public function afreg_custom_post_type() {

			$labels = array(
				'name'                => esc_html__('Registration Fields', 'addify_b2b'),
				'singular_name'       => esc_html__('Registration Field', 'addify_b2b'),
				'add_new'             => esc_html__('Add New Field', 'addify_b2b'),
				'add_new_item'        => esc_html__('Add New Field', 'addify_b2b'),
				'edit_item'           => esc_html__('Edit Registration Field', 'addify_b2b'),
				'new_item'            => esc_html__('New Registration Field', 'addify_b2b'),
				'view_item'           => esc_html__('View Registration Field', 'addify_b2b'),
				'search_items'        => esc_html__('Search Registration Field', 'addify_b2b'),
				'exclude_from_search' => true,
				'not_found'           => esc_html__('No registration field found', 'addify_b2b'),
				'not_found_in_trash'  => esc_html__('No registration field found in trash', 'addify_b2b'),
				'parent_item_colon'   => '',
				'all_items'           => esc_html__('Registration Fields', 'addify_b2b'),
				'menu_name'           => esc_html__('Registration Fields', 'addify_b2b'),
			);

			$args = array(
				'labels'             => $labels,
				'menu_icon'          => '',
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => 'addify-b2b',
				'query_var'          => true,
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 30,
				'rewrite'            => array(
					'slug'       => 'addify_reg',
					'with_front' =>false,
				),
				'supports'           => array( 'title' ),
			);

			register_post_type( 'afreg_fields', $args );
		}


		public function get_states() {

			if (isset($_POST['nonce']) && '' != $_POST['nonce']) {

				$nonce = sanitize_text_field( $_POST['nonce'] );
			} else {
				$nonce = 0;
			}

			$country  = '';
			$af_state = '';

			if ( ! wp_verify_nonce( $nonce, 'afreg-ajax-nonce' ) ) {
				wp_die( esc_html__('Security Violated', 'addify_b2b') );
			}

			if (!empty($_POST['country'])) {

				$country = sanitize_text_field($_POST['country']);
			}

			if (!empty($_POST['width'])) {

				$width = sanitize_text_field($_POST['width']);
			}

			if (!empty($_POST['name'])) {

				$name = sanitize_text_field($_POST['name']);
			}

			if (!empty($_POST['label'])) {

				$label = sanitize_text_field($_POST['label']);
			}

			if (!empty($_POST['message'])) {

				$message = sanitize_text_field($_POST['message']);
			}

			if (!empty($_POST['required'])) {

				$required = sanitize_text_field($_POST['required']);
			}

			if (!empty($_POST['af_state'])) {

				$af_state = sanitize_text_field($_POST['af_state']);
			}
			
			if (!empty($_POST['placeholder'])) {
				$placeholder = __(sanitize_text_field($_POST['placeholder']), 'addify_b2b');
			} else {
				$placeholder = __('Select a county / state...', 'addify_b2b');
			}

			global $woocommerce;
			$countries_obj = new WC_Countries();

			if (!empty($countries_obj)) {
				$states = $countries_obj->get_states( $country );
			}
			
			if (!empty($states) && !empty($country)) {
				?>

				<p id="dropdown_state" class="form-row <?php echo esc_attr($width); ?>">
					<label for="<?php echo esc_attr($name); ?>"><?php echo esc_html__( $label, 'addify_b2b' ); ?> 
					<?php 
					if (1 == $required) {
						?>
						<span class="required">*</span> <?php } ?>
					</label>

					<select class="js-example-basic-single" name="billing_state">
						<option value=""><?php echo esc_attr($placeholder); ?></option>

						<?php foreach ($states as $key => $value) { ?>
							<option value="<?php echo esc_attr($key); ?>" <?php echo selected($af_state, $key); ?>><?php echo esc_attr($value); ?></option>
						<?php } ?>
					</select>

					<?php if (isset($message) && ''!=$message) { ?>
						<span style="width:100%;float: left"><?php echo esc_html__($message, 'addify_b2b'); ?></span>
					<?php } ?>
				</p>

			<?php } elseif (is_array($states) && !empty($country)) { ?>
				
				<p id="dropdown_state" class="form-row <?php echo esc_attr($width); ?>">
					<input type="hidden" name="billing_state" value="<?php echo esc_attr($country); ?>" />
				</p>



			<?php } else { ?>
				<label for="<?php echo esc_attr($name); ?>"><?php echo esc_html__( $label, 'addify_b2b' ); ?> 
				<?php 
				if (1 == $required) {
					?>
					<span class="required">*</span> <?php } ?>
				</label>
				<p id="dropdown_state" class="form-row <?php echo esc_attr($width); ?>">
					<input type="text" name="billing_state" value="<?php echo esc_attr($af_state); ?>" />
				</p>

			<?php } ?>

			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('.js-example-basic-single').select2();
				});
			</script>


			<?php 
			die();
		}

		public function afreg_emails( $emails ) {

			require_once AFREG_PLUGIN_DIR . 'classes/afreg-admin-email-class.php';
			$emails['afreg_admin_email_new_user'] = new Addify_Registration_Fields_Admin_Email();
			$emails['afreg_admin_email_new_user']->init_form_fields();


			require_once AFREG_PLUGIN_DIR . 'classes/afreg-admin-email-class-update-account.php';
			$emails['afreg_admin_email_update_user'] = new Addify_Registration_Fields_Admin_Email_Update_Account();
			$emails['afreg_admin_email_update_user']->init_form_fields();


			require_once AFREG_PLUGIN_DIR . 'classes/afreg-user-email-class.php';
			$emails['afreg_user_email_new_user'] = new Addify_Registration_Fields_User_Email();


			require_once AFREG_PLUGIN_DIR . 'classes/afreg-pending-user-email-class.php';
			$emails['afreg_pending_user_email_user'] = new Addify_Registration_Fields_Pending_User_Email();


			require_once AFREG_PLUGIN_DIR . 'classes/afreg-approved-user-email-class.php';
			$emails['afreg_approved_user_email_user'] = new Addify_Registration_Fields_Approved_User_Email();


			require_once AFREG_PLUGIN_DIR . 'classes/afreg-disapproved-user-email-class.php';
			$emails['afreg_disapproved_user_email_user'] = new Addify_Registration_Fields_Disapproved_User_Email();

			
			

			return $emails;
		}
	}

	new Addify_Registration_Fields_Addon();

}