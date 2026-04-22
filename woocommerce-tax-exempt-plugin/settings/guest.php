<?php

/**
 * Guest users settings
 */

add_settings_section(
	'aftax-guest-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Guest User Settings', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'aftax_guest_sec_callback', // Callback used to render the description of the section.
	'aftax_guest_setting_section' // Page on which to add this section of options.
);

//Enable tax exemption message for guest users
add_settings_field(
	'aftax_enable_guest_message',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Show tax exemption message', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_enable_guest_message_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_guest_setting_section',   // The page on which this option will be displayed.
	'aftax-guest-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'If this option is checked then a message will be displayed for guest user about tax exemption.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_guest_setting_fields',
	'aftax_enable_guest_message'
);


//message text for guest users
add_settings_field(
	'aftax_guest_message_text',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Message Text', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_guest_message_text_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_guest_setting_section',   // The page on which this option will be displayed.
	'aftax-guest-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This message will be displayed for guest users on checkout page.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_guest_setting_fields',
	'aftax_guest_message_text'
);













/**
 * Heading of guest user settings
 */
function aftax_guest_sec_callback() {
	?>
	<p><?php esc_html_e( 'Show tax exemption message for guest users.', 'addify_b2b' ); ?></p>
	<?php
}

function aftax_enable_guest_message_callback( $args ) {

	?>

	
	<input type="checkbox" name="aftax_enable_guest_message" id="aftax_enable_guest_message" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'aftax_enable_guest_message' ) ) ); ?> />
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}


function aftax_guest_message_text_callback( $args ) {

	?>
		<?php

			$content   = get_option( 'aftax_guest_message_text' );
			$content   =wpautop( wptexturize(stripslashes($content)) );
			$editor_id = 'aftax_guest_message_text';
			$settings  = array( 'textarea_name' => 'aftax_guest_message_text' );

			wp_editor( $content, $editor_id, $settings );

		?>

	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}


