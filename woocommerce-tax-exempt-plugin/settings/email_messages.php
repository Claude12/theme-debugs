<?php
/**
 * Email and messages settings
 */

add_settings_section(
	'aftax-email-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Email & Notification Settings', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'aftax_email_sec_callback', // Callback used to render the description of the section.
	'aftax_email_messages_setting_section' // Page on which to add this section of options.
);

//admin email
add_settings_field(
	'aftax_admin_email',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Admin/Shop Manager Email', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_admin_email_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_email_messages_setting_section',   // The page on which this option will be displayed.
	'aftax-email-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'All admin emails that are related to our module will be sent to this email address.', 'addify_b2b' ),
	)
);

register_setting(
	'aftax_email_messages_setting_fields',
	'aftax_admin_email'
);


//add or update info message
add_settings_field(
	'aftax_add_tax_info_message',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Add/Update Tax Info Message', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_add_tax_info_message_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_email_messages_setting_section',   // The page on which this option will be displayed.
	'aftax-email-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This message will be shown when user add or update tax info in my account.', 'addify_b2b' ),
	)
);

register_setting(
	'aftax_email_messages_setting_fields',
	'aftax_add_tax_info_message'
);



//Add or Update info section
add_settings_section(
	'aftax-add-update-info-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Add/Update info Email Messages', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'aftax_add_update_info_callback', // Callback used to render the description of the section.
	'aftax_add_update_info_setting_section' // Page on which to add this section of options.
);

//admin email message
add_settings_field(
	'aftax_admin_email_message',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Admin Email Message', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_admin_email_message_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_add_update_info_setting_section',   // The page on which this option will be displayed.
	'aftax-add-update-info-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This message will be used in admin email when a user add or update tax info from my account. You can use {user_name}, {customer_email}, {form_data}, {approve_link}, {disapprove_link} variables to add data in the message.', 'addify_b2b' ),
	)
);

register_setting(
	'aftax_email_messages_setting_fields',
	'aftax_admin_email_message'
);

//customer email message
add_settings_field(
	'aftax_customer_email_message',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Customer Email Message', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_customer_email_message_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_add_update_info_setting_section',   // The page on which this option will be displayed.
	'aftax-add-update-info-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This message will be used in customer email when a user add or update tax info from my account. You can use {user_name}, {customer_email}, {form_data} variables to add data in the message.', 'addify_b2b' ),
	)
);

register_setting(
	'aftax_email_messages_setting_fields',
	'aftax_customer_email_message'
);



//Approve info section
add_settings_section(
	'aftax-approve-info-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Approve Tax info Email Messages', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'aftax_approve_info_callback', // Callback used to render the description of the section.
	'aftax_approve_info_setting_section' // Page on which to add this section of options.
);

//admin email message
add_settings_field(
	'aftax_admin_approve_tax_info_email_message',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Approve Tax Info Email Message (Admin)', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_admin_approve_tax_info_email_message_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_approve_info_setting_section',   // The page on which this option will be displayed.
	'aftax-approve-info-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This message will be used in admin email when admin approves submitted tax info. You can use {user_name}, {customer_email}, {form_data} variables to add data in the message.', 'addify_b2b' ),
	)
);

register_setting(
	'aftax_email_messages_setting_fields',
	'aftax_admin_approve_tax_info_email_message'
);

//customer email message
add_settings_field(
	'aftax_approve_tax_info_email_message',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Approve Tax Info Email Message (Customer)', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_approve_tax_info_email_message_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_approve_info_setting_section',   // The page on which this option will be displayed.
	'aftax-approve-info-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This message will be used in customer email when admin approves submitted tax info. You can use {user_name}, {customer_email}, {form_data} variables to add data in the message.', 'addify_b2b' ),
	)
);

register_setting(
	'aftax_email_messages_setting_fields',
	'aftax_approve_tax_info_email_message'
);



//Disapprove info section
add_settings_section(
	'aftax-disapprove-info-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Disapprove Tax info Email Messages', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'aftax_disapprove_info_callback', // Callback used to render the description of the section.
	'aftax_disapprove_info_setting_section' // Page on which to add this section of options.
);

//customer email message
add_settings_field(
	'aftax_disapprove_tax_info_email_message',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Disapprove Tax Info Email Message (Customer)', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_disapprove_tax_info_email_message_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_disapprove_info_setting_section',   // The page on which this option will be displayed.
	'aftax-disapprove-info-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This message will be used in customer email when admin disapprove submitted tax info. You can use {user_name}, {customer_email}, {form_data} variables to add data in the message.', 'addify_b2b' ),
	)
);

register_setting(
	'aftax_email_messages_setting_fields',
	'aftax_disapprove_tax_info_email_message'
);


//Expire info section
add_settings_section(
	'aftax-expire-info-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Expire Tax info Email Messages', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'aftax_expire_info_callback', // Callback used to render the description of the section.
	'aftax_expire_info_setting_section' // Page on which to add this section of options.
);

//admin email message
add_settings_field(
	'aftax_expire_tax_info_email_message',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Expire Tax Info Email Message (Admin)', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_expire_tax_info_email_message_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_expire_info_setting_section',   // The page on which this option will be displayed.
	'aftax-expire-info-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This message will be used in admin email when submitted tax information is expired. You can use {user_name}, {customer_email}, {form_data} variables to add data in the message.', 'addify_b2b' ),
	)
);

register_setting(
	'aftax_email_messages_setting_fields',
	'aftax_expire_tax_info_email_message'
);

//customer email message
add_settings_field(
	'aftax_customer_expire_tax_info_email_message',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Expire Tax Info Email Message (Customer)', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_customer_expire_tax_info_email_message_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_expire_info_setting_section',   // The page on which this option will be displayed.
	'aftax-expire-info-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This message will be used in customer email when submitted tax information is expired. You can use {user_name}, {customer_email}, {form_data} variables to add data in the message.', 'addify_b2b' ),
	)
);

register_setting(
	'aftax_email_messages_setting_fields',
	'aftax_customer_expire_tax_info_email_message'
);




/**
 * Heading of email and messages settings
 */
function aftax_email_sec_callback() {
	?>
	<p><?php esc_html_e( 'Manage email and notification settings.', 'addify_b2b' ); ?></p>
	<?php
}

function aftax_admin_email_callback( $args ) {

	?>

	
	<input type="text" name="aftax_admin_email" id="aftax_admin_email" value="<?php echo esc_attr( get_option( 'aftax_admin_email' ) ); ?>" />
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}


function aftax_add_tax_info_message_callback( $args ) {

	?>

	
	<textarea class="aftax_textarea"  name="aftax_add_tax_info_message" id="aftax_add_tax_info_message" rows="10"><?php echo esc_attr( get_option( 'aftax_add_tax_info_message' ) ); ?></textarea>
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}



/**
 * Heading add update info section
 */
function aftax_add_update_info_callback() {    
	?>
	<p><?php esc_html_e( 'Manage add or update tax information email messages.', 'addify_b2b' ); ?></p>
	<?php
}

function aftax_admin_email_message_callback( $args ) {

	?>
		<?php

			$content   = get_option( 'aftax_admin_email_message' );
			$content   =wpautop( wptexturize(stripslashes($content)) );
			$editor_id = 'aftax_admin_email_message';
			$settings  = array( 'textarea_name' => 'aftax_admin_email_message' );

			wp_editor( $content, $editor_id, $settings );

		?>

	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_customer_email_message_callback( $args ) {

	?>
		<?php

			$content   = get_option( 'aftax_customer_email_message' );
			$content   =wpautop( wptexturize(stripslashes($content)) );
			$editor_id = 'aftax_customer_email_message';
			$settings  = array( 'textarea_name' => 'aftax_customer_email_message' );

			wp_editor( $content, $editor_id, $settings );

		?>

	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

/**
 * Heading approve info section
 */
function aftax_approve_info_callback() {    
	?>
	<p><?php esc_html_e( 'Manage approve tax information email messages.', 'addify_b2b' ); ?></p>
	<?php
}

function aftax_approve_tax_info_email_message_callback( $args ) {

	?>
		<?php

			$content   = get_option( 'aftax_approve_tax_info_email_message' );
			$content   =wpautop( wptexturize(stripslashes($content)) );
			$editor_id = 'aftax_approve_tax_info_email_message';
			$settings  = array( 'textarea_name' => 'aftax_approve_tax_info_email_message' );

			wp_editor( $content, $editor_id, $settings );

		?>

	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_admin_approve_tax_info_email_message_callback( $args ) {

	?>
		<?php

			$content   = get_option( 'aftax_admin_approve_tax_info_email_message' );
			$content   =wpautop( wptexturize(stripslashes($content)) );
			$editor_id = 'aftax_admin_approve_tax_info_email_message';
			$settings  = array( 'textarea_name' => 'aftax_admin_approve_tax_info_email_message' );

			wp_editor( $content, $editor_id, $settings );

		?>

	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}


/**
 * Heading disapprove info section
 */
function aftax_disapprove_info_callback() {    
	?>
	<p><?php esc_html_e( 'Manage disapprove tax information email messages.', 'addify_b2b' ); ?></p>
	<?php
}

function aftax_disapprove_tax_info_email_message_callback( $args ) {

	?>
		<?php

			$content   = get_option( 'aftax_disapprove_tax_info_email_message' );
			$content   =wpautop( wptexturize(stripslashes($content)) );
			$editor_id = 'aftax_disapprove_tax_info_email_message';
			$settings  = array( 'textarea_name' => 'aftax_disapprove_tax_info_email_message' );

			wp_editor( $content, $editor_id, $settings );

		?>

	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}




/**
 * Heading expire info section
 */
function aftax_expire_info_callback() {    
	?>
	<p><?php esc_html_e( 'Manage expire tax information email messages.', 'addify_b2b' ); ?></p>
	<?php
}

function aftax_expire_tax_info_email_message_callback( $args ) {

	?>
		<?php

			$content   = get_option( 'aftax_expire_tax_info_email_message' );
			$content   =wpautop( wptexturize(stripslashes($content)) );
			$editor_id = 'aftax_expire_tax_info_email_message';
			$settings  = array( 'textarea_name' => 'aftax_expire_tax_info_email_message' );

			wp_editor( $content, $editor_id, $settings );

		?>

	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_customer_expire_tax_info_email_message_callback( $args ) {

	?>
		<?php

			$content   = get_option( 'aftax_customer_expire_tax_info_email_message' );
			$content   =wpautop( wptexturize(stripslashes($content)) );
			$editor_id = 'aftax_customer_expire_tax_info_email_message';
			$settings  = array( 'textarea_name' => 'aftax_customer_expire_tax_info_email_message' );

			wp_editor( $content, $editor_id, $settings );

		?>

	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}




