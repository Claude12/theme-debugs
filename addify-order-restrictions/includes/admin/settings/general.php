<?php

defined( 'ABSPATH' ) || exit;

add_settings_section(
	'afor-general-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'General Settings', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'addify-or-general'                           // Page on which to add this section of options.
);

add_settings_field(
	'afor_show_on_cart_page',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Show on Cart page', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afor_show_on_cart_page_callback',   // The name of the function responsible for rendering the option interface.
	'addify-or-general',                          // The page on which this option will be displayed.
	'afor-general-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Show restriction message(s) on cart page.', 'addify_b2b' ) )
);

register_setting(
	'addify-order-restrictions-fields',
	'afor_show_on_cart_page'
);

if ( !function_exists( 'afor_show_on_cart_page_callback' ) ) {
	function afor_show_on_cart_page_callback( $args ) {
		?>
		<input type="checkbox" name="afor_show_on_cart_page" id="" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afor_show_on_cart_page' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

add_settings_field(
	'afor_show_on_checkout_page',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Show on Checkout page', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afor_show_on_checkout_page_callback',   // The name of the function responsible for rendering the option interface.
	'addify-or-general',                          // The page on which this option will be displayed.
	'afor-general-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Show restriction message(s) on checkout page.', 'addify_b2b' ) )
);

register_setting(
	'addify-order-restrictions-fields',
	'afor_show_on_checkout_page'
);

if ( !function_exists( 'afor_show_on_checkout_page_callback' ) ) {
	function afor_show_on_checkout_page_callback( $args ) {
		?>
		<input type="checkbox" name="afor_show_on_checkout_page" id="" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afor_show_on_checkout_page' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}
