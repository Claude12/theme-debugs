<?php
add_settings_section(  
	'af-tax-sec',         // ID used to identify this section and with which to register options  
	esc_html__('Tax Display Settings', 'addify_b2b'),   // Title to be displayed on the administration page  
	'', // Callback used to render the description of the section  
	'af_tax_diaplay_section'                           // Page on which to add this section of options  
);

add_settings_field(   
	'afb2b_tax_display',                      // ID used to identify the field throughout the theme  
	esc_html__('Tax display by user roles', 'addify_b2b'),    // The label to the left of the option interface element  
	'afb2b_tax_display_callback',   // The name of the function responsible for rendering the option interface  
	'af_tax_diaplay_section',                          // The page on which this option will be displayed  
	'af-tax-sec',         // The name of the section to which this field belongs  
	''
);  
register_setting(  
	'af_tax_diaplay_fields',  
	'afb2b_tax_display'  
);

function afb2b_tax_display_callback( $args ) {

	$roles        = get_editable_roles();
	$roles_values = get_option('afb2b_tax_display');
	?>
	<table class="addify-table-optoin">
		<tbody>
	<?php
	foreach ( $roles as $key => $value ) { 

		$radio = isset( $roles_values[ $key ] ) ? $roles_values[ $key ] : '';

		?>
		
		<tr class="addify-option-field">
			<th>
				<div class="option-head">
					<b><?php echo esc_html__( $value['name'], 'addify_b2b' ); ?></b>
				</div>
			</th>
			<td>
				<input type="radio" value="incl" name="afb2b_tax_display[<?php echo esc_html( $key ); ?>]" <?php echo checked('incl', $radio); ?> ><?php echo esc_html__('Including Tax', 'addify_b2b'); ?>
				<input type="radio" value="excl" name="afb2b_tax_display[<?php echo esc_html( $key ); ?>]" <?php echo checked('excl', $radio); ?> ><?php echo esc_html__('Excluding Tax', 'addify_b2b'); ?>
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
				<input type="radio" value="incl" name="afb2b_tax_display[guest]" <?php echo checked('incl', $radio); ?> ><?php echo esc_html__('Including Tax', 'addify_b2b'); ?>
				<input type="radio" value="excl" name="afb2b_tax_display[guest]" <?php echo checked('excl', $radio); ?> ><?php echo esc_html__('Excluding Tax', 'addify_b2b'); ?>
			</td>
		</tr>
		</tbody>
	</table>
	<?php
}
