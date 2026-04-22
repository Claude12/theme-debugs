<?php


if ( ! defined( 'WPINC' ) ) {
	die; 
}

if ( !class_exists( 'Addify_Tax_Expire_Info_User_Email' ) ) { 

	class Addify_Tax_Expire_Info_User_Email extends WC_Email {

		public $email_content;

		/**
		 * Constructor of membership activated.
		 */
		public function __construct() { 
			$this->id             = 'aftax_expire_info_user_email'; // Unique ID to Store Emails Settings
			$this->title          = __( 'Addify Expire Tax Information Email to Customer', 'addify_b2b' ); // Title of email to show in Settings
			$this->customer_email = true; // Set true for customer email and false for admin email.
			$this->description    = __( 'This email will be sent to customer when tax information is expired.', 'addify_b2b' ); // description of email
			$this->template_base  = AFTAX_PLUGIN_DIR . 'templates/'; // Base directory of template 
			$this->template_html  = 'emails/aftax-expire-info-email-user.php'; // HTML template path
			$this->template_plain = 'emails/plain/aftax-expire-info-email-user.php'; // Plain template path

			$this->placeholders = array( // Placeholders/Variables to be used in email
				

			);

			// Call to the  parent constructor.
			parent::__construct(); // Must call constructor of parent class

			

			// Trigger function.
			add_action( 'aftax_expire_info_notification_user', array( $this, 'trigger' ), 10, 4 ); // action hook(s) to trigger email 
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {

			
			return __( 'Tax Exemption Expired', 'addify_b2b' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Tax Exemption Expired', 'addify_b2b' );
		}


		public function trigger( $customer_id, $tax_field, $taxarea_field, $file_field ) {

			$this->setup_locale();

				$customer = new WP_User($customer_id);

				$customer_details = '';
				
						

				$user_meta = get_userdata($customer_id);

				$user_login = stripslashes($customer->user_login);
				$user_email = stripslashes($customer->user_email);

				$this->recipient = $user_email;

				//custom message
				$email_content = get_option('aftax_customer_expire_tax_info_email_message');
						

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
