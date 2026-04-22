<?php
/**
 * Quote details in my Account.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/my-account/quote-details-my-account.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

$quote_contents         = get_post_meta( $afrfq_id, 'quote_contents', true );
$quote_status           = get_post_meta( $afrfq_id, 'quote_status', true );
$quote_coverter         = get_post_meta( $afrfq_id, 'converted_by', true );
$order_for_this_quote   = get_post_meta($afrfq_id, 'order_for_this_quote', true);
$afrfq_quote_notes      = get_post_meta( $afrfq_id, 'afrfq_quote_notes', true )?get_post_meta( $afrfq_id, 'afrfq_quote_notes', true ):array();
$date_format = get_option('date_format');
$time_format = get_option('time_format');

$quote_user = get_post_meta( $afrfq_id, '_customer_user', true );

if ( is_user_logged_in() ) {
	$customer = get_current_user_id();
}

if ( $customer != $quote_user ) {

	$func = '<div class="woocommerce-MyAccount-content">
	<div class="woocommerce-notices-wrapper"></div><div class="woocommerce-error">' . esc_html__( 'Invalid quote.', 'addify_b2b' ) . ' <a href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '" class="wc-forward">My account</a></div></div>';

	echo wp_kses_post( $func );
	return;
}

$conv_enable = 'yes' === get_option( 'afrfq_enable_converted_by' ) ? true : false;
$statuses    = array(
	'af_pending'            => __( 'Pending', 'addify_b2b' ),
	'af_in_process'         => __( 'In Process', 'addify_b2b' ),
	'af_accepted'           => __( 'Accepted', 'addify_b2b' ),
	'af_converted_to_cart'  => __( 'Converted to Cart', 'addify_b2b' ),
	'af_converted'          => __( 'Converted to Order', 'addify_b2b' ),
	'af_declined'           => __( 'Declined', 'addify_b2b' ),
	'af_cancelled'          => __( 'Cancelled', 'addify_b2b' ),
);


if ( ! isset( $af_quote ) ) {
	$af_quote = new AF_R_F_Q_Quote();
}

$quote_totals = $af_quote->get_calculated_totals( $quote_contents, $afrfq_id );

$allowed_user_roles = get_option('afrfq_enable_for_specific_user_role');

$user_id        = get_post_meta( $afrfq_id, '_customer_user', true );
$user           = ! empty( $user_id ) ? get_user_by( 'id', intval( $user_id ) ) : null;
$user_role      = is_object( $user ) ? $user->roles : array( 'guest' );

$price_display    = 'yes' === get_option( 'afrfq_enable_pro_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
$of_price_display = 'yes' === get_option( 'afrfq_enable_off_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
$tax_display      = 'yes' === get_option( 'afrfq_enable_tax' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;


?>
<section class="woocommerce-order-details addify-quote-details">
	<?php do_action( 'addify_before_quote_table' ); ?>

	<table class="shop_table order_details quote_details" cellspacing="0">
		<tr>
			<th class="quote-number"><?php esc_html_e( 'Quote #', 'addify_b2b' ); ?></th>
			<td class="quote-number"><?php echo esc_html( $afrfq_id ); ?> </td>
		</tr>
		<tr>
			<th class="quote-date"><?php esc_html_e( 'Quote Date', 'addify_b2b' ); ?></th>
			<td class="quote-date"><?php echo esc_attr( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $quote->post_date ) ) ); ?> </td>
		</tr>
		<tr>
			<th class="quote-status"><?php esc_html_e( 'Current Status', 'addify_b2b' ); ?></th>
			<td class="quote-status"><?php echo isset( $statuses[ $quote_status ] ) ? esc_html( $statuses[ $quote_status ] ) : 'Pending'; ?> </td>
		</tr>
		<?php if ( $conv_enable && 'af_converted' === $quote_status ) : ?>
			<tr>
				<th class="quote-converter"><?php esc_html_e( 'Converted by', 'addify_b2b' ); ?></th>
				<td class="quote-converter"><?php echo esc_html( $quote_coverter ); ?> </td>
			</tr>
		<?php endif; ?>

		<?php
		if ( !empty($order_for_this_quote) ) : 
			$order_obj = wc_get_order( $order_for_this_quote );

			if ( $order_obj ) {
				$view_order_url = $order_obj->get_view_order_url();
			}
			?>
			<tr>
				<th class="order-id"><?php esc_html_e( 'Order #', 'addify_b2b' ); ?></th>
				<td class="order-id"><a href="<?php echo esc_url( $view_order_url ); ?>"><?php echo esc_html( $order_for_this_quote ); ?></a>
			</td>
			</tr>
		<?php endif; ?>

		
	</table>

	<?php
	$convert_to_cart_url = get_post_meta( $afrfq_id, 'afrfq_convert_to_cart_url', true );
	if ( 'af_converted_to_cart' === $quote_status && !empty($convert_to_cart_url)) :
		?>
		<div class="afrfq_cart_link_section" style="margin: 25px 0; padding: 15px; background-color: #f8fafc; border-left: 4px solid green;">
			<p style="margin-bottom: 12px; font-size: 16px; color: #2d3748;">
				<?php echo esc_html__( 'Great news! Your quote items are now waiting in your cart.', 'addify_b2b' ); ?>
				<?php echo esc_html__( 'Ready to complete your purchase?', 'addify_b2b' ); ?>
			</p>
			<a 
				href="<?php echo esc_url( $convert_to_cart_url ); ?>" 
				class="button" 
				style="display: inline-block; padding: 10px 20px; background-color: green; color: white; text-decoration: none; border-radius: 4px; font-weight: 500;"
			>
				<?php echo esc_html__( 'Proceed to Checkout', 'addify_b2b' ); ?>
				<span style="margin-left: 5px;">→</span>
			</a>
		</div>
	<?php endif; ?>

		
	<?php
	if ( !empty($afrfq_quote_notes) ) : 
		?>
		<h2><?php echo esc_html__( 'Quote updates', 'addify_b2b' ); ?></h2>

		<ol class="afrfq_quote_note notes">
			<?php foreach ( $afrfq_quote_notes as $note ) : ?>
				<?php if ( ! empty( $note['is_customer_note'] ) ) : ?>
					<li class="afrfq_quote_note note">
						<div class="afrfq_quote_note_inner ">
							<div class="afrfq_quote_note_text ">
								<p class="afrfq_quote_note_meta">
									<?php
										echo esc_html(
											date_i18n( 'l', strtotime( $note['datetime'] ) ) . ' ' .
											date_i18n( $date_format, strtotime( $note['datetime'] ) ) . ', ' .
											date_i18n( $time_format, strtotime( $note['datetime'] ) )
										);
									?>
								</p>
								<div class="afrfq_quote_note_description">
									<p><?php echo wp_kses_post( $note['message'] ); ?></p>
								</div>
							</div>
						</div>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ol>

	<?php endif; ?>


	<h2><?php echo esc_html__( 'Customer Information', 'addify_b2b' ); ?></h2>

	<table class="shop_table order_details quote_details" cellspacing="0">
		<?php
		$quote_fields_obj = new AF_R_F_Q_Quote_Fields();
		$quote_fields     = (array) $quote_fields_obj->quote_fields;

		foreach ( $quote_fields as $key => $field ) :

			$field_id = $field->ID;

			$afrfq_field_name        = get_post_meta( $field_id, 'afrfq_field_name', true );
			$afrfq_field_type        = get_post_meta( $field_id, 'afrfq_field_type', true );
			$afrfq_field_label       = get_post_meta( $field_id, 'afrfq_field_label', true );
			$afrfq_field_value       = get_post_meta( $field_id, 'afrfq_field_value', true );
			$afrfq_field_title       = get_post_meta( $field_id, 'afrfq_field_title', true );
			$afrfq_field_terms       = get_post_meta( $field_id, 'afrfq_field_terms', true );
			$afrfq_field_options     = (array) get_post_meta( $field_id, 'afrfq_field_options', true );

			$field_data              = get_post_meta( $afrfq_id, $afrfq_field_name, true );

			// Get default value if set.
			if ( empty( $field_data ) || empty( $afrfq_field_name ) ) {
				continue;
			}
			?>
				<tr>
					<th><?php echo esc_attr($afrfq_field_label); ?></th>
					<td>
					<?php
					switch ( $afrfq_field_type ) {
						case 'terms_cond':
							?>
							<?php echo wp_kses_post( $afrfq_field_terms ); ?>
						<?php
							break;
						case 'text':
						case 'time':
						case 'date':
						case 'datetime':
						case 'email':
						case 'number':
						case 'textarea':
							?>
							<?php echo esc_attr($field_data); ?>
							<?php
							break;
						case 'file':
								$file_url  = AFRFQ_UPLOAD_URL . $field_data ;
							if ( file_exists( AFRFQ_UPLOAD_DIR . $field_data ) ) {
								?>
									<a href="<?php echo esc_url( $file_url ); ?>" target="_blank" download>
									<?php echo esc_html__('Download', 'addify_b2b'); ?>
									</a>
									<?php
							} else {
								echo 'File not found.';
							}
									
							break;
						case 'select':
						case 'multiselect':
						case 'radio':
						case 'checkbox':
								$selected_options = array();

							if (is_array($field_data)) {
								foreach ( (array) $afrfq_field_options as $option ) {
									$value = strtolower( trim( $option ) );
									if (in_array($value, $field_data)) {
										$selected_options[] = $option;
									}
								}
							} else {
								foreach ( (array) $afrfq_field_options as $option ) {
									$value = strtolower( trim( $option ) );
									if ($value === $field_data) {
										$selected_options[] = $option;
									}
								}
							}
							?>
							<?php echo esc_attr(implode(',', $selected_options)); ?>

										
							<?php
							break;

					}
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<h2><?php echo esc_html__( 'Quote Details', 'addify_b2b' ); ?></h2>
	<div class="afrfq_quote_product_table_wrapper">
		<table class="shop_table shop_table_responsive cart order_details quote_details" cellspacing="0">
			<thead>
				<tr>
					<th class="product-thumbnail">&nbsp;</th>
					<th class="product-name"><?php esc_html_e( 'Product', 'addify_b2b' ); ?></th>
					<?php if ( $price_display ) : ?>
						<th class="product-price"><?php esc_html_e( 'Price', 'addify_b2b' ); ?></th>
					<?php endif; ?>
					<?php if ( $of_price_display ) : ?>
						<th class="product-price"><?php esc_html_e( 'Offered Price', 'addify_b2b' ); ?></th>
					<?php endif; ?>
					<th class="product-quantity"><?php esc_html_e( 'Quantity', 'addify_b2b' ); ?></th>
					<?php if ( $price_display ) : ?>
						<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'addify_b2b' ); ?></th>
					<?php endif; ?>
					<?php if ( $of_price_display ) : ?>
						<th class="product-subtotal"><?php esc_html_e( 'Offered Subtotal', 'addify_b2b' ); ?></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php do_action( 'addify_before_quote_contents' ); ?>
	
				<?php
				if ( ! empty( $quote_contents ) ) {
	
	
					foreach ( $quote_contents as $quote_item_key => $quote_item ) {
	
						if ( ! isset( $quote_item['data'] ) || ! is_object( $quote_item['data'] ) ) {
							continue;
						}

	
						$_product      = apply_filters( 'addify_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key );

						// incase product type is custom and now the plugin which registers that product type is not active then it simply empties the quote
						// this case is mainly added to tackle composite product in quote when configurable product plugin is not active
						if (is_object($_product) && $_product instanceof __PHP_Incomplete_Class) {
							continue;
						}
						
						$product_id    = apply_filters( 'addify_quote_item_product_id', $quote_item['product_id'], $quote_item, $quote_item_key );
						$price         = empty( $quote_item['addons_price'] ) ? $_product->get_price() : $quote_item['addons_price'];

						$price                   = empty( $quote_item['price_calculator_price'] ) ? $price : $quote_item['price_calculator_price'];

						$price         = isset($quote_item['composite_product_price']) ? $quote_item['composite_product_price'] : $price;
	
						$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;
	
						$component_name = isset($quote_item['component_name']) ? $quote_item['component_name'] . '<br>' : '';
	
	
						if ( $_product && $_product->exists() && $quote_item['quantity'] > 0 && apply_filters( 'addify_quote_item_visible', true, $quote_item, $quote_item_key ) ) {
							$product_permalink = apply_filters( 'addify_quote_item_permalink', $_product->is_visible() ? $_product->get_permalink( $quote_item ) : '', $quote_item, $quote_item_key );
							?>
						<tr class="addify__quote-item <?php echo esc_attr( apply_filters( 'addify_quote_item_class', 'cart_item', $quote_item, $quote_item_key ) ); ?>">
	
							<td class="product-thumbnail">
							<?php
							$thumbnail = apply_filters( 'addify_quote_item_thumbnail', $_product->get_image(), $quote_item, $quote_item_key );
	
							if ( ! $product_permalink ) {
								echo wp_kses_post( $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) ); // phpcs:ignore WordPress.Security.EscapeOutput
							}
							?>
							</td>
	
							<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'addify_b2b' ); ?>">
							<?php
								echo wp_kses_post($component_name);
							if ( ! $product_permalink ) {
								echo wp_kses_post( apply_filters( 'addify_quote_item_name', $_product->get_name(), $quote_item, $quote_item_key ) . '&nbsp;' );
							} else {
								echo wp_kses_post( apply_filters( 'addify_quote_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $quote_item, $quote_item_key ) );
							}
							?>
							<br>
							<?php
							if (!empty($_product->get_sku())) {
								echo '<div class="wc-order-item-sku"><strong>' . esc_html__( 'SKU:', 'woocommerce' ) . '</strong> ' . esc_html( $_product->get_sku() ) . '</div>';
							}
							do_action( 'addify_after_quote_item_name', $quote_item, $quote_item_key );
	
							// Meta data.
							echo wp_kses_post( wc_get_formatted_cart_item_data( $quote_item ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	
							// Backorder notification.
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $quote_item['quantity'] ) ) {
								echo wp_kses_post( apply_filters( 'addify_quote_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'addify_b2b' ) . '</p>', $product_id ) );
							}
							?>
							</td>
	
							<?php if ( $price_display ) : ?>
								<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'addify_b2b' ); ?>">
									<?php echo wp_kses_post( wc_price( $price ) ); ?>
								</td>
							<?php endif; ?>
							
							<?php if ( $of_price_display && ( !isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products']) ) ) : ?>
								<td class="product-price" data-title="<?php esc_attr_e( 'Offered Price', 'addify_b2b' ); ?>">
									<?php echo wp_kses_post( wc_price( $offered_price ) ); ?>
								</td>
							<?php elseif ($of_price_display) : ?>
								<td></td>
							<?php endif; ?>
							
							<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'addify_b2b' ); ?>">
								<?php
								$qty_display = $quote_item['quantity'];
								// phpcs:ignore WordPress.Security.EscapeOutput
								echo wp_kses_post( apply_filters( 'addify_rfq_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&nbsp;%s', $qty_display ) . '</strong>', $quote_item ) );
								?>
							</td>
	
								<?php if ( $price_display && ( !isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products']) ) ) : ?>
								<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'addify_b2b' ); ?>">
									<?php echo wp_kses_post( wc_price( $price * $qty_display ) ); ?>
								</td>
							<?php endif; ?>
								<?php if ( $of_price_display && ( !isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products']) ) ) : ?>	
								<td class="product-subtotal" data-title="<?php esc_attr_e( 'Offered Subtotal', 'addify_b2b' ); ?>">
									<?php echo wp_kses_post( wc_price( $offered_price * $qty_display ) ); ?>
								</td>
							<?php endif; ?>
						</tr>
							<?php
						}
					}
				}
				?>
			</tbody>
		</table>
	</div>
			<?php do_action( 'addify_after_quote_contents' ); ?>

	<?php do_action( 'addify_after_quote_table' ); ?>


	<?php do_action( 'addify_before_quote_collaterals' ); ?>
	
	<div class="cart-collaterals">
		<?php
			/**
			 * Cart collaterals hook.
			 *
			 * @hooked addify_cross_sell_display
			 * @hooked addify_quote_totals - 10
			 */
			do_action( 'addify_quote_collaterals' );
		?>
		<?php if ( $price_display || $of_price_display ) : ?>
			<div class="cart_totals">

				<?php do_action( 'woocommerce_before_cart_totals' ); ?>

				<h2><?php esc_html_e( 'Quote totals', 'addify_b2b' ); ?></h2>

				<table cellspacing="0" class="shop_table shop_table_responsive">

				<?php if ( $price_display ) : ?>
						<tr class="cart-subtotal">
							<th><?php esc_html_e( 'Subtotal(standard)', 'addify_b2b' ); ?></th>
							<td data-title="<?php esc_attr_e( 'Subtotal', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_subtotal'] ) ); ?></td>
						</tr>
						<?php
					endif;

				if ( isset( $quote_totals['_offered_total'] ) && $of_price_display ) {
					?>
						<tr class="cart-subtotal">
							<th><?php esc_html_e( 'Offered Price Subtotal', 'addify_b2b' ); ?></th>
							<td data-title="<?php esc_attr_e( 'Offered Price Subtotal', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_offered_total'] ) ); ?></td>
						</tr>
						<?php
				}

				if ( wc_tax_enabled() && $tax_display && $price_display ) {
					?>
						<tr class="tax-rate">
							<th><?php echo esc_html__( 'Vat(standard)', 'addify_b2b' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
							<td data-title="<?php echo esc_html__( 'Vat', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_tax_total'] ) ); ?></td>
						</tr>
						<?php
				}

				if ( wc_tax_enabled() && $tax_display && $of_price_display ) {
					?>
						<tr class="tax-rate">
							<th><?php echo esc_html__( 'Offered Vat', 'addify_b2b' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
							<td data-title="<?php echo esc_html__( 'Offered Vat', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_offered_tax_total'] ) ); ?></td>
						</tr>
						<?php
				}
				?>

					<?php if ( $quote_totals['_shipping_total'] ) : ?>
						<tr class="shipping-cost">
							<th><?php echo esc_html__( 'Shipping Cost', 'addify_b2b' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
							<td data-title="<?php echo esc_html__( 'Shipping', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_shipping_total'] ) ); ?></td>
						</tr>
					<?php endif; ?>
					
					<?php if ( $price_display ) : ?>
						<tr class="order-total">
							<th><?php esc_html_e( 'Total(standard)', 'addify_b2b' ); ?></th>
							<td data-title="<?php esc_attr_e( 'Total', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_total'] ) ); ?></td>
						</tr>
					<?php endif; ?>

					<?php if ( $of_price_display ) : ?>
						<tr class="order-total">
							<th><?php esc_html_e( 'Offered Total', 'addify_b2b' ); ?></th>
							<td data-title="<?php esc_attr_e( 'Offered Total', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_offered_total_after_tax'] ) ); ?></td>
						</tr>
					<?php endif; ?>
				
				
				</table>				
			</div>
		<?php endif; ?>

		<?php
		$afrfq_enable = 'yes' === get_option( 'afrfq_enable_convert_order' ) ? true : false;
		$afrfq_enable_convert_to_cart = 'yes' === get_option( 'afrfq_enable_convert_to_cart' ) ? true : false;

		if ( in_array( $quote_status, array( 'af_accepted', 'af_in_process', 'af_converted_to_cart' ), true ) && ( $afrfq_enable || $afrfq_enable_convert_to_cart ) ) :
			?>
			<form method="post">
				<?php wp_nonce_field( '_afrfq__wpnonce', '_afrfq__wpnonce' ); ?>
				<div class="addify_converty_to_order_button">
				<?php if ($afrfq_enable) : ?>
					<button type="submit" value="<?php echo intval( $afrfq_id ); ?>" name="addify_convert_to_order_customer" id="addify_convert_to_order_customer" class="button button-primary button-large" >
						<?php echo esc_html__( 'Convert to Order', 'addify_b2b' ); ?>
					</button>
				<?php endif; ?>
				<?php if ('af_converted_to_cart' !== $quote_status && $afrfq_enable_convert_to_cart) : ?>
					<button type="submit" value="<?php echo intval( $afrfq_id ); ?>" name="addify_convert_to_cart_customer" id="addify_convert_to_cart_customer" class="button button-primary button-large" >
						<?php echo esc_html__( 'Convert to Cart', 'addify_b2b' ); ?>
					</button>
				<?php endif; ?>
				</div>
			</form>
		<?php endif; ?>

		


	</div>
</section>
