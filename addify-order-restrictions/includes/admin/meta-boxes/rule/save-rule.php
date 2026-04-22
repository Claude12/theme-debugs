
<?php

defined( 'ABSPATH' ) || exit;

global $post;

$meta_keys = array(
	'afor_customers',
	'afor_user_roles',
	'afor_min_quantity',
	'afor_max_quantity',
	'afor_min_amount',
	'afor_max_amount',
	'afor_cart_amount',
	'afor_restriction_message',
);

$_nonce = isset( $_POST['afor_nonce_field'] ) ? sanitize_text_field( wp_unslash( $_POST['afor_nonce_field'] ) ) : 0;

if ( empty( $_POST['afor_nonce_field'] ) || ! wp_verify_nonce( $_nonce, 'afor_nonce_action' ) ) {
	die( 'Failed Security Check' );
}

foreach ( $meta_keys as $meta_key ) {

	if ( isset( $_POST[ $meta_key ] ) ) {

		update_post_meta( $post_id, $meta_key, sanitize_meta( $meta_key, $_POST[ $meta_key ], 'af_order_rule' ) );

	} else {

		delete_post_meta( $post_id, $meta_key );
	}
}
