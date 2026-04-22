<?php


if ( ! defined( 'WPINC' ) ) {
	die; 
}

if ( !class_exists( 'Addify_Tax_Info_Admin_Email' ) ) { 

	class Addify_Tax_Info_Admin_Email extends WC_Email {

		public $email_content;

		/**
		 * Constructor of membership activated.
		 */
		public function __construct() { 
			$this->id             = 'aftax_info_admin_email'; // Unique ID to Store Emails Settings
			$this->title          = __( 'Addify Tax Information Email to Admin', 'addify_b2b' ); // Title of email to show in Settings
			$this->customer_email = false; // Set true for customer email and false for admin email.
			$this->description    = __( 'This email will be sent to admin when tax information is added or updated by the user.', 'addify_b2b' ); // description of email
			$this->template_base  = AFTAX_PLUGIN_DIR . 'templates/'; // Base directory of template 
			$this->template_html  = 'emails/aftax-info-email-admin.php'; // HTML template path
			$this->template_plain = 'emails/plain/aftax-info-email-admin.php'; // Plain template path

			$this->placeholders = array( // Placeholders/Variables to be used in email
				

			);

			// Call to the  parent constructor.
			parent::__construct(); // Must call constructor of parent class

			// Other settings.
			if (!empty(get_option('aftax_admin_email'))) {

				$this->recipient = $this->get_option( 'recipient', get_option( 'aftax_admin_email' ) );

			} else {
				$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
			}

			// Trigger function.
			add_action( 'aftax_info_notification_admin', array( $this, 'trigger' ), 10, 4 ); // action hook(s) to trigger email 

			add_filter('woocommerce_email_attachments', array( $this, 'attach_file_woocommerce_email' ), 10, 4);
		}

		public function attach_file_woocommerce_email( $attachments, $id, $object ) { 

		$emailTemplateIds = array(
			'aftax_info_admin_email',
			'aftax_info_user_email',
			'aftax_expire_info_user_email',
			'aftax_expire_info_admin_email',
			'aftax_disapprove_info_email',
			'aftax_approve_info_user_email',
			'aftax_approve_info_admin_email',
		);   



			if (in_array($id, $emailTemplateIds)) {

				$user_info              = get_userdata( $object->ID );
				$aftax_fileupload_field = $user_info->aftax_fileupload_field;

				$afTax_path    = AFTAX_MEDIA_PATH . $aftax_fileupload_field; 
				$attachments[] = $afTax_path;
			}

			return $attachments;
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {

			//Old versions compatibility.
			if (!empty(get_option('aftax_admin_email_subject'))) {

				return __(get_option('aftax_admin_email_subject'), 'addify_b2b');

			} else {

				return __( '{user_name} New Tax Exemption Form Submitted', 'addify_b2b' );

			}           
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'New Tax Exemption Form Submitted', 'addify_b2b' );
		}


		public function trigger( $customer_id, $tax_field, $taxarea_field, $file_field ) {

			$this->setup_locale();

				$customer = new WP_User($customer_id);

				$customer_details = '';
				
						

				$user_meta = get_userdata($customer_id);

				$user_login = stripslashes($customer->user_login);
				$user_email = stripslashes($customer->user_email);

				//custom message
				$email_content = get_option('aftax_admin_email_message');

				

				//Approve user link, this will work only when approve new user setting is enabled.
				
				$default_admin_url = admin_url( 'user-edit.php?action=approved&user_id=' . $customer_id );
				$approve_link      = wp_nonce_url($default_admin_url );

				//Disapprove user link, this will work only when approve new user setting is enabled.
				$default_admin_url2 = admin_url( 'user-edit.php?action=disapproved&user_id=' . $customer_id );
				$disapprove_link    = wp_nonce_url($default_admin_url2 );

				$email_content = str_replace('{approve_link}', $approve_link, $email_content);
				$email_content = str_replace('{disapprove_link}', $disapprove_link, $email_content);
						

				$email_content = str_replace('{user_name}', $user_login, $email_content);
				$email_content = str_replace('{customer_email}', $user_email, $email_content);

				//Form Fields
			if (!empty($tax_field)) {


				$customer_details .= '<p><b>' . esc_html__(get_option('aftax_text_field_label') . ': ', 'addify_b2b') . '</b>' . $tax_field . '</p>';
			}

			if (!empty($taxarea_field)) {


				$customer_details .= '<p><b>' . esc_html__(get_option('aftax_textarea_field_label') . ': ', 'addify_b2b') . '</b>' . $taxarea_field . '</p>';
			}

			if (!empty($file_field)) {

				$customer_details .= '<p><b>' . esc_html__(get_option('aftax_fileupload_field_label') . ': ', 'addify_b2b') . '</b>' . esc_html__('See Attachment', 'addify_b2b') . '</p>';

			}


				$email_content = str_replace('{form_data}', $customer_details, $email_content);

				$this->email_content = $email_content;
				$this->object        = $customer;
				
				
			
				
			

			if ( $this->is_enabled() && $this->get_recipient() ) {

				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}


		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'customer'           => $this->object,
					'email_heading'      => $this->get_heading(),
					'email_content'      => $this->email_content,
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => true,
					'plain_text'         => false,
					'email'              => $this,
				),
				'',
				$this->template_base
			);
		}

	
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'customer'           => $this->object,
					'email_heading'      => $this->get_heading(),
					'email_content'      => $this->email_content,
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => true,
					'plain_text'         => false,
					'email'              => $this,
				),
				'',
				$this->template_base
			);
		}
	}

	

}
