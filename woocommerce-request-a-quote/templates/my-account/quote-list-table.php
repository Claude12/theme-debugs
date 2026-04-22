<?php
/**
 * Quote details in my Account.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/my-account/quote-list-table.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $customer_quotes ) ) {


	?>
	<table class="shop_table shop_table_responsive cart my_account_orders my_account_quotes">
		<thead>
			<tr>
				<th data-title=""><?php echo esc_html__( 'Quote', 'addify_b2b' ); ?></th>
				<th><?php echo esc_html__( 'Status', 'addify_b2b' ); ?></th>
				<th><?php echo esc_html__( 'Date', 'addify_b2b' ); ?></th>
				<th><?php echo esc_html__( 'Action', 'addify_b2b' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php

			foreach ( $customer_quotes as $quote ) {
				$quote_status = get_post_meta( $quote->ID, 'quote_status', true );
				$hashed_quote_id= base64_encode( $quote->ID . '|' . hash_hmac( 'sha256', $quote->ID, wp_salt( 'auth' ) ) );

				?>
				<tr>
					<td data-title="ID">
						<a href="<?php echo esc_url( wc_get_endpoint_url( 'request-quote', $quote->ID ) ); ?>">
							<?php echo esc_html__( '#', 'addify_b2b' ) . intval( $quote->ID ); ?>
						</a>
					</td>
					<td data-title="Status">
						<?php echo isset( $statuses[ $quote_status ] ) ? esc_html( $statuses[ $quote_status ] ) : 'Pending'; ?>
					</td>
					<td data-title="Date">
						<time datetime="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $quote->post_date ) ) ); ?>" title="<?php echo esc_attr( strtotime( $quote->post_date ) ); ?>"><?php echo esc_attr( date_i18n( get_option( 'date_format' ), strtotime( $quote->post_date ) ) ); ?></time>
					</td>							
					<td class="afrfq-quote-actions" data-title="Action">
						<a href="<?php echo esc_url( wc_get_endpoint_url( 'request-quote', $quote->ID ) ); ?>" class="woocommerce-button button view afrfq-view-quote">
							<?php echo esc_html__( 'View', 'addify_b2b' ); ?>
						</a>

					<?php
					if ('yes' !== get_option( 'afrfq_disable_for_customers' )) {
						$pdf_download_url = wp_nonce_url(admin_url('admin-ajax.php?action=af_rfq_download_quote_pdf_account_page&quote_id=' . rawurlencode($hashed_quote_id)), 'afquote-ajax-nonce');
						?>
						<a href="<?php echo esc_url($pdf_download_url); ?>" class="woocommerce-button button afrfq-pdf-download">
							<img src="<?php echo esc_url( AFRFQ_URL . '/assets/images/pdf-icon.png' ); ?>" alt="<?php esc_attr_e( 'Download PDF', 'addify_b2b' ); ?>">
						</a>
					<?php } ?>
						<?php
						$afrfq_enable = 'yes' === get_option( 'afrfq_enable_convert_order' ) ? true : false;
						$afrfq_enable_convert_to_cart = 'yes' === get_option( 'afrfq_enable_convert_to_cart' ) ? true : false;
						$convert_to_cart_url = get_post_meta( $quote->ID, 'afrfq_convert_to_cart_url', true );

						if ( in_array( $quote_status, array( 'af_accepted', 'af_in_process', 'af_converted_to_cart' ), true ) ) :
							?>
							<form class="quote-convert-to-order" method="post">
								<?php wp_nonce_field( '_afrfq__wpnonce', '_afrfq__wpnonce' ); ?>
								<?php if ('af_converted_to_cart' !== $quote_status && $afrfq_enable_convert_to_cart) : ?>
									<button type="submit" value="<?php echo intval( $quote->ID ); ?>" name="addify_convert_to_cart_customer" id="addify_convert_to_cart_customer" class="button button-primary button-large"><?php echo esc_html__( 'Convert to Cart', 'addify_b2b' ); ?></button>
								<?php endif; ?>

								<?php if ($afrfq_enable && 'af_converted_to_cart' !== $quote_status) : ?>
									<button type="submit" value="<?php echo intval( $quote->ID ); ?>" name="addify_convert_to_order_customer" id="addify_convert_to_order_customer" class="button button-primary button-large"><?php echo esc_html__( 'Convert to Order', 'addify_b2b' ); ?></button>
								<?php endif; ?>
								
								<?php if ('af_converted_to_cart' == $quote_status) : ?>
									<button
										type="button"
										class="button button-primary button-large afrfq-proceed-btn"
										data-url="<?php echo esc_url( $convert_to_cart_url ); ?>">
										<?php esc_html_e( 'Proceed to Checkout', 'addify_b2b' ); ?>
									</button>
								<?php endif; ?>
							</form>
						<?php endif; ?>
						
					</td>
				</tr>
						<?php } ?>
		</tbody>
	</table>

<?php } else { ?>

	<div class="woocommerce-MyAccount-content">
		<div class="woocommerce-notices-wrapper"></div>
		<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
			<a class="woocommerce-Button button" href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php echo esc_html__( 'Go to shop', 'addify_b2b' ); ?></a><?php echo esc_html__( 'No quote has been made yet.', 'addify_b2b' ); ?></div>
	</div>
	<?php
}

