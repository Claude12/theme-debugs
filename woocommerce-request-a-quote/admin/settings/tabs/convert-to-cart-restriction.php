<?php
/**
 * Convert to cart restriction tab fields.
 *
 * @package  woocommerce-request-a-quote
 * @version  2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_settings_section(
	'afrfq-convert-to-cart-restrict-sec',        
	esc_html__( 'Convert to Cart Restriction', 'addify_b2b' ),   
	'', 
	'afrfq_convert_to_cart_restrict_section'                           
);

// Add payment methods field
add_settings_field(
	'afrfq_disabled_payment_methods',
	esc_html__( 'Hide Payment Methods', 'addify_b2b' ),
	'afrfq_disabled_payment_methods_callback',
	'afrfq_convert_to_cart_restrict_section',
	'afrfq-convert-to-cart-restrict-sec',
	array( esc_html__( 'Select payment methods to hide when converting quote to cart.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_convert_to_cart_restrict_fields',
	'afrfq_disabled_payment_methods'
);

if ( ! function_exists( 'afrfq_disabled_payment_methods_callback' ) ) {
	/**
	 *  AF_R_F_Q_Admin.
	 *
	 * @param array $args returns array.
	 */
	function afrfq_disabled_payment_methods_callback( $args = array() ) {
		// Get available payment gateways
		$available_gateways = WC()->payment_gateways->payment_gateways();
		$selected_methods = (array) get_option('afrfq_disabled_payment_methods', array());
		?>
		<select name="afrfq_disabled_payment_methods[]" id="afrfq_disabled_payment_methods" multiple="multiple" style="min-width: 300px;">
			<?php foreach ( $available_gateways as $gateway ) : ?>
				<option value="<?php echo esc_attr( $gateway->id ); ?>" <?php echo in_array( $gateway->id, $selected_methods ) ? 'selected="selected"' : ''; ?>>
					<?php echo esc_html( $gateway->get_title() ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<p class="description afreg_additional_fields_section_title"><?php echo wp_kses_post( $args[0] ); ?></p>
		<?php
	}
}
