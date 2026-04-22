<?php

add_settings_section(  
	'afb2b_role_pricing_template_settings_section',
	'',
	'afb2b_role_page_2_section_callback', 
	'addify-role-pricing-template-settings'  
);

add_settings_field(   
	'afb2b_role_pricing_design_type', 
	esc_html__('Pricing Design', 'addify_b2b'),
	'afb2b_role_pricing_design_type_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Select the pricing design template.', 'addify_b2b'),
	)
	);  
	register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_pricing_design_type'  
);



add_settings_field(   
	'afb2b_role_enable_template_heading', 
	esc_html__('Enable Template Heading', 'addify_b2b'),
	'afb2b_role_enable_template_heading_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Enable the heading for template.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_enable_template_heading'  
);

add_settings_field(   
	'afb2b_role_template_heading_text', 
	esc_html__('Template Heading Text', 'addify_b2b'),
	'afb2b_role_template_heading_text_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Enter template heading text.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_template_heading_text'  
);

add_settings_field(   
	'afb2b_role_template_heading_text_font_size', 
	esc_html__('Template Heading Font Size', 'addify_b2b'),
	'afb2b_role_template_heading_text_font_size_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Enter font size for template heading, by default theme values will be inherited.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_template_heading_text_font_size'  
);

add_settings_field(   
	'afb2b_role_enable_template_icon', 
	esc_html__('Enable Template Icon', 'addify_b2b'),
	'afb2b_role_enable_template_icon_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Enable the icon for template.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_enable_template_icon'  
);

add_settings_field(   
	'afb2b_role_template_icon', 
	esc_html__('Upload Template Icon', 'addify_b2b'),
	'afb2b_role_template_icon_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Upload the icon for template. Leave it blank to use default icon.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_template_icon'  
);

add_settings_field(   
	'afb2b_role_template_font_family', 
	esc_html__('Enter Font Family for Template', 'addify_b2b'),
	'afb2b_role_template_font_family_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__("Specify the font family for the template text, or leave it blank to use the website's default font family.", 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_template_font_family'  
);

//default template settings
add_settings_field(   
	'afb2b_role_default_template_text_color', 
	esc_html__('Text Color', 'addify_b2b'),
	'afb2b_role_default_template_text_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose text color (for default template).', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_default_template_text_color'  
);

add_settings_field(   
	'afb2b_role_default_template_font_size', 
	esc_html__('Font Size', 'addify_b2b'),
	'afb2b_role_default_template_font_size_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose font size (for default template).', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_default_template_font_size'  
);



//table settings
add_settings_field(   
	'afb2b_role_enable_save_column', 
	esc_html__('Enable Save Column', 'addify_b2b'),
	'afb2b_role_enable_save_column_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Enable the save column for template.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_enable_save_column'  
);

add_settings_field(   
	'afb2b_role_table_header_color', 
	esc_html__('Table Header Color', 'addify_b2b'),
	'afb2b_role_table_header_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose table header background color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_table_header_color'  
);

add_settings_field(   
	'afb2b_role_table_header_text_color', 
	esc_html__('Table Header Text Color', 'addify_b2b'),
	'afb2b_role_table_header_text_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose table header text color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_table_header_text_color'  
);

add_settings_field(   
	'afb2b_role_table_odd_rows_color', 
	esc_html__('Table Odd Rows Color', 'addify_b2b'),
	'afb2b_role_table_odd_rows_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose table odd rows background color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_table_odd_rows_color'  
);

add_settings_field(   
	'afb2b_role_table_odd_rows_text_color', 
	esc_html__('Table Odd Rows Text Color', 'addify_b2b'),
	'afb2b_role_table_odd_rows_text_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose table odd rows text color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_table_odd_rows_text_color'  
);

add_settings_field(   
	'afb2b_role_table_even_rows_color', 
	esc_html__('Table Even Rows Color', 'addify_b2b'),
	'afb2b_role_table_even_rows_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose table even rows background color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_table_even_rows_color'  
);


add_settings_field(   
	'afb2b_role_table_even_rows_text_color', 
	esc_html__('Table Even Rows Text Color', 'addify_b2b'),
	'afb2b_role_table_even_rows_text_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose table even rows text color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_table_even_rows_text_color'  
);


add_settings_field(   
	'afb2b_role_enable_table_border', 
	esc_html__('Enable Table Border', 'addify_b2b'),
	'afb2b_role_enable_table_border_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Enable if do you want to use table border as a separator.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_enable_table_border'  
);

add_settings_field(   
	'afb2b_role_table_border_color', 
	esc_html__('Table Border Color', 'addify_b2b'),
	'afb2b_role_table_border_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose table border color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_table_border_color'
);

add_settings_field(   
	'afb2b_role_table_header_font_size', 
	esc_html__('Table Header Font Size', 'addify_b2b'),
	'afb2b_role_table_header_font_size_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Font size for table header, by default theme values will be inherited.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_table_header_font_size'
);

add_settings_field(   
	'afb2b_role_table_rows_font_size', 
	esc_html__('Table Rows Font Size', 'addify_b2b'),
	'afb2b_role_table_rows_font_size_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Font size for table rows, by default theme values will be inherited.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_table_rows_font_size'
);

//list fields

add_settings_field(   
	'afb2b_role_list_border_color', 
	esc_html__('List Border Color', 'addify_b2b'),
	'afb2b_role_list_border_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose list border color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_list_border_color'
);

add_settings_field(   
	'afb2b_role_list_background_color', 
	esc_html__('List Background Color', 'addify_b2b'),
	'afb2b_role_list_background_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose list background color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_list_background_color'
);

add_settings_field(   
	'afb2b_role_list_text_color', 
	esc_html__('List Text Color', 'addify_b2b'),
	'afb2b_role_list_text_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose list text color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_list_text_color'
);


add_settings_field(   
	'afb2b_role_selected_list_background_color', 
	esc_html__('Selected List Background Color', 'addify_b2b'),
	'afb2b_role_selected_list_background_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose selected list background color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_selected_list_background_color'
);

add_settings_field(   
	'afb2b_role_selected_list_text_color', 
	esc_html__('Selected List Text Color', 'addify_b2b'),
	'afb2b_role_selected_list_text_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose selected list text color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_selected_list_text_color'
);

//card fields
add_settings_field(   
	'afb2b_role_card_border_color', 
	esc_html__('Card Border Color', 'addify_b2b'),
	'afb2b_role_card_border_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose card border color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_card_border_color'
);

add_settings_field(   
	'afb2b_role_card_background_color', 
	esc_html__('Card Background Color', 'addify_b2b'),
	'afb2b_role_card_background_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose card background color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_card_background_color'
);

add_settings_field(   
	'afb2b_role_card_text_color', 
	esc_html__('Card Text Color', 'addify_b2b'),
	'afb2b_role_card_text_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose card text color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_card_text_color'
);


add_settings_field(   
	'afb2b_role_selected_card_border_color', 
	esc_html__('Selected Card Border Color', 'addify_b2b'),
	'afb2b_role_selected_card_border_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose selected card border color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_selected_card_border_color'
);


add_settings_field(   
	'afb2b_role_enable_card_sale_tag', 
	esc_html__('Enable Sale Tag', 'addify_b2b'),
	'afb2b_role_enable_card_sale_tag_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Enable sale tag for card.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_enable_card_sale_tag'
);

add_settings_field(   
	'afb2b_role_sale_tag_background_color', 
	esc_html__('Sale Tag Background Color', 'addify_b2b'),
	'afb2b_role_sale_tag_background_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose sale tag background color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_sale_tag_background_color'
);


add_settings_field(   
	'afb2b_role_sale_tag_text_color', 
	esc_html__('Sale Tag Text Color', 'addify_b2b'),
	'afb2b_role_sale_tag_text_color_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('Choose sale tag text color.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_sale_tag_text_color'
);

add_settings_field(   
	'afb2b_role_reset_settings_to_default', 
	esc_html__('Reset', 'addify_b2b'),
	'afb2b_role_reset_settings_to_default_callback',
	'addify-role-pricing-template-settings',
	'afb2b_role_pricing_template_settings_section',
	array(
		esc_html__('To revert all settings to default.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting_pricing_template_settings',  
	'afb2b_role_reset_settings_to_default'
);


	function afb2b_role_page_2_section_callback() { 
		?>
		<?php 
	}


	function afb2b_role_pricing_design_type_callback( $args ) {  
		if (!get_option('afb2b_role_pricing_design_type')) {
			update_option('afb2b_role_pricing_design_type', 'default_template');
		}
		?>
	<select name="afb2b_role_pricing_design_type" id="afb2b_role_pricing_design_type">
		<option value="default_template" <?php selected( get_option('afb2b_role_pricing_design_type'), 'default_template' ); ?>>Default Template</option>
		<option value="table" <?php selected( get_option('afb2b_role_pricing_design_type'), 'table' ); ?>>Table</option>
		<option value="list" <?php selected( get_option('afb2b_role_pricing_design_type'), 'list' ); ?>>List</option>
		<option value="card" <?php selected( get_option('afb2b_role_pricing_design_type'), 'card' ); ?>>Card</option>
	</select>
	<p class="description"><?php echo esc_attr($args[0]); ?></p>
	<img class='afb2b_role_default_template_img' src="<?php echo esc_url(AFB2B_URL . '/images/default_template.png'); ?>" style='display:none'/>
	<img class='afb2b_role_table_img' src="<?php echo esc_url(AFB2B_URL . '/images/table.png'); ?>" style='display:none'/>
	<img class='afb2b_role_card_img' src="<?php echo esc_url(AFB2B_URL . '/images/card.png'); ?>" style='display:none'/>
	<img class='afb2b_role_list_img' src="<?php echo esc_url(AFB2B_URL . '/images/list.png'); ?>" style='display:none'/>
	<?php      
	}

	function afb2b_role_enable_template_heading_callback( $args ) {  
		?>
	<input type="checkbox" id="afb2b_role_enable_template_heading" name="afb2b_role_enable_template_heading" value="yes" <?php echo checked('yes', esc_attr( get_option('afb2b_role_enable_template_heading'))); ?> >
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_template_heading_text_callback( $args ) { 
		if (!get_option('afb2b_role_template_heading_text') || '' == get_option('afb2b_role_template_heading_text')) {
			update_option('afb2b_role_template_heading_text', 'Select your Deal');
		} 
		?>
	<input type="text"  name="afb2b_role_template_heading_text" id="afb2b_role_template_heading_text" value="<?php echo esc_attr(get_option('afb2b_role_template_heading_text')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_template_heading_text_font_size_callback( $args ) {  
		if (!get_option('afb2b_role_template_heading_text_font_size') || '' == get_option('afb2b_role_template_heading_text_font_size')) {
			update_option('afb2b_role_template_heading_text_font_size', '28');
		} 
		?>
	<input type="text"  name="afb2b_role_template_heading_text_font_size" id="afb2b_role_template_heading_text_font_size" value="<?php echo esc_attr(get_option('afb2b_role_template_heading_text_font_size')); ?>" />px
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_enable_template_icon_callback( $args ) {  
		?>
	<input type="checkbox" id="afb2b_role_enable_template_icon" name="afb2b_role_enable_template_icon" value="yes" <?php echo checked('yes', esc_attr( get_option('afb2b_role_enable_template_icon'))); ?> >
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_template_font_family_callback( $args ) {  
		?>
	<input type="text"  name="afb2b_role_template_font_family" id="afb2b_role_template_font_family" value="<?php echo esc_attr(get_option('afb2b_role_template_font_family')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}


	function afb2b_role_template_icon_callback( $args ) {  
		$image = get_option( 'afb2b_role_template_icon' );
		?>
	<div id='afb2b_role_template_icon_container'>
		<div >
			<img id="afb2b_role_selected_image_display" src="<?php echo esc_url( $image ); ?>" width="50" />
		</div>		
		<input type="hidden" value="<?php echo esc_url( $image ); ?>" name="afb2b_role_template_icon" id="afb2b_role_template_icon">
		<input  type="button"  id="upload-image-btn" class="button-secondary" value="<?php echo esc_html__( 'Upload Image', 'addify_b2b' ); ?>">
		<input type="button"   id="remove_image_upload" style="height: 30px;" value="<?php echo esc_html__( 'Remove Image', 'addify_b2b' ); ?>" > 		
		<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	</div>
	<?php      
	}

//default template settings
	function afb2b_role_default_template_text_color_callback( $args ) { 
		if (!get_option('afb2b_role_default_template_text_color')) {
			update_option('afb2b_role_default_template_text_color', '#6d6d6d');
		}
		?>
	<input type="color"  name="afb2b_role_default_template_text_color" class="afb2b_role_default_template_row" id="afb2b_role_default_template_text_color" value="<?php echo esc_attr(get_option('afb2b_role_default_template_text_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_default_template_font_size_callback( $args ) {  
		if (!get_option('afb2b_role_default_template_font_size') || '' == get_option('afb2b_role_default_template_font_size')) {
			update_option('afb2b_role_default_template_font_size', '20');
		} 
		?>
	<input type="text"  name="afb2b_role_default_template_font_size" class="afb2b_role_default_template_row" id="afb2b_role_default_template_font_size" value="<?php echo esc_attr(get_option('afb2b_role_default_template_font_size')); ?>" />px
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}


//table template settings
	function afb2b_role_enable_save_column_callback( $args ) {
		?>
	<input type="checkbox" id="afb2b_role_enable_save_column" class='afb2b_role_table_row' name="afb2b_role_enable_save_column" value="yes" <?php echo checked('yes', esc_attr( get_option('afb2b_role_enable_save_column'))); ?> >
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php 
	}
	function afb2b_role_table_header_color_callback( $args ) { 
		if (!get_option('afb2b_role_table_header_color')) {
			update_option('afb2b_role_table_header_color', '#FFFFFF');
		}
		?>
	<input type="color"  name="afb2b_role_table_header_color" class="afb2b_role_table_row" id="afb2b_role_table_header_color" value="<?php echo esc_attr(get_option('afb2b_role_table_header_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_table_odd_rows_color_callback( $args ) {  
		if (!get_option('afb2b_role_table_odd_rows_color')) {
			update_option('afb2b_role_table_odd_rows_color', '#FFFFFF');
		}
		?>
	<input type="color"  name="afb2b_role_table_odd_rows_color" class="afb2b_role_table_row" id="afb2b_role_table_odd_rows_color" value="<?php echo esc_attr(get_option('afb2b_role_table_odd_rows_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_table_even_rows_color_callback( $args ) { 
		if (!get_option('afb2b_role_table_even_rows_color')) {
			update_option('afb2b_role_table_even_rows_color', '#FFFFFF');
		} 
		?>
	<input type="color"  name="afb2b_role_table_even_rows_color" class="afb2b_role_table_row" id="afb2b_role_table_even_rows_color" value="<?php echo esc_attr(get_option('afb2b_role_table_even_rows_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_table_header_text_color_callback( $args ) {  
		if (!get_option('afb2b_role_table_header_text_color')) {
			update_option('afb2b_role_table_header_text_color', '#000000');
		} 
		?>
	<input type="color"  name="afb2b_role_table_header_text_color" class="afb2b_role_table_row" id="afb2b_role_table_header_text_color" value="<?php echo esc_attr(get_option('afb2b_role_table_header_text_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_table_odd_rows_text_color_callback( $args ) {  
		if (!get_option('afb2b_role_table_odd_rows_text_color')) {
			update_option('afb2b_role_table_odd_rows_text_color', '#000000');
		} 
		?>
	<input type="color"  name="afb2b_role_table_odd_rows_text_color" class="afb2b_role_table_row" id="afb2b_role_table_odd_rows_text_color" value="<?php echo esc_attr(get_option('afb2b_role_table_odd_rows_text_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_table_even_rows_text_color_callback( $args ) {  
		if (!get_option('afb2b_role_table_even_rows_text_color')) {
			update_option('afb2b_role_table_even_rows_text_color', '#000000');
		} 
		?>
	<input type="color"  name="afb2b_role_table_even_rows_text_color" class="afb2b_role_table_row" id="afb2b_role_table_even_rows_text_color" value="<?php echo esc_attr(get_option('afb2b_role_table_even_rows_text_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_enable_table_border_callback( $args ) {  
		?>
	<input type="checkbox" id="afb2b_role_enable_table_border" class="afb2b_role_table_row" name="afb2b_role_enable_table_border" value="yes" <?php echo checked('yes', esc_attr( get_option('afb2b_role_enable_table_border'))); ?> >
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_table_border_color_callback( $args ) { 
		if (!get_option('afb2b_role_table_border_color')) {
			update_option('afb2b_role_table_border_color', '#CFCFCF');
		} 

		?>
	<input type="color"  name="afb2b_role_table_border_color" class="afb2b_role_table_row" id="afb2b_role_table_border_color" value="<?php echo esc_attr(get_option('afb2b_role_table_border_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_table_header_font_size_callback( $args ) {  
		if (!get_option('afb2b_role_table_header_font_size') || '' == get_option('afb2b_role_table_header_font_size')) {
			update_option('afb2b_role_table_header_font_size', '18');
		} 
		?>
	<input type="text"  name="afb2b_role_table_header_font_size" class="afb2b_role_table_row" id="afb2b_role_table_header_font_size" value="<?php echo esc_attr(get_option('afb2b_role_table_header_font_size')); ?>" />px
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_table_rows_font_size_callback( $args ) {  
		if (!get_option('afb2b_role_table_rows_font_size') || '' == get_option('afb2b_role_table_rows_font_size')) {
			update_option('afb2b_role_table_rows_font_size', '16');
		} 

		?>
	<input type="text"  name="afb2b_role_table_rows_font_size" class="afb2b_role_table_row" id="afb2b_role_table_rows_font_size" value="<?php echo esc_attr(get_option('afb2b_role_table_rows_font_size')); ?>" />px
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

//list template settings

	function afb2b_role_list_border_color_callback( $args ) {  
		if (!get_option('afb2b_role_list_border_color')) {
			update_option('afb2b_role_list_border_color', '#95B0EE');
		} 

		?>
	<input type="color"  name="afb2b_role_list_border_color" class="afb2b_role_list_row" id="afb2b_role_list_border_color" value="<?php echo esc_attr(get_option('afb2b_role_list_border_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_list_background_color_callback( $args ) { 
		if (!get_option('afb2b_role_list_background_color')) {
			update_option('afb2b_role_list_background_color', '#FFFFFF');
		}  
		?>
	<input type="color"  name="afb2b_role_list_background_color" class="afb2b_role_list_row" id="afb2b_role_list_background_color" value="<?php echo esc_attr(get_option('afb2b_role_list_background_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}
	function afb2b_role_list_text_color_callback( $args ) { 
		if (!get_option('afb2b_role_list_text_color')) {
			update_option('afb2b_role_list_text_color', '#000000');
		}  
		?>
	<input type="color"  name="afb2b_role_list_text_color" class="afb2b_role_list_row" id="afb2b_role_list_text_color" value="<?php echo esc_attr(get_option('afb2b_role_list_text_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_selected_list_background_color_callback( $args ) { 
		if (!get_option('afb2b_role_selected_list_background_color')) {
			update_option('afb2b_role_selected_list_background_color', '#DFEBFF');
		}   
		?>
	<input type="color"  name="afb2b_role_selected_list_background_color" class="afb2b_role_list_row" id="afb2b_role_selected_list_background_color" value="<?php echo esc_attr(get_option('afb2b_role_selected_list_background_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}
	function afb2b_role_selected_list_text_color_callback( $args ) {  
		if (!get_option('afb2b_role_selected_list_text_color')) {
			update_option('afb2b_role_selected_list_text_color', '#000000');
		}  
		?>
	<input type="color"  name="afb2b_role_selected_list_text_color" class="afb2b_role_list_row" id="afb2b_role_selected_list_text_color" value="<?php echo esc_attr(get_option('afb2b_role_selected_list_text_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

//card settings

	function afb2b_role_card_border_color_callback( $args ) {  
		if (!get_option('afb2b_role_card_border_color')) {
			update_option('afb2b_role_card_border_color', '#A3B39E');
		}  
		?>
	<input type="color"  name="afb2b_role_card_border_color" class="afb2b_role_card_row" id="afb2b_role_card_border_color" value="<?php echo esc_attr(get_option('afb2b_role_card_border_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_card_background_color_callback( $args ) {  
		if (!get_option('afb2b_role_card_background_color')) {
			update_option('afb2b_role_card_background_color', '#FFFFFF');
		}  
		?>
	<input type="color"  name="afb2b_role_card_background_color" class="afb2b_role_card_row" id="afb2b_role_card_background_color" value="<?php echo esc_attr(get_option('afb2b_role_card_background_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_card_text_color_callback( $args ) {  
		if (!get_option('afb2b_role_card_text_color')) {
			update_option('afb2b_role_card_text_color', '#000000');
		}  
		?>
	<input type="color"  name="afb2b_role_card_text_color" class="afb2b_role_card_row" id="afb2b_role_card_text_color" value="<?php echo esc_attr(get_option('afb2b_role_card_text_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_selected_card_border_color_callback( $args ) {  
		if (!get_option('afb2b_role_selected_card_border_color')) {
			update_option('afb2b_role_selected_card_border_color', '#27CA34');
		}  
		?>
	<input type="color"  name="afb2b_role_selected_card_border_color" class="afb2b_role_card_row" id="afb2b_role_selected_card_border_color" value="<?php echo esc_attr(get_option('afb2b_role_selected_card_border_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_enable_card_sale_tag_callback( $args ) {  
		?>
	<input type="checkbox" id="afb2b_role_enable_card_sale_tag" class="afb2b_role_card_row" name="afb2b_role_enable_card_sale_tag" value="yes" <?php echo checked('yes', esc_attr( get_option('afb2b_role_enable_card_sale_tag'))); ?> >
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_sale_tag_background_color_callback( $args ) { 
		if (!get_option('afb2b_role_sale_tag_background_color')) {
			update_option('afb2b_role_sale_tag_background_color', '#FF0000');
		}   
		?>
	<input type="color"  name="afb2b_role_sale_tag_background_color" class="afb2b_role_card_row" id="afb2b_role_sale_tag_background_color" value="<?php echo esc_attr(get_option('afb2b_role_sale_tag_background_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}
	function afb2b_role_sale_tag_text_color_callback( $args ) {  
		if (!get_option('afb2b_role_sale_tag_text_color')) {
			update_option('afb2b_role_sale_tag_text_color', '#FFFFFF');
		}  
		?>
	<input type="color"  name="afb2b_role_sale_tag_text_color" class="afb2b_role_card_row" id="afb2b_role_sale_tag_text_color" value="<?php echo esc_attr(get_option('afb2b_role_sale_tag_text_color')); ?>" />
	<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	function afb2b_role_reset_settings_to_default_callback( $args ) {  
		
		?>
	<a  name="afb2b_role_reset_settings_to_default"  class="button" id="afb2b_role_reset_settings_to_default"><?php esc_attr_e('Reset to Default', 'addify_b2b'); ?></a>
	<p class="description" style='color:red'> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
	}

	