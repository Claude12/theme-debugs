<?php
/**
 * Email Quote Contents.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/emails/quote-contents.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'addify_rfq_before_email_quote_contents' );

?>
<div style="margin-top:12px;margin-bottom:10px">

	<table style="border-collapse: collapse; border: 0; color:#636363;vertical-align:middle;width:100%;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif" cellspacing="0" cellpadding="6" border="1">
		<thead>
			<tr>
				<th scope="col" style="color:#000;border:1px solid #e5e5e5;vertical-align:middle;padding:9px 12px;text-align:left;"><?php echo esc_html__( 'Image', 'addify_b2b' ); ?>
				</th>
				<th scope="col" style="color:#000;border:1px solid #e5e5e5;vertical-align:middle;padding:9px 12px;text-align:left;">
					<?php echo esc_html__( 'Product', 'addify_b2b' ); ?>
				</th>
				<th scope="col" style="color:#000;border:1px solid #e5e5e5;vertical-align:middle;padding:9px 12px;text-align:center; width: 58px;">
					<?php echo esc_html__( 'Quantity', 'addify_b2b' ); ?>
				</th>
				<?php if ( $price_display ) : ?>
					<th scope="col" style="color:#000;border:1px solid #e5e5e5;vertical-align:middle;padding:9px 12px;text-align:center; width: 50px;">
						<?php echo esc_html__( 'Price', 'addify_b2b' ); ?>
					</th>
				<?php endif; ?>
				<?php if ( $of_price_display ) : ?>
					<th scope="col" style="color:#000;border:1px solid #e5e5e5;vertical-align:middle;padding:9px 8px; text-align:center; width: 88px;">
						<?php echo esc_html__( 'Offered Price', 'addify_b2b' ); ?>
					</th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( (array) $quote_contents as $key => $item ) :

				$product = isset( $item['data'] ) ? $item['data'] : '';

				if ( ! is_object( $product ) ) {
					continue;
				}

				$price         = empty( $item['addons_price'] ) ? $product->get_price() : $item['addons_price'];
				$price         = empty( $item['role_base_price'] ) ? $price : $item['role_base_price'];
				$price         = empty( $item['price_calculator_price'] ) ? $price : $item['price_calculator_price'];
				$price         = isset( $item['composite_product_price'] ) ? $item['composite_product_price'] : $price;

				$component_name = isset($item['component_name']) ? $item['component_name'] : '';
				$offered_price = isset( $item['offered_price'] ) ? floatval( $item['offered_price'] ) : $price;
				
				
				?>
				<tr>
					<td style="color:#636363;border:1px solid #e5e5e5;padding: 7px 10px;width: 60px;text-align: center;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word">
						<?php
						$image_id  = $product->get_image_id();
						$image_src = wp_get_attachment_image_src( $image_id, 'woocommerce_gallery_thumbnail' );
						$image_url = $image_src ? $image_src[0] : '';
						if ( strpos( $image_url, 'http' ) === false ) {
							$image_url = home_url( $image_url );
						}
						?>
						<img src="<?php echo esc_url( $image_url ); ?>" width="60px" style=" margin-right: 0;">
					</td>
					<td style="color:#636363;border:1px solid #e5e5e5;padding:9px 12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word">
						<?php
						if ($component_name) {
							echo esc_html($component_name) . '<br>';
						}
						echo esc_html( $product->get_name() );
						echo wp_kses_post( wc_get_formatted_cart_item_data( $item ) );
						if (!empty($product->get_sku())) {    
							?>
							<br>
							<small>
								<b><?php echo esc_html__( 'SKU:', 'addify_b2b' ); ?></b><?php echo esc_html( $product->get_sku() ); ?>
							</small>
						<?php
						}
						?>
					</td>
					<td style="color:#636363;border:1px solid #e5e5e5;padding:9px 12px;text-align:center;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
					<?php echo esc_attr( $item['quantity'] ); ?>
					</td>

					<?php if ( $price_display ) : ?>
						<td style="color:#636363;border:1px solid #e5e5e5;padding:9px 12px;text-align:center;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
						<?php echo wp_kses_post( wc_price( $price ) ); ?>
						</td>
					<?php endif; ?>

					<?php if ( $of_price_display && ( !isset($item['composite_child_products']) || empty($item['composite_child_products']) ) ) : ?>
						<td style="color:#636363;border:1px solid #e5e5e5;padding:9px 12px;text-align:center;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
						<?php echo wp_kses_post( wc_price( $offered_price ) ); ?>
						</td>
					<?php elseif ($of_price_display) : ?>
						<td></td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
		</tbody>
		<tfoot>
			<?php $colspan_value = ( 3 == $colspan ) ? 2 : ''; ?>
			<?php if ( $price_display ) : ?>
			<tr>
				<td colspan="2" style="border: 0;"></td>
				<th scope="row" colspan="<?php echo intval($colspan_value); ?>" style="color:#000; border-left:1px solid #e5e5e5; border-bottom:1px solid #e5e5e5; vertical-align:middle;padding:9px 12px;text-align:left; min-width: 113px;">
					<?php echo esc_html__( 'Subtotal( Standard)', 'addify_b2b' ); ?></th>
				<td style="color:#636363;border: solid #e5e5e5; border-width: 0 1px 1px 1px;vertical-align:middle;padding:9px 12px;text-align:center;">
					<?php echo wp_kses_post( wc_price( $quote_subtotal ) ); ?>
				</td>
			</tr>
			<?php endif; ?>

			<?php if ( $of_price_display ) : ?>
			<tr>
				<td colspan="2" style="border: 0;"></td>
				<th scope="row" colspan="<?php echo intval($colspan_value); ?>" style="color:#000;border-left:1px solid #e5e5e5; border-bottom:1px solid #e5e5e5; vertical-align:middle;padding:9px 12px;text-align:left; min-width: 113px;">
					<?php echo esc_html__( 'Subtotal (Offered)', 'addify_b2b' ); ?></th>
				<td style="color:#636363;border: solid #e5e5e5; border-width: 0 1px 1px 1px;vertical-align:middle;padding:9px 12px;text-align:center;">
					<?php echo wp_kses_post( wc_price( $offered_total ) ); ?>
				</td>
			</tr>
			<?php endif; ?>

			<?php if ( wc_tax_enabled() && ( $price_display ) && $tax_display ) : ?>
				<tr>
					<td colspan="2" style="border: 0;"></td>
					<th scope="row" colspan="<?php echo intval($colspan_value); ?>" style="color:#000;border-left:1px solid #e5e5e5;vertical-align:middle;padding:9px 12px;text-align:left; min-width: 113px;">
						<?php echo esc_html__( 'Vat (Standard)', 'addify_b2b' ); ?></th>
					<td style="color:#636363;border: solid #e5e5e5; border-width: 0 1px 1px 1px;vertical-align:middle;padding:9px 12px;text-align:center;">
						<?php echo wp_kses_post( wc_price( $vat_total ) ); ?>
					</td>
				</tr>
			<?php endif; ?>

			<?php if ( wc_tax_enabled() && ( $of_price_display ) && $tax_display ) : ?>
				<tr>
					<td colspan="2" style="border: 0;"></td>
					<th scope="row" colspan="<?php echo intval($colspan_value); ?>" style="color:#000;border-left:1px solid #e5e5e5;vertical-align:middle;padding:9px 12px;text-align:left; min-width: 113px;">
						<?php echo esc_html__( 'Vat (Offered)', 'addify_b2b' ); ?></th>
					<td style="color:#636363;border: solid #e5e5e5; border-width: 0 1px 1px 1px;vertical-align:middle;padding:9px 12px;text-align:center;">
						<?php echo wp_kses_post( wc_price( $offered_vat ) ); ?>
					</td>
				</tr>
			<?php endif; ?>

			<?php if ( $shipping_cost && ( $price_display || $of_price_display ) ) : ?>
				<tr>
					<td colspan="2" style="border: 0;"></td>
					<th scope="row" colspan="<?php echo intval($colspan_value); ?>" style="color:#000;border-left:1px solid #e5e5e5;vertical-align:middle;padding:9px 12px;text-align:left; min-width: 113px;">
						<?php echo esc_html__( 'Shipping Cost', 'addify_b2b' ); ?></th>
					<td style="color:#636363;border: solid #e5e5e5; border-width: 0 1px 1px 1px;vertical-align:middle;padding:9px 12px;text-align:center;">
						<?php echo wp_kses_post( wc_price( $shipping_cost ) ); ?>
					</td>
				</tr>
			<?php endif; ?>

			<?php if ( $price_display ) : ?>
			<tr>
				<td colspan="2" style="border: 0;"></td>
				<th scope="row" colspan="<?php echo intval($colspan_value); ?>" style="color:#000;border-left:1px solid #e5e5e5;vertical-align:middle;padding:9px 12px;text-align:left; min-width: 110px;">
					<?php echo esc_html__( 'Total (Standard)', 'addify_b2b' ); ?></th>
				<td style="color:#636363;border: solid #e5e5e5; border-width: 0 1px 1px 1px;vertical-align:middle;padding:9px 12px;text-align:center;">
					<?php echo wp_kses_post( wc_price( $quote_total ) ); ?>
				</td>
			</tr>
			<?php endif; ?>

			<?php if ( $of_price_display ) : ?>
			<tr>
				<td colspan="2" style="border: 0;"></td>
				<th scope="row" colspan="<?php echo intval($colspan_value); ?>" style="color:#000;border-left:1px solid #e5e5e5;vertical-align:middle;padding:9px 12px;text-align:left; min-width: 110px;">
					<?php echo esc_html__( 'Total (Offered)', 'addify_b2b' ); ?></th>
				<td style="color:#636363;border: solid #e5e5e5; border-width: 0 1px 1px 1px;vertical-align:middle;padding:9px 12px;text-align:center;">
					<?php echo wp_kses_post( wc_price( $quote_offered_total_after_tax ) ); ?>
				</td>
			</tr>
			<?php endif; ?>
		</tfoot>
	</table>

	<?php 
	$btn_background_color = get_option( 'afrfq_email_button_background_color', '#008000' );
	$btn_text_color = get_option( 'afrfq_email_button_text_color', '#FFFFFF' );
	
	?>

	<?php if ( 'af_converted_to_cart' === $quote_status && 'af_email_to_customer' === $email_type ) : ?>
		<div style="margin-top: 50px; text-align: center;">
			<a href="<?php echo esc_url( $afrfq_convert_to_cart_url ); ?>" target="_blank"
			   style="background-color: <?php echo esc_attr( $btn_background_color ); ?>; color: <?php echo esc_attr( $btn_text_color ); ?>; padding: 12px 25px; text-align: center; text-decoration: none; display: inline-block; font-size: 14px; margin: 10px 0; border-radius: 4px; font-weight: bold;">
				<?php echo esc_html__( 'PROCEED TO CHECKOUT', 'addify_b2b' ); ?>
				<span style="font-size: 22px; "><?php echo esc_html__( '→', 'addify_b2b' ); ?></span>
			</a>
		</div>
	<?php endif; ?>

</div>
