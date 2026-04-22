<?php
/**
 * General settings tab fields
 *
 * @package  woocommerce-request-a-quote
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_settings_section(
	'afrfq-page-template',         // ID used to identify this section and with which to register options.
	esc_html__( 'Quote Page Template', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'afrfq_button_setting_section'                           // Page on which to add this section of options.
);

add_settings_field(
	'afrfq_select_page_template',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Select Quote Page Layout', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_select_page_template_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq-page-template',         // The name of the section to which this field belongs.
	array( esc_html__( 'Select quote page layout.', 'addify_b2b' ) )
);
register_setting(
	'afrfq_button_setting_fields',
	'afrfq_select_page_template'
);

add_settings_field(
	'afrfq_enable_table_header_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Table Header Color', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_enable_table_header_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq-page-template',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable quote table header color.', 'addify_b2b' ) )
);
register_setting(
	'afrfq_button_setting_fields',
	'afrfq_enable_table_header_color'
);

add_settings_field(
	'afrfq_table_header_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Table Header Text Color', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_table_header_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq-page-template',         // The name of the section to which this field belongs.
	array( esc_html__( 'Customize quote table header text.', 'addify_b2b' ) )
);
register_setting(
	'afrfq_button_setting_fields',
	'afrfq_table_header_color'
);

add_settings_field(
	'afrfq_table_heade_bg_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Table Header Background Color', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_table_heade_bg_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq-page-template',         // The name of the section to which this field belongs.
	array( esc_html__( 'Customize quote table header background text.', 'addify_b2b' ) )
);
register_setting(
	'afrfq_button_setting_fields',
	'afrfq_table_heade_bg_color'
);

add_settings_section(
	'afrfq-button-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Place Quote Button Settings', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'afrfq_button_setting_section'                           // Page on which to add this section of options.
);

add_settings_field(
	'afrfq_submit_button_text',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Place Quote Button Text', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_submit_button_text_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq-button-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Customize place quote button text.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_button_setting_fields',
	'afrfq_submit_button_text'
);

add_settings_field(
	'afrfq_enable_qoute_button_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Quote Button Color', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_enable_qoute_button_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq-button-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable place quote button color.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_button_setting_fields',
	'afrfq_enable_qoute_button_color'
);

add_settings_field(
	'afrfq_submit_button_fg_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Place Quote Button Color', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_submit_button_fg_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq-button-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Customize place quote text color.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_button_setting_fields',
	'afrfq_submit_button_fg_color'
);

add_settings_field(
	'afrfq_submit_button_bg_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Place Quote Button Background Color', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_submit_button_bg_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq-button-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Customize place quote background color.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_button_setting_fields',
	'afrfq_submit_button_bg_color'
);



if ( ! function_exists( 'afrfq_select_page_template_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_select_page_template_callback( $args = array() ) {
		$value = get_option( 'afrfq_select_page_template' );
		?>
		<select name="afrfq_select_page_template" id="afrfq_select_page_template">
			<option value="template_one" <?php echo ( 'template_one' === $value ) ? 'selected' : ''; ?>><?php echo esc_html__( 'One Column', 'addify_b2b' ); ?></option>
			<option value="template_two" <?php echo ( 'template_two' === $value ) ? 'selected' : ''; ?>><?php echo esc_html__( 'Two Column', 'addify_b2b' ); ?></option>
		</select>
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}


if ( ! function_exists( 'afrfq_submit_button_text_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_submit_button_text_callback( $args = array() ) {
		$value = get_option( 'afrfq_submit_button_text' );
		$value = empty( $value ) ? __( 'Place Quote', 'addify_b2b' ) : $value;
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_template_class" type="text" name="afrfq_submit_button_text" id="afrfq_submit_button_text" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_qoute_button_color_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_qoute_button_color_callback( $args = array() ) {

		?>
		<input type="checkbox" name="afrfq_enable_qoute_button_color" id="afrfq_enable_qoute_button_color" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_qoute_button_color' ) ) ); ?> />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
			<?php
	}
}


if ( ! function_exists( 'afrfq_submit_button_bg_color_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_submit_button_bg_color_callback( $args = array() ) {
		$value = get_option( 'afrfq_submit_button_bg_color' );
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_template_class" type="color" name="afrfq_submit_button_bg_color" id="afrfq_submit_button_bg_color" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_submit_button_fg_color_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_submit_button_fg_color_callback( $args = array() ) {
		$value = get_option( 'afrfq_submit_button_fg_color' );
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_template_class" type="color" name="afrfq_submit_button_fg_color" id="afrfq_submit_button_fg_color" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_table_header_color_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_table_header_color_callback( $args = array() ) {
		$value = get_option( 'afrfq_table_header_color' );
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_template_class" type="color" name="afrfq_table_header_color" id="afrfq_table_header_color" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_table_heade_bg_color_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_table_heade_bg_color_callback( $args = array() ) {
		$value = get_option( 'afrfq_table_heade_bg_color' );
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_template_class" type="color" name="afrfq_table_heade_bg_color" id="afrfq_table_heade_bg_color" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_table_header_color_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_table_header_color_callback( $args = array() ) {

		?>
		<input type="checkbox" name="afrfq_enable_table_header_color" id="afrfq_enable_table_header_color" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_table_header_color' ) ) ); ?> />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
			<?php
	}
}
