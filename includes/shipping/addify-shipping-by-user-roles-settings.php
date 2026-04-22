<?php
add_settings_section(  
	'addify-role-based-payments_sec',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'afrbs_page_1_section_callback', // Callback used to render the description of the section  
	'addify-role-based-shipping'                           // Page on which to add this section of options  
);

add_settings_field(   
	'afb2b_shipping',                      // ID used to identify the field throughout the theme  
	esc_html__('Select Shipping Methods for User Roles', 'addify_b2b'),    // The label to the left of the option interface element  
	'afrbs_user_roles_callback',   // The name of the function responsible for rendering the option interface  
	'addify-role-based-shipping',                          // The page on which this option will be displayed  
	'addify-role-based-payments_sec',         // The name of the section to which this field belongs  
	''
);  
register_setting(  
	'afrolebased_shipping_setting',  
	'afb2b_shipping'  
);

function afrbs_page_1_section_callback() {
	?>
	<h1>
		<?php esc_html__('Role Based Shipping Methods', 'addify_b2b' ); ?> 
	</h1>
	<?php
}

function afrbs_user_roles_callback( $args ) {
	?>
		<div class="afpvu_accordian">
	<?php
	$roles            = get_editable_roles();
	$shipping_methods = WC()->shipping->get_shipping_methods();
	foreach ( $roles as $key => $value ) {
		?>
				<div id="accordion" class="accordion">
					<h3><?php echo esc_html__($value['name'], 'addify_b2b'); ?></h3>
					<div>
						<p>
							<table class="addify-table-optoin">
								<tbody>
									<tr class="addify-option-field">
										<th>
											<div class="option-head">
												<b><?php echo esc_html__('Select Shipping Methods:', 'addify_b2b'); ?></b>
											</div>
										</th>
										<td>
										<?php
										$saved_options = get_option('afb2b_shipping');
										if (isset($saved_options[ $key ])) {
											$saved_options = $saved_options[ $key ];
										} else {
											$saved_options = array();
										}
												 
										foreach ($shipping_methods as $key1 => $shipping_method) {
											?>
													<input type="checkbox" name="afb2b_shipping[<?php echo esc_attr($key); ?>][]" id="afrfq_enable_name_field" value="<?php echo esc_attr($key1); ?>" 
																										<?php 
																										if (in_array($key1, $saved_options) ) {
																															echo 'checked';
																										}
																										?>
													>
											<?php echo esc_html__($shipping_method->method_title); ?>
													<br>
											<?php
										}
										?>
										</td>
									</tr>
								</tbody>
							</table>
						</p>
					</div>
				</div>
		<?php
	}
	?>
			<div id="accordion" class="accordion">
				<h3><?php echo esc_html__('Guest', 'addify_b2b'); ?></h3>
				<div>
					<p>
						<table class="addify-table-optoin">
							<tbody>
								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<b><?php echo esc_html__('Select Payment Methods:', 'addify_b2b'); ?></b>
										</div>
									</th>
									<td>
										<?php
										$saved_options = get_option('afb2b_shipping');
										if (isset($saved_options['guest'])) {
											$saved_options = $saved_options['guest'];
										} else {
											$saved_options = array();
										}
											 
										foreach ($shipping_methods as $key1 => $shipping_method) {
											?>
												<input type="checkbox" name="afb2b_shipping[guest][]" id="afrfq_enable_name_field" value="<?php echo esc_attr($key1); ?>" 
																																					<?php 
																																					if (in_array($key1, $saved_options) ) {
																																																			echo 'checked';
																																					}
																																					?>
												>
											<?php echo esc_html__($shipping_method->method_title); ?>
												<br>
											<?php
										}
										?>
									</td>
								</tr>
							</tbody>
						</table>
					</p>
				</div>
			</div>
		</div>

	<?php
}
