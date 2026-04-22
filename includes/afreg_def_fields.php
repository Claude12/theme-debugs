<?php
add_settings_section(  
	'page_5_section',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'afreg_page_5_section_callback', // Callback used to render the description of the section  
	'addify-afreg-5'                           // Page on which to add this section of options  
);

add_settings_field(   
	'afreg_deafult_fields',                      // ID used to identify the field throughout the theme  
	esc_html__('Default Fields', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_deafult_fields_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-5',                          // The page on which this option will be displayed  
	'page_5_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('Enable/Disable Default Fields of WooCommerce.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-5',  
	'afreg_deafult_fields'  
);

function afreg_page_5_section_callback() {
	?>
	<div class="afb2b_setting_div">
			<h3><?php echo esc_html__('Default Fields for Registration Settings', 'addify_b2b'); ?></h3>
	</div>
	<?php
}

function afreg_deafult_fields_callback( $args ) {
	?>
	<div class="addify_df_fields">
	<?php
		 

	$def_posts = get_posts(
		array(
			'post_type'   => 'def_reg_fields',
			'numberposts' => -1,
			'order'       => 'ASC',
			'post_status' => 'any',
			'orderby'     => 'menu_order',
		)
	);

	foreach ($def_posts as $def_post) :
		$required    = get_post_meta($def_post->ID, 'is_required', true);
		$width       = get_post_meta($def_post->ID, 'width', true);
		$message     = get_post_meta($def_post->ID, 'message', true);
		$placeholder = get_post_meta($def_post->ID, 'placeholder', true);
		?>
		<div class="accordion">
			<div class="field_title"><b>
			<?php 
			echo esc_html__($def_post->post_title, 'addify_b2b'
										); 
			?>
										</b></div>
			<div class="field_status"><b>
			<?php 
			echo esc_html__($def_post->post_status, 'addify_b2b'
										); 
			?>
											</b></div>
		</div>
		<div class="panel">
			<input type="hidden" value="<?php echo intval($def_post->ID); ?>" name="post_ids[]">
			<p>
				<label for="label">
				<?php 
				echo esc_html__('Label:', 'addify_b2b'
									); 
				?>
									</label>
					<input type="text" value="<?php echo esc_attr($def_post->post_title); ?>" name="field_label[]" class="deffields">
				</p>

				<p>
				<label for="placeholder">
				<?php 
				echo esc_html__('Placeholder:', 'addify_b2b'
										); 
				?>
											</label>
					<input type="text" value="<?php echo esc_attr($placeholder); ?>" name="field_placeholder[]" class="deffields">
				</p>

				<p>
				<label for="message">
				<?php 
				echo esc_html__('Message:', 'addify_b2b'
									); 
				?>
										</label>
					<input type="text" value="<?php echo esc_attr($message); ?>" name="field_message[]" class="deffields">
				</p>

				<p>
				<label for="required">
				<?php 
				echo esc_html__('Required:', 'addify_b2b'
										); 
				?>
										</label>
					<input <?php checked($required, 1); ?> type="checkbox" value="1" name="field_required[]" class="">
				</p>

				<p>
				<label for="sort_order">
				<?php 
				echo esc_html__('Sort Order:', 'addify_b2b'
										); 
				?>
										</label>
					<input type="text" value="<?php echo intval($def_post->menu_order); ?>" name="field_sort_order[]" class="deffields">
				</p>

				<p><label for="width">
				<?php 
				echo esc_html__('Field Width:', 'addify_b2b'
									); 
				?>
									</label> 
					<select name="field_width[]" class="deffields">
						<option <?php selected($width, 'afreg_full'); ?> value="afreg_full">
													<?php 
														echo esc_html__('Full Width', 'addify_b2b'
																							); 
													?>
																							</option>
					<?php 
					if ('State / County' != $def_post->post_title && 'Country' != $def_post->post_title ) {
						?>
							<option <?php selected($width, 'afreg_half'); ?> value="afreg_half">
														<?php 
															echo esc_html__('Half Width', 'addify_b2b'
																								); 
														?>
																								</option>
						<?php
					}
					?>
					   
					</select>
				</p>    


				<p><label for="status">
				<?php 
				echo esc_html__('Status:', 'addify_b2b'
									); 
				?>
										</label> 
					<select name="field_status[]" class="deffields">
						<option <?php selected($def_post->post_status, 'publish'); ?> value="publish">
																	<?php 
																		echo esc_html__('Publish', 'addify_b2b'
																									); 
																	?>
																									</option>
						<option <?php selected($def_post->post_status, 'unpublish'); ?> value="unpublish">
																	<?php 
																		echo esc_html__('Unpublish', 'addify_b2b'
																										); 
																	?>
																										</option>
					</select>
				</p>    


		</div>
	<?php endforeach; ?>
	</div>
	<script>
	var acc = document.getElementsByClassName("accordion");
	var i;

	for (i = 0; i < acc.length; i++) {
		acc[i].addEventListener("click", function() {
		this.classList.toggle("active");
		var panel = this.nextElementSibling;
		if (panel.style.maxHeight){
			panel.style.maxHeight = null;
		} else {
			panel.style.maxHeight = panel.scrollHeight + "px";
		} 
		});
	}

	jQuery(document).ready(function($){

		$('.submit_b2b_settings .button-primary').on('click' , function(e){
			e.preventDefault();
			afregsaveFields(this);
		});
	});
	</script>
	<?php
}
