<?php

/**
 * Tax Exemption request settings
 */

add_settings_section(
	'aftax-request-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Tax Exemption Request Settings', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'aftax_request_sec_callback', // Callback used to render the description of the section.
	'aftax_request_setting_section' // Page on which to add this section of options.
);

//User Roles
add_settings_field(
	'aftax_requested_roles[]',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Select User Roles', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_requested_roles_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_request_setting_section',   // The page on which this option will be displayed.
	'aftax-request-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'Choose user roles to allow them to request for tax exemption.', 'addify_b2b' ),
	)
);

register_setting(
	'aftax_request_setting_fields',
	'aftax_requested_roles'
);

//Auto approval tax exemption request
add_settings_field(
	'aftax_enable_auto_tax_exempt',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Auto Approve Tax Exempt Request', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_enable_auto_tax_exempt_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_request_setting_section',   // The page on which this option will be displayed.
	'aftax-request-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'If this option is checked then tax exempt requests will be auto-approved and users of above selected user roles will be eligible for tax exempt right after submitting the info.', 'addify_b2b' ),
	)
);

register_setting(
	'aftax_request_setting_fields',
	'aftax_enable_auto_tax_exempt'
);


//Enable tax exemption message on checkout page
add_settings_field(
	'aftax_enable_tax_exm_msg',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Show Tax Exemption Message on Checkout Page', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_enable_tax_exm_msg_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_request_setting_section',   // The page on which this option will be displayed.
	'aftax-request-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'If this option is checked then a message will be displayed for the above selected user role users about tax exemption.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_request_setting_fields',
	'aftax_enable_tax_exm_msg'
);


//Message text
add_settings_field(
	'aftax_role_message_text',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Message Text', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_role_message_text_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_request_setting_section',   // The page on which this option will be displayed.
	'aftax-request-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This will be visible for the user roles customer has selected above.', 'addify_b2b' ),
		'label_for'   => 'aftax_role_message_text',
		'tip'         => 'dfasfdsfdsfdsfdsfdsfdsfdsfdsfdsfdsfdsf',
		'class'       => 'aftax_role_message_text_div',
	)
);

register_setting(
	'aftax_request_setting_fields',
	'aftax_role_message_text'
);






/**
 * Heading of Tax Exemption request
 */
function aftax_request_sec_callback() {
	?>
	<p><?php esc_html_e( 'Select user roles for whom you want to display tax exemption form in "My Account" page and a checkbox in the checkout page to notify them that tax exemption is available.', 'addify_b2b' ); ?></p>
	<?php
}


function aftax_requested_roles_callback( $args ) {

	?>


<select name="aftax_requested_roles[]" id="aftax_requested_roles" class="aftax_exempted_user_roles" multiple="multiple">
		<?php
		$aftax_exempted_user_roles = (array) maybe_unserialize( get_option( 'aftax_requested_roles' ) );
	
		global $wp_roles;
		$roles = $wp_roles->get_names();
		if (  isset($roles['guest']) ) {
			unset($roles['guest']);
		}
		foreach ( $roles as $key => $value ) {

			?>

			<option value="<?php echo esc_attr( $key ); ?>" 
			<?php
			if ( ! empty( $aftax_exempted_user_roles ) && in_array( $key, $aftax_exempted_user_roles ) ) {
				echo 'selected';
			}
			?>
			><?php echo esc_attr( $value ); ?>
				</option>

		<?php } ?>
		
	
	</select>
	
	
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_enable_auto_tax_exempt_callback( $args ) {

	?>

	
	<input type="checkbox" name="aftax_enable_auto_tax_exempt" id="aftax_enable_auto_tax_exempt" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'aftax_enable_auto_tax_exempt' ) ) ); ?> />
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}


function aftax_enable_tax_exm_msg_callback( $args ) {

	?>

	
	<input type="checkbox" name="aftax_enable_tax_exm_msg" id="aftax_enable_tax_exm_msg" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'aftax_enable_tax_exm_msg' ) ) ); ?> />
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_role_message_text_callback( $args ) {

	?>
		<?php

			$content   = get_option( 'aftax_role_message_text' );
			$content   =wpautop( wptexturize(stripslashes($content)) );
			$editor_id = 'aftax_role_message_text';
			$settings  = array( 'textarea_name' => 'aftax_role_message_text' );

			wp_editor( $content, $editor_id, $settings );

		?>

	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}




