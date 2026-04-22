<?php
add_settings_section(  
	'addify-cart-based-discount_sec',         // ID used to identify this section and with which to register options  
	'Discount Settings',   // Title to be displayed on the administration page  
	'', // Callback used to render the description of the section  
	'addify-cart-based-discount'                           // Page on which to add this section of options  
);


add_settings_field(   
	'af_dcv_coupons_enable',                      // ID used to identify the field throughout the theme  
	esc_html__('Disable Coupons', 'addify_b2b'),    // The label to the left of the option interface element  
	'afcbd_coupons_callback',   // The name of the function responsible for rendering the option interface  
	'addify-cart-based-discount',                          // The page on which this option will be displayed  
	'addify-cart-based-discount_sec',         // The name of the section to which this field belongs  
	array( esc_html__('Enable checkbox to disable coupons when cart discount is applied.', 'addify_b2b') )
);  
register_setting(  
	'afcart_discount_setting',  
	'af_dcv_coupons_enable'  
);


add_settings_field(   
	'af_dcv_coupons_message_disable',                      // ID used to identify the field throughout the theme  
	'',    // The label to the left of the option interface element  
	'af_dcv_coupons_message_disable_callback',   // The name of the function responsible for rendering the option interface  
	'addify-cart-based-discount',                          // The page on which this option will be displayed  
	'addify-cart-based-discount_sec',         // The name of the section to which this field belongs  
	''
);  
register_setting(  
	'afcart_discount_setting',  
	'af_dcv_coupons_message_disable'  
);

add_settings_field(   
	'af_dcv_disable_btb',                      // ID used to identify the field throughout the theme  
	esc_html__('Disable for B2B', 'addify_b2b'),    // The label to the left of the option interface element  
	'afcbd_disable_b2b_callback',   // The name of the function responsible for rendering the option interface  
	'addify-cart-based-discount',                          // The page on which this option will be displayed  
	'addify-cart-based-discount_sec',         // The name of the section to which this field belongs  
	array( esc_html__('Enable checkbox to remove cart discount if the product level discount is applied using Addify B2B or Addify Role Base Pricing.', 'addify_b2b') )
);  
register_setting(  
	'afcart_discount_setting',  
	'af_dcv_disable_btb'  
);



function afcbd_coupons_callback( $args ) {
	
	?>
	<input class="af_dcv_coupons_enable_class" type="checkbox" name="af_dcv_coupons_enable" id="af_dcv_coupons_enable" value="1" <?php echo checked( '1', esc_attr( get_option( 'af_dcv_coupons_enable' ) ) ); ?> />
	<p class="description"> <?php echo esc_attr( $args[0] ); ?> </p>
	<?php
}

function af_dcv_coupons_message_disable_callback() {
	
	?>
	<textarea class="af_dcv_couple_message_disable_class" name="af_dcv_coupons_message_disable" id="af_dcv_coupons_message_disable" rows="7" cols="60" placeholder="<?php echo esc_html__('Write a message for restricted coupons.', 'addify_b2b'); ?>"><?php echo esc_attr( get_option( 'af_dcv_coupons_message_disable' ) ); ?></textarea>
	
	<?php
}

function afcbd_disable_b2b_callback( $args ) {
	
	?>
	<input type="checkbox" name="af_dcv_disable_btb" id="af_dcv_disable_btb" value="1" <?php echo checked( '1', esc_attr( get_option( 'af_dcv_disable_btb' ) ) ); ?> />
	<p class="description"> <?php echo esc_attr( $args[0] ); ?> </p>
	<?php
}

