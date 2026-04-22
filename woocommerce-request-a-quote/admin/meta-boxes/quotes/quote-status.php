<?php
/**
 * Addify Add to Quote.
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

global $post;
$quote_status   = get_post_meta( $post->ID, 'quote_status', true );
$hashed_quote_id= base64_encode( $post->ID . '|' . hash_hmac( 'sha256', $post->ID, wp_salt( 'auth' ) ) );
$download_url = wp_nonce_url(admin_url('admin-ajax.php?action=download_quote_pdf&quote_id=' . $hashed_quote_id), 'afquote-ajax-nonce');


$quote_statuses = array(
	'af_pending'                => __( 'Pending', 'addify_b2b' ),
	'af_in_process'             => __( 'In Process', 'addify_b2b' ),
	'af_accepted'               => __( 'Accepted', 'addify_b2b' ),
	'af_converted_to_cart'      => __( 'Converted to Cart', 'addify_b2b' ),
	'af_converted'              => __( 'Converted to Order', 'addify_b2b' ),
	'af_declined'               => __( 'Declined', 'addify_b2b' ),
	'af_cancelled'              => __( 'Cancelled', 'addify_b2b' ),
);

?>

<p class="post-attributes-label-wrapper quote-status-label-wrapper">
	<label class="post-attributes-label" for="quote_status">
		<?php echo esc_html__( 'Current Status', 'addify_b2b' ); ?>
	</label>
</p>
<select name="quote_status" id="quote_status">

	<?php foreach ( $quote_statuses as $value => $label ) : ?>
		<option value="<?php echo esc_html( $value ); ?>" <?php echo selected( $value, $quote_status ); ?> >
			<?php echo esc_html( $label ); ?>
		</option>
	<?php endforeach; ?>

</select> 

<p class="post-attributes-label-wrapper afrfq-label-wrapper">
	<label class="post-attributes-label" for="quote_status">
		<?php esc_html_e( 'Notify Customer', 'addify_b2b' ); ?>
	</label>
</p>
	<select name="afrfq_notify_customer" id="afrfq_notify_customer" >
		<option value="no"><?php esc_html_e( 'No', 'addify_b2b' ); ?></option>
		<option value="yes"><?php esc_html_e( 'Yes', 'addify_b2b' ); ?></option>
	</select>
<p class="desciption">
	<?php esc_html_e( 'Select "Yes" to notify customer via email.', 'addify_b2b' ); ?>
</p>

<a href= <?php echo esc_url($download_url); ?> class="woocommerce-button button fas fa-file-pdf"><?php esc_html_e('Download PDF', 'addify_b2b'); ?></a>

