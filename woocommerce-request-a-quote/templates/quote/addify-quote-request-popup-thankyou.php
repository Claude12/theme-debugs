<?php
/**
 * Quote Request Popup Thankyou Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/quote/addify-quote-request-popup-thankyou.php
 *
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;
if ( ! empty( get_option( 'afrfq_success_message' ) ) ) {
	$afrfqmsg = get_option( 'afrfq_success_message' );
} else {
	$afrfqmsg = 'Your quote has been submitted successfully.';
}
?>

<div class="afrfq-popup-form-thankyou">
	<div>
		<div class="afrfq-icon-circle">
			<img src="<?php echo esc_url( AFRFQ_URL . 'assets/images/check.png' ); ?>" alt="<?php esc_attr_e( 'Icon', 'addify_b2b' ); ?>">
		</div>
	</div>
	<div>
		<div class="afrfq-popup-form-thank-you-heading">
			<?php esc_html_e('Thank you!', 'addify_b2b'); ?>
		</div>
		<div class="afrfq-popup-form-thankyou-sub-heading">
		<?php 
			echo esc_html( $afrfqmsg ); 
		?>
		</div>
	</div>
	<div>
		<button class="afrfq-continue-button"><?php esc_html_e('Continue Shopping', 'addify_b2b'); ?></button>
	</div>
</div>