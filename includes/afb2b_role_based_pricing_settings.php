<?php

add_settings_section(  
	'page_1_section',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'csp_page_1_section_callback', // Callback used to render the description of the section  
	'addify-role-pricing-1'                           // Page on which to add this section of options  
);


add_settings_field(   
	'csp_enable_tire_price_table', 
	esc_html__('Enable Tiered Pricing Table', 'addify_b2b'),
	'csp_enable_tire_price_table_callback',
	'addify-role-pricing-1',
	'page_1_section',
	array(
		wp_kses_post('Enable tiered pricing table on the product page.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting-group-1',  
	'csp_enable_tire_price_table'  
);


add_settings_field(   
	'csp_range_msg', 
	esc_html__('Tiered Pricing Table', 'addify_b2b'),
	'csp_range_msg_callback',
	'addify-role-pricing-1',
	'page_1_section',
	array(
		wp_kses_post('This message will be used for displaying tiered prices with min and max quantities. Use "{min_qty}" for minimum quantity, "{max_qty}" for maximum  and "{pro_price}" for the product price.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting-group-1',  
	'csp_range_msg'  
);

add_settings_field(   
	'csp_enfore_min_max_qty',                        
	esc_html__('Enforce Min & Max Quantity', 'addify_b2b'),
	'csp_enfore_min_max_qty_callback',   // The name of the function responsible for rendering the option interface
	'addify-role-pricing-1',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                    // The array of arguments to pass to the callback. In this case, just a description.  
		wp_kses_post('If this option is enabled, the user will not be allowed to add to cart beyond the minimum and maximum quantity.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting-group-1',  
	'csp_enfore_min_max_qty'  
);


add_settings_field(   
	'csp_min_qty_error_msg',                        
	esc_html__('Min Qty Error Message', 'addify_b2b'),
	'csp_min_qty_error_msg_callback',   // The name of the function responsible for rendering the option interface
	'addify-role-pricing-1',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                    // The array of arguments to pass to the callback. In this case, just a description.  
		wp_kses_post('This message will be used when user add quantity less than minimum qty set. Use "%u" for number of quantity.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting-group-1',  
	'csp_min_qty_error_msg'  
);

add_settings_field(   
	'csp_max_qty_error_msg', 
	esc_html__('Max Qty Error Message', 'addify_b2b'),
	'csp_max_qty_error_msg_callback',
	'addify-role-pricing-1',
	'page_1_section',
	array(
		wp_kses_post('This message will be used when user add quantity greater than maximum qty set. Use "%u" for number of quantity.', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting-group-1',  
	'csp_max_qty_error_msg'  
);


add_settings_field(   
	'csp_update_cart_error_msg',
	esc_html__('Update Cart Error Message', 'addify_b2b'),
	'csp_update_cart_error_msg_callback',
	'addify-role-pricing-1', 
	'page_1_section',  
	array(
		wp_kses_post('This message will be used when user update product in cart. Use "%pro" for Product Name, "%min" for Minimum Quantity and "%max" for Maximum Quantity. ', 'addify_b2b'),
	)
);  
register_setting(  
	'afrolebased_setting-group-1',  
	'csp_update_cart_error_msg'  
);


function csp_page_1_section_callback() {
	?>
	<div class="afb2b_setting_div">
		<p><?php echo esc_html__('Manage module general settings from here.', 'addify_b2b'); ?></p>
	</div>

	<?php
} // function csp_page_1_section_callback

function csp_enable_tire_price_table_callback( $args ) {
	?>
	
	<input type="checkbox" id="csp_enable_tire_price_table" name="csp_enable_tire_price_table" value="yes" <?php echo checked('yes', esc_attr( get_option('csp_enable_tire_price_table'))); ?> >
	<p class="description csp_range_msg"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end csp_range_msg_callback


function csp_range_msg_callback( $args ) {
	?>
	
	<input type="text"  name="csp_range_msg" id="csp_range_msg" class="login_title2" value="<?php echo esc_attr(get_option('csp_range_msg')); ?>" />
	<p class="description csp_range_msg"> <?php echo esc_attr($args[0]); ?> </p>
	<p class="description csp_range_msg"> <?php echo esc_html('Leave it blank to use default message "{min_qty} - {max_qty} quantity the price is {pro_price}/each"', 'addify_b2b'); ?> </p>
	<?php      
} // end csp_range_msg_callback

function csp_enfore_min_max_qty_callback( $args ) {  
	?>
			<input type="checkbox" id="csp_enfore_min_max_qty" name="csp_enfore_min_max_qty" value="yes" <?php echo checked('yes', esc_attr( get_option('csp_enfore_min_max_qty'))); ?> >
			<p class="description"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
}

function csp_min_qty_error_msg_callback( $args ) {
	?>
	
	<input type="text"  name="csp_min_qty_error_msg" id="csp_min_qty_error_msg" class="login_title2" value="<?php echo esc_attr(get_option('csp_min_qty_error_msg')); ?>" />
	<p class="description csp_min_qty_error_msg"> <?php echo esc_attr($args[0]); ?> </p>
   
	<?php      
} // end csp_min_qty_error_msg_callback

function csp_max_qty_error_msg_callback( $args ) {
	?>
	
	<input type="text"  name="csp_max_qty_error_msg" id="csp_max_qty_error_msg" class="login_title2" value="<?php echo esc_attr(get_option('csp_max_qty_error_msg')); ?>" />
	<p class="description csp_max_qty_error_msg"> <?php echo esc_attr($args[0]); ?> </p>
   
	<?php      
} // end csp_max_qty_error_msg_callback 

function csp_update_cart_error_msg_callback( $args ) {
	?>
	
	<input type="text"  name="csp_update_cart_error_msg" id="csp_update_cart_error_msg" class="login_title2" value="<?php echo esc_attr(get_option('csp_update_cart_error_msg')); ?>" />
	<p class="description csp_update_cart_error_msg"> <?php echo esc_attr($args[0]); ?> </p>
   
	<?php      
} // end csp_update_cart_error_msg_callback 
