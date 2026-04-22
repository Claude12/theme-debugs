<?php
/**
 * Discount Cart Message Restriction.
 *
 * @package Discount Cart By Total Value.
 */

$af_discount_message_enable = get_post_meta( get_the_ID(), 'dcv_message_check', true );

$af_discount_notifi_message = get_post_meta( get_the_ID(), 'af_dcv_discount_notifi_message', true );

$af_discount_success_message = get_post_meta( get_the_ID(), 'af_dcv_discount_success_message', true );

?>
<div class="main-af-discount">
	<?php wp_nonce_field( 'wp_verify_nonce', 'af_dc_nonce' ); ?>
	<table>
		<tbody>				
			<tr>
				<th class="af_dc_table_heading" style="text-align:left;">
					<?php echo esc_html__( 'Display Message', 'addify_b2b' ); ?>
				</th>
				<td class="af_dc_table_content">
					<input type="checkbox" name="dcv_message_check" id="dcv_message_check" class="af_dcv_check"  <?php if ( ! empty( $af_discount_message_enable ) ) : ?> 
					checked
					<?php endif ?> >
					<p class="af_dc_discount_descrip"><?php echo esc_html__( 'Select this checkbox to display discount related messages on cart page.', 'addify_b2b' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th class="af_dc_table_heading" style="text-align:left;">
				</th>
				<td class="af_dc_table_content af_dcv_discount_message">
					<input type="textarea" id="af_dcv_discount_success_message" name="af_dcv_discount_success_message"  placeholder="enter message" 
					<?php if ( ! empty( $af_discount_success_message ) ) : ?>
					value="<?php echo esc_html( $af_discount_success_message ); ?>" 
					<?php endif ?> >
					<p class="af_dc_discount_descrip"><?php echo esc_html__( 'Display Success Message: This message will be displayed when a discount is applied based on above conditions. Customize message using variables - {discount_amount}, {prod_name}', 'addify_b2b' ); ?>
					</p>
				</td>
			</tr>								
			<tr>
				<th class="af_dc_table_heading" style="text-align:left;">
				</th>
				<td class="af_dc_table_content af_dcv_discount_message">
					<input type="textarea" id="af_dcv_discount_notifi_message" name="af_dcv_discount_notifi_message"  placeholder="enter message" 
					<?php if ( ! empty( $af_discount_notifi_message ) ) : ?>
					value="<?php echo esc_html( $af_discount_notifi_message ); ?>" 
					<?php endif ?>>
					<p class="af_dc_discount_descrip"><?php echo esc_html__( 'Message Before Discount: This message appears before the discount is applied notifying customers to qualify for specific quantity or order amount to get discount. Customize message using variables like {required_amount}, {required_quantity}, {discount_amount}, {prod_name}.', 'addify_b2b' ); ?>
					</p>
				</td>
			</tr>				
		</tbody>
	</table>
</div>




