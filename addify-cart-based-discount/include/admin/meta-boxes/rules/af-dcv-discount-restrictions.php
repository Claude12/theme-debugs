<?php
/**
 * Discount Cart Restriction.
 *
 * @package Discount Cart By Total Value.
 */

global $wp_roles;

$af_dc_discount_wp_roles = $wp_roles->get_names();

$af_dc_discount_wp_roles['guest'] = 'Guest';

$af_dc_discount_wp_roles['for_all'] = 'For all';

$af_coupons_enable = get_post_meta( get_the_ID(), 'af_dcv_coupons_enable', true );

$af_btb_enable = get_post_meta( get_the_ID(), 'af_disable_btb', true );

$af_dcv_discount_type = get_post_meta( get_the_ID(), 'dcv_discount_type', true );

$dcv_discount_by_total_price = get_post_meta( get_the_ID(), 'dcv_discount_by_total_price', true );

$af_dcv_discount_row_details = (array) get_post_meta( get_the_ID(), 'af_dis_detail', true );

?>
<div class="main-af-discount">
	<?php wp_nonce_field( 'wp_verify_nonce', 'af_dc_nonce' ); ?>
	<table>
		<tbody>												
			<tr>
				<th class="af_dc_table_heading" style="text-align:left;">
					<?php echo esc_html__( 'Discount Based on', 'addify_b2b' ); ?>
				</th>
				<td class="af_dc_table_content">
					<select  name="dcv_discount_type"  class="af_dcv_discount_type"  id="dcv_discount_type" style="width:100% !important;">
						<option value="total" <?php if ( 'total' === $af_dcv_discount_type ) : ?>
						selected 
							<?php endif ?> ><?php echo esc_html__( 'Amount Range', 'addify_b2b' ); ?>
						</option>
						<option value="quantity" <?php if ( 'quantity' === $af_dcv_discount_type ) : ?>
						selected 
							<?php endif ?> ><?php echo esc_html__( 'Quantity Range', 'addify_b2b' ); ?>
						</option>
					</select>
				</td>
			</tr>	
			<tr class="af_dcv_discount_by_total_price">
				<th class="af_dc_table_heading" style="text-align:left;">
					<?php echo esc_html__( 'Apply Discount to', 'addify_b2b' ); ?>
				</th>
				<td class="af_dc_table_content">
					<select  name="dcv_discount_by_total_price" id="dcv_discount_by_total_price" style="width:100% !important;">
						<option value="per_price" <?php if ( 'per_price' === $dcv_discount_by_total_price ) : ?>
						selected 
							<?php endif ?> ><?php echo esc_html__( 'Product Price', 'addify_b2b' ); ?>
						</option>
						<option value="subtotal" <?php if ( 'subtotal' === $dcv_discount_by_total_price ) : ?>
						selected 
							<?php endif ?> ><?php echo esc_html__( 'Product Subtotal', 'addify_b2b' ); ?>
						</option>
					</select>
				</td>
			</tr>						
		</tbody>
	</table>
	<!-- discount rows -->
	<div class="af_discount_row_table">
		<table class="af_dc_discount_add_row">
			<thead>
				<tr>
					<th><?php echo esc_html__( 'User Roles', 'addify_b2b' ); ?></th>
					<th><?php echo esc_html__( 'Adjustment Type', 'addify_b2b' ); ?></th>
					<th><?php echo esc_html__( 'Minimum', 'addify_b2b' ); ?></th>
					<th><?php echo esc_html__( 'Maximum', 'addify_b2b' ); ?></th>
					<th><?php echo esc_html__( 'Discount Value ', 'addify_b2b' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ( isset( $af_dcv_discount_row_details['min'] ) && is_array( $af_dcv_discount_row_details['min'] ) ) {
				foreach ( $af_dcv_discount_row_details['min'] as $key => $af_fields ) {
					?>
				<tr class="af-discount-row">
					<td>
						<select  name="af_dis_detail[af_dcv_user_roles][<?php echo esc_attr( $key ); ?>]" class="af_select_box af_user_roles_select" id="af_dis_detail[af_dcv_user_roles]">
								<?php foreach ( $af_dc_discount_wp_roles as $role_key => $value ) { ?>
								<option value="<?php echo esc_html( $role_key ); ?>" 
										<?php if ( isset( $af_dcv_discount_row_details['af_dcv_user_roles'][ $key ] ) && ( in_array( $role_key, (array) $af_dcv_discount_row_details['af_dcv_user_roles'][ $key ] ) ) ) : ?>
										selected <?php endif ?> >
										<?php echo esc_html( $value ); ?>
								</option>
								<?php } ?>
						</select>
					</td>
					<td>
						<div class="meta-box-rule">
							<select name="af_dis_detail[dicount_type][<?php echo esc_attr( $key ); ?>]">
								<option value="fixed" <?php if ( isset( $af_dcv_discount_row_details['dicount_type'][ $key ] ) && 'fixed' === $af_dcv_discount_row_details['dicount_type'][ $key ] ) : ?>  
								selected 
									<?php endif ?> > 
									<?php echo esc_html__( 'Fixed Discount', 'addify_b2b' ); ?>
								</option>
								<option value="percentage" 
								<?php if ( isset( $af_dcv_discount_row_details['dicount_type'][ $key ] ) && 'percentage' === $af_dcv_discount_row_details['dicount_type'][ $key ] ) : ?>
									selected 
									<?php endif ?> >
									<?php echo esc_html__( 'Percentage', 'addify_b2b' ); ?>
								</option>
							</select>
						</div>
					</td>					
					<td>
						<div class="meta-box-rule" >
							<input id="dcv_minimum" type="Number" name="af_dis_detail[min][<?php echo esc_attr( $key ); ?>]" min="1" pattern="^[0-9]+" <?php if ( ! empty( $af_fields ) ) : ?> 
								value="<?php echo esc_attr( $af_fields ); ?>"
						<?php endif ?>>
						</div>
					</td>
					<td>
						<div class="meta-box-rule">
							<input id="max_value" type="Number" name="af_dis_detail[max][<?php echo esc_attr( $key ); ?>]" min="1" pattern="^[0-9]+" <?php if ( isset( $af_dcv_discount_row_details['max'][ $key ] ) ) : ?> 
								value="<?php echo esc_attr( $af_dcv_discount_row_details['max'][ $key ] ); ?>"
						<?php endif ?> >
						</div>
					</td>
					<td>
						<div class="meta-box-rule">
							<input id="dcv_number" type="Number" min="1" pattern="^[0-9]+" name="af_dis_detail[discount_value][<?php echo esc_attr( $key ); ?>]" 
						<?php if ( isset( $af_dcv_discount_row_details['discount_value'][ $key ] ) ) : ?>
							value="<?php echo esc_attr( $af_dcv_discount_row_details['discount_value'][ $key ] ); ?>" 
							<?php endif ?> >
						</div>
					</td>
					<td><i id="af_close_btn_colour" class="af-close-row fa fa-close button button-primary button-large"></i></td>
				</tr>
					<?php
				}
			}
			?>
			</tbody>
		</table>
		<div class="af-dcv-repeater-btn">
			<p><?php echo esc_html__( 'Note: When discount value is more than or less than the subtotal then rule will not be applied.', 'addify_b2b' ); ?></p>
			<i id="af_add_btn_colour" class="af-add-row button button-primary button-large"><?php echo esc_html__( 'Add', 'addify_b2b' ); ?></i>
		</div>
	</div>
</div>




