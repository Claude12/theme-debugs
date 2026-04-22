<?php
add_settings_section(  
	'addify-role-based-payments_sec',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'afrbp_page_1_section_callback', // Callback used to render the description of the section  
	'addify-role-based-payments'                           // Page on which to add this section of options  
);

add_settings_field(   
	'afb2b_payments',                      // ID used to identify the field throughout the theme  
	esc_html__('Select Payment Method for User Roles', 'addify_b2b'),    // The label to the left of the option interface element  
	'afrbp_user_roles_callback',   // The name of the function responsible for rendering the option interface  
	'addify-role-based-payments',                          // The page on which this option will be displayed  
	'addify-role-based-payments_sec',         // The name of the section to which this field belongs  
	''
);  
register_setting(  
	'afrolebased_payments_setting',  
	'afb2b_payments'  
);

function afrbp_page_1_section_callback() {
	?>
	<h1>
		<?php esc_html_e('Role Based Payment Methods', 'addify_b2b' ); ?>
	</h1>
	<?php
}

function afrbp_user_roles_callback( $args ) {
	?>
	<div class="afpvu_accordian">
	<?php
	$roles                    = get_editable_roles();
	$payment_gateways_obj     = new WC_Payment_Gateways(); 
	$enabled_payment_gateways = $payment_gateways_obj->payment_gateways();

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
												<b><?php echo esc_html__('Select Payment Methods:', 'addify_b2b'); ?></b>
											</div>
										</th>
										<td>
										<?php
										$saved_options = get_option('afb2b_payments');
										if (isset($saved_options[ $key ])) {
											$saved_options = $saved_options[ $key ];
										} else {
											$saved_options = array();
										}
												 
										foreach ($enabled_payment_gateways as $key1 => $payment_gateway) {
											?>
											<input type="checkbox" name="afb2b_payments[<?php echo esc_attr($key); ?>][]" id="afrfq_enable_name_field" value="<?php echo esc_attr($key1); ?>" 
												<?php 
												if (in_array($key1, $saved_options) ) {
													echo 'checked';
												}
												?>
											>
											<?php echo esc_html__($payment_gateway->title); ?>
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
										$saved_options = get_option('afb2b_payments');
										if (isset($saved_options['guest'])) {
											$saved_options = $saved_options['guest'];
										} else {
											$saved_options = array();
										}
											 
										foreach ($enabled_payment_gateways as $key1 => $payment_gateway) {
											?>
												<input type="checkbox" name="afb2b_payments[guest][]" id="afrfq_enable_name_field" value="<?php echo esc_attr($key1); ?>" 															 
																																					<?php 
																																					if (in_array($key1, $saved_options) ) {
																																						echo 'checked';
																																					}
																																					?>
												>
											<?php echo esc_html__($payment_gateway->title); ?>
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
