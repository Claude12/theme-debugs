<?php
/**
 * Quote Request Popup Review Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/quote/addify-quote-request-popup-review-page.php.
 *
 * @package WooCommerce/Templates
 * @version 3.8.0
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

?>
<style>
	
	.afrfq-popup-review-info-page {
		/* padding-top:20px; */
	 
	}

	.afrfq-popup-form-card {
		background: #fff;
		border-radius: 6px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		margin-bottom: 30px;
	}

	.afrfq-popup-form-card-header {
		padding: 15px 20px;
		border-bottom: 1px solid #eee;
	}

	.afrfq-popup-form-heading {
		font-size: 18px;
		font-weight:600;
	}

	.afrfq-popup-form-sub-heading {
		margin: 5px 0 0;
		font-size: 14px;
		color: #666;
	}

	.afrfq-popup-form-item-table {
		width: 100%;
		border-collapse: collapse;
	}

	.afrfq-popup-form-item-table th {
		padding: 12px 15px;
		font-size: 15px;
		font-weight: 600;
	}

	.afrfq-popup-form-item-table td {
		padding: 12px 15px;
		border-bottom: 1px solid #eee;
		vertical-align: middle;
		font-size:15px !important;
	}

	.afrfq-popup-form-item-thumb img {
		width: 50px;
		height: 50px;
		object-fit: cover;
		border-radius: 4px;
	}

	/* Form Elements */
	.afrfq-popup-form-card-body {
		padding: 20px;
	}

	.afrfq-popup-form-elements {
		display: flex;
		flex-wrap: wrap;
		gap: 15px;
	}

	.afrfq-popup-form-group {
		flex: 1 0 calc(50% - 15px);
		min-width: 200px;
	}

	.afrfq-popup-form-label {
		margin-bottom: 7px;
		font-size: 15px;
		line-height: 25px;
		font-weight: 700;
	}

	.afrfq-popup-form-value {
		padding: 8px 0;
	}

	/* Responsive Adjustments */
	@media (max-width: 768px) {
		.afrfq-popup-form-group {
			flex: 1 0 100%;
		}
	}
</style>

<div class="afrfq-popup-review-info-page">
	<!-- Products Section -->
	<div class="afrfq-popup-form-card">

		<div class="afrfq-popup-form-card">
			<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents  addify-quote-form__contents afrfq-popup-form-item-table" cellspacing="0">
				<thead>
					<tr>
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
					<?php
					foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

						if ( ! isset( $quote_item['data'] ) || ! is_object( $quote_item['data'] ) ) {
							continue;
						}
		
						$_product      = apply_filters( 'addify_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key );
						$product_id    = apply_filters( 'addify_quote_item_product_id', $quote_item['product_id'], $quote_item, $quote_item_key );
						$price         = empty( $quote_item['role_base_price'] ) ? $_product->get_price() : $quote_item['role_base_price'];
						$price         = empty( $quote_item['price_calculator_price'] ) ? $price : $quote_item['price_calculator_price'];
						$price         = isset( $quote_item['composite_product_price'] ) ? $quote_item['composite_product_price'] : $price;
						$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;

						$component_name = isset($quote_item['component_name']) ? $quote_item['component_name'] : '';
		
						if ( $_product && $_product->exists() && $quote_item['quantity'] > 0 && apply_filters( 'addify_quote_item_visible', true, $quote_item, $quote_item_key ) ) {
							$product_permalink = apply_filters( 'addify_quote_item_permalink', $_product->is_visible() ? $_product->get_permalink( $quote_item ) : '', $quote_item, $quote_item_key );
							?>
							<tr class="<?php echo esc_attr( apply_filters( 'addify_quote_item_class', 'cart_item', $quote_item, $quote_item_key ) ); ?>">

								<td class="product-thumbnail">
									<div class="afrfq-popup-form-item-thumb">
										<?php
										$thumbnail = apply_filters( 'addify_quote_item_thumbnail', $_product->get_image(), $quote_item, $quote_item_key );
				
										if ( ! $product_permalink ) {
											echo wp_kses_post( $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput
										} else {
											printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) ); // phpcs:ignore WordPress.Security.EscapeOutput
										}
										?>
									</div>
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
											$args['price'] = empty( $quote_item['role_base_price'] ) ? $_product->get_price() : $quote_item['role_base_price'];
											$args['price'] = empty( $quote_item['price_calculator_price'] ) ? $args['price'] : $quote_item['price_calculator_price'];
											$args['price'] = isset( $quote_item['composite_product_price'] ) ? $quote_item['composite_product_price'] : $args['price'];

											echo wp_kses_post( apply_filters( 'addify_quote_item_price', $af_quote->get_product_price( $_product, $args ), $quote_item, $quote_item_key ) ); // phpcs:ignore WordPress.Security.EscapeOutput
										?>
									</td>
								<?php endif; ?>
								
								<?php if ( $of_price_display && ( !isset($quote_item['composite_child_products']) || empty($quote_item['composite_child_products']) ) ) : ?>
									<td class="product-price offered-price" data-title="<?php esc_attr_e( 'Offered Price', 'addify_b2b' ); ?>">
									   <?php echo wp_kses_post( wc_price($offered_price) ); ?>
									</td>
								<?php elseif ($of_price_display) : ?>
									<td></td>
								<?php endif; ?>	
		
								<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'addify_b2b' ); ?>">
								<?php
									echo esc_attr( $quote_item['quantity'] );
								?>
								</td>
		
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
				  
				</tbody>
			</table>

			<?php do_action( 'addify_before_quote_collaterals' ); ?>
				<?php if ( $price_display || $of_price_display ) : ?>
					<div class="cart-collaterals">
						<?php
							/**
							 * Quote collateral's hook.
							 */
							do_action( 'addify_quote_collaterals' );
						?>
						<div class="cart_totals">

							<?php do_action( 'addify_rfq_before_quote_totals' ); ?>

							<h2><?php esc_html_e( 'Quote totals', 'addify_b2b' ); ?></h2>

							<?php
							if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-totals-table.php' ) ) {

								include get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-totals-table.php';

							} else {

								wc_get_template(
									'quote/quote-totals-table.php',
									array(),
									'/woocommerce/addify/rfq/',
									AFRFQ_PLUGIN_DIR . 'templates/'
								);
							}
							?>
						</div>
					</div>
				<?php endif; ?>
		</div>
	</div>


	<?php

	$user_id = get_current_user_id();

	$quote_fields_obj = new AF_R_F_Q_Quote_Fields();
	$quote_fields     = (array) $quote_fields_obj->quote_fields;
	$cache_data       = wc()->session->get( 'quote_fields_data' );
	?>

	<div class="afrfq-popup-form-card">
		<div class="afrfq-popup-form-card-header">
			<h3 class="afrfq-popup-form-heading"><?php esc_html_e( 'Contact information', 'addify_b2b' ); ?></h3>
		</div>
		
		<div class="afrfq-popup-form-card-body">
			<div class="afrfq-popup-form-elements">
				<?php
				foreach ( $quote_fields as $key => $field ) :

					$field_id = $field->ID;

					$afrfq_field_name        = get_post_meta( $field_id, 'afrfq_field_name', true );
					$afrfq_field_type        = get_post_meta( $field_id, 'afrfq_field_type', true );
					$afrfq_field_label       = get_post_meta( $field_id, 'afrfq_field_label', true );
					$afrfq_field_value       = get_post_meta( $field_id, 'afrfq_field_value', true );
					$afrfq_field_title       = get_post_meta( $field_id, 'afrfq_field_title', true );
					$afrfq_field_terms       = get_post_meta( $field_id, 'afrfq_field_terms', true );
					$afrfq_field_options     = (array) get_post_meta( $field_id, 'afrfq_field_options', true );

					if ( isset( $cache_data[ $afrfq_field_name ] ) ) {
						$field_data = $cache_data[ $afrfq_field_name ];
					} else {
						$field_data = $quote_fields_obj->get_field_default_value( $field_id, $user_id );
					}

					if (empty($field_data) && 'file' != $afrfq_field_type) {
						continue;
					}

					if ('file' == $afrfq_field_type && '' == wc()->session->get('quote_fields_file_name')) {
						continue;
					}

					

					if ( empty( $afrfq_field_name ) ) {
						continue;
					}

					?>
						<div class="afrfq-popup-form-group">
							<div class="afrfq-popup-form-label">
							   <?php echo esc_attr($afrfq_field_label); ?>
							</div>
							<?php
							switch ( $afrfq_field_type ) {
								case 'terms_cond':
									?>
									<p class="addify-option-field">
									  <span><?php echo wp_kses_post( $afrfq_field_terms ); ?></span>
									</p>
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
										<div class="afrfq-popup-form-value">
											<?php echo esc_attr($field_data); ?>
										</div>
									<?php
									break;
								case 'file':
									?>
									<div class="afrfq-popup-form-value">
											<?php
											$file_name = wc()->session? wc()->session->get('quote_fields_file_name'):'';
											$file_url  = AFRFQ_TEMP_UPLOAD_URL . $file_name ;

											if ( file_exists( AFRFQ_TEMP_UPLOAD_DIR . $file_name ) ) {
												?>
												<a href="<?php echo esc_url( $file_url ); ?>" target="_blank" download>
													<?php echo esc_html__('File', 'addify_b2b'); ?>
												</a>
												<?php
											} else {
												echo 'File not found.';
											}
											?>
										</div>
									<?php
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
										<div class="afrfq-popup-form-value">
												<?php echo esc_attr(implode(',', $selected_options)); ?>
										</div>

												
									<?php
									break;

							}
							?>
						</div>

				<?php endforeach; ?>
			</div>
		</div>

	</div>
</div>
	
	
	




