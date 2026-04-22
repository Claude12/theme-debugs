<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Afb2b_Role_Front_Ajax_Controller {

	public function __construct() {
		add_action('wp_ajax_afb2b_role_get_variation_price', array( $this, 'afb2b_role_get_variation_price' ));
			
		add_action('wp_ajax_nopriv_afb2b_role_get_variation_price', array( $this, 'afb2b_role_get_variation_price' ));
	}


	public function afb2b_role_get_variation_price() {

		$nonce = isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( $nonce, 'afb2b-role-ajax-nonce' ) ) {
			wp_die( esc_html__( 'Failed security check!', 'addify_b2b' ) );
		}

		$variation_id = isset( $_POST['variation_id'] ) ? sanitize_text_field( wp_unslash( $_POST['variation_id'] ) ) : '';

		$variable_product = wc_get_product($variation_id);

		$price = $variable_product->get_price();        

		$response = array(
			'price'           => $price,
			'formatted_price' => html_entity_decode(strip_tags(wc_price($price))),
			'message'         => 'Price Fetched Successfully!',
		);

		wp_send_json_success( $response );          
		die();
	}
}

new Afb2b_Role_Front_Ajax_Controller();
