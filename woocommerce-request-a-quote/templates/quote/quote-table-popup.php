<?php
/**
 * Customer information table for email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/quote/quote-table-popup.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $af_quote ) ) {
	$af_quote = new AF_R_F_Q_Quote();
}


$allowed_user_roles = get_option('afrfq_enable_for_specific_user_role');

if (is_user_logged_in()) {
	$user = wp_get_current_user(); // object.
	$user_role = $user->roles;
} else {
	$user_role = array( 'guest' );
}

$price_display    = 'yes' === get_option( 'afrfq_enable_pro_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
$of_price_display = 'yes' === get_option( 'afrfq_enable_off_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
$tax_display      = 'yes' === get_option( 'afrfq_enable_tax' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;

$colspan          = 4;
$colspan          = $price_display ? $colspan + 2 : $colspan;
$colspan          = $of_price_display ? $colspan + 2 : $colspan;

do_action( 'addify_before_quote_table' );

$af_page_template = !empty(get_option('afrfq_select_popup_template')) ? get_option('afrfq_select_popup_template') : 'template_one';
if ( 'template_one' == $af_page_template ) {
	?>
	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents  addify-quote-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-thumbnail"><?php esc_html_e( 'Image', 'addify_b2b' ); ?></th>
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
			foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

				if ( ! isset( $quote_item['data'] ) || ! is_object( $quote_item['data'] ) ) {
					continue;
				}

				$_product      = apply_filters( 'addify_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key );
				$product_id    = apply_filters( 'addify_quote_item_product_id', $quote_item['product_id'], $quote_item, $quote_item_key );
				$price         = empty( $quote_item['addons_price'] ) ? $_product->get_price() : $quote_item['addons_price'];
				$price         = empty( $quote_item['role_base_price'] ) ? $_product->get_price() : $quote_item['role_base_price'];
				$price         = empty( $quote_item['price_calculator_price'] ) ? $price : $quote_item['price_calculator_price'];
				$price         = isset( $quote_item['composite_product_price'] ) ? $quote_item['composite_product_price'] : $price;

				$component_name = isset($quote_item['component_name']) ? $quote_item['component_name'] : '';

				$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;

				if ( $_product && $_product->exists() && $quote_item['quantity'] > 0 && apply_filters( 'addify_quote_item_visible', true, $quote_item, $quote_item_key ) ) {
					$product_permalink = apply_filters( 'addify_quote_item_permalink', $_product->is_visible() ? $_product->get_permalink( $quote_item ) : '', $quote_item, $quote_item_key );
					?>
					<tr class=" <?php echo esc_attr( apply_filters( 'addify_quote_item_class', 'cart_item', $quote_item, $quote_item_key ) ); ?>">

					<?php if (!isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products'])) : ?>
						<td class="product-remove">
							<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo wp_kses_post(
									apply_filters(
										'addify_quote_item_remove_link',
										sprintf(
											'<a href="%s" class="remove remove-cart-item remove-quote-item" aria-label="%s" data-cart_item_key="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
											esc_attr( $quote_item_key ),
											esc_html__( 'Remove this item', 'addify_b2b' ),
											esc_attr( $quote_item_key ),
											esc_attr( $product_id ),
											esc_attr( $_product->get_sku() )
										),
										$quote_item_key
									)
								);
							?>
						</td>
						<?php else : ?>
							<td class="product-remove"></td>
					<?php endif; ?>

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
						if ($component_name) {
							echo esc_html($component_name) . '<br>';
						}
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'addify_quote_item_name', $_product->get_name(), $quote_item, $quote_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'addify_quote_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $quote_item, $quote_item_key ) );
						}

						do_action( 'addify_after_quote_item_name', $quote_item, $quote_item_key );

						// Meta data.
						echo wp_kses_post( wc_get_formatted_cart_item_data( $quote_item ) ); // phpcs:ignore WordPress.Security.EscapeOutput

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $quote_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'addify_quote_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'addify_b2b' ) . '</p>', $product_id ) );
						}

						if (!empty($_product->get_sku())) {
							echo wp_kses_post( sprintf( '<p><small><b>SKU:</b>%s</small></p>', esc_attr( $_product->get_sku() ) ) );
						}
						?>
						</td>

						<?php if ( $price_display ) : ?>
							<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'addify_b2b' ); ?>">
								<?php
									$args['qty']   = 1;
									$args['price'] = empty( $quote_item['addons_price'] ) ? $_product->get_price() : $quote_item['addons_price'];
									$args['price'] = empty( $quote_item['role_base_price'] ) ? $_product->get_price() : $quote_item['role_base_price'];
									$args['price'] = empty( $quote_item['price_calculator_price'] ) ? $args['price'] : $quote_item['price_calculator_price'];
									$args['price'] = isset( $quote_item['composite_product_price'] ) ? $quote_item['composite_product_price'] : $args['price'];

									echo wp_kses_post( apply_filters( 'addify_quote_item_price', $af_quote->get_product_price( $_product, $args ), $quote_item, $quote_item_key ) ); // phpcs:ignore WordPress.Security.EscapeOutput
								?>
							</td>
						<?php endif; ?>
						
						<?php if ( $of_price_display && ( !isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products']) ) ) : ?>
							<td class="product-price offered-price" data-title="<?php esc_attr_e( 'Offered Price', 'addify_b2b' ); ?>">
								<input type="number" class="input-text offered-price-input text" step="any" name="offered_price[<?php echo esc_attr( $quote_item_key ); ?>]" value="<?php echo esc_attr( $offered_price ); ?>">
							</td>
						<?php elseif ($of_price_display) : ?>
							<td></td>
						<?php endif; ?>	

						<?php 
						// current product is composite child product
						$allow_edit_in_quote = true;
						if (isset($quote_item['composite_child_products']) && !empty($quote_item['composite_child_products'])) {
							$allow_edit_in_quote = false;

							$parent_product_id = isset($quote_item['composite_child_products']['parent_product_id']) && !empty($quote_item['composite_child_products']['parent_product_id']) ? $quote_item['composite_child_products']['parent_product_id'] : '';
							$comp_key = isset($quote_item['composite_child_products']['comp_key']) && !empty($quote_item['composite_child_products']['comp_key']) ? $quote_item['composite_child_products']['comp_key'] : '';
							if ($parent_product_id && $comp_key) {
								$allow_edit = get_post_meta($parent_product_id, 'af_cp_allow_edit_in_cart', true) ;

								if ('yes' == $allow_edit) {
									$is_component_range_enabled = get_post_meta($parent_product_id, 'af_composite_product_quantity_op', true);
									
									if ('range' == $is_component_range_enabled[ $comp_key ]) {
										$allow_edit_in_quote = true;
									}
								} else {
									$allow_edit_in_quote = false;
								}
							}

						}

						if ($allow_edit_in_quote) :
							?>
							<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'addify_b2b' ); ?>">
							<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '<input type="hidden" name="quote_qty[%s]" value="1" />', $quote_item_key );
							} else {
								$max_product_quantity = apply_filters('addify_quote_product_quantity_maximum', $_product->get_max_purchase_quantity(), $_product, $quote_item);
								woocommerce_quantity_input(
									array(
										'input_name'   => "quote_qty[{$quote_item_key}]",
										'input_value'  => $quote_item['quantity'],
										'max_value'    => $max_product_quantity,
										'min_value'    => '0',
										'product_name' => $_product->get_name(),
									),
									$_product,
									true
								);
							}
							?>
							</td>
						<?php else : ?>
							<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'addify_b2b' ); ?>">
							<?php
							echo esc_attr( $quote_item['quantity'] );
							?>
							</td>
						<?php endif; ?>

						<?php if ( $price_display && ( !isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products']) ) ) : ?>
							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'addify_b2b' ); ?>">
								<?php
									$args['qty']   = $quote_item['quantity'];
									$args['price'] = empty( $quote_item['addons_price'] ) ? $_product->get_price() : $quote_item['addons_price'];
									$args['price'] = empty( $quote_item['role_base_price'] ) ? $args['price'] : $quote_item['role_base_price'];
									$args['price'] = empty( $quote_item['price_calculator_price'] ) ? $args['price'] : $quote_item['price_calculator_price'];
									$args['price'] = isset( $quote_item['composite_product_price'] ) ? $quote_item['composite_product_price'] : $args['price'];
									echo wp_kses_post( apply_filters( 'addify_quote_item_subtotal', $af_quote->get_product_subtotal( $_product, $quote_item['quantity'], $args ), $quote_item, $quote_item_key ) ); // phpcs:ignore WordPress.Security.EscapeOutput
								?>
							</td>
						<?php endif; ?>	

						<?php if ( $of_price_display && ( !isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products']) ) ) : ?>
							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Offered Subtotal', 'addify_b2b' ); ?>">
								<?php
									echo wp_kses_post( apply_filters( 'addify_quote_item_subtotal', wc_price( $offered_price * $quote_item['quantity'] ), $quote_item, $quote_item_key ) ); // phpcs:ignore WordPress.Security.EscapeOutput
								?>
							</td>
						<?php endif; ?>
						
					</tr>
					<?php
				}
			}
			?>
				<td colspan="<?php echo esc_attr( $colspan ); ?>" class="actions" style="display: none;">

					<?php
					$afrfq_update_button_text     = get_option( 'afrfq_update_button_text' );
					$afrfq_update_button_bg_color = get_option( 'afrfq_update_button_bg_color' );
					$afrfq_update_button_fg_color = get_option( 'afrfq_update_button_fg_color' );
					$afrfq_update_button_text     = empty( $afrfq_update_button_text ) ? __( 'Update Quote', 'addify_b2b' ) : $afrfq_update_button_text;
					?>

					<style type="text/css">
						.afrfq_update_quote_btn{
							color: <?php echo esc_html( $afrfq_update_button_fg_color ); ?> !important;
							background-color: <?php echo esc_html( $afrfq_update_button_bg_color ); ?> !important;
						}
					</style>

					<button type="button" type="submit" id="afrfq_update_quote_btn" class="button afrfq_update_quote_btn" name="update_quote" value="<?php esc_html( $afrfq_update_button_text ); ?>"><?php echo esc_html( $afrfq_update_button_text ); ?></button> 

					<?php do_action( 'addify_quote_actions' ); ?>

					<?php wp_nonce_field( 'addify-cart', 'addify-cart-nonce' ); ?>
				</td>
			</tbody>
			<?php do_action( 'addify_quote_contents' ); ?>
			</tbody>
		</table>

<?php
} else if ( 'template_two' == $af_page_template ) {
	?>
	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents  addify-quote-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-thumbnail"><?php esc_html_e( 'Image', 'addify_b2b' ); ?></th>
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
			foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

				if ( ! isset( $quote_item['data'] ) || ! is_object( $quote_item['data'] ) ) {
					continue;
				}

				$_product      = apply_filters( 'addify_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key );
				$product_id    = apply_filters( 'addify_quote_item_product_id', $quote_item['product_id'], $quote_item, $quote_item_key );
				$price         = empty( $quote_item['addons_price'] ) ? $_product->get_price() : $quote_item['addons_price'];
				$price         = empty( $quote_item['role_base_price'] ) ? $_product->get_price() : $quote_item['role_base_price'];
				$price         = empty( $quote_item['price_calculator_price'] ) ? $price : $quote_item['price_calculator_price'];
				$price         = isset( $quote_item['composite_product_price'] ) ? $quote_item['composite_product_price'] : $price;
				
				$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;

				$component_name = isset($quote_item['component_name']) ? $quote_item['component_name'] : '';

				if ( $_product && $_product->exists() && $quote_item['quantity'] > 0 && apply_filters( 'addify_quote_item_visible', true, $quote_item, $quote_item_key ) ) {
					$product_permalink = apply_filters( 'addify_quote_item_permalink', $_product->is_visible() ? $_product->get_permalink( $quote_item ) : '', $quote_item, $quote_item_key );
					?>
					<tr class=" <?php echo esc_attr( apply_filters( 'addify_quote_item_class', 'cart_item', $quote_item, $quote_item_key ) ); ?>">

					<?php if (!isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products'])) : ?>
						<td class="product-remove">
							<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo wp_kses_post(
									apply_filters(
										'addify_quote_item_remove_link',
										sprintf(
											'<a href="%s" class="remove remove-cart-item remove-quote-item" aria-label="%s" data-cart_item_key="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
											esc_attr( $quote_item_key ),
											esc_html__( 'Remove this item', 'addify_b2b' ),
											esc_attr( $quote_item_key ),
											esc_attr( $product_id ),
											esc_attr( $_product->get_sku() )
										),
										$quote_item_key
									)
								);
							?>
						</td>
						<?php else : ?>
							<td class="product-remove">
						</td>
					<?php endif; ?>

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
						if ($component_name) {
							echo esc_html($component_name) . '<br>';
						}
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'addify_quote_item_name', $_product->get_name(), $quote_item, $quote_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'addify_quote_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $quote_item, $quote_item_key ) );
						}

						do_action( 'addify_after_quote_item_name', $quote_item, $quote_item_key );

						// Meta data.
						echo wp_kses_post( wc_get_formatted_cart_item_data( $quote_item ) ); // phpcs:ignore WordPress.Security.EscapeOutput

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $quote_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'addify_quote_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'addify_b2b' ) . '</p>', $product_id ) );
						}
						if (!empty($_product->get_sku())) {
							echo wp_kses_post( sprintf( '<p><small><b>SKU:</b>%s</small></p>', esc_attr( $_product->get_sku() ) ) );
						}
						?>
						</td>

						<?php if ( $price_display ) : ?>
							<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'addify_b2b' ); ?>">
								<?php
									$args['qty']   = 1;
									$args['price'] = empty( $quote_item['addons_price'] ) ? $_product->get_price() : $quote_item['addons_price'];
									$args['price'] = empty( $quote_item['role_base_price'] ) ? $_product->get_price() : $quote_item['role_base_price'];
									$args['price'] = empty( $quote_item['price_calculator_price'] ) ? $args['price'] : $quote_item['price_calculator_price'];
									$args['price'] = isset( $quote_item['composite_product_price'] ) ? $quote_item['composite_product_price'] : $args['price'];
									echo wp_kses_post( apply_filters( 'addify_quote_item_price', $af_quote->get_product_price( $_product, $args ), $quote_item, $quote_item_key ) ); // phpcs:ignore WordPress.Security.EscapeOutput
								?>
							</td>
						<?php endif; ?>
						
						<?php if ( $of_price_display && ( !isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products']) ) ) : ?>
							<td class="product-price offered-price" data-title="<?php esc_attr_e( 'Offered Price', 'addify_b2b' ); ?>">
								<input type="number" class="input-text offered-price-input text" step="any" name="offered_price[<?php echo esc_attr( $quote_item_key ); ?>]" value="<?php echo esc_attr( $offered_price ); ?>">
							</td>
						<?php elseif ($of_price_display) : ?>
							<td></td>
						<?php endif; ?>	

						<?php 
						// current product is composite child product
						$allow_edit_in_quote = true;
						if (isset($quote_item['composite_child_products']) && !empty($quote_item['composite_child_products'])) {
							$allow_edit_in_quote = false;

							$parent_product_id = isset($quote_item['composite_child_products']['parent_product_id']) && !empty($quote_item['composite_child_products']['parent_product_id']) ? $quote_item['composite_child_products']['parent_product_id'] : '';
							$comp_key = isset($quote_item['composite_child_products']['comp_key']) && !empty($quote_item['composite_child_products']['comp_key']) ? $quote_item['composite_child_products']['comp_key'] : '';
							if ($parent_product_id && $comp_key) {
								$allow_edit = get_post_meta($parent_product_id, 'af_cp_allow_edit_in_cart', true) ;

								if ('yes' == $allow_edit) {
									$is_component_range_enabled = get_post_meta($parent_product_id, 'af_composite_product_quantity_op', true);
									
									if ('range' == $is_component_range_enabled[ $comp_key ]) {
										$allow_edit_in_quote = true;
									}
								} else {
									$allow_edit_in_quote = false;
								}
							}

						}
						if ($allow_edit_in_quote) :
							?>
							<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'addify_b2b' ); ?>">
							<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '<input type="hidden" name="quote_qty[%s]" value="1" />', $quote_item_key );
							} else {
								$max_product_quantity = apply_filters('addify_quote_product_quantity_maximum', $_product->get_max_purchase_quantity(), $_product, $quote_item);
								woocommerce_quantity_input(
									array(
										'input_name'   => "quote_qty[{$quote_item_key}]",
										'input_value'  => $quote_item['quantity'],
										'max_value'    => $max_product_quantity,
										'min_value'    => '0',
										'product_name' => $_product->get_name(),
									),
									$_product,
									true
								);
							}
							?>
							</td>
						<?php else : ?>
							<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'addify_b2b' ); ?>">
							<?php
							echo esc_attr( $quote_item['quantity'] );
							?>
							</td>
						<?php endif; ?>

						<?php if ( $price_display && ( !isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products']) ) ) : ?>
							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'addify_b2b' ); ?>">
								<?php
									$args['qty']   = $quote_item['quantity'];
									$args['price'] = empty( $quote_item['addons_price'] ) ? $_product->get_price() : $quote_item['addons_price'];
									$args['price'] = empty( $quote_item['role_base_price'] ) ? $args['price'] : $quote_item['role_base_price'];
									$args['price'] = empty( $quote_item['price_calculator_price'] ) ? $args['price'] : $quote_item['price_calculator_price'];
									$args['price'] = isset( $quote_item['composite_product_price'] ) ? $quote_item['composite_product_price'] : $args['price'];
									echo wp_kses_post( apply_filters( 'addify_quote_item_subtotal', $af_quote->get_product_subtotal( $_product, $quote_item['quantity'], $args ), $quote_item, $quote_item_key ) ); // phpcs:ignore WordPress.Security.EscapeOutput
								?>
							</td>
						<?php endif; ?>	

						<?php if ( $of_price_display && ( !isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products']) ) ) : ?>
							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Offered Subtotal', 'addify_b2b' ); ?>">
								<?php
									echo wp_kses_post( apply_filters( 'addify_quote_item_subtotal', wc_price( $offered_price * $quote_item['quantity'] ), $quote_item, $quote_item_key ) ); // phpcs:ignore WordPress.Security.EscapeOutput
								?>
							</td>
						<?php endif; ?>
						
					</tr>
					<td colspan="<?php echo esc_attr( $colspan ); ?>" class="actions" style="display: none;">

					<?php
					$afrfq_update_button_text     = get_option( 'afrfq_update_button_text' );
					$afrfq_update_button_bg_color = get_option( 'afrfq_update_button_bg_color' );
					$afrfq_update_button_fg_color = get_option( 'afrfq_update_button_fg_color' );
					$afrfq_update_button_text     = empty( $afrfq_update_button_text ) ? __( 'Update Quote', 'addify_b2b' ) : $afrfq_update_button_text;
					?>

					<style type="text/css">
						.afrfq_update_quote_btn{
							color: <?php echo esc_html( $afrfq_update_button_fg_color ); ?> !important;
							background-color: <?php echo esc_html( $afrfq_update_button_bg_color ); ?> !important;
						}
					</style>

					<button type="button" type="submit" id="afrfq_update_quote_btn" class="button afrfq_update_quote_btn" name="update_quote" value="<?php esc_html( $afrfq_update_button_text ); ?>"><?php echo esc_html( $afrfq_update_button_text ); ?></button> 

					<?php do_action( 'addify_quote_actions' ); ?>

					<?php wp_nonce_field( 'addify-cart', 'addify-cart-nonce' ); ?>
				</td>
					<?php
				}
			}
			?>
			
			</tbody>
			<?php do_action( 'addify_quote_contents' ); ?>
			</tbody>
		</table>

<?php
}
?>
	
<?php do_action( 'addify_after_quote_contents' ); ?>

<?php do_action( 'addify_after_quote_table' ); ?>
