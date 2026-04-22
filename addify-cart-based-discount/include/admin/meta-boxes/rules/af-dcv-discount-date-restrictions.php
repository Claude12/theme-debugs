<?php
/**
 * Discount Cart Date Restriction.
 *
 * @package Discount Cart By Total Value.
 */

$af_discount_start_date = get_post_meta( get_the_ID(), 'af_dcv_discount_start_date', true );

$af_discount_end_date = get_post_meta( get_the_ID(), 'af_dcv_discount_end_date', true );

?>
<div class="main-af-discount">
	<?php wp_nonce_field( 'wp_verify_nonce', 'af_dc_nonce' ); ?>	

	<table>
		<tbody>
			<tr>
				<th class="af_dc_table_heading" style="text-align:left;">
					<?php echo esc_html__( 'Rule Date:', 'addify_b2b' ); ?>
				</th>
				<td class="af_dc_table_content">
					<div class="af-dcv-date-main">
						<div class="af-dcv-from-date">
							<label><?php echo esc_html__( 'From', 'addify_b2b ' ); ?></label>
							<input type="date" name="af_dcv_discount_start_date" id="af_dcv_discount_start_date"  <?php if ( ! empty( $af_discount_start_date ) ) : ?> 
							value="<?php echo esc_html( $af_discount_start_date ); ?>"
							<?php endif ?> >
						</div>
						<div class="af-dcv-to-date">
							<label><?php echo esc_html__( 'To', 'addify_b2b ' ); ?></label>
							<input type="date" name="af_dcv_discount_end_date" id="af_dcv_discount_end_date"  <?php if ( ! empty( $af_discount_end_date ) ) : ?> 
							value="<?php echo esc_html( $af_discount_end_date ); ?>"
							<?php endif ?> >
						</div>
					</div>
					<p><?php echo esc_html__( 'Choose start and end date for discount rule to apply. Leave empty for no duration limit.', 'addify_b2b' ); ?></p>
				</td>
			</tr>					
		</tbody>
	</table>
</div>


