<?php
/**
 * Discount Cart.
 *
 * @package Discount Cart By Total Value.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Discount Ajax Class.
 */
class Af_Discount_Cart_Ajax {

	/**
	 * Contructor.
	 */
	public function __construct() {

		add_action( 'wp_ajax_af_dcv_discount_get_products', array( $this, 'af_dcv_discount_get_products' ) );

		add_action( 'wp_ajax_af_dcv_discount_get_categories', array( $this, 'af_dcv_discount_get_categories' ) );
	}
	/**
	 * Get products.
	 */
	public function af_dcv_discount_get_products() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

		if ( ! wp_verify_nonce( $nonce, 'af_dc_nonce' ) ) {
			die( 'Failed ajax security check!' );
		}

		$pro = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';

		$data_array = array();
		$args       = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			's'              => $pro,
			'orderby'        => 'title',
			'order'          => 'ASC',

		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$title        = ( mb_strlen( get_the_title() ) > 50 ) ? mb_substr( get_the_title(), 0, 49 ) . '...' : get_the_title();
				$data_array[] = array( get_the_ID(), $title );
			}
			wp_reset_postdata();
		}

		echo wp_json_encode( $data_array );
		wp_die();
	}
	/**
	 * Get categories.
	 */
	public function af_dcv_discount_get_categories() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

		if ( ! wp_verify_nonce( $nonce, 'af_dc_nonce' ) ) {
			die( 'Failed ajax security check!' );
		}

		$pro = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';

		$product_categories = get_terms( array(
			'taxonomy'   => 'product_cat',
			'name__like' => $pro,
		) );

		if ( ! empty( $product_categories ) ) {
			foreach ( $product_categories as $proo ) {
				$pro_front_post = ( mb_strlen( $proo->name ) > 50 ) ? mb_substr( $proo->name, 0, 49 ) . '...' : $proo->name;
				$data_array[]   = array( $proo->term_id, $pro_front_post ); // array( Post ID, Post Title ).
			}
		}
		echo wp_json_encode( $data_array );
		die();
	}
}

new Af_Discount_Cart_Ajax();
