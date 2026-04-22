<?php

defined( 'ABSPATH' ) || exit;

class AF_O_R_Front {

	public $restriction_rules;

	public function __construct() {

		$this->restriction_rules = $this->get_rules();

		add_action('woocommerce_checkout_process', array( $this, 'validate_cart' ) );

		if ( 'yes' == get_option('afor_show_on_cart_page') ) {
			add_action('woocommerce_before_cart_table', array( $this, 'validate_cart' ) );
		}
		
		if ( 'yes' == get_option('afor_show_on_checkout_page') ) {
			add_action('woocommerce_before_checkout_form', array( $this, 'validate_cart' ) );
		}
	}

	public function get_rules() {

		$args = array(
			'post_type'   => 'af_order_rule',
			'post_status' => 'publish',
			'fields'      => 'ids',
			'numberposts' => -1,
		);

		return get_posts( $args );
	}

	public function get_placeholders( $rule_id ) {

		$afor_min_amount   = (float) get_post_meta( $rule_id, 'afor_min_amount', true );
		$afor_max_amount   = (float) get_post_meta( $rule_id, 'afor_max_amount', true );
		$afor_min_quantity = (int) get_post_meta( $rule_id, 'afor_min_quantity', true );
		$afor_max_quantity = (int) get_post_meta( $rule_id, 'afor_max_quantity', true );

		$price_type    = get_post_meta( $rule_id, 'afor_cart_amount', true );
		$cart_amount   = 'total' == $price_type ? wc()->cart->get_total('edit') : wc()->cart->get_subtotal() ;
		$cart_quantity = wc()->cart->get_cart_contents_count();

		$placeholders = array(
			'{remaining_qunatity}' => $afor_min_quantity - $cart_quantity > 0 ? $afor_min_quantity - $cart_quantity : '',
			'{remaining_amount}'   => $afor_min_amount - $cart_amount > 0 ? wc_price( $afor_min_amount - $cart_amount ) : '',
			'{exceeded_quntiity}'  => $cart_quantity - $afor_max_quantity  > 0 ? $cart_quantity - $afor_max_quantity : '',
			'{exceeded_amount}'    => $cart_amount - $afor_max_amount > 0 ? wc_price( $cart_amount - $afor_max_amount ) : '',
		);

		return $placeholders;
	}

	public function validate_cart() {

		if ( wc()->cart->is_empty() ) {
			return;
		}

		foreach ( $this->restriction_rules as $rule_id ) {

			if ( $this->is_rule_valid_for_customer( $rule_id ) ) {

				if ( !$this->is_cart_valid_for_rule( $rule_id ) ) {

					$message      = wpautop( wptexturize( get_post_meta( $rule_id, 'afor_restriction_message', true ) ) );
					$placeholders = $this->get_placeholders( $rule_id );
					$message      = str_replace( array_keys( $placeholders ), array_values( $placeholders ), $message );

					wc_add_notice( $message, 'error');
				}
			}
		}

		if ( is_cart() ) {
			wc_print_notices();
		}
	}

	public function is_rule_valid_for_customer( $rule_id ) {

		$afor_customers  = (array) get_post_meta( $rule_id, 'afor_customers', true );
		$afor_user_roles = (array) get_post_meta( $rule_id, 'afor_user_roles', true );

		if ( is_user_logged_in() ) {

			$customer_id = get_current_user_id();

			if ( in_array( $customer_id, $afor_customers ) ) {
				return true;
			}
		}

		$user_role = is_user_logged_in() ? current( wp_get_current_user()->roles ) : 'guest';

		if ( in_array( $user_role, $afor_user_roles ) ) {
			return true;
		}

		return false;
	}

	public function is_cart_valid_for_rule( $rule_id ) {

		$price_type = get_post_meta( $rule_id, 'afor_cart_amount', true );

		$afor_min_amount = get_post_meta( $rule_id, 'afor_min_amount', true );
		$afor_max_amount = get_post_meta( $rule_id, 'afor_max_amount', true );

		$cart_amount = 'total' == $price_type ? wc()->cart->get_total('edit') : wc()->cart->get_subtotal() ;

		if ( ! $this->match_cart_amount( $cart_amount, $afor_min_amount, $afor_max_amount ) ) {
			return false;
		}

		$afor_min_quantity = get_post_meta( $rule_id, 'afor_min_quantity', true );
		$afor_max_quantity = get_post_meta( $rule_id, 'afor_max_quantity', true );

		if ( ! $this->match_cart_quantity( wc()->cart->get_cart_contents_count() , $afor_min_quantity, $afor_max_quantity ) ) {
			return false;
		}

		return true;
	}

	public function match_cart_amount( $cart_total, $lower_limit = 0.0, $upper_limit = 0.0 ) {

		$flag = false;

		if ( 0.0 === floatval( $lower_limit ) && 0.0 === floatval( $upper_limit ) ) {

			$flag = true;

		} elseif ( 0.0 === floatval( $lower_limit ) && $cart_total <= floatval( $upper_limit ) ) {


				$flag = true;

		} elseif ( 0.0 === floatval( $upper_limit ) && $cart_total >= $lower_limit ) {

			$flag = true;
		} elseif ( $lower_limit <= $cart_total && $cart_total <= $upper_limit ) {

			$flag = true;
		}

		return $flag;
	}

	public function match_cart_quantity( $cart_quantity, $lower_limit = 0, $upper_limit = 0 ) {

		$flag = false;

		if ( 0 === intval( $lower_limit ) && 0 === intval( $upper_limit ) ) {

			$flag = true;

		} elseif ( 0 === intval( $lower_limit ) && $cart_quantity <= intval( $upper_limit ) ) {


				$flag = true;

		} elseif ( 0 === intval( $upper_limit ) && $cart_quantity >= $lower_limit ) {

			$flag = true;

		} elseif ( $lower_limit <= $cart_quantity && $cart_quantity <= $upper_limit ) {

			$flag = true;
		}

		return $flag;
	}
}

new AF_O_R_Front();
