<div class="afrfq_admin_main">
	<?php wp_nonce_field( 'afrfq_fields_nonce_action', 'afrfq_field_nonce' ); ?>
	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Apply on All User Roles', 'addify_b2b' ); ?></strong></label></div>
	<div class="afrfq_admin_main_right">
		<?php
			$afrfq_apply_on_all_user_role = get_post_meta( $post->ID, 'afrfq_apply_on_all_user_role', true );
		?>
		<input type="checkbox" name="afrfq_apply_on_all_user_role" id="afrfq_apply_on_all_user_role" value="yes" <?php echo checked( 'yes', $afrfq_apply_on_all_user_role ); ?>>
		<p class="description"><?php echo esc_html__( 'Check this if you want to apply this rule on all user roles.', 'addify_b2b' ); ?></p>
	</div>
</div>

<div class="afrfq_admin_main adf-all-user-roles" id="">
	
	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Quote for User Roles', 'addify_b2b' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">
		<?php
		global $wp_roles;
		$roles = $wp_roles->get_names();
		?>
		<select class="afrfq-hide-categories" name="afrfq_hide_user_role[]" data-placeholder="<?php echo esc_html__('Select Roles', 'addify_b2b'); ?>" multiple="multiple">
		<?php
		foreach ( $roles as $key => $value ) {
			?>
			<option value="<?php echo esc_attr( $key ); ?>" 
										<?php
										if ( ! empty( $afrfq_hide_user_role ) && in_array( (string) $key, $afrfq_hide_user_role, true ) ) {
							echo 'selected'; }
										?>
			><?php echo esc_attr( $value ); ?>
			</option>

		<?php } ?>
			<option value="guest" 
			<?php
			if ( ! empty( $afrfq_hide_user_role ) && in_array( 'guest', $afrfq_hide_user_role, true ) ) {
echo 'selected'; }
			?>
			><?php echo esc_html__( 'Guest', 'addify_b2b' ); ?></option>
		</select>
	</div>

</div>

<div class="afrfq_admin_main">
	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Apply on All Products', 'addify_b2b' ); ?></strong></label></div>
	<div class="afrfq_admin_main_right">
		<?php
			$applied_on_all_products = get_post_meta( $post->ID, 'afrfq_apply_on_all_products', true );
		?>
		<input type="checkbox" name="afrfq_apply_on_all_products" id="afrfq_apply_on_all_products" value="yes" <?php echo checked( 'yes', $applied_on_all_products ); ?>>
		<p class="description"><?php echo esc_html__( 'Check this if you want to apply this rule on all products.', 'addify_b2b' ); ?></p>
	</div>
</div>

<div class="afrfq_admin_main">
	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Apply on Out of Stock Products Only', 'addify_b2b' ); ?></strong></label></div>
	<div class="afrfq_admin_main_right">
		<?php
			$afrfq_apply_on_oos_products = get_post_meta( $post->ID, 'afrfq_apply_on_oos_products', true );
		?>
		<input type="checkbox" name="afrfq_apply_on_oos_products" id="afrfq_apply_on_oos_products" value="yes" <?php echo checked( 'yes', $afrfq_apply_on_oos_products ); ?>>
		<p class="description"><?php echo esc_html__( 'Check this if you want to apply this rule for "out of stock products" only.', 'addify_b2b' ); ?></p>
		<p class="description"><?php echo esc_html__( 'Select replace add to cart button with quote button in order to activate it.', 'addify_b2b' ); ?></p>
	</div>
</div>

<div class="afrfq_admin_main hide_all_pro">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Quote Rule for Selected Products', 'addify_b2b' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">
		<select class="select_box wc-enhanced-select afrfq_hide_products" name="afrfq_hide_products[]" id="afrfq_hide_products"  multiple='multiple'>
			<?php

			if ( ! empty( $afrfq_hide_products ) ) {

				foreach ( $afrfq_hide_products as $pro ) {

					$prod_post = get_post( $pro );

					?>

					<option value="<?php echo intval( $pro ); ?>" selected="selected"><?php echo esc_attr( $prod_post->post_title ); ?></option>

					<?php
				}
			}
			?>
		</select>
	</div>

</div>

<div class="afrfq_admin_main hide_all_pro">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Quote Rule for Selected Categories', 'addify_b2b' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">
			<?php

			$pre_vals = $afrfq_hide_categories;

			$args = array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			);

			$product_cat = get_terms( $args );
			?>
			<select class="afrfq-hide-categories" name="afrfq_hide_categories[]" data-placeholder="<?php echo esc_html__('Select Categories', 'addify_b2b'); ?>" multiple="multiple">
			<?php
			foreach ( $product_cat as $parent_product_cat ) {

				?>
				<option value="<?php echo intval( $parent_product_cat->term_id ); ?>"  
											<?php
											if ( ! empty( $pre_vals ) && in_array( (string) $parent_product_cat->term_id, $pre_vals, true ) ) {
							echo 'selected'; }
											?>
				><?php echo esc_attr( $parent_product_cat->name ); ?></option>
				<?php
			}
			?>
			</select>
	</div>

</div>

<div class="afrfq_admin_main hide_all_pro">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Quote Rule for Selected Brands', 'addify_b2b' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">
			<?php

			$pre_vals = $afrfq_hide_brands;

			$args = array(
				'taxonomy'   => 'product_brand',
				'hide_empty' => false,
			);

			$product_brand = get_terms( $args );
			?>
			<select class="afrfq-hide-brands" name="afrfq_hide_brands[]" data-placeholder="<?php echo esc_html__('Select Brands', 'addify_b2b'); ?>" multiple="multiple">
			<?php
			foreach ( $product_brand as $brand_id ) {

				?>
				<option value="<?php echo intval( $brand_id->term_id ); ?>"  
											<?php
											if ( ! empty( $pre_vals ) && in_array( (string) $brand_id->term_id, $pre_vals, true ) ) {
							echo 'selected'; }
											?>
				><?php echo esc_attr( $brand_id->name ); ?></option>
				<?php
			}
			?>
			</select>
	</div>

</div>

<div class="afrfq_admin_main">
	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Enable Pop up', 'addify_b2b' ); ?></strong></label></div>
	<div class="afrfq_admin_main_right">
		<?php
			$afrfq_enable_add_to_quote_popup = get_post_meta( $post->ID, 'afrfq_enable_add_to_quote_popup', true );
		?>
		<input type="checkbox" name="afrfq_enable_add_to_quote_popup" id="afrfq_enable_add_to_quote_popup" value="yes" <?php echo checked( 'yes', $afrfq_enable_add_to_quote_popup ); ?>>
		<p class="description"><?php echo esc_html__( 'Check this if you want to enable the option to add a quote via a popup.', 'addify_b2b' ); ?></p>
	</div>
</div>


<div class="afrfq_admin_main">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Hide Price', 'addify_b2b' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">

		<select name="afrfq_is_hide_price" class="select_box_small" id="afrfq_is_hide_price" onchange="afrfq_HidePrice(this.value)">
			<option value="no" <?php echo selected( 'no', esc_attr( $afrfq_is_hide_price ) ); ?>><?php echo esc_html__( 'No', 'addify_b2b' ); ?></option>
			<option value="yes" <?php echo selected( 'yes', esc_attr( $afrfq_is_hide_price ) ); ?>><?php echo esc_html__( 'Yes', 'addify_b2b' ); ?></option>
		</select>

	</div>

</div>

<div class="afrfq_admin_main" id="hpircetext">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Hide Price Text', 'addify_b2b' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">

		<?php
		if ( ! empty( $afrfq_hide_price_text ) ) {
			$afpricetext = $afrfq_hide_price_text;
		} else {
			$afpricetext = '';
		}
		?>
		<textarea cols="50" rows="5" name="afrfq_hide_price_text" id="afrfq_hide_price_text" /><?php echo esc_textarea( $afpricetext ); ?></textarea>
		<br><i><?php echo esc_html__( 'Display the above text when price is hidden, e.g "Price is hidden"', 'addify_b2b' ); ?></i>

	</div>

</div>

<div class="afrfq_admin_main">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Hide Add to Cart Button', 'addify_b2b' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">

		<select name="afrfq_is_hide_addtocart" class="select_box_small" id="afrfq_is_hide_addtocart" onchange="getCustomURL(this.value)">
			<option value="replace" <?php echo selected( 'replace', esc_attr( $afrfq_is_hide_addtocart ) ); ?>>
				<?php echo esc_html__( 'Replace Add to Cart button with a Quote Button', 'addify_b2b' ); ?>
			</option>
			<option value="addnewbutton" <?php echo selected( 'addnewbutton', esc_attr( $afrfq_is_hide_addtocart ) ); ?>>
				<?php echo esc_html__( 'Keep Add to Cart button and add a new Quote Button', 'addify_b2b' ); ?>
			</option>
			<option value="replace_custom" <?php echo selected( 'replace_custom', esc_attr( $afrfq_is_hide_addtocart ) ); ?>>
				<?php echo esc_html__( 'Replace Add to Cart with custom button', 'addify_b2b' ); ?>
			</option>
			<option value="addnewbutton_custom" <?php echo selected( 'addnewbutton_custom', esc_attr( $afrfq_is_hide_addtocart ) ); ?>>
				<?php echo esc_html__( 'Keep Add to Cart and add a new custom button', 'addify_b2b' ); ?>
			</option>
		</select>

	</div>

</div>

<div class="afrfq_admin_main" id="afcustom_link">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Custom Button Link', 'addify_b2b' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">

		<?php
		if ( ! empty( $afrfq_custom_button_link ) ) {
			$afrfq_custom_button_link = $afrfq_custom_button_link;
		} else {
			$afrfq_custom_button_link = '';
		}
		?>
		<input type="text" class="afrfq_input_class" name="afrfq_custom_button_link" id="afrfq_custom_button_link" value="<?php echo esc_attr( $afrfq_custom_button_link ); ?>">
		<br><i><?php echo esc_html__( 'Link for custom button e.g "http://www.example.com"', 'addify_b2b' ); ?></i>

	</div>

</div>

<div class="afrfq_admin_main">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Custom Button Label', 'addify_b2b' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">

		<?php
		if ( ! empty( $afrfq_custom_button_text ) ) {
			$afcustombuttontext = $afrfq_custom_button_text;
		} else {
			$afcustombuttontext = __( 'Add to Quote', 'addify_b2b' );
		}
		?>
		<input type="text" name="afrfq_custom_button_text" style="width:60%" id="afrfq_custom_button_text" value="<?php echo esc_html( $afcustombuttontext ); ?>" >
		<br><i><?php echo esc_html__( 'Display the above label on custom button, e.g "Request a Quote".', 'addify_b2b' ); ?></i>

	</div>

</div>
