<?php

if (! defined('ABSPATH') ) { 
	exit; // restict for direct access
}
?>

<div class="csp_admin_main">
	<div class="csp_admin_main_left"><label><strong><?php echo esc_html__('Rule Priority', 'addify_b2b'); ?></strong></label></div>
	<div class="csp_admin_main_right">
		<input type="number" name="csp_rule_priority" class="rule_input" min="0" max="100" placeholder="0" value="<?php echo esc_attr($post->menu_order); ?>" />
		<input type="hidden" name="csp_rules" class="rule_input" value="yes" />
		<p class="csp_msg"><?php echo esc_html__('Provide number between 0 and 100, If more than one rules are applied on same item then rule with higher priority will be applied. 1 is high and 100 is low.', 'addify_b2b'); ?></p>
	</div>
</div>

<div class="csp_admin_main">
	<div class="csp_admin_main_left"><label><strong><?php echo esc_html__('Apply on All Products', 'addify_b2b'); ?></strong></label></div>
	<div class="csp_admin_main_right">
		<?php
		$applied_on_all_products = get_post_meta($post->ID, 'csp_apply_on_all_products', true);
		?>
		<input type="checkbox" name="csp_apply_on_all_products" id="csp_apply_on_all_products" value="yes" <?php echo checked('yes', $applied_on_all_products); ?>>
		<p class="csp_msg"><?php echo esc_html__('Check this if you want to apply this rule on all products.', 'addify_b2b'); ?></p>
	</div>
</div>

<div class="csp_admin_main hide_all_pro">
	<div class="csp_admin_main_left"><label><strong><?php echo esc_html__('Select Products', 'addify_b2b'); ?></strong></label></div>
	<div class="csp_admin_main_right">
		<?php
		$applied_on = get_post_meta($post->ID, 'csp_applied_on_products', true);
		?>
		<select name="csp_applied_on_products[]" id="csp_applied_on_products" class="applied_on_products sel_pros" multiple="multiple" style="width:100%">

			<?php
			if (!empty($applied_on)) {

				foreach ( $applied_on as $pro) {

					$prod_post = get_post($pro);

					?>

						<option value="<?php echo intval($pro); ?>" selected="selected"><?php echo esc_attr($prod_post->post_title); ?></option>

					<?php 
				}
			}
			?>
			
		</select>

		

	</div>
</div>

<div class="csp_admin_main hide_all_pro">
	<div class="csp_admin_main_left"><label><strong><?php echo esc_html__('Select Categories', 'addify_b2b'); ?></strong></label></div>
	<div class="csp_admin_main_right">
			
		<div class="all_cats">
			<ul>

				<?php


				if (!empty($csp_applied_on_categories)) {

					$pre_vals = $csp_applied_on_categories;
						

				}


					

					$product_cat = get_terms( array(
						'taxonomy' => 'product_cat',
						
					) );

					foreach ($product_cat as $parent_product_cat) { 
						?>
							<li class="par_cat">
								<input type="checkbox" class="parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval($parent_product_cat->term_id); ?>" 
																																						<?php 
																																						if (!empty($pre_vals) && in_array($parent_product_cat->term_id, $pre_vals)) {
																																							echo 'checked'; 
																																						} 
																																						?>
								/>
						<?php echo esc_attr($parent_product_cat->name); ?>

						<?php
						$child_args         = array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => false,
							'parent'     => $parent_product_cat->term_id,
						);
						$child_product_cats = get_terms($child_args);
						if (!empty($child_product_cats)) { 
							?>
										<ul>
							<?php foreach ($child_product_cats as $child_product_cat) { ?>
												<li class="child_cat">
													<input type="checkbox" class="child parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval($child_product_cat->term_id); ?>" 
																																													<?php 
																																													if (!empty($pre_vals) &&in_array($child_product_cat->term_id, $pre_vals)) {
																																														echo 'checked'; 
																																													} 
																																													?>
													/>
								<?php echo esc_attr($child_product_cat->name); ?>


								<?php 
								//2nd level
								$child_args2 = array(
									'taxonomy'   => 'product_cat',
									'hide_empty' => false,
									'parent'     => $child_product_cat->term_id,
								);

								$child_product_cats2 = get_terms($child_args2);
								if (!empty($child_product_cats2)) { 
									?>

														<ul>
									<?php foreach ($child_product_cats2 as $child_product_cat2) { ?>

																<li class="child_cat">
																	<input type="checkbox" class="child parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval($child_product_cat2->term_id); ?>" 
																																																	<?php 
																																																	if (!empty($pre_vals) &&in_array($child_product_cat2->term_id, $pre_vals)) {
																																																		echo 'checked'; 
																																																	} 
																																																	?>
																	/>
										<?php echo esc_attr($child_product_cat2->name); ?>


										<?php 
										//3rd level
										$child_args3 = array(
											'taxonomy'   => 'product_cat',
											'hide_empty' => false,
											'parent'     => $child_product_cat2->term_id,
										);

										$child_product_cats3 = get_terms($child_args3);
										if (!empty($child_product_cats3)) { 
											?>

																		<ul>
											<?php foreach ($child_product_cats3 as $child_product_cat3) { ?>

																				<li class="child_cat">
																					<input type="checkbox" class="child parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval($child_product_cat3->term_id); ?>" 
																																																					<?php 
																																																					if (!empty($pre_vals) &&in_array($child_product_cat3->term_id, $pre_vals)) {
																																																						echo 'checked'; 
																																																					} 
																																																					?>
																					/>
												<?php echo esc_attr($child_product_cat3->name); ?>


												<?php 
												//4th level
												$child_args4 = array(
													'taxonomy' => 'product_cat',
													'hide_empty' => false,
													'parent'   => $child_product_cat3->term_id,
												);

												$child_product_cats4 = get_terms($child_args4);
												if (!empty($child_product_cats4)) { 
													?>

																						<ul>
													<?php foreach ($child_product_cats4 as $child_product_cat4) { ?>

																								<li class="child_cat">
																									<input type="checkbox" class="child parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval($child_product_cat4->term_id); ?>" 
																																																									<?php 
																																																									if (!empty($pre_vals) &&in_array($child_product_cat4->term_id, $pre_vals)) {
																																																										echo 'checked'; 
																																																									} 
																																																									?>
																									/>
														<?php echo esc_attr($child_product_cat4->name); ?>


														<?php 
														//5th level
														$child_args5 = array(
															'taxonomy' => 'product_cat',
															'hide_empty' => false,
															'parent'   => $child_product_cat4->term_id,
														);

														$child_product_cats5 = get_terms($child_args5);
														if (!empty($child_product_cats5)) { 
															?>

																										<ul>
															<?php foreach ($child_product_cats5 as $child_product_cat5) { ?>

																												<li class="child_cat">
																													<input type="checkbox" class="child parent" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval($child_product_cat5->term_id); ?>" 
																																																													<?php 
																																																													if (!empty($pre_vals) &&in_array($child_product_cat5->term_id, $pre_vals)) {
																																																														echo 'checked'; 
																																																													} 
																																																													?>
																													/>
																<?php echo esc_attr($child_product_cat5->name); ?>


																<?php 
																//6th level
																$child_args6 = array(
																	'taxonomy' => 'product_cat',
																	'hide_empty' => false,
																	'parent'   => $child_product_cat5->term_id,
																);

																$child_product_cats6 = get_terms($child_args6);
																if (!empty($child_product_cats6)) { 
																	?>

																														<ul>
																	<?php foreach ($child_product_cats6 as $child_product_cat6) { ?>

																																<li class="child_cat">
																																	<input type="checkbox" class="child" name="csp_applied_on_categories[]" id="csp_applied_on_categories" value="<?php echo intval($child_product_cat6->term_id); ?>" 
																																																															<?php 
																																																															if (!empty($pre_vals) && in_array($child_product_cat6->term_id, $pre_vals)) {
																																																																echo 'checked'; 
																																																															} 
																																																															?>
																																	/>
																		<?php echo esc_attr($child_product_cat6->name); ?>
																																</li>

																	<?php } ?>
																														</ul>

																<?php } ?>




																												</li>

															<?php } ?>
																										</ul>

														<?php } ?>


																								</li>

													<?php } ?>
																						</ul>

												<?php } ?>


																				</li>

											<?php } ?>
																		</ul>

										<?php } ?>


																</li>

															

									<?php } ?>
														</ul>

								<?php } ?>



												</li>
							<?php } ?>
										</ul>
						<?php } ?>
						
							</li>
						<?php 
					}
					?>
			</ul>
		</div>
	</div>

</div>

<div class="csp_admin_main hide_all_pro">

	<?php
	global $post;

	

	$rbp_products_brand = get_terms( 'product_brand' );

	if ( in_array( 'woocommerce-brands/woocommerce-brands.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) : 
		?>

	<div class="csp_admin_main_left"><label><strong><?php echo esc_html__('Select Brands', 'addify_b2b'); ?></strong></label></div>

	<div class="csp_admin_main_right">
		<?php

		global $post;
		$rbp_selected_brands = json_decode( get_post_meta( $post->ID, 'rbp_multi_brands', true ) );
		$rbp_selected_brands = is_array( $rbp_selected_brands ) ? $rbp_selected_brands : array();

		?>
<select name="rbp_multi_brands[]" id="rbp_product_brand" data-placeholder="Choose Brands..." class="rbp_chose_select_brand" multiple="multiple" style="width: 100%;">;
		<?php

		foreach ( $rbp_products_brand  as $rbp_brands ) {
			?>
		<option value="<?php echo esc_html( $rbp_brands->term_id ); ?>"
			<?php
			if ( in_array( (string) $rbp_brands->term_id, (array) $rbp_selected_brands, true ) ) {
				echo 'selected';
			}
			?>
	><?php echo esc_html( $rbp_brands->name ); ?>
		</option>
			<?php
		}
		?>
	</select>	
		<p class="description"> </p>
	</div>
	<?php endif; ?>

</div>


<div class="csp_admin_main">

	
	<h3><?php echo esc_html__('Role Based Pricing(By Customers)', 'addify_b2b'); ?></h3>
	<p><?php echo esc_html__('If more than one rule is applied on same customer then rule that is added last will be applied.', 'addify_b2b'); ?></p>
		<div class="cdiv">
		<table cellspacing="0" cellpadding="0" border="1" width="100%">
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
			if (!empty($rcus_base_price)) {
				foreach ($rcus_base_price as $cus_price) { 

					if (!empty($cus_price['replace_orignal_price'])) {

							$replace_orignal_price = 'yes';
					} else {
						$replace_orignal_price = 'no';
					}
					if (!isset($cus_price['customer_name'])) {
						continue;
					}
					$author_obj = get_user_by('id', $cus_price['customer_name']);
					if (null == $author_obj ) {
							continue;
					}

					?>

				<tr id="filter-row-rule<?php echo intval($post->ID); ?><?php echo intval($a); ?>">
					<td align="center" class="cname">
						<select class="sel22" name="rcus_base_price[<?php echo intval($a); ?>][customer_name]">

							<option value="<?php echo intval($cus_price['customer_name']); ?>" selected="selected"><?php echo esc_attr($author_obj->display_name); ?>(<?php echo esc_attr($author_obj->user_email); ?>)</option>
							
						</select>
					</td>
					<td align="center" class="cname">
						<select name="rcus_base_price[<?php echo intval($a); ?>][discount_type]">

							<option value="fixed_price" <?php echo selected('fixed_price', $cus_price['discount_type']); ?>><?php echo esc_html__('Fixed Price', 'addify_b2b'); ?></option>
							<option value="fixed_increase" <?php echo selected('fixed_increase', $cus_price['discount_type']); ?>><?php echo esc_html__('Fixed Increase', 'addify_b2b'); ?></option>
							<option value="fixed_decrease" <?php echo selected('fixed_decrease', $cus_price['discount_type']); ?>><?php echo esc_html__('Fixed Decrease', 'addify_b2b'); ?></option>
							<option value="percentage_decrease" <?php echo selected('percentage_decrease', $cus_price['discount_type']); ?>><?php echo esc_html__('Percentage Decrease', 'addify_b2b'); ?></option>
							<option value="percentage_increase" <?php echo selected('percentage_increase', $cus_price['discount_type']); ?>><?php echo esc_html__('Percentage Increase', 'addify_b2b'); ?></option>

						</select>
					</td>
					<td align="center" class="cname">
						<input class="csp_input" type="text" name="rcus_base_price[<?php echo intval($a); ?>][discount_value]" value = "<?php echo esc_attr($cus_price['discount_value']); ?>" />
					</td>
					<td align="center" class="cname">
						<input class="csp_input" type="number" min="0" name="rcus_base_price[<?php echo intval($a); ?>][min_qty]" value="<?php echo esc_attr($cus_price['min_qty']); ?>" />
					</td>
					<td align="center" class="cname">
						<input class="csp_input" type="number" min="0" name="rcus_base_price[<?php echo intval($a); ?>][max_qty]" value="<?php echo esc_attr($cus_price['max_qty']); ?>" />
					</td>

					<td align="center" class="cname">
						<input type="checkbox" name="rcus_base_price[<?php echo intval($a); ?>][replace_orignal_price]" value="yes" <?php echo checked('yes', $replace_orignal_price); ?> />
					</td>
					
					<td align="center" class="cname">
						<a onclick="jQuery('#filter-row-rule<?php echo intval($post->ID); ?><?php echo intval($a); ?>').remove();" class="button button-danger button-large">X</a>
					</td>
				</tr>

					<?php 
					++$a;
				} 
			} 
			?>
				
			</tbody>

			<tfoot>
				<tr class="topfilters" id="beforerulectf"></tr>
			</tfoot>
		</table>

		<div class="add_rule_bt_div">
			<input type="button" class="btt2 button button-primary button-large" value="<?php echo esc_html__('Add Rule', 'addify_b2b'); ?>" onClick="addGlobalRule();">
		</div>

		</div>

</div>

<div class="csp_admin_main">
	

	<div class="options_group">
		
		<h3><?php echo esc_html__('Role Based Pricing(By User Roles)', 'addify_b2b'); ?></h3>
		<div class="cdiv">
			
			<table cellspacing="0" cellpadding="0" border="1" width="100%">
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

					if (!empty($rrole_base_price)) {

						foreach ($rrole_base_price as $role_price) {

							if (!empty($role_price['replace_orignal_price'])) {

								$replace_orignal_price = 'yes';
							} else {
								$replace_orignal_price = 'no';
							}

								

							?>


									<tr id="filter-row-rule-role<?php echo intval($b); ?>">

										<td align="center" class="cname">

											<select name="rrole_base_price[<?php echo intval($b); ?>][user_role]">

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

											<select name="rrole_base_price[<?php echo intval($b); ?>][discount_type]">

												<option value="fixed_price" <?php echo selected('fixed_price', $role_price['discount_type']); ?>><?php echo esc_html__('Fixed Price', 'addify_b2b'); ?></option>
												<option value="fixed_increase" <?php echo selected('fixed_increase', $role_price['discount_type']); ?>><?php echo esc_html__('Fixed Increase', 'addify_b2b'); ?></option>
												<option value="fixed_decrease" <?php echo selected('fixed_decrease', $role_price['discount_type']); ?>><?php echo esc_html__('Fixed Decrease', 'addify_b2b'); ?></option>
												<option value="percentage_decrease" <?php echo selected('percentage_decrease', $role_price['discount_type']); ?>><?php echo esc_html__('Percentage Decrease', 'addify_b2b'); ?></option>
												<option value="percentage_increase" <?php echo selected('percentage_increase', $role_price['discount_type']); ?>><?php echo esc_html__('Percentage Increase', 'addify_b2b'); ?></option>

											</select>

										</td>

										<td align="center" class="cname">

											<input value="<?php echo esc_attr($role_price['discount_value']); ?>" class="csp_input" type="text" name="rrole_base_price[<?php echo intval($b); ?>][discount_value]">

										</td>

										<td align="center" class="cname">

											<input value="<?php echo esc_attr($role_price['min_qty']); ?>" class="csp_input" type="number" min="0" value="0" name="rrole_base_price[<?php echo intval($b); ?>][min_qty]">

										</td>

										<td class="cname">

											<input value="<?php echo esc_attr($role_price['max_qty']); ?>" class="csp_input" align="center" type="number" min="0" value="0" name="rrole_base_price[<?php echo intval($b); ?>][max_qty]">

										</td>

										<td align="center" class="cname">
											<input type="checkbox" name="rrole_base_price[<?php echo intval($b); ?>][replace_orignal_price]" value="yes" <?php echo checked('yes', $replace_orignal_price); ?> />
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
					<tr class="topfilters" id="beforerulectf_role"></tr>
				</tfoot>

			</table>

			<div class="add_rule_bt_div">
				<input type="button" class="btt2 button button-primary button-large" value="<?php echo esc_html__('Add Rule', 'addify_b2b'); ?>" onClick="addGlobalRuleRole();">
			</div>

		</div>

	</div>

</div>


<script type="text/javascript" defer>
	var filter_row_rule = 10000;

	function addGlobalRule() {

		var aa = jQuery('.sel2').val();


		html  = "<tr id='filter-row-rule" + filter_row_rule + "'>";

			html += "<td align='center' class='cname'>";

				html += "<select class='sel2' name='rcus_base_price[" + filter_row_rule + "][customer_name]'>";

					

				html += '</select>';

			html += '</td>';

			html += '<td align="center" class="cname">';

				html += '<select name="rcus_base_price[' + filter_row_rule + '][discount_type]">';


				html += '<option value="fixed_price"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Price', 'addify_b2b'))))); ?></option>';
					html += '<option value="fixed_increase"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Increase', 'addify_b2b'))))); ?></option>';
					html += '<option value="fixed_decrease"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Decrease', 'addify_b2b'))))); ?></option>';
					html += '<option value="percentage_decrease"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Percentage Decrease', 'addify_b2b'))))); ?></option>';
					html += '<option value="percentage_increase"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Percentage Increase', 'addify_b2b'))))); ?></option>';

				html += '</select>';

			html += '</td>';

			html += "<td align='center' class='cname'>";

				html += '<input class="csp_input" type="text" name="rcus_base_price[' + filter_row_rule + '][discount_value]">';

			html += '</td>';

			html += '<td align="center" class="cname">';

				html += '<input class="csp_input" type="number" min="0" value="0" name="rcus_base_price[' + filter_row_rule + '][min_qty]">';

			html += '</td>';

			html += '<td class="cname">';

				html += '<input class="csp_input" align="center" type="number" min="0" value="0" name="rcus_base_price[' + filter_row_rule + '][max_qty]">';

			html += '</td>';

			html += '<td class="cname" align="center">';

				html += '<input class="" align="center" type="checkbox" value="yes" name="rcus_base_price[' + filter_row_rule + '][replace_orignal_price]">';

			html += '</td>';


			html += '<td align="center" class="cname">';

				html += '<a onclick="jQuery(\'#filter-row-rule' + filter_row_rule + '\').remove();" class="button button-danger"><?php esc_html_e('X', 'addify_b2b'); ?></a>';

			html += '</td>';

		html  += '</tr>';

		jQuery('#beforerulectf').before(html);

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


	var filter_row_rule_role = 20000;

	function addGlobalRuleRole() {

		


		html  = '<tr id="filter-row-rule-role' + filter_row_rule_role + '">';

			html += '<td align="center" class="cname">';
				html += '<select name="rrole_base_price[' + filter_row_rule_role + '][user_role]">';

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

				html += '<select name="rrole_base_price[' + filter_row_rule_role + '][discount_type]">';
					
					html += '<option value="fixed_price"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Price', 'addify_b2b'))))); ?></option>';
					html += '<option value="fixed_increase"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Increase', 'addify_b2b'))))); ?></option>';
					html += '<option value="fixed_decrease"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Fixed Decrease', 'addify_b2b'))))); ?></option>';
					html += '<option value="percentage_decrease"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Percentage Decrease', 'addify_b2b'))))); ?></option>';
					html += '<option value="percentage_increase"><?php echo esc_html(trim(preg_replace('/\s+/', ' ', str_replace('&#039;', "\'", esc_html__('Percentage Increase', 'addify_b2b'))))); ?></option>';

				html += '</select>';

			html += '</td>';

			html += '<td align="center" class="cname">';

				html += '<input class="csp_input" type="text" name="rrole_base_price[' + filter_row_rule_role + '][discount_value]">';

			html += '</td>';

			html += '<td align="center" class="cname">';

				html += '<input class="csp_input" type="number" min="0" value="0" name="rrole_base_price[' + filter_row_rule_role + '][min_qty]">';

			html += '</td>';

			html += '<td class="cname">';

				html += '<input class="csp_input" align="center" type="number" min="0" value="0" name="rrole_base_price[' + filter_row_rule_role + '][max_qty]">';

			html += '</td>';

			html += '<td class="cname" align="center">';

				html += '<input class="" align="center" type="checkbox" value="yes" name="rrole_base_price[' + filter_row_rule_role + '][replace_orignal_price]">';

			html += '</td>';


			html += '<td align="center" class="cname">';

				html += '<a onclick="jQuery(\'#filter-row-rule-role' + filter_row_rule_role + '\').remove();" class="button button-danger"><?php esc_html_e('X', 'addify_b2b'); ?></a>';

			html += '</td>';

		html  += '</tr>';

		jQuery('#beforerulectf_role').before(html);


		filter_row_rule_role++;

	}
</script>
