<?php

/**
 * Exempted Customers & Roles settings
 */

add_settings_section(
	'aftax-cus-roles-exempt',         // ID used to identify this section and with which to register options.
	esc_html__( 'General Settings', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'aftax_cus_roles_exempt_callback', // Callback used to render the description of the section.
	'aftax_exempted_customer_roles_setting_section' // Page on which to add this section of options.
);

//Exempted Customers
add_settings_field(
	'aftax_exempted_customers[]',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Choose Customers', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_exempted_customers_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_exempted_customer_roles_setting_section',   // The page on which this option will be displayed.
	'aftax-cus-roles-exempt',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'Choose customers whom you want to give tax exemption.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_exempted_customer_roles_setting_fields',
	'aftax_exempted_customers'
);

//Exempted User Roles
add_settings_field(
	'aftax_exempted_user_roles[]',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Select User Roles', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_exempted_user_roles_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_exempted_customer_roles_setting_section',   // The page on which this option will be displayed.
	'aftax-cus-roles-exempt',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'Choose user roles to grant them tax exemption status.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_exempted_customer_roles_setting_fields',
	'aftax_exempted_user_roles'
);









/**
 * Heading of Exempt Customers & Roles
 */
function aftax_cus_roles_exempt_callback() {
	?>
	<p><?php esc_html_e( 'In this section, you can specify the customers and user roles who are exempted from tax. These customers and roles are not required to fill the tax form from "My Account" page.', 'addify_b2b' ); ?></p>
	<?php
}


function aftax_exempted_customers_callback( $args ) {

	?>

	
	<select name="aftax_exempted_customers[]" id="aftax_exempted_customers" multiple="multiple">
		<?php
			$afcustomers = (array) maybe_unserialize( get_option( 'aftax_exempted_customers' ) );

		if ( ! empty( $afcustomers ) ) {
			foreach ( $afcustomers as $usr ) {
				$author_obj = get_user_by( 'id', $usr );
				if ( !is_a( $author_obj, 'WP_User') ) {
					continue;
				}
				?>
				<option value="<?php echo intval( $usr ); ?>" selected="selected"><?php echo esc_attr( $author_obj->display_name ); ?>(<?php echo esc_attr( $author_obj->user_email ); ?>)
				</option>
				<?php
			}
		}
		?>

	</select>
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}


function aftax_exempted_user_roles_callback( $args ) {

	?>
<select name="aftax_exempted_user_roles[]" id="aftax_exempted_user_roles" class="aftax_exempted_user_roles" multiple="multiple">
		<?php
		$aftax_exempted_user_roles = (array) maybe_unserialize( get_option( 'aftax_exempted_user_roles' ) );
		global $wp_roles;
		$roles = $wp_roles->get_names();
		if ( ! isset($roles['guest']) ) {
			$roles['guest'] = 'Guest';
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



