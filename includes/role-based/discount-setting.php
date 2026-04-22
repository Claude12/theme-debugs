<?php
add_settings_section(  
	'af-discount-sec',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'', // Callback used to render the description of the section  
	'addify-role-pricing-discount'                           // Page on which to add this section of options  
);

add_settings_field(   
	'afb2b_discount_price',                      // ID used to identify the field throughout the theme  
	esc_html__('Price for discount by user roles', 'addify_b2b'),    // The label to the left of the option interface element  
	'afb2b_discount_display_callback',   // The name of the function responsible for rendering the option interface  
	'addify-role-pricing-discount',                          // The page on which this option will be displayed  
	'af-discount-sec',         // The name of the section to which this field belongs  
	array( esc_html__('Select price (Regular/Sale) to apply discount of role based pricing.') )
);  
register_setting(  
	'afrolebased_setting_discount',  
	'afb2b_discount_price'  
);

function afb2b_discount_display_callback( $args ) {

	$roles        = get_editable_roles();
	$roles_values = get_option('afb2b_discount_price');
	?>
	<table class="addify-table-optoin">
		<tbody>
	<?php
	foreach ( $roles as $key => $value ) { 

		$radio = isset( $roles_values[ $key ] ) ? $roles_values[ $key ] : 'sale';
		?>
		
		<tr class="addify-option-field">
			<th>
				<div class="option-head">
					<b><?php echo esc_html__( $value['name'], 'addify_b2b' ); ?></b>
				</div>
			</th>
			<td>
				<input type="radio" value="regular" name="afb2b_discount_price[<?php echo esc_html( $key ); ?>]" <?php echo checked('regular', $radio); ?> ><?php echo esc_html__('Regular Price', 'addify_b2b'); ?>
				<input type="radio" value="sale" name="afb2b_discount_price[<?php echo esc_html( $key ); ?>]" <?php echo checked('sale', $radio); ?> ><?php echo esc_html__('Sale Price', 'addify_b2b'); ?>
			</td>
		</tr>
		
		<?php
	}
	$radio = isset( $roles_values['guest'] ) ? $roles_values['guest'] : '';
	?>
		<tr class="addify-option-field">
			<th>
				<div class="option-head">
					<b><?php echo esc_html__( 'Guest', 'addify_b2b' ); ?></b>
				</div>
			</th>
			<td>
				<input type="radio" value="regular" name="afb2b_discount_price[guest]" <?php echo checked('regular', $radio); ?> ><?php echo esc_html__('Regular Price', 'addify_b2b'); ?>
				<input type="radio" value="sale" name="afb2b_discount_price[guest]" <?php echo checked('sale', $radio); ?> ><?php echo esc_html__('Sale Price', 'addify_b2b'); ?>
			</td>
		</tr>
	</tbody>
		</table>
	<p><?php echo esc_html( current($args) ); ?></p>
	<?php
}
