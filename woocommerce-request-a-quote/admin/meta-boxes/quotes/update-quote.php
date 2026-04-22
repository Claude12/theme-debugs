<?php
/**
 * Customer information table for email.
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

global $post;
$quote_id = $post->ID;

// Update customer info.
$af_fields_obj = new AF_R_F_Q_Quote_Fields();
$quote_fields  = $af_fields_obj->afrfq_get_fields_enabled();


// Update Quote.
$quote_contents = get_post_meta( $post->ID, 'quote_contents', true );

$quotes = $quote_contents;
$contain_composite_product = false;

foreach ( $quote_contents as $quote_item_key => $quote_item ) {

	if ( isset( $quote_item['data'] ) && is_object( $quote_item['data'] ) && get_class( $quote_item['data'] ) === '__PHP_Incomplete_Class' ) {
		$contain_composite_product = true;
		break;
	}

	if ( isset( $form_data['quote_qty'][ $quote_item_key ] ) ) {
		$quotes[ $quote_item_key ]['quantity'] = $form_data['quote_qty'][ $quote_item_key ]> 0 ? intval( $form_data['quote_qty'][ $quote_item_key ] ) :$quotes[ $quote_item_key ]['quantity'] ;
	}

	if ( isset( $form_data['offered_price'][ $quote_item_key ] ) ) {
		$quotes[ $quote_item_key ]['offered_price'] = $form_data['offered_price'][ $quote_item_key ]>=0 ? floatval( $form_data['offered_price'][ $quote_item_key ] ) :$quotes[ $quote_item_key ]['offered_price'] ;
	}
}

if ( $contain_composite_product ) {
	set_transient( 'afrfq_quote_save_error_' . get_current_user_id(), __( 'Update failed: Some product data could not be loaded. Please verify that all required components are available.', 'addify_b2b' ), 30 );  
	return;
}

// update field data
foreach ( $quote_fields as $key => $field ) {

	$field_id          = $field->ID;
	$afrfq_field_name  = get_post_meta( $field_id, 'afrfq_field_name', true );
	$afrfq_field_type  = get_post_meta( $field_id, 'afrfq_field_type', true );
	$afrfq_field_label = get_post_meta( $field_id, 'afrfq_field_label', true );

	if ( isset( $form_data[ $afrfq_field_name ] ) && ! empty( $form_data[ $afrfq_field_name ] ) ) {

		if ( 'file' === $afrfq_field_type ) {

			continue;

		} else {

			update_post_meta( $quote_id, $afrfq_field_name, $form_data[ $afrfq_field_name ] );
		}
	}
}

if ( isset( $form_data['_customer_user'] ) ) {
	update_post_meta( $quote_id, '_customer_user', $form_data['_customer_user'] );
}


update_post_meta( $post->ID, 'quote_contents', $quotes );

if ( isset( $form_data['afrfq_shipping_cost'] ) ) {
	update_post_meta( $post->ID, 'afrfq_shipping_cost', sanitize_text_field( wp_unslash( $form_data['afrfq_shipping_cost'] ) ) );
}

do_action( 'addify_quote_contents_updated', $post->ID );

if ( isset( $form_data['quote_status'] ) ) {

	$old_status = get_post_meta( $post->ID, 'quote_status', true );

	update_post_meta( $post->ID, 'old_status', $form_data['quote_status'] );
	update_post_meta( $post->ID, 'quote_status', $form_data['quote_status'] );
	update_post_meta( $post->ID, 'afrfq_notify_customer', $form_data['afrfq_notify_customer'] );
	do_action( 'addify_rfq_quote_status_updated', $post->ID, $form_data['quote_status'], $old_status );
	$afrfq_user_email_pdf = get_option( 'afrfq_user_email_pdf' );

}

if ( ( 'yes' == $form_data['afrfq_notify_customer'] ) ) {

	do_action( 'addify_rfq_send_quote_email_to_customer', $post->ID );
	do_action( 'addify_rfq_send_quote_email_to_admin', $post->ID );
}


if ( isset( $form_data['afrfq_cart_link_restriction_type'] ) ) {
	update_post_meta( $post->ID, 'afrfq_cart_link_restriction_type', $form_data['afrfq_cart_link_restriction_type'] );
} else {
	update_post_meta( $post->ID, 'afrfq_cart_link_restriction_type', '' );
}

if ( isset( $form_data['afrfq_cart_link_expiry_time'] ) ) {
	update_post_meta( $post->ID, 'afrfq_cart_link_expiry_time', $form_data['afrfq_cart_link_expiry_time'] );
}
