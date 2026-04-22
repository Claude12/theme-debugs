<?php
/**
 * Customer information table for email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/quote/quote-totals-table.php.
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



$quote_totals = $af_quote->get_calculated_totals( wc()->session->get( 'quotes' ) );

$quote_subtotal = isset( $quote_totals['_subtotal'] ) ? $quote_totals['_subtotal'] : 0;
$vat_total      = isset( $quote_totals['_tax_total'] ) ? $quote_totals['_tax_total'] : 0;
$offered_vat_total      = isset( $quote_totals['_offered_tax_total'] ) ? $quote_totals['_offered_tax_total'] : 0;
$quote_total    = isset( $quote_totals['_total'] ) ? $quote_totals['_total'] : 0;
$offered_total_after_tax    = isset( $quote_totals['_offered_total_after_tax'] ) ? $quote_totals['_offered_total_after_tax'] : 0;
$offered_total  = isset( $quote_totals['_offered_total'] ) ? $quote_totals['_offered_total'] : 0;

if ( $price_display || $of_price_display ) : ?>

	<table cellspacing="0" class="shop_table shop_table_responsive table_quote_totals">
		
		<?php if ( $price_display ) : ?>
			<tr class="cart-subtotal">
				<th><?php esc_html_e( 'Subtotal (Standard)', 'addify_b2b' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Subtotal (Standard)', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $quote_subtotal ) ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $of_price_display ) : ?>
			<tr class="cart-subtotal offered">
				<th><?php esc_html_e( 'Offered Price Subtotal', 'addify_b2b' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Offered Price Subtotal', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $offered_total ) ); ?></td>
			</tr>
		<?php endif; ?>

		<?php
		if ( wc_tax_enabled() && $tax_display ) :
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = '';

			if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
				/* translators: %s location. */
				$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
			}

			?>
			<?php if ( $price_display ) : ?>
			<tr class="tax-rate">
				<th><?php echo esc_html__( 'Vat (Standard)', 'addify_b2b' ) . wp_kses_post( $estimated_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
				<td data-title="<?php echo esc_html__( 'Vat (Standard)', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $vat_total ) ); ?></td>
			</tr>
			<?php endif; ?>
			<?php if ( $of_price_display ) : ?>
			<tr class="tax-rate">
				<th><?php echo esc_html__( 'Offered Vat', 'addify_b2b' ) . wp_kses_post( $estimated_text ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
				<td data-title="<?php echo esc_html__( 'Offered Vat', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $offered_vat_total ) ); ?></td>
			</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( $price_display ) : ?>
			<tr class="order-total">
				<th><?php esc_html_e( 'Total (Standard)', 'addify_b2b' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Total', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $quote_total ) ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $of_price_display ) : ?>
			<tr class="order-total offered">
				<th><?php esc_html_e( 'Offered Total', 'addify_b2b' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Offered Total', 'addify_b2b' ); ?>"><?php echo wp_kses_post( wc_price( $offered_total_after_tax ) ); ?></td>
			</tr>
		<?php endif; ?>

	</table>

<?php endif; ?>


