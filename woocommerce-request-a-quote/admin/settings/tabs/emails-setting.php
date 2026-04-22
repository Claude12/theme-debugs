<?php
/**
 * Email settings fields
 *
 * @package  woocommerce-request-a-quote
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_settings_section(
	'afrfq-emails-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Emails Settings', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'afrfq_emails_section'                           // Page on which to add this section of options.
);

add_settings_field(
	'afrfq_admin_email',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Admin/Shop Manager Email Address(es)', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_admin_email_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_emails_section',                          // The page on which this option will be displayed.
	'afrfq-emails-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'All admin emails that are related to our module will be sent to this email address. If this email is empty then default admin email address is used. You can add more than one email addresses separated by comma (,).', 'addify_b2b' ) )
);

register_setting(
	'afrfq_emails_fields',
	'afrfq_admin_email'
);

add_settings_field(
	'afrfq_admin_email_pdf',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Pdf Send to Admin by Email', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_admin_email_pdf_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_emails_section',                          // The page on which this option will be displayed.
	'afrfq-emails-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Select this checkbox to automatically attach quote PDF with the quote status emails sent to the store admin.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_emails_fields',
	'afrfq_admin_email_pdf'
);


add_settings_field(
	'afrfq_user_email_pdf',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Pdf Send to Customer by Email', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_user_email_pdf_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_emails_section',                          // The page on which this option will be displayed.
	'afrfq-emails-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Select this checkbox to automatically attach quote PDF with the quote status emails sent to customers.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_emails_fields',
	'afrfq_user_email_pdf'
);

add_settings_field(
	'afrfq_email_button_background_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Email Button Background Color', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_email_button_background_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_emails_section',                          // The page on which this option will be displayed.
	'afrfq-emails-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Choose the background color for buttons in quote emails.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_emails_fields',
	'afrfq_email_button_background_color'
);

add_settings_field(
	'afrfq_email_button_text_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Email Button Text Color', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_email_button_text_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_emails_section',                          // The page on which this option will be displayed.
	'afrfq-emails-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Choose the text color for buttons in quote emails.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_emails_fields',
	'afrfq_email_button_text_color'
);




add_settings_field(
	'afrfq_emails',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Request a Quote Emails', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_emails_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_emails_section',                          // The page on which this option will be displayed.
	'afrfq-emails-sec',         // The name of the section to which this field belongs.
	array( '' )
);

register_setting(
	'afrfq_emails_fields',
	'afrfq_emails'
);

if ( ! function_exists( 'afrfq_user_email_pdf_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_user_email_pdf_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_user_email_pdf" id="afrfq_user_email_pdf" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_user_email_pdf' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}



if ( ! function_exists( 'afrfq_email_button_background_color_callback' ) ) {
	/**
	 *  AF_R_F_Q_Admin.
	 *
	 * @param array $args returns array.
	 */
	function afrfq_email_button_background_color_callback( $args = array() ) {
		$selected_color = get_option( 'afrfq_email_button_background_color', '#008000' ); 
		?>
		<input type="color" name="afrfq_email_button_background_color" id="afrfq_email_button_background_color" value="<?php echo esc_attr( $selected_color ); ?>" />
		<p class="description afreg_additional_fields_section_title">
			<?php echo wp_kses_post( $args[0] ); ?>
		</p>
		<?php
	}
	
}

if ( ! function_exists( 'afrfq_email_button_text_color_callback' ) ) {
	/**
	 *  AF_R_F_Q_Admin.
	 *
	 * @param array $args returns array.
	 */
	function afrfq_email_button_text_color_callback( $args = array() ) {
		$selected_color = get_option( 'afrfq_email_button_text_color', '#FFFFFF' ); 
		?>
		<input type="color" name="afrfq_email_button_text_color" id="afrfq_email_button_text_color" value="<?php echo esc_attr( $selected_color ); ?>" />
		<p class="description afreg_additional_fields_section_title">
			<?php echo wp_kses_post( $args[0] ); ?>
		</p>
		<?php
	}
	
}


if ( ! function_exists( 'afrfq_admin_email_pdf_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_admin_email_pdf_callback( $args = array() ) {
		?>
			<input type="checkbox" name="afrfq_admin_email_pdf" id="afrfq_admin_email_pdf" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_admin_email_pdf' ) ) ); ?> />
<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_emails_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_emails_callback( $args = array() ) {
		include_once AFRFQ_PLUGIN_DIR . 'admin/settings/email-settings.php';
		?>

		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}




if ( ! function_exists( 'afrfq_admin_email_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_admin_email_callback( $args = array() ) {
		$value = get_option( 'afrfq_admin_email' );
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_class" type="email" multiple name="afrfq_admin_email" id="afrfq_admin_email" />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}
