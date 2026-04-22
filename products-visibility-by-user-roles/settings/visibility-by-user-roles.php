<?php

defined('ABSPATH') || exit;

//Visibility By User Role

add_settings_section(  
	'page_1_section',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'afpvu_page_2_section_callback', // Callback used to render the description of the section  
	'addify-products-visibility-2'                           // Page on which to add this section of options  
);


add_settings_field (   
	'afpvu_user_role_visibility',                      // ID used to identify the field throughout the theme  
	esc_html__('Visibility By User Roles', 'addify_b2b'),    // The label to the left of the option interface element  
	'afpvu_user_role_visibility_callback',   // The name of the function responsible for rendering the option interface  
	'addify-products-visibility-2',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	'' 
);  
register_setting(  
	'setting-group-2',  
	'afpvu_user_role_visibility'  
);

function afpvu_page_2_section_callback() { 
	?>
	
	  
		<p class="impnote"><?php echo esc_html__('Please note that Visibility by User Roles have high priority. If following configurations are active for any user role – the global settings won’t work for that specific role.', 'addify_b2b'); ?></p>

	<?php 
} // function afreg_page_1_section_callback

//User Role Visibility
function afpvu_user_role_visibility_callback() {
	?>
		<div class="afpvu_accordian">
			<div id="accordion">
				<?php

				global $wp_roles;
				$roless = $wp_roles->get_names();

				//Guest
				$guest_role = array( 'guest' => 'Guest' );

				$roles = array_merge($roless, $guest_role);

				if (!empty(get_option('afpvu_user_role_visibility'))) {

					$role_selected_data = get_option('afpvu_user_role_visibility');

				} else {

					$role_selected_data = array();

				}
				

				if ( !empty( $roles)) {

					foreach ($roles as $key => $value) {

						if (!empty($role_selected_data[ esc_attr( $key ) ]['afpvu_enable_role']) && 'yes' == $role_selected_data[ esc_attr( $key ) ]['afpvu_enable_role']) {

							$active_role = '( Rule Active )';

						} else {

							$active_role = '';

						}

						$role_show_hide = isset( $role_selected_data[ esc_attr( $key ) ]['afpvu_show_hide_role'] ) ? $role_selected_data[ esc_attr( $key ) ]['afpvu_show_hide_role'] : '';
						
						?>
						
							<h3><?php echo esc_attr( $value ); ?><span class="ruleactive"><?php echo esc_html__($active_role, 'addify_b2b'); ?></span></h3>
						  
							<div class="<?php echo esc_attr($key); ?>">
							<p>
								<input type="hidden" name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $key ); ?>">
								

									<div class="afpuv_role_inner">
										<div class="afpuv_role_inner_left">
											<label><?php echo esc_html__('Enable for this Role', 'addify_b2b'); ?></label>
										</div>
										<div class="afpuv_role_inner_right">
											<input type="checkbox" name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_enable_role]" value="yes" 
												<?php 
												if (!empty($role_selected_data[ esc_attr( $key ) ]['afpvu_enable_role'])) {
													echo checked('yes', $role_selected_data[ esc_attr( $key ) ]['afpvu_enable_role']); } 
												?>
											>
										</div>
									</div>

									<div class="afpuv_role_inner">
										<div class="afpuv_role_inner_left">
											<label><?php echo esc_html__('Show/Hide', 'addify_b2b'); ?></label>
										</div>
										<div class="afpuv_role_inner_right">
											<select name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_show_hide_role]">
												<option value="hide" <?php echo selected('hide', $role_show_hide ); ?>><?php echo esc_html__('Hide', 'addify_b2b'); ?></option>
												<option value="show" <?php echo selected('show', $role_show_hide ); ?>><?php echo esc_html__('Show', 'addify_b2b'); ?></option>
											</select>
										</div>
									</div>

									<div class="afpuv_role_inner">
										<div class="afpuv_role_inner_left">
											<label><?php echo esc_html__('Select Products', 'addify_b2b'); ?></label>
										</div>
										<div class="afpuv_role_inner_right">
											
												<select name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_applied_products_role][]" class="afpvu_applied_products" multiple="multiple">
													
													<?php

													if (!empty($role_selected_data[ esc_attr( $key ) ]['afpvu_applied_products_role'])) {

														foreach ( $role_selected_data[ esc_attr( $key ) ]['afpvu_applied_products_role'] as $pro) {

															$prod_post = get_post($pro);
															if ( !is_object( $prod_post ) ) {
																continue;
															}
															?>

															<option value="<?php echo intval($pro); ?>" selected="selected"><?php echo esc_attr($prod_post->post_title); ?></option>

															<?php 
														}
													}
													?>

												</select>
											
										</div>
									</div>


									<div class="afpuv_role_inner">
										<div class="afpuv_role_inner_left">
											<label><?php echo esc_html__('Select Categories', 'addify_b2b'); ?></label>
										</div>
										<div class="afpuv_role_inner_right">
											
											<div class="all_cats">
												<ul>
													<?php
													if (!empty($role_selected_data[ esc_attr( $key ) ]['afpvu_applied_categories_role'])) {

														$pre_vals = $role_selected_data[ esc_attr( $key ) ]['afpvu_applied_categories_role'];
													} else {
														$pre_vals = array();
													}
													

													$args = array(
														'taxonomy' => 'product_cat',
														'hide_empty' => false,
														'parent'   => 0,
													);

													$product_cat = get_terms( $args );
													foreach ($product_cat as $parent_product_cat) {
														?>
														<li class="par_cat">
															<input type="checkbox" class="parent" name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_applied_categories_role][]"  value="<?php echo intval($parent_product_cat->term_id); ?>" 
															<?php 
															if (!empty($pre_vals) && in_array($parent_product_cat->term_id, $pre_vals)) { 
																echo 'checked';
															}
															?>
															/>
															<?php echo esc_attr($parent_product_cat->name); ?>

															<?php
															$child_args         = array(
																'taxonomy' => 'product_cat',
																'hide_empty' => false,
																'parent'   => intval($parent_product_cat->term_id),
															);
															$child_product_cats = get_terms( $child_args );
															if (!empty($child_product_cats)) {
																?>
																<ul>
																	<?php foreach ($child_product_cats as $child_product_cat) { ?>
																		<li class="child_cat">
																			<input type="checkbox" class="child parent" name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_applied_categories_role][]"  value="<?php echo intval($child_product_cat->term_id); ?>" 
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
																				'taxonomy' => 'product_cat',
																				'hide_empty' => false,
																				'parent'   => intval($child_product_cat->term_id),
																			);

																			$child_product_cats2 = get_terms( $child_args2 );
																			if (!empty($child_product_cats2)) {
																				?>

																				<ul>
																					<?php foreach ($child_product_cats2 as $child_product_cat2) { ?>

																						<li class="child_cat">
																							<input type="checkbox" class="child parent" name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_applied_categories_role][]"  value="<?php echo intval($child_product_cat2->term_id); ?>" 
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
																								'taxonomy' => 'product_cat',
																								'hide_empty' => false,
																								'parent'   => intval($child_product_cat2->term_id),
																							);

																							$child_product_cats3 = get_terms( $child_args3 );
																							if (!empty($child_product_cats3)) {
																								?>

																								<ul>
																									<?php foreach ($child_product_cats3 as $child_product_cat3) { ?>

																										<li class="child_cat">
																											<input type="checkbox" class="child parent" name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_applied_categories_role][]"  value="<?php echo intval($child_product_cat3->term_id); ?>" 
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
																												'parent'   => intval($child_product_cat3->term_id),
																											);

																											$child_product_cats4 = get_terms( $child_args4 );
																											if (!empty($child_product_cats4)) {
																												?>

																												<ul>
																													<?php foreach ($child_product_cats4 as $child_product_cat4) { ?>

																														<li class="child_cat">
																															<input type="checkbox" class="child parent" name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_applied_categories_role][]"  value="<?php echo intval($child_product_cat4->term_id); ?>"
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
																																'parent'   => intval($child_product_cat4->term_id),
																															);

																															$child_product_cats5 = get_terms( $child_args5 );
																															if (!empty($child_product_cats5)) {
																																?>

																																<ul>
																																	<?php foreach ($child_product_cats5 as $child_product_cat5) { ?>

																																		<li class="child_cat">
																																			<input type="checkbox" class="child parent" name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_applied_categories_role][]"  value="<?php echo intval($child_product_cat5->term_id); ?>" 
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
																																				'parent'   => intval($child_product_cat5->term_id),
																																			);

																																			$child_product_cats6 = get_terms( $child_args6 );
																																			if (!empty($child_product_cats6)) {
																																				?>

																																				<ul>
																																					<?php foreach ($child_product_cats6 as $child_product_cat6) { ?>

																																						<li class="child_cat">
																																							<input type="checkbox" class="child" name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_applied_categories_role][]"  value="<?php echo intval($child_product_cat6->term_id); ?>" 
																																							<?php
																																							if (!empty($pre_vals) &&in_array($child_product_cat6->term_id, $pre_vals)) {
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


									<div class="afpuv_role_inner">
										<div class="afpuv_role_inner_left">
											<label><?php echo esc_html__('Redirection Mode', 'addify_b2b'); ?></label>
										</div>
										<div class="afpuv_role_inner_right">

											<?php

											if (!empty($role_selected_data[ esc_attr( $key ) ]['afpvu_role_redirection_mode'])) {

												$afpvu_role_redirection_mode = $role_selected_data[ esc_attr( $key ) ]['afpvu_role_redirection_mode'];
											} else {
												$afpvu_role_redirection_mode = '';
											}

											if (!empty($role_selected_data[ esc_attr( $key ) ]['afpvu_role_custom_url'])) {

												$afpvu_role_custom_url = $role_selected_data[ esc_attr( $key ) ]['afpvu_role_custom_url'];
											} else {
												$afpvu_role_custom_url = '';
											}

											?>
											
											<select name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_role_redirection_mode]" onchange="getRedirectMode(this.value, '<?php echo esc_attr($key); ?>')" id="<?php echo esc_attr( $key ); ?>afpvu_role_redirection_mode">
												<option value="custom_url" <?php echo selected('custom_url', esc_attr($afpvu_role_redirection_mode)); ?>><?php echo esc_html__('Custom URL', 'addify_b2b'); ?></option>
												<option value="custom_message" <?php echo selected('custom_message', esc_attr($afpvu_role_redirection_mode)); ?>><?php echo esc_html__('Custom Message', 'addify_b2b'); ?></option>
											</select>
												
											
										</div>
									</div>


									<div class="afpuv_role_inner showcustomurl">
										<div class="afpuv_role_inner_left">
											<label><?php echo esc_html__('Custom URL', 'addify_b2b'); ?></label>
										</div>
										<div class="afpuv_role_inner_right">
											
											<input type="text" class="setting_input_fields" name="afpvu_user_role_visibility[<?php echo esc_attr( $key ); ?>][afpvu_role_custom_url]" value="<?php echo esc_attr($afpvu_role_custom_url); ?>">
											
										</div>
									</div>

									

									<div class="afpuv_role_inner showcustommessage">
										<div class="afpuv_role_inner_left">
											<label><?php echo esc_html__('Custom Message', 'addify_b2b'); ?></label>
										</div>
										<div class="afpuv_role_inner_right">
											
											<?php

											if (!empty($role_selected_data[ esc_attr( $key ) ]['afpvu_custom_message_role'])) {
												$content = $role_selected_data[ esc_attr( $key ) ]['afpvu_custom_message_role'];
											} else {
												$content = '';
											}
											
											$editor_id = 'afpvu_user_role_visibility' . esc_attr( $key );
											$settings  = array(
												'tinymce' => true,
												'textarea_rows' => 10,
												'quicktags' => array( 'buttons' => 'em,strong,link' ),
												'textarea_name' => 'afpvu_user_role_visibility[' . esc_attr( $key ) . '][afpvu_custom_message_role]',
											);

											wp_editor( $content, $editor_id, $settings );


											?>
												
											
										</div>
									</div>
									<script>

										jQuery(document).ready(function($){

											var value1 = $("#<?php echo esc_attr( $key ); ?>afpvu_role_redirection_mode option:selected").val();
												if ('custom_url' == value1) {

												jQuery('.<?php echo esc_attr( $key ); ?> .showcustomurl').show();
												jQuery('.<?php echo esc_attr( $key ); ?> .showcustommessage').hide();
											} else if ('custom_message' == value1) {

												jQuery('.<?php echo esc_attr( $key ); ?> .showcustomurl').hide();
												jQuery('.<?php echo esc_attr( $key ); ?> .showcustommessage').show();
											}
										});
										
										function getRedirectMode(value, role) {

											if ('custom_url' == value) {

												jQuery('.'+role+' .showcustomurl').show();
												jQuery('.'+role+' .showcustommessage').hide();
											} else if ('custom_message' == value) {

												jQuery('.'+role+' .showcustomurl').hide();
												jQuery('.'+role+' .showcustommessage').show();
											}

										}
									</script>
							
							</p>
							</div>
						
						<?php 
					}
				}

				?>
			</div>
		</div>
	

	<?php 
} // function afpvu_user_role_visibility_callback
