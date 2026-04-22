<?php

/**
 * General Settings of plugin
 */

add_settings_section(
	'aftax-general-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'General Settings', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'aftax_general_section_callback', // Callback used to render the description of the section.
	'aftax_general_setting_section' // Page on which to add this section of options.
);

//Enable or Disable auto tax exemption
add_settings_field(
	'aftax_enable_auto_tax_exempttion',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Remove Tax Automatically', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_enable_auto_tax_exempttion_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_general_setting_section',   // The page on which this option will be displayed.
	'aftax-general-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'Automatically remove tax from checkout. Keep this unchecked if you want to show a checkbox on checkout page to let customers manually remove tax.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_general_setting_fields',
	'aftax_enable_auto_tax_exempttion'
);


//Enable Text field
add_settings_field(
	'aftax_enable_text_field[]',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Text Field', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_enable_text_field_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_general_setting_section',   // The page on which this option will be displayed.
	'aftax-general-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This text field will be shown in tax form in user my account page. This field can be used to collect name, tax id etc.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_general_setting_fields',
	'aftax_enable_text_field'
);


//Label for Text field
add_settings_field(
	'aftax_text_field_label',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Text Field Label', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_text_field_label_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_general_setting_section',   // The page on which this option will be displayed.
	'aftax-general-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'Label for text field.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_general_setting_fields',
	'aftax_text_field_label'
);


//Enable Textarea field
add_settings_field(
	'aftax_enable_textarea_field[]',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Textarea Field', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_enable_textarea_field_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_general_setting_section',   // The page on which this option will be displayed.
	'aftax-general-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This textarea field will be shown in tax form in user my account page. This field can be used to collect additional info etc.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_general_setting_fields',
	'aftax_enable_textarea_field'
);

//Label for Textarea field
add_settings_field(
	'aftax_textarea_field_label',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Textarea Field Label', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_textarea_field_label_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_general_setting_section',   // The page on which this option will be displayed.
	'aftax-general-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'Label for textarea field.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_general_setting_fields',
	'aftax_textarea_field_label'
);

//Enable File upload field
add_settings_field(
	'aftax_enable_fileupload_field[]',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable File Upload Field', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_enable_fileupload_field_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_general_setting_section',   // The page on which this option will be displayed.
	'aftax-general-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'This file upload field will be shown in tax form in user my account page. This field can be used to collect tax certificate etc.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_general_setting_fields',
	'aftax_enable_fileupload_field'
);

//Label for Textarea field
add_settings_field(
	'aftax_fileupload_field_label',                      // ID used to identify the field throughout the theme.
	esc_html__( 'File Upload Field Label', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_fileupload_field_label_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_general_setting_section',   // The page on which this option will be displayed.
	'aftax-general-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'Label for fileupload field.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_general_setting_fields',
	'aftax_fileupload_field_label'
);

//Allowed upload file types
add_settings_field(
	'aftax_allowed_file_types',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Allowed Upload File Types', 'addify_b2b' ),    // The label to the left of the option interface element.
	'aftax_allowed_file_types_callback',   // The name of the function responsible for rendering the option interface.
	'aftax_general_setting_section',   // The page on which this option will be displayed.
	'aftax-general-sec',         // The name of the section to which this field belongs.
	array( 
		'description' => esc_html__( 'Specify allowed upload file types. Add comma(,) separated values like doc,pdf , etc to allow multiple file types.', 'addify_b2b' ), 
	)
);

register_setting(
	'aftax_general_setting_fields',
	'aftax_allowed_file_types'
);

/**
 * Heading of general settings.
 */
function aftax_general_section_callback() {
	?>
	<p><?php esc_html_e( 'In general settings you can set auto/manual tax exemption choose which field(s) you want to show on the tax exemption request form.', 'addify_b2b' ); ?></p>
	<?php
}

function aftax_enable_auto_tax_exempttion_callback( $args ) {

	?>

	
	<input type="checkbox" name="aftax_enable_auto_tax_exempttion" id="aftax_enable_auto_tax_exempttion" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'aftax_enable_auto_tax_exempttion' ) ) ); ?> />
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_enable_text_field_callback( $args ) {

	$values = (array) maybe_unserialize( get_option( 'aftax_enable_text_field' ) );

	?>

	
	<input class="aftax_checkbox" type="checkbox" name="aftax_enable_text_field[]" id="aftax_enable_text_field" value="enable" 
	<?php
	if ( in_array( 'enable', $values ) ) {
		echo 'checked';
	}
	?>
													/><?php echo esc_html__( 'Enable', 'addify_b2b' ); ?>

	<input class="aftax_checkbox" type="checkbox" name="aftax_enable_text_field[]" id="aftax_enable_text_field_required" value="required" 
	<?php
	if ( in_array( 'required', $values ) ) {
		echo 'checked';
	}
	?>
													/><?php echo esc_html__( 'Required', 'addify_b2b' ); ?>
													<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_text_field_label_callback( $args ) {

	?>

	
	<input type="text" name="aftax_text_field_label" id="aftax_text_field_label" value="<?php echo esc_attr( get_option( 'aftax_text_field_label' ) ); ?>" />
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_enable_textarea_field_callback( $args ) {

	$values = (array) maybe_unserialize( get_option( 'aftax_enable_textarea_field' ) );

	?>

	
	<input class="aftax_checkbox" type="checkbox" name="aftax_enable_textarea_field[]" id="aftax_enable_textarea_field" value="enable" 
	<?php
	if ( in_array( 'enable', $values ) ) {
		echo 'checked';
	}
	?>
													/><?php echo esc_html__( 'Enable', 'addify_b2b' ); ?>

	<input class="aftax_checkbox" type="checkbox" name="aftax_enable_textarea_field[]" id="aftax_enable_textarea_field_required" value="required" 
	<?php
	if ( in_array( 'required', $values ) ) {
		echo 'checked';
	}
	?>
													/><?php echo esc_html__( 'Required', 'addify_b2b' ); ?>
													<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_textarea_field_label_callback( $args ) {

	?>

	
	<input type="text" name="aftax_textarea_field_label" id="aftax_textarea_field_label" value="<?php echo esc_attr( get_option( 'aftax_textarea_field_label' ) ); ?>" />
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_enable_fileupload_field_callback( $args ) {

	$values = (array) maybe_unserialize( get_option( 'aftax_enable_fileupload_field' ) );

	?>

	
	<input class="aftax_checkbox" type="checkbox" name="aftax_enable_fileupload_field[]" id="aftax_enable_fileupload_field" value="enable" 
	<?php
	if ( in_array( 'enable', $values ) ) {
		echo 'checked';
	}
	?>
													/><?php echo esc_html__( 'Enable', 'addify_b2b' ); ?>

	<input class="aftax_checkbox" type="checkbox" name="aftax_enable_fileupload_field[]" id="aftax_enable_fileupload_field_required" value="required" 
	<?php
	if ( in_array( 'required', $values ) ) {
		echo 'checked';
	}
	?>
													/><?php echo esc_html__( 'Required', 'addify_b2b' ); ?>
													<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_fileupload_field_label_callback( $args ) {

	?>

	
	<input type="text" name="aftax_fileupload_field_label" id="aftax_fileupload_field_label" value="<?php echo esc_attr( get_option( 'aftax_fileupload_field_label' ) ); ?>" />
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}

function aftax_allowed_file_types_callback( $args ) {

	?>

	
	<input type="text" name="aftax_allowed_file_types" id="aftax_allowed_file_types" value="<?php echo esc_attr( get_option( 'aftax_allowed_file_types' ) ); ?>" />
	<p class="aftax_des"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php
}












