<div id='addify_csp_panel_customer' class='panel woocommerce_options_panel'>
	<div class="options_group">
		
		<p><strong><?php echo esc_html__('Important Notes:', 'addify_b2b'); ?></strong></p>
		<ol>
			<li><strong><?php echo esc_html__('Pricing Priority:', 'addify_b2b'); ?></strong>
				<ul>
					<li>I - <?php echo esc_html__('Price Specific to a Customer', 'addify_b2b'); ?></li>
					<li>II - <?php echo esc_html__('Price Specific to a Role', 'addify_b2b'); ?></li>
					<li>III - <?php echo esc_html__('Regular Product Price', 'addify_b2b'); ?></li>
				</ul>
			</li>
		</ol>

		<div class="af_price_div">
			<h3><?php echo esc_html__('Role Based Pricing(By Customers)', 'addify_b2b'); ?></h3>
			<p><?php echo esc_html__('If more than one rule is applied on same customer then rule that is added last will be applied.', 'addify_b2b'); ?></p>
			<div class="cdiv">
				<table cellspacing="0" cellpadding="0" border="1" width="900">
					<thead>
						<tr>
							<th align="center" class="cname"><?php echo esc_html__('Customer', 'addify_b2b'); ?></th>
							<th align="center" class="cname"><?php echo esc_html__('Adjustment Type', 'addify_b2b'); ?></th>
							<th align="center" class="cname"><?php echo esc_html__('Value', 'addify_b2b'); ?></th>
							<th align="center" class="cname"><?php echo esc_html__('Min Qty', 'addify_b2b'); ?></th>
							<th align="center" class="cname"><?php echo esc_html__('Max Qty', 'addify_b2b'); ?></th>
							<th align="center" class="cname"><?php echo esc_html__('Replace Original Price?', 'addify_b2b'); ?>
								<div class="tooltip">?
									<span class="tooltiptext"><?php echo esc_html__('This will only work for Fixed Price, Fixed Decrease and Percentage Decrease.', 'addify_b2b'); ?></span>
								</div>
							</th>
							<th align="center" class="cname"><?php echo esc_html__('Remove', 'addify_b2b'); ?></th>
						</tr>
					</thead>

					<tbody>

						<?php

						$a = 1;
						
						if (!empty($cus_base_prices)) {

							foreach ($cus_base_prices as $cus_price) {

								if (!empty($cus_price['replace_orignal_price'])) {
									$replace_orignal_price = 'yes';
								} else {
									$replace_orignal_price = 'no';
								}
								if (!isset($cus_price['customer_name']) ) {
									continue;
								}
								$author_obj = get_user_by('id', $cus_price['customer_name']);
								if (null == $author_obj ) {
									continue;
								}
								
								?>


										<tr id="filter-row-rule<?php echo intval($a); ?>">

											<td align="center" class="cname">

												<select class="sel22" name="cus_base_price[<?php echo intval($a); ?>][customer_name]">


												<option value="<?php echo intval($author_obj->ID); ?>" selected="selected"><?php echo esc_attr($author_obj->display_name); ?>(<?php echo esc_attr($author_obj->user_email); ?>)</option>

												</select>

											</td>

											<td align="center" class="cname">

												<select name="cus_base_price[<?php echo intval($a); ?>][discount_type]">

													<option value="fixed_price" <?php echo selected('fixed_price', $cus_price['discount_type']); ?>><?php echo esc_html__('Fixed Price', 'addify_b2b'); ?></option>
													<option value="fixed_increase" <?php echo selected('fixed_increase', $cus_price['discount_type']); ?>><?php echo esc_html__('Fixed Increase', 'addify_b2b'); ?></option>
													<option value="fixed_decrease" <?php echo selected('fixed_decrease', $cus_price['discount_type']); ?>><?php echo esc_html__('Fixed Decrease', 'addify_b2b'); ?></option>
													<option value="percentage_decrease" <?php echo selected('percentage_decrease', $cus_price['discount_type']); ?>><?php echo esc_html__('Percentage Decrease', 'addify_b2b'); ?></option>
													<option value="percentage_increase" <?php echo selected('percentage_increase', $cus_price['discount_type']); ?>><?php echo esc_html__('Percentage Increase', 'addify_b2b'); ?></option>

												</select>

											</td>

											<td align="center" class="cname">

												<input value="<?php echo esc_attr($cus_price['discount_value']); ?>" class="csp_input" type="text" name="cus_base_price[<?php echo intval($a); ?>][discount_value]">

											</td>

											<td align="center" class="cname">

												<input value="<?php echo esc_attr($cus_price['min_qty']); ?>" class="csp_input" type="number" min="0" value="0" name="cus_base_price[<?php echo intval($a); ?>][min_qty]">

											</td>

											<td class="cname">

												<input value="<?php echo esc_attr($cus_price['max_qty']); ?>" class="csp_input" align="center" type="number" min="0" value="0" name="cus_base_price[<?php echo intval($a); ?>][max_qty]">

											</td>

											<td align="center" class="cname">
												<input type="checkbox" name="cus_base_price[<?php echo intval($a); ?>][replace_orignal_price]" value="yes" <?php echo checked('yes', $replace_orignal_price); ?> />
											</td>


											<td align="center" class="cname">

												<a onclick="jQuery('#filter-row-rule<?php echo intval($a); ?>').remove();" class="button button-danger"><?php esc_html_e('X', 'addify_b2b'); ?></a>

											</td>

										</tr>


								<?php

								++$a;
							}

						}

						?>
						
					</tbody>

					<tfoot>
						<tr class="topfilters" id="beforetff"></tr>
					</tfoot>
				</table>

				<div class="add_rule_bt_div">
					<input type="button" class="btt2 button button-primary button-large" value="<?php echo esc_html__('Add Rule', 'addify_b2b'); ?>" onClick="addRule();">
				</div>
			</div>
		</div>

	</div>
</div>

<!-- User Roles -->
<div id='addify_csp_panel_role' class='panel woocommerce_options_panel'>
	<div class="options_group">
		
		<p><strong><?php echo esc_html__('Important Notes:', 'addify_b2b'); ?></strong></p>
		<ol>
			<li><strong><?php echo esc_html__('Pricing Priority:', 'addify_b2b'); ?></strong>
				<ul>
					<li>I - <?php echo esc_html__('Price Specific to a Customer', 'addify_b2b'); ?></li>
					<li>II - <?php echo esc_html__('Price Specific to a Role', 'addify_b2b'); ?></li>
					<li>III - <?php echo esc_html__('Regular Product Price', 'addify_b2b'); ?></li>
				</ul>
			</li>
		</ol>

		<div class="af_price_div">
			<h3><?php echo esc_html__('Role Based Pricing(By User Roles)', 'addify_b2b'); ?></h3>
			<table width="900" cellspacing="0" cellpadding="0" border="1">
				<thead>
					<tr>
						<th align="center" class="cname"><?php echo esc_html__('User Role', 'addify_b2b'); ?></th>
						<th align="center" class="cname"><?php echo esc_html__('Adjustment Type', 'addify_b2b'); ?></th>
						<th align="center" class="cname"><?php echo esc_html__('Value', 'addify_b2b'); ?></th>
						<th align="center" class="cname"><?php echo esc_html__('Min Qty', 'addify_b2b'); ?></th>
						<th align="center" class="cname"><?php echo esc_html__('Max Qty', 'addify_b2b'); ?></th>
						<th align="center" class="cname"><?php echo esc_html__('Replace Original Price?', 'addify_b2b'); ?>
							<div class="tooltip">?
								<span class="tooltiptext"><?php echo esc_html__('This will only work for Fixed Price, Fixed Decrease and Percentage Decrease.', 'addify_b2b'); ?></span>
							</div>
						</th>
						<th align="center" class="cname"><?php echo esc_html__('Remove', 'addify_b2b'); ?></th>
					</tr>
					
				</thead>
				
				<tbody>
					
					<?php
						$b = 1;
						
					if (!empty($role_base_prices)) {


						foreach ($role_base_prices as $role_price) {

							$replace_orignal_price = isset( $role_price['replace_orignal_price'] ) ? $role_price['replace_orignal_price'] : '';
							?>


									<tr id="filter-row-rule-role<?php echo intval($b); ?>">

										<td align="center" class="cname">

											<select name="role_base_price[<?php echo intval($b); ?>][user_role]">

											<?php

											global $wp_roles;
											$roles = $wp_roles->get_names();
											foreach ($roles as $key => $value) { 
												?>

													<option value="<?php echo esc_attr($key); ?>" <?php echo selected(esc_attr($key), $role_price['user_role']); ?>><?php echo esc_attr(translate_user_role( $value, 'default' )); ?></option>
											
												<?php } ?>

												<option value="guest" <?php echo selected('guest', $role_price['user_role']); ?>><?php echo esc_html__('Guest', 'addify_b2b'); ?></option>

											</select>

										</td>

										<td align="center" class="cname">

											<select name="role_base_price[<?php echo intval($b); ?>][discount_type]">

												<option value="fixed_price" <?php echo selected('fixed_price', $role_price['discount_type']); ?>><?php echo esc_html__('Fixed Price', 'addify_b2b'); ?></option>
												<option value="fixed_increase" <?php echo selected('fixed_increase', $role_price['discount_type']); ?>><?php echo esc_html__('Fixed Increase', 'addify_b2b'); ?></option>
												<option value="fixed_decrease" <?php echo selected('fixed_decrease', $role_price['discount_type']); ?>><?php echo esc_html__('Fixed Decrease', 'addify_b2b'); ?></option>
												<option value="percentage_decrease" <?php echo selected('percentage_decrease', $role_price['discount_type']); ?>><?php echo esc_html__('Percentage Decrease', 'addify_b2b'); ?></option>
												<option value="percentage_increase" <?php echo selected('percentage_increase', $role_price['discount_type']); ?>><?php echo esc_html__('Percentage Increase', 'addify_b2b'); ?></option>

											</select>

										</td>

										<td align="center" class="cname">

											<input value="<?php echo esc_attr($role_price['discount_value']); ?>" class="csp_input" type="text" name="role_base_price[<?php echo intval($b); ?>][discount_value]">

										</td>

										<td align="center" class="cname">

											<input value="<?php echo esc_attr($role_price['min_qty']); ?>" class="csp_input" type="number" min="0" value="0" name="role_base_price[<?php echo intval($b); ?>][min_qty]">

										</td>

										<td class="cname">

											<input value="<?php echo esc_attr($role_price['max_qty']); ?>" class="csp_input" align="center" type="number" min="0" value="0" name="role_base_price[<?php echo intval($b); ?>][max_qty]">

										</td>

										<td align="center" class="cname">
											<input type="checkbox" name="role_base_price[<?php echo intval($b); ?>][replace_orignal_price]" value="yes" <?php echo checked('yes', $replace_orignal_price ); ?> />
										</td>


										<td align="center" class="cname">

											<a onclick="jQuery('#filter-row-rule-role<?php echo intval($b); ?>').remove();" class="button button-danger"><?php esc_html_e('X', 'addify_b2b'); ?></a>

										</td>

									</tr>


								<?php
								++$b;

						}

					}


					?>


				</tbody>
				<tfoot>
					<tr class="topfilters_rolebase" id="beforetff_rolebase"></tr>
				</tfoot>

			</table>


			<div class="add_rule_bt_div">
				<input type="button" class="btt2 button button-primary button-large" value="<?php echo esc_html__('Add Rule', 'addify_b2b'); ?>" onClick="addRuleRoleBase();">
			</div>

		</div>

	</div>
</div>

<script type="text/javascript" defer>
	var filter_row_rule = 10000;

	function addRule() {

		var aa = jQuery('.sel2').val();


		html  = '<tr id="filter-row-rule' + filter_row_rule + '">';

			html += '<td align="center" class="cname">';

				html += '<select class="sel2" name="cus_base_price[' + filter_row_rule + '][customer_name]">';

					

				html += '</select>';

			html += '</td>';

			html += '<td align="center" class="cname">';

				html += '<select name="cus_base_price[' + filter_row_rule + '][discount_type]">';

					html += '<option value="fixed_price"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Price', 'addify_b2b'))))); ?></option>';
					html += '<option value="fixed_increase"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Increase', 'addify_b2b'))))); ?></option>';
					html += '<option value="fixed_decrease"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Decrease', 'addify_b2b'))))); ?></option>';
					html += '<option value="percentage_decrease"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Percentage Decrease', 'addify_b2b'))))); ?></option>';
					html += '<option value="percentage_increase"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Percentage Increase', 'addify_b2b'))))); ?></option>';

					html += '</select>';

			html += '</td>';

			html += '<td align="center" class="cname">';

				html += '<input class="csp_input" type="text" name="cus_base_price[' + filter_row_rule + '][discount_value]">';

			html += '</td>';

			html += '<td align="center" class="cname">';

				html += '<input class="csp_input" type="number" min="0" value="0" name="cus_base_price[' + filter_row_rule + '][min_qty]">';

			html += '</td>';

			html += '<td class="cname">';

				html += '<input class="csp_input" align="center" type="number" min="0" value="0" name="cus_base_price[' + filter_row_rule + '][max_qty]">';

			html += '</td>';

			html += '<td class="cname" align="center">';

				html += '<input class="" align="center" type="checkbox" value="yes" name="cus_base_price[' + filter_row_rule + '][replace_orignal_price]">';

			html += '</td>';


			html += '<td align="center" class="cname">';

				html += '<a onclick="jQuery(\'#filter-row-rule' + filter_row_rule + '\').remove();" class="button button-danger"><?php esc_html_e('X', 'addify_b2b'); ?></a>';

			html += '</td>';

		html  += '</tr>';

		jQuery('#beforetff').before(html);

		var ajaxurl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
		var nonce   = '<?php echo esc_attr(wp_create_nonce('afrolebase-ajax-nonce')); ?>';

		jQuery('.sel2').select2({

			ajax: {
				url: ajaxurl, // AJAX URL is predefined in WordPress admin
				dataType: 'json',
				type: 'POST',
				delay: 250, // delay in ms while typing when to perform a AJAX search
				data: function (params) {
					return {
						q: params.term, // search query
						action: 'cspsearchUsers', // AJAX action for admin-ajax.php
						nonce: nonce // AJAX nonce for admin-ajax.php
					};
				},
				processResults: function( data ) {
					var options = [];
					if ( data ) {
	   
						// data is the array of arrays, and each of them contains ID and the Label of the option
						jQuery.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
							options.push( { id: text[0], text: text[1]  } );
						});
	   
					}
					return {
						results: options
					};
				},
				cache: true
			},
			multiple: false,
			placeholder: 'Choose Users',
			minimumInputLength: 3 // the minimum of symbols to input before perform a search
			
		});

		filter_row_rule++;

	}



	var filter_row_rule_role = 10000;

	function addRuleRoleBase() {

		html  = '<tr id="filter-row-rule-role' + filter_row_rule_role + '">';

			html += '<td align="center" class="cname">';
				html += '<select name="role_base_price[' + filter_row_rule_role + '][user_role]">';

					<?php

						global $wp_roles;
						$roles = $wp_roles->get_names();
					foreach ($roles as $key => $value) { 
						?>

						html += '<option value="<?php echo esc_attr($key); ?>"><?php echo esc_attr(translate_user_role( $value, 'default' )); ?></option>';

					<?php } ?>
					html += '<option value="guest"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Guest', 'addify_b2b'))))); ?></option>';
				html += '</select>';
			html += '</td>';


			html += '<td align="center" class="cname">';
				html += '<select name="role_base_price[' + filter_row_rule_role + '][discount_type]">';

					html += '<option value="fixed_price"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Price', 'addify_b2b'))))); ?></option>';
					html += '<option value="fixed_increase"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Increase', 'addify_b2b'))))); ?></option>';
					html += '<option value="fixed_decrease"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Decrease', 'addify_b2b'))))); ?></option>';
					html += '<option value="percentage_decrease"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Percentage Decrease', 'addify_b2b'))))); ?></option>';
					html += '<option value="percentage_increase"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Percentage Increase', 'addify_b2b'))))); ?></option>';

					html += '</select>';
			html += '</td>';

			html += '<td align="center" class="cname">';

				html += '<input class="csp_input" type="text" name="role_base_price[' + filter_row_rule_role + '][discount_value]">';

			html += '</td>';

			html += '<td align="center" class="cname">';

				html += '<input class="csp_input" type="number" min="0" value="0" name="role_base_price[' + filter_row_rule_role + '][min_qty]">';

			html += '</td>';

			html += '<td class="cname">';

				html += '<input class="csp_input" align="center" type="number" min="0" value="0" name="role_base_price[' + filter_row_rule_role + '][max_qty]">';

			html += '</td>';

			html += '<td class="cname" align="center">';

				html += '<input class="" align="center" type="checkbox" value="yes" name="role_base_price[' + filter_row_rule_role + '][replace_orignal_price]">';

			html += '</td>';


			html += '<td align="center" class="cname">';

				html += '<a onclick="jQuery(\'#filter-row-rule-role' + filter_row_rule_role + '\').remove();" class="button button-danger"><?php esc_html_e('X', 'addify_b2b'); ?></a>';

			html += '</td>';

		html += '</tr>';

		jQuery('#beforetff_rolebase').before(html);


		filter_row_rule_role++;

	}


</script>
