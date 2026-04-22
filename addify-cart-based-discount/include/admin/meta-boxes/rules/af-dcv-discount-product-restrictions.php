<?php
/**
 * Discount Cart.
 *
 * @package Discount Cart By Total Value.
 */

$af_dcv_products = (array) get_post_meta( get_the_ID(), 'af_dcv_discount_products', true );

$af_dcv_categories = (array) get_post_meta( get_the_ID(), 'af_dcv_discount_category', true );

$af_dcv_prod_tag = (array) get_post_meta( get_the_ID(), 'af_dcv_discount_product_tag', true );

$af_dcv_discount_for_prod_cart = get_post_meta( get_the_ID(), 'af_dcv_discount_for_prod_cart', true );

$af_dcv_discount_product_method = get_post_meta( get_the_ID(), 'af_dcv_discount_product_method', true );

?>
<div class="main-af-discount">
	<?php wp_nonce_field( 'wp_verify_nonce', 'af_dc_nonce' ); ?>	

	<table>
		<tbody>
			<tr>
				<th class="af_dc_table_heading" style="text-align:left;">
					<?php echo esc_html__( 'Select Discount Type', 'addify_b2b' ); ?>
				</th>
				<td class="af_dc_table_content">
					<select name="af_dcv_discount_for_prod_cart" id="af_dcv_discount_for_prod_cart" class="af_dcv_discount_product_cart">
						<option value="product_discount" <?php if ( 'product_discount' === $af_dcv_discount_for_prod_cart ) : ?>  
						selected 
							<?php endif ?> > 
							<?php echo esc_html__( 'Product Discount', 'addify_b2b' ); ?>
						</option>
						<option value="cart_discount" 
						<?php if ( 'cart_discount' === $af_dcv_discount_for_prod_cart ) : ?>
							selected 
							<?php endif ?> >
							<?php echo esc_html__( 'Entire Cart Discount', 'addify_b2b' ); ?>
						</option>
					</select>
					<p><?php echo esc_html__( 'Apply discount to entire cart or to specific products in cart.', 'addify_b2b' ); ?></p>					
				</td>
			</tr>	
			<tr class="af_dcv_products_method_field">
				<th class="af_dc_table_heading af_dcv_prod_method" style="text-align:left;">
					<?php echo esc_html__( 'Select Products', 'addify_b2b' ); ?>
				</th>
				<td class="af_dc_table_content">
					<select name="af_dcv_discount_product_method" id="af_dcv_discount_product_method" class="af_dcv_discount_product_methods">
						<option value="all_product" <?php if ( 'all_product' === $af_dcv_discount_product_method ) : ?>  
						selected 
							<?php endif ?> > 
							<?php echo esc_html__( 'All Products', 'addify_b2b' ); ?>
						</option>
						<option value="specific_product" 
						<?php if ( 'specific_product' === $af_dcv_discount_product_method ) : ?>
							selected 
							<?php endif ?> >
							<?php echo esc_html__( 'Specific Products', 'addify_b2b' ); ?>
						</option>
					</select>
					<p><?php echo esc_html__( 'Apply discount to all or specific products', 'addify_b2b' ); ?></p>					
				</td>
			</tr>								
			<tr class="af_dcv_products_method_field">
				<th class="af_dc_table_heading af_dcv_products_hide">

					<?php echo esc_html__( 'Select Specific Products', 'addify_b2b' ); ?>

				</th>
				<td class="af_dc_table_content af_dcv_products_hide">
					<select  name="af_dcv_discount_products[]" class="af_dcv_discount_product_list af_dcv_select_live_search " multiple id="af_dcv_discount_products">
						<?php
						foreach ( $af_dcv_products as $key => $af_id ) :
							$prod = wc_get_product( $af_id );
							if ( $prod ) :
								?>
								<option value="<?php echo esc_html( $af_id ); ?>" selected>
									<?php echo esc_html( $prod->get_name() ); ?>
								</option>

								<?php
							endif;
						endforeach
						?>
					</select>
				</td>
			</tr>		
			<tr class="af_dcv_products_method_field">
				<th class="af_dc_table_heading af_dcv_products_hide" style="text-align:left;">

					<?php echo esc_html__( 'Select Products Categories', 'addify_b2b' ); ?>

				</th>
				<td class="af_dc_table_content af_dcv_products_hide">
					<select  name="af_dcv_discount_category[]" class="af_dcv_discount_category_list af_dcv_select_live_search" multiple id="af_dcv_discount_category">
						<?php
						foreach ( $af_dcv_categories as $key => $af_cat_id ) :
							if ( $af_cat_id ) :
								?>
								<option value="<?php echo esc_html( $af_cat_id ); ?>" selected>
									<?php echo esc_html( get_term( $af_cat_id )->name ); ?> 
								</option>
								<?php
							endif;
						endforeach
						?>
					</select>
				</td>
			</tr>			
			<tr class="af_dcv_products_method_field">
				<th class="af_dc_table_heading af_dcv_products_hide" style="text-align:left;">

					<?php echo esc_html__( 'Select Products Tags', 'addify_b2b' ); ?>

				</th>
				<td class="af_dc_table_content af_dcv_products_hide">
					<select  name="af_dcv_discount_product_tag[]" class="af_dcv_discount_tag_select_live_search"  multiple id="af_dcv_discount_product_tag">
						<?php
						$af_tag_terms = get_terms( 'product_tag' );
						if ( ! empty( $af_tag_terms ) ) {

							foreach ( $af_tag_terms as $key => $af_term ) {
								?>
								<option value="<?php echo esc_html( $af_term->term_id ); ?>"
									<?php if ( in_array( $af_term->term_id, $af_dcv_prod_tag ) ) : ?>
										selected 
									<?php endif ?> >
									<?php
									echo esc_html( $af_term->name );
									?>
								</option>
								<?php
							}
						}
						?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</div>


