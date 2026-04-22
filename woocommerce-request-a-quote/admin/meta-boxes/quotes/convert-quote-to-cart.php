<?php
/**
 * Convert quote to cart.
 *
 * This quote class handles quote to cart conversion.
 *
 * @package addify-request-a-quote
 * @version 2.8.0
 */

defined( 'ABSPATH' ) || exit;

global $post;
// Generate a secure token for this quote
$quote_id = (int) $post->ID; // or use $quote_id as available
$token = wp_hash( $quote_id . '|' . time() . '|' . wp_generate_password( 12, false ) );

// Build the link
$convert_to_cart_url = add_query_arg(
	array(
		'afrfq_action' => 'convert_to_cart',
		'quote_id'     => $quote_id,
		'token'        => $token,
	),
	home_url( '/' )
);

// Save the link, token, and creation date to post meta for later validation
update_post_meta( $quote_id, 'afrfq_convert_to_cart_url', esc_url_raw( $convert_to_cart_url ) );
update_post_meta( $quote_id, 'afrfq_convert_to_cart_token', $token );
update_post_meta( $quote_id, 'afrfq_cart_link_creation_date', current_time('mysql') );

// Reset the 'used' flag when generating a new link
delete_post_meta( $quote_id, 'afrfq_cart_link_used' );

update_post_meta( $post->ID, 'quote_status', 'af_converted_to_cart' );

do_action( 'addify_rfq_send_quote_email_to_admin', $post->ID );

do_action( 'addify_rfq_send_quote_email_to_customer', $post->ID );
