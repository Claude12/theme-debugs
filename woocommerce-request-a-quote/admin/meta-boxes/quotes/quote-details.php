<?php
/**
 * Quote details in Meta box.
 *
 * It shows the details of quotes items in meta box.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;


global $post;

$quote_contents = get_post_meta( $post->ID, 'quote_contents', true );
$quote_status   = get_post_meta( $post->ID, 'quote_status', true );
$user_id        = get_post_meta( $post->ID, '_customer_user', true );

$order_id_for_this_quote = get_post_meta( $post->ID, 'order_for_this_quote', true );
$af_quote                = new AF_R_F_Q_Quote( $quote_contents );

$quote_totals = $af_quote->get_calculated_totals( (array) $quote_contents, $post->ID );

$quote_id = $post->ID;

?>
<div class="woocommerce_order_items_wrapper wc-order-items-editable addify_quote_items_wrapper">
	<?php
	do_action( 'addify_rfq_order_details_before_order_table', $post );

	require AFRFQ_PLUGIN_DIR . 'admin/meta-boxes/quotes/quote-details-table.php';

	do_action( 'addify_rfq_order_details_after_order_table', $post );
	?>
	<!-- The Modal -->
	<div id="af-backbone-add-product-modal" class="af-backbone-modal">
		<!-- Modal content -->
			<div class="af-backbone-modal-content">
			<section class="af-backbone-modal-main" role="main">
				<header class="af-backbone-modal-header">
					<h1><?php echo esc_html__( 'Add product', 'addify_b2b' ); ?></h1>
						<span class="af-backbone-close">&times;</span>
				</header>
				<article style="max-height: 316.5px;">
					<form action="" method="post">
						<table class="widefat">
							<thead>
								<tr>
									<th><?php echo esc_html__( 'Product', 'addify_b2b' ); ?></th>
									<th><?php echo esc_html__( 'Quantity', 'addify_b2b' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<td>
									<select class="af-single_select-product">	
									</select>
								</td>
								<td>
									<input type="number" min='1' name="afacr_product_quantity" value="1">
								</td>
							</tbody>
						</table>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" disabled value="<?php echo intval( $post->ID ); ?>" class="button button-primary button-large">
							<?php echo esc_html__( 'Add to Quote', 'addify_b2b' ); ?>
						</button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="addify_converty_to_order_button">
		<div class="left-buttons">
			<button type="button" id="addify_add_item" name="addify_add_item" class="button add-product" >
				<?php echo esc_html__( 'Add product(s)', 'addify_b2b' ); ?>
			</button>
		</div>
		<?php if ( 'af_converted' !== $quote_status ) : ?>
			<div class="right-buttons">
				<button type="submit" name="addify_convert_to_order" class="button button-primary button-large" >
					<?php echo esc_html__( 'Convert to Order', 'addify_b2b' ); ?>
				</button>

			</div>
		<?php else : ?>
			<div class="right-buttons" style="text-align: right;">
				
					<?php if ( ! empty( $order_id_for_this_quote ) ) : ?>

						<b><?php echo esc_html__( 'This quote has been converted to ', 'addify_b2b' ) . '<a href="admin.php?page=wc-orders&action=edit&id=' . intval( $order_id_for_this_quote ) . '"> order # ' . intval( $order_id_for_this_quote ); ?></a></b>

					<?php endif; ?>
				
			</div>
		<?php endif; ?>

		<?php if ( 'af_converted_to_cart' !== $quote_status && 'af_converted' !== $quote_status ) : ?>
			<div class="right-buttons">
				<button type="submit" name="addify_convert_to_cart" class="button button-primary button-large" >
					<?php echo esc_html__( 'Convert to Cart', 'addify_b2b' ); ?>
				</button>
			</div>
		<?php endif; ?>
	</div>

	<?php
	$convert_to_cart_url = get_post_meta( $post->ID, 'afrfq_convert_to_cart_url', true );
	if ( 'af_converted_to_cart' === $quote_status && !empty($convert_to_cart_url)) :
		?>
		<div class="afacr-metabox-fields">
			
			<table class="addify-table-optoin">

					<tr class="addify-option-field">
						<th>
							<div class="option-head">
								<h3>
									<?php echo esc_html__( 'Enable Link Expiration', 'addify_b2b' ); ?>
								</h3>
							</div>
						</th>
						<td>
							<input type="checkbox" name="afrfq_cart_link_restriction_type" id="afrfq_cart_link_restriction_type" value="expires_on_date" <?php checked( get_post_meta($post->ID, 'afrfq_cart_link_restriction_type', true), 'expires_on_date' ); ?> />
							<p class="description afreg_additional_fields_section_title"><?php echo esc_html__( 'Enable expiry for cart conversion links. When enabled, links will expire after the specified number of days.', 'addify_b2b' ); ?></p>
						</td>
					</tr>

					<tr class="addify-option-field">
						<th>
							<div class="option-head">
								<h3>
									<?php echo esc_html__( 'Cart Link Expiry Time', 'addify_b2b' ); ?>
								</h3>
							</div>
						</th>
						<td>
							<input type="number" min="1" name="afrfq_cart_link_expiry_time" id="afrfq_cart_link_expiry_time" value="<?php echo esc_attr( get_post_meta($post->ID, 'afrfq_cart_link_expiry_time', true) ); ?>" /><?php echo esc_html__('days', 'addify_b2b'); ?>
							<p class="description afreg_additional_fields_section_title"><?php echo esc_html__( 'Enter the number of days after which the cart link will expire. If left blank, defaults to 7 days.', 'addify_b2b' ); ?></p>
						</td>
					</tr>

				</table>
			</div>


		<div class="afrfq-cart-link-section">
			<label>
				<?php echo esc_html__( 'Cart link for reference:', 'addify_b2b' ); ?>
			</label>
			<div class="afrfq-cart-link-inner-container">
				<input type="text" id="afrfq_convert_to_cart_url" value="<?php echo esc_url( $convert_to_cart_url ); ?>" readonly />
				<button type="submit" name="afrfq_regenerate_cart_url" class="afrfq-regenerate-cart-url button button-secondary"><?php echo esc_html__( 'Regenerate', 'addify_b2b' ); ?></button>
				<button type="button" class="afrfq-copy-cart-url button button-secondary"><?php echo esc_html__( 'Copy', 'addify_b2b' ); ?></button>
			</div>
			<span id="afrfq_copy_success" style="display:none; color: #46b450; font-size: 13px; margin-top: 6px;"><?php echo esc_html__( 'Copied!', 'addify_b2b' ); ?></span>
		</div>
	<?php endif; ?>

</div>
