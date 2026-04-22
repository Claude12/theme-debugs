<?php
/*
 * Tax Exempt for WooCommerce
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Addify_Tax_Exempt')) {

	class Addify_Tax_Exempt {
	

		public function __construct() {

			$this->aftax_global_constents_vars();

			$this->set_media_path_url();

			add_action('init', array( $this, 'aftax_wc_init' ));

			include_once AFTAX_PLUGIN_DIR . 'af-te-general-function.php';

			if (is_admin()) {
				include_once AFTAX_PLUGIN_DIR . 'class_aftax_admin.php';
			} else {
				include_once AFTAX_PLUGIN_DIR . 'class-aftax-front.php';
			}
			add_filter('woocommerce_email_classes', array( $this, 'aftax_emails' ), 90, 1);
		}



		public function aftax_is_taxes_enabled() {
			return function_exists('wc_tax_enabled') && wc_tax_enabled();
		}




		public function aftax_wc_init() {

			if (defined('WC_PLUGIN_FILE')) {
				//Define a cron job interval if it doesn't exist
				add_filter('cron_schedules', array( $this, 'aftax_cron_for_expire_info' ));
				//Schedule an event unless already scheduled
				add_action('wp', array( $this, 'aftax_new_cron_job' ));
				//Trigger cron hook
				add_action('aftax_send_expire_info_email', array( $this, 'aftax_send_expire_info_email_callback' ));


			}
		}



		public function aftax_cron_for_expire_info( $schedules ) {

			$schedules['every_one_minute'] = array(
				'interval' => 60,
				'display'  => __('Every 1 minute'),
			);
			return $schedules;
		}

		public function aftax_new_cron_job() {

			if (!wp_next_scheduled('aftax_send_expire_info_email')) {
				wp_schedule_event(time(), 'every_one_minute', 'aftax_send_expire_info_email');
			}
		}

		public function aftax_send_expire_info_email_callback() {

			wc()->mailer();

			$users = get_users(
				array(
					'meta_key'     => 'aftax_tax_expire_date',
					'meta_value'   => '',
					'meta_compare' => '!=',
				)
			);

			foreach ($users as $s_user) {

				$user_id                    = $s_user->ID;
				$user_info                  = get_userdata($user_id);
				$aftax_text_field           = $user_info->aftax_text_field;
				$aftax_textarea_field       = $user_info->aftax_textarea_field;
				$aftax_fileupload_field     = $user_info->aftax_fileupload_field;
				$aftax_tax_exemption_status = $user_info->aftax_tax_exemption_status;
				$aftax_tax_expire_date      = $user_info->aftax_tax_expire_date;


				$current_date = gmdate('Y-m-d');

				if (!empty($aftax_tax_expire_date)) {

					$exp_date = $aftax_tax_expire_date;
				} else {

					$exp_date = '';
				}

				if ('' != $exp_date && $current_date > $exp_date) {

					update_metadata('user', $user_id, 'aftax_tax_exemption_status', 'expired', '');
				}

				$aftax_is_expire_sent_email = $user_info->aftax_tax_info_expire_email;


				if ('sent' !== $aftax_is_expire_sent_email && 'expired' === $aftax_tax_exemption_status) {
					//Email to admin
					do_action('aftax_expire_info_notification_admin', $user_id, $aftax_text_field, $aftax_textarea_field, $aftax_fileupload_field);

					//Email to user
					do_action('aftax_expire_info_notification_user', $user_id, $aftax_text_field, $aftax_textarea_field, $aftax_fileupload_field);

					update_metadata('user', $user_id, 'aftax_tax_info_expire_email', 'sent', '');
				}
			}
		}

		private function set_media_path_url() {

			$upload_dir = wp_upload_dir();

			$upload_path = $upload_dir['basedir'] . '/addify-tax-exempt/';

			if (!is_dir($upload_path)) {
				mkdir($upload_path);
			}

			$upload_url = $upload_dir['baseurl'] . '/addify-tax-exempt/';

			if (!defined('AFTAX_MEDIA_URL')) {
				define('AFTAX_MEDIA_URL', $upload_url);
			}

			if (!defined('AFTAX_MEDIA_PATH')) {
				define('AFTAX_MEDIA_PATH', $upload_path);
			}

			if (!file_exists(AFTAX_MEDIA_PATH . 'index.html')) {
				$index_file = fopen(AFTAX_MEDIA_PATH . 'index.html', 'w');
				fclose($index_file);
			}

			if (!file_exists(AFTAX_MEDIA_PATH . '.htaccess')) {
				$index_file = fopen(AFTAX_MEDIA_PATH . '.htaccess', 'w');
				fwrite($index_file, 'Deny from all');
				fclose($index_file);
			}
		}

		public function aftax_global_constents_vars() {


			if (!defined('AFTAX_URL')) {
				define('AFTAX_URL', plugin_dir_url(__FILE__));
			}

			if (!defined('AFTAX_BASENAME')) {
				define('AFTAX_BASENAME', plugin_basename(__FILE__));
			}

			if (!defined('AFTAX_PLUGIN_DIR')) {
				define('AFTAX_PLUGIN_DIR', plugin_dir_path(__FILE__));
			}
		}

		public function aftax_emails( $emails ) {

			require_once AFTAX_PLUGIN_DIR . 'classes/aftax-info-admin-email-class.php';
			$emails['aftax_info_admin_email'] = new Addify_Tax_Info_Admin_Email();

			require_once AFTAX_PLUGIN_DIR . 'classes/aftax-info-user-email-class.php';
			$emails['aftax_info_user_email'] = new Addify_Tax_Info_User_Email();

			require_once AFTAX_PLUGIN_DIR . 'classes/aftax-approve-info-admin-email-class.php';
			$emails['aftax_approve_info_admin_email'] = new Addify_Tax_Approve_Info_Admin_Email();

			require_once AFTAX_PLUGIN_DIR . 'classes/aftax-approve-info-user-email-class.php';
			$emails['aftax_approve_info_user_email'] = new Addify_Tax_Approve_Info_User_Email();

			require_once AFTAX_PLUGIN_DIR . 'classes/aftax-disapprove-info-email-class.php';
			$emails['aftax_disapprove_info_email'] = new Addify_Tax_Disapprove_Info_Email();

			require_once AFTAX_PLUGIN_DIR . 'classes/aftax-expire-info-admin-email-class.php';
			$emails['aftax_expire_info_admin_email'] = new Addify_Tax_Expire_Info_Admin_Email();

			require_once AFTAX_PLUGIN_DIR . 'classes/aftax-expire-info-user-email-class.php';
			$emails['aftax_expire_info_user_email'] = new Addify_Tax_Expire_Info_User_Email();





			return $emails;
		}
	}

	new Addify_Tax_Exempt();

}
