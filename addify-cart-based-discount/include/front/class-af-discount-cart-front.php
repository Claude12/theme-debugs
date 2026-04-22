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
 * Discount Cart Front Class.
 * */
class AF_Discount_Cart_Front {
	/**
	 * Constructor.
	 * */
	public function __construct() {

		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'af_dcv_discount_cart_total' ), 10, 1 );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'af_dcv_discount_product_total' ), 100, 1 );
	}
	/**
	 * Apply cart discount.
	 *
	 * @param WC_Cart $cart cart object.
	 * */
	public function af_dcv_discount_cart_total( $cart ) {

		$af_discount_rule_applied = false;

		$af_discount_rules = self::af_dcv_discount_get_post_detail(
			array(
				'post_type'   => 'discount_cart',
				'post_status' => 'publish',
				'fields'      => 'ids',
			)
		);

		$af_dcv_b2b_rest_enable = get_option( 'af_dcv_disable_btb' );

		foreach ( WC()->cart->get_cart()  as $hash => $product ) {

			if ( 'yes' === $af_dcv_b2b_rest_enable ) {

				if ( is_plugin_active( 'b2b/addify_b2b.php' ) ) {

					if ( self::check_b2b_product_discount() ) {

						return;
					}
				}

				if ( is_plugin_active( 'role-based-pricing-for-woocommerce/addify-role-based-pricing.php' ) ) {

					if ( self::af_check_rbp_applied_discount( $product['data'] ) ) {

						return;
					}
				}
			}
		}

		$af_current_user = is_user_logged_in() ? current( wp_get_current_user()->roles ) : 'guest';

		foreach ( $af_discount_rules as $rule_id ) {

			$af_dcv_for_prod_cart = get_post_meta( $rule_id, 'af_dcv_discount_for_prod_cart', true );

			$af_dcv_for_prod_method = get_post_meta( $rule_id, 'af_dcv_discount_product_method', true );

			if ( ( 'cart_discount' !== $af_dcv_for_prod_cart ) && ( 'all_product' !== $af_dcv_for_prod_method ) ) {

				continue;
			}

			$af_dcv_cart_total = $cart->get_subtotal();

			$af_dcv_cart_quantity = $cart->get_cart_contents_count();

			$af_dcv_shipping_total = WC()->cart->get_cart_shipping_total();

			$af_dcv_discount_user = (array) get_post_meta( $rule_id, 'dcv_roles_checkboxs', true );

			$af_dcv_selected_products = (array) get_post_meta( $rule_id, 'af_dcv_discount_products', true );

			$af_dcv_selected_category_id = (array) get_post_meta( $rule_id, 'af_dcv_discount_category', true );

			$af_dcv_selected_tag_id = (array) get_post_meta( $rule_id, 'af_dcv_discount_product_tag', true );

			$af_dcv_msg_enable = get_post_meta( $rule_id, 'dcv_message_check', true );

			$af_dcv_success_message = get_post_meta( $rule_id, 'af_dcv_discount_success_message', true );

			$af_discount_notifi_message = get_post_meta( $rule_id, 'af_dcv_discount_notifi_message', true );

			$af_dcv_discount_type = get_post_meta( $rule_id, 'dcv_discount_type', true );

			$af_dcv_discount_row_details = (array) get_post_meta( $rule_id, 'af_dis_detail', true );

			$af_dcv_coupon_enb = get_option( 'af_dcv_coupons_enable' );

			$af_dcv_coupon_message = get_option( 'af_dcv_couple_message_disable' );

			$af_dcv_discount_variables = array();

			if ( ( 'cart_discount' === $af_dcv_for_prod_cart ) || ( ( 'product_discount' === $af_dcv_for_prod_cart ) && ( 'all_product' === $af_dcv_for_prod_method ) ) ) {

				$af_discount_rule_applied = true;
			}

			$af_dcv_discount_applied = false;

			if ( $af_discount_rule_applied ) {

				if ( isset( $af_dcv_discount_row_details['min'] ) && is_array( $af_dcv_discount_row_details['min'] ) ) {

					foreach ( $af_dcv_discount_row_details['min'] as $key => $af_dcv_discount_details ) {

						$af_discount_user_roles = 'for_all' === $af_dcv_discount_row_details['af_dcv_user_roles'][ $key ] ? array( $af_current_user ) : $af_dcv_discount_row_details['af_dcv_user_roles'][ $key ];

						$af_min_value           = $af_dcv_discount_row_details['min'][ $key ];
						$af_max_value           = $af_dcv_discount_row_details['max'][ $key ];
						$af_discount_adjustment = $af_dcv_discount_row_details['dicount_type'][ $key ];
						$af_discount_value      = $af_dcv_discount_row_details['discount_value'][ $key ];

						if ( ! empty( $af_discount_user_roles ) && ! in_array( $af_current_user, (array) $af_discount_user_roles, true ) ) {

							continue;
						}

						$af_dcv_discount_variables = self::af_check_discount_required_values( $af_dcv_discount_type, $af_dcv_cart_total, $af_dcv_cart_quantity, $af_min_value, $af_max_value, $af_discount_value );

						if ( self::af_dcv_calculate_discount_date( $rule_id ) ) {

							if ( self::af_check_discount_applicable( $rule_id, $af_dcv_discount_type, $af_dcv_cart_total, $af_dcv_cart_quantity, $af_min_value, $af_max_value ) ) {

								$af_new_discount = self::af_dcv_calculate_discount_amount( $af_dcv_cart_total, $af_discount_value, $af_discount_adjustment );

								$af_dcv_discount_variables['discount_amount'] = $af_discount_value;

								foreach ( $af_dcv_discount_variables as $key => $af_value ) {

									$af_dcv_success_message = str_replace( '{' . $key . '}', $af_value, $af_dcv_success_message );
								}

								$af_dcv_discount_applied = true;

								$cart->add_fee( 'Discount Price', -$af_new_discount );

								$af_dcv_applied_coupons = WC()->cart->get_applied_coupons();

								if ( 'yes' === $af_dcv_coupon_enb && ! empty( $af_dcv_applied_coupons ) ) {

									foreach ( $af_dcv_applied_coupons as $af_dcv_coupon ) {

										if ( ! empty( $af_dcv_coupon ) ) {

											wc_clear_notices();

											wc_add_notice( $af_dcv_coupon_message, 'notice' );

											$cart->remove_coupon( $af_dcv_coupon );
										}
									}
								}

								if ( is_cart() && 'on' === $af_dcv_msg_enable && ! empty( $af_dcv_success_message ) ) {

									wc_add_notice( $af_dcv_success_message, 'success' );
								}
							}

							if ( ! $af_dcv_discount_applied ) {

								foreach ( $af_dcv_discount_variables as $key => $af_value ) {

									$af_discount_notifi_message = str_replace( '{' . $key . '}', $af_value, $af_discount_notifi_message );
								}

								if ( is_cart() && 'on' === $af_dcv_msg_enable && ! empty( $af_discount_notifi_message ) ) {

									if ( ( 'quantity' === $af_dcv_discount_type && $af_dcv_cart_quantity < $af_min_value ) || ( 'total' === $af_dcv_discount_type && $af_dcv_cart_total < $af_min_value ) ) {

										wc_add_notice( $af_discount_notifi_message, 'notice' );
										continue 2;
									}
								}
							}
						}
					}
				}
			}
		}
	}
	/**
	 * Discount cart for product.
	 *
	 * @param WC_Cart $cart cart object.
	 * */
	public function af_dcv_discount_product_total( $cart ) {

		$af_discount_rules = self::af_dcv_discount_get_post_detail(
			array(
				'post_type'   => 'discount_cart',
				'post_status' => 'publish',
				'fields'      => 'ids',
			)
		);

		$af_dcv_b2b_rest_enable = get_option( 'af_dcv_disable_btb' );

		$af_current_user = is_user_logged_in() ? current( wp_get_current_user()->roles ) : 'guest';

		foreach ( WC()->cart->get_cart()  as $hash => $product ) {

			if ( 'yes' === $af_dcv_b2b_rest_enable ) {

				if ( is_plugin_active( 'b2b/addify_b2b.php' ) ) {

					if ( self::check_b2b_product_discount() ) {
						return;
					}
				}

				if ( is_plugin_active( 'role-based-pricing-for-woocommerce/addify-role-based-pricing.php' ) ) {

					if ( self::af_check_rbp_applied_discount( $product['data'] ) ) {
						return;
					}
				}
			}

			$af_discount_rule_applied = false;

			$af_dcv_product_id = $product['product_id'];

			$af_dcv_product_quantity = $product['quantity'];

			// $af_dcv_product_price = $product['data']->get_price();

			// $af_dcv_product_name = $product['data']->get_name();

			if (isset($product['data']) && is_object($product['data'])) {
				$af_dcv_product_price = $product['data']->get_price();
				$af_dcv_product_name  = $product['data']->get_name();
			} else {
				$af_dcv_product_price = 0;
				$af_dcv_product_name  = '';
			}

			$af_dcv_prod_subtotal = $af_dcv_product_price * $af_dcv_product_quantity; 

			foreach ( $af_discount_rules as $rule_id ) {

				$af_dcv_for_prod_cart = get_post_meta( $rule_id, 'af_dcv_discount_for_prod_cart', true );

				$af_dcv_for_prod_method = get_post_meta( $rule_id, 'af_dcv_discount_product_method', true );

				if ( ( 'product_discount' !== $af_dcv_for_prod_cart ) || ( 'specific_product' !== $af_dcv_for_prod_method ) ) {

					continue;
				}

				$af_dcv_discount_user = (array) get_post_meta( $rule_id, 'dcv_roles_checkboxs', true );

				$af_dcv_selected_products = (array) get_post_meta( $rule_id, 'af_dcv_discount_products', true );

				$af_dcv_selected_category_id = (array) get_post_meta( $rule_id, 'af_dcv_discount_category', true );

				$af_dcv_selected_tag_id = (array) get_post_meta( $rule_id, 'af_dcv_discount_product_tag', true );

				$af_dcv_msg_enable = get_post_meta( $rule_id, 'dcv_message_check', true );

				$af_dcv_success_message = get_post_meta( $rule_id, 'af_dcv_discount_success_message', true );

				$af_discount_notifi_message = get_post_meta( $rule_id, 'af_dcv_discount_notifi_message', true );

				$af_dcv_discount_type = get_post_meta( $rule_id, 'dcv_discount_type', true );

				$dcv_discount_by_total_price = get_post_meta( $rule_id, 'dcv_discount_by_total_price', true );
				$af_dcv_discount_row_details = (array) get_post_meta( $rule_id, 'af_dis_detail', true );

				$af_dcv_coupon_enb = get_option( 'af_dcv_coupons_enable' );

				$af_dcv_coupon_message = get_option( 'af_dcv_couple_message_disable' );

				$af_dcv_discount_variables = array();

				if ( ( ! empty( $af_dcv_selected_products ) && in_array( $af_dcv_product_id, $af_dcv_selected_products ) ) || ( $af_dcv_selected_category_id && has_term( $af_dcv_selected_category_id, 'product_cat', $af_dcv_product_id ) ) ||
					( $af_dcv_selected_tag_id && has_term( $af_dcv_selected_tag_id, 'product_tag', $af_dcv_product_id ) ) ) {

					$af_discount_rule_applied = true;
				}

				if ( empty( $af_dcv_selected_products ) && empty( $af_dcv_selected_category_id ) && empty( $af_dcv_selected_tag_id ) ) {

					continue;
				}

			$af_dcv_discount_applied = false;

				if ( $af_discount_rule_applied ) {

					if ( isset( $af_dcv_discount_row_details['min'] ) && is_array( $af_dcv_discount_row_details['min'] ) ) {

						foreach ( $af_dcv_discount_row_details['min'] as $key => $af_dcv_discount_details ) {

							$af_discount_user_roles = 'for_all' === $af_dcv_discount_row_details['af_dcv_user_roles'][ $key ] ? array( $af_current_user ) : $af_dcv_discount_row_details['af_dcv_user_roles'][ $key ];

							$af_min_value           = $af_dcv_discount_row_details['min'][ $key ];
							$af_max_value           = $af_dcv_discount_row_details['max'][ $key ];
							$af_discount_adjustment = $af_dcv_discount_row_details['dicount_type'][ $key ];
							$af_discount_value      = $af_dcv_discount_row_details['discount_value'][ $key ];

							if ( ! empty( $af_discount_user_roles ) && ! in_array( $af_current_user, (array) $af_discount_user_roles, true ) ) {

								continue;
							}

							$af_dcv_discount_variables = self::af_check_discount_required_values( $af_dcv_discount_type, $af_dcv_prod_subtotal, $af_dcv_product_quantity, $af_min_value, $af_max_value, $af_discount_value );

							$af_dcv_discount_variables['prod_name'] = $af_dcv_product_name;

							if ( self::af_dcv_calculate_discount_date( $rule_id ) ) {

								if ( self::af_check_discount_applicable( $rule_id, $af_dcv_discount_type, $af_dcv_prod_subtotal, $af_dcv_product_quantity, $af_min_value, $af_max_value ) ) {

									if ( 'subtotal' === $dcv_discount_by_total_price ) {

										$af_new_discount = self::af_dcv_calculate_discount_amount( $af_dcv_prod_subtotal, $af_discount_value, $af_discount_adjustment );
									} else {

										$af_new_discount = self::af_dcv_calculate_discount_amount( $af_dcv_product_price, $af_discount_value, $af_discount_adjustment );
									}

									$af_dcv_discount_variables['discount_amount'] = $af_discount_value;

									foreach ( $af_dcv_discount_variables as $key => $af_value ) {

										$af_dcv_success_message = str_replace( '{' . $key . '}', $af_value, $af_dcv_success_message );
									}

									if ( self::af_product_discount_applied( $cart, $hash, $dcv_discount_by_total_price, $af_dcv_prod_subtotal, $af_new_discount, $af_dcv_product_quantity, $af_dcv_product_price ) ) {

										$af_dcv_discount_applied = true;
									}

									$af_dcv_applied_coupons = WC()->cart->get_applied_coupons();

									if ( ( 'yes' === $af_dcv_coupon_enb && $af_dcv_discount_applied ) && ! empty( $af_dcv_applied_coupons ) ) {

										foreach ( $af_dcv_applied_coupons as $af_dcv_coupon ) {

											if ( ! empty( $af_dcv_coupon ) ) {

												wc_clear_notices();

												wc_add_notice( $af_dcv_coupon_message, 'notice' );

												$cart->remove_coupon( $af_dcv_coupon );

											}
										}
									}

									if ( ( $af_dcv_discount_applied && is_cart() ) && ( 'on' === $af_dcv_msg_enable && ! empty( $af_dcv_success_message ) ) ) {

										wc_add_notice( $af_dcv_success_message, 'success' );
									}
								}

								if ( ! $af_dcv_discount_applied ) {

									foreach ( $af_dcv_discount_variables as $key => $af_value ) {

										$af_discount_notifi_message = str_replace( '{' . $key . '}', $af_value, $af_discount_notifi_message );
									}

									if ( is_cart() && 'on' === $af_dcv_msg_enable && ! empty( $af_discount_notifi_message ) ) {

										if ( ( 'quantity' === $af_dcv_discount_type && $af_dcv_product_quantity < $af_min_value ) || ( 'total' === $af_dcv_discount_type && $af_dcv_prod_subtotal < $af_min_value ) ) {

											wc_add_notice( $af_discount_notifi_message, 'notice' );

											continue 3;
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	/**
	 * Calculate discount date.
	 *
	 * @param int    $rule_id rule id.
	 * */
	public static function af_dcv_calculate_discount_date( $rule_id ) {

		$af_dcv_valid_date = false;

		$af_dcv_discount_start_date = get_post_meta( $rule_id, 'af_dcv_discount_start_date', true ) ? gmdate( 'Y-m-d', strtotime( get_post_meta( $rule_id, 'af_dcv_discount_start_date', true ) ) ) : '';

		$af_dcv_discount_end_date = get_post_meta( $rule_id, 'af_dcv_discount_end_date', true ) ? gmdate( 'Y-m-d', strtotime( get_post_meta( $rule_id, 'af_dcv_discount_end_date', true ) ) ) : '';

		$af_dcv_current_date = gmdate( 'Y-m-d' );

		if ( strtotime( $af_dcv_current_date ) >= strtotime( $af_dcv_discount_start_date ) && strtotime( $af_dcv_current_date ) <= strtotime( $af_dcv_discount_end_date ) ) {

			$af_dcv_valid_date = true;
		}

		if ( empty( $af_dcv_discount_start_date ) && empty( $af_dcv_discount_end_date ) ) {

			$af_dcv_valid_date = true;
		}

		return $af_dcv_valid_date;
	}
	/**
	 * Check discount is applicable.
	 *
	 * @param int    $rule_id rule id.
	 * @param string $af_dcv_discount_type discount type.
	 * @param int    $af_dcv_cart_total cart total.
	 * @param int    $af_dcv_cart_quantity cart quantity.
	 * @param int    $af_min_value cart minimum value.
	 * @param int    $af_max_value cart maximum value.
	 * */
	public static function af_check_discount_applicable( $rule_id, $af_dcv_discount_type, $af_dcv_cart_total, $af_dcv_cart_quantity, $af_min_value, $af_max_value ) {

		if ( 'total' === $af_dcv_discount_type ) {

			if ( $af_dcv_cart_total >= $af_min_value && $af_dcv_cart_total <= $af_max_value ) {

				return true;
			}
		} elseif ( 'quantity' === $af_dcv_discount_type ) {

			if ( $af_dcv_cart_quantity >= $af_min_value && $af_dcv_cart_quantity <= $af_max_value ) {

				return true;
			}
		}
		return false;
	}
	/**
	 * Calculate discount amount.
	 *
	 * @param int    $af_dcv_cart_total cart total.
	 * @param int    $af_discount_value dicount value.
	 * @param string $af_discount_adjustment discount adjustments.
	 * */
	public static function af_dcv_calculate_discount_amount( $af_dcv_cart_total, $af_discount_value, $af_discount_adjustment ) {

		$af_new_discount = 0;

		if ( 'fixed' === $af_discount_adjustment && $af_discount_value < $af_dcv_cart_total ) {

			$af_new_discount = $af_discount_value;

		} elseif ( 'percentage' === $af_discount_adjustment && $af_discount_value < '101' ) {

			$af_new_discount = ( $af_discount_value / 100 ) * $af_dcv_cart_total;

		}

		return $af_new_discount;
	}
	/**
	 * Required discount notice variables.
	 *
	 * @param string $af_dcv_discount_type discount type.
	 * @param int    $af_dcv_cart_total cart total.
	 * @param int    $af_dcv_cart_quantity cart quantity.
	 * @param int    $af_min_value cart minimum value.
	 * @param int    $af_max_value cart maximum value.
	 * @param int    $af_discount_value rule id.
	 * */
	public static function af_check_discount_required_values( $af_dcv_discount_type, $af_dcv_cart_total, $af_dcv_cart_quantity, $af_min_value, $af_max_value, $af_discount_value ) {

		$af_dcv_required_values = array();

		if ( 'total' === $af_dcv_discount_type ) {

			if ( $af_dcv_cart_total < $af_min_value ) {

				$af_min_value -= $af_dcv_cart_total;

				$af_dcv_required_values['required_amount'] = $af_min_value;

				$af_dcv_required_values['discount_amount'] = $af_discount_value;
			}
		} elseif ( 'quantity' === $af_dcv_discount_type ) {

			if ( $af_dcv_cart_quantity < $af_min_value ) {

				$af_min_value -= $af_dcv_cart_quantity;

				$af_dcv_required_values['required_quantity'] = $af_min_value;

				$af_dcv_required_values['discount_amount'] = $af_discount_value;
			}
		}

		return $af_dcv_required_values;
	}
	/**
	 * Get post details.
	 *
	 * @param array $arg post details.
	 * */
	public static function af_dcv_discount_get_post_detail( $arg ) {
		$af_dcv_args = array(
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',

		);

		$af_dcv_args = array_merge( $af_dcv_args, (array) $arg );

		return get_posts( $af_dcv_args );
	}
	/**
	 * Product discount applied.
	 *
	 * @param WC_Cart $cart cart object.
	 * @param array   $hash cart object index.
	 * @param string  $dcv_discount_by_total_price discount type.
	 * @param int     $af_dcv_prod_subtotal product subtotal.
	 * @param int     $af_new_discount discount value.
	 * @param int     $af_dcv_product_quantity product quantity.
	 * @param int     $af_dcv_product_price product price.
	 * */
	public static function af_product_discount_applied( $cart, $hash, $dcv_discount_by_total_price, $af_dcv_prod_subtotal, $af_new_discount, $af_dcv_product_quantity, $af_dcv_product_price ) {

		if ( 'subtotal' === $dcv_discount_by_total_price && $af_new_discount < $af_dcv_prod_subtotal ) {

			$af_dcv_new_price = ( $af_dcv_prod_subtotal - $af_new_discount ) / $af_dcv_product_quantity;

			$cart->cart_contents[ $hash ]['data']->set_price( $af_dcv_new_price );

			return true;
		}

		if ( ( 'per_price' === $dcv_discount_by_total_price ) && ( $af_new_discount < $af_dcv_product_price ) ) {

			$af_dcv_new_price = ( $af_dcv_product_price - $af_new_discount );

			$cart->cart_contents[ $hash ]['data']->set_price( $af_dcv_new_price );

			return true;
		}
	}
	/**
	 * Check RBP discount applied.
	 *
	 * @param WC_Product $product object.
	 * */
	public static function af_check_rbp_applied_discount( $product ) {

		$af_price = new AF_C_S_P_Price();

		$role_based_price = $af_price->get_price_of_product( $product );

		if ( false !== $role_based_price ) {

			$af_dcv_rbp_discount = true;

			return $af_dcv_rbp_discount;
		}
	}
	/**
	 * Check b2b discount applied.
	 * */
	public static function check_b2b_product_discount() {

		$allfetchedrules = new Front_Class_Addify_Customer_And_Role_Pricing();

		$is_product_discount_applied = false;

		$cart_object = wc()->cart;

		// Avoiding hook repetition (when using price calculations for example).
		$user           = wp_get_current_user();
		$role           = (array) $user->roles;
		$quantity       = 0;
		$rule_cus_price = '';

		$price_for_discount = get_option( 'afb2b_discount_price' );
		$active_currency    = get_woocommerce_currency();
		$base_currency      = get_option( 'woocommerce_currency' );


		if ( is_user_logged_in() ) {

			foreach ( $cart_object->get_cart() as $key => $value ) {

				$customer_discount  = false;
				$role_discount      = false;
				$customer_discount1 = false;
				$role_discount1     = false;

				$quantity += $value['quantity'];

				if ( 0 !== $value['variation_id'] ) {

					$product_id = $value['variation_id'];
					$parent_id  = $value['product_id'];

				} else {

					$product_id = $value['product_id'];
					$parent_id  = 0;

				}

				$first_role = current( $user->roles );

				if ( ! empty( $price_for_discount[ $first_role ] ) && 'sale' === $price_for_discount[ $first_role ] && ! empty( $value['data']->get_sale_price() ) ) {

					$pro_price = $value['data']->get_sale_price();

				} elseif ( ! empty( $price_for_discount[ $first_role ] ) && 'regular' === $price_for_discount[ $first_role ] && ! empty( $value['data']->get_regular_price() ) ) {

					$pro_price = $value['data']->get_regular_price();

				} else {

					$pro_price = $value['data']->get_price();
				}

				// get customer specific price.
				$cus_base_price = get_post_meta( $product_id, '_cus_base_price', true );

				// get role base price.
				$role_base_price = get_post_meta( $product_id, '_role_base_price', true );

				// customer pricing.
				if ( ! empty( $cus_base_price ) ) {

					foreach ( $cus_base_price as $cus_price ) {

						if ( isset( $cus_price['customer_name'] ) && $user->ID == $cus_price['customer_name'] ) {

							if ( ( $value['quantity'] >= $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] )
								|| ( $value['quantity'] >= $cus_price['min_qty'] && '' === $cus_price['max_qty'] )
								|| ( $value['quantity'] >= $cus_price['min_qty'] && 0 === $cus_price['max_qty'] )
								|| ( '' === $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] )
								|| ( 0 === $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] )
							) {
								if ( 'fixed_price' === $cus_price['discount_type'] ) {

									// aelia currency switcher compatibility.
									$converted_amount = apply_filters( 'wc_aelia_cs_convert', $cus_price['discount_value'], $base_currency, $active_currency );

									$customer_discount           = true;
									$is_product_discount_applied = true;

								} elseif ( 'fixed_increase' === $cus_price['discount_type'] ) {

									if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

										$newprice = 0;
										$newprice = $newprice + $cus_price['discount_value'];
									} else {

										$newprice = $pro_price + $cus_price['discount_value'];
									}

									// aelia currency switcher compatibility.
									$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

									$customer_discount           = true;
									$is_product_discount_applied = true;

								} elseif ( 'fixed_decrease' === $cus_price['discount_type'] ) {

									if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

										$newprice = 0;
									} else {

										$newprice = $pro_price - $cus_price['discount_value'];

										if ( 0 > $newprice ) {

											$newprice = 0;

										} else {

											$newprice = $newprice;

										}
									}

									// Aelia currency switcher compatibility.
									$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

									$customer_discount           = true;
									$is_product_discount_applied = true;

								} elseif ( 'percentage_decrease' === $cus_price['discount_type'] ) {

									if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

										$newprice = 0;
									} else {

										$percent_price = $pro_price * $cus_price['discount_value'] / 100;

										$newprice = $pro_price - $percent_price;

										if ( 0 > $newprice ) {

											$newprice = 0;

										} else {

											$newprice = $newprice;

										}
									}

									// Aelia currency switcher compatibility.
									$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

									$customer_discount           = true;
									$is_product_discount_applied = true;

								} elseif ( 'percentage_increase' === $cus_price['discount_type'] ) {

									if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

										$newprice = 0;
									} else {

										$percent_price = $pro_price * $cus_price['discount_value'] / 100;

										$newprice = $pro_price + $percent_price;
									}

									// Aelia currency switcher compatibility.
									$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

									$customer_discount           = true;
									$is_product_discount_applied = true;

								} else {

									$customer_discount = false;
								}
							}
						}
					}
				} else {

					$customer_discount = false;
				}

				// Role Based Pricing.
				// chcek if there is customer specific pricing then role base pricing will not work.
				if ( ! $customer_discount ) {

					if ( ! empty( $role_base_price ) ) {

						foreach ( $role_base_price as $role_price ) {

							if ( isset( $role_price['user_role'] ) && current( $role ) === $role_price['user_role'] ) {

								if ( ( $value['quantity'] >= $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									|| ( $value['quantity'] >= $role_price['min_qty'] && '' === $role_price['max_qty'] )
									|| ( $value['quantity'] >= $role_price['min_qty'] && 0 === $role_price['max_qty'] )
									|| ( '' === $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									|| ( 0 === $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
								) {

									if ( 'fixed_price' === $role_price['discount_type'] ) {

										// aelia currency switcher compatibility.
										$newprice = apply_filters( 'wc_aelia_cs_convert', $role_price['discount_value'], $base_currency, $active_currency );

										$role_discount               = true;
										$is_product_discount_applied = true;

									} elseif ( 'fixed_increase' === $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

											$newprice = 0;
											$newprice = $newprice + $role_price['discount_value'];
										} else {

											$newprice = $pro_price + $role_price['discount_value'];
										}

										// aelia currency switcher compatibility.
										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$role_discount               = true;
										$is_product_discount_applied = true;

									} elseif ( 'fixed_decrease' === $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

											$newprice = 0;

										} else {

											$newprice = $pro_price - $role_price['discount_value'];
											if ( 0 > $newprice ) {

												$newprice = 0;

											} else {

												$newprice = $newprice;

											}
										}

										// aelia currency switcher compatibility.
										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$role_discount               = true;
										$is_product_discount_applied = true;

									} elseif ( 'percentage_decrease' === $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price - $percent_price;

											if ( 0 > $newprice ) {

												$newprice = 0;

											} else {

												$newprice = $newprice;

											}
										}

										// aelia currency switcher compatibility.
										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$role_discount               = true;
										$is_product_discount_applied = true;

									} elseif ( 'percentage_increase' === $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;
										}

										// aelia currency switcher compatibility.
										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$role_discount               = true;
										$is_product_discount_applied = true;

									} else {

										$role_discount = false;
									}
								}
							}
						}
					} else {

						$role_discount = false;
					}
				}

				// rules.
				if ( false === $customer_discount && false === $role_discount ) {

					if ( empty( $allfetchedrules->csp_load() ) ) {

						echo '';

					} else {

						$all_rules = $allfetchedrules->csp_load();

					}

					if ( ! empty( $all_rules ) ) {

						$rule_check = false;

						foreach ( $all_rules as $rule ) {

							$istrue = false;

							$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
							$products                = get_post_meta( $rule->ID, 'csp_applied_on_products', true );
							$categories              = get_post_meta( $rule->ID, 'csp_applied_on_categories', true );
							$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

							if ( ! $rule_check ) {

								if ( 'yes' === $applied_on_all_products ) {
									$istrue = true;

								} elseif ( ! empty( $products ) && ( in_array( $product_id, $products ) || in_array( $parent_id, $products ) ) ) {
									$istrue = true;

								}

								if ( ! empty( $categories ) ) {
									foreach ( $categories as $cat ) {

										if ( ! empty( $cat ) && ( has_term( $cat, 'product_cat', $product_id )  ||  has_term( $cat, 'product_cat', $parent_id ) ) ) {

											$istrue = true;

										}
									}
								}

								if ( ! empty( $rbp_slected_brands ) ) {
									foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

										if ( ! empty( $rbp_brand_slect ) && ( has_term( $rbp_brand_slect, 'product_brand', $product_id )  ||  has_term( $rbp_brand_slect, 'product_brand', $parent_id ) ) ) {

											$istrue = true;

										}
									}
								}

								if ( $istrue ) {

									// get Rule customer specifc price.
									$rule_cus_base_price = get_post_meta( $rule->ID, 'rcus_base_price', true );
									// get role base price.
									$rule_role_base_price = get_post_meta( $rule->ID, 'rrole_base_price', true );
									

									if ( ! empty( $rule_cus_base_price ) ) {
									// print_r($rule_cus_base_price);

										foreach ( $rule_cus_base_price as $rule_cus_price ) {

											if ( isset( $rule_cus_price['customer_name'] ) && $user->ID == $rule_cus_price['customer_name'] ) {

												if ( ( $value['quantity'] >= $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] )
													|| ( $value['quantity'] >= $rule_cus_price['min_qty'] && '' === $rule_cus_price['max_qty'] )
													|| ( $value['quantity'] >= $rule_cus_price['min_qty'] && 0 === $rule_cus_price['max_qty'] )
													|| ( '' === $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] )
													|| ( 0 === $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] )
												) {

													$rule_check = true;

													if ( 'fixed_price' === $rule_cus_price['discount_type'] ) {

														// aelia currency switcher compatibility.
														$newprice = apply_filters( 'wc_aelia_cs_convert', $rule_cus_price['discount_value'], $base_currency, $active_currency );

														$customer_discount1          = true;
														$is_product_discount_applied = true;

													} elseif ( 'fixed_increase' === $rule_cus_price['discount_type'] ) {
														if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;
															$newprice = $newprice + $rule_cus_price['discount_value'];

														} else {

															$newprice = $pro_price + $rule_cus_price['discount_value'];
														}

															//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														$customer_discount1 = true;

														$is_product_discount_applied = true;

													} elseif ( 'fixed_decrease' === $rule_cus_price['discount_type'] ) {

														if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

															$newprice = 0;

														} else {

															$newprice = $pro_price - $rule_cus_price['discount_value'];
															if ( 0 > $newprice ) {

																$newprice = 0;

															} else {

																$newprice = $newprice;

															}
														}

														// aelia currency switcher compatibility.
														$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

														$customer_discount1          = true;
														$is_product_discount_applied = true;

													} elseif ( 'percentage_decrease' === $rule_cus_price['discount_type'] ) {

														if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

															$newprice = 0;

														} else {

															$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

															$newprice = $pro_price - $percent_price;

															if ( 0 > $newprice ) {

																$newprice = 0;

															} else {

																$newprice = $newprice;

															}
														}

														// aelia currency switcher compatibility.
														$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

														$customer_discount1          = true;
														$is_product_discount_applied = true;

													} elseif ( 'percentage_increase' === $rule_cus_price['discount_type'] ) {

														if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

															$newprice = 0;

														} else {

															$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

															$newprice = $pro_price + $percent_price;
														}

														// aelia currency switcher compatibility.
														$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

														$customer_discount1          = true;
														$is_product_discount_applied = true;

													} else {

														$customer_discount1 = false;
													}
												}
											}
										}
									} else {

										$customer_discount1 = false;
									}
									// Rule Role Based Pricing.
									// chcek if there is customer specific pricing then role base pricing will not work.
									if ( ! $customer_discount1 ) {

										if ( ! empty( $rule_role_base_price ) ) {

											foreach ( $rule_role_base_price as $rule_role_price ) {

												if ( isset( $rule_role_price['user_role'] ) && current( $role ) === $rule_role_price['user_role'] ) {

													if ( ( $value['quantity'] >= $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
													|| ( $value['quantity'] >= $rule_role_price['min_qty'] && '' === $rule_role_price['max_qty'] )
													|| ( $value['quantity'] >= $rule_role_price['min_qty'] && 0 === $rule_role_price['max_qty'] )
													|| ( '' === $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
													|| ( 0 === $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
													) {

														$rule_check = true;

														if ( 'fixed_price' === $rule_role_price['discount_type'] ) {

																// aelia currency switcher compatibility.
															$newprice = apply_filters( 'wc_aelia_cs_convert', $rule_role_price['discount_value'], $base_currency, $active_currency );

															$is_product_discount_applied = true;

														} elseif ( 'fixed_increase' === $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

																$newprice = 0;
																$newprice = $newprice + $rule_role_price['discount_value'];

															} else {

																$newprice = $pro_price + $rule_role_price['discount_value'];
															}

																// aelia currency switcher compatibility.
															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$is_product_discount_applied = true;

														} elseif ( 'fixed_decrease' === $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {
																$newprice = 0;

															} else {

																$newprice = $pro_price - $rule_role_price['discount_value'];

																if ( 0 > $newprice ) {

																	$newprice = 0;

																} else {

																	$newprice = $newprice;

																}
															}

																// aelia currency switcher compatibility.
															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$is_product_discount_applied = true;

														} elseif ( 'percentage_decrease' === $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {
																$newprice = 0;

															} else {

																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice = $pro_price - $percent_price;

																if ( 0 > $newprice ) {

																	$newprice = 0;

																} else {

																	$newprice = $newprice;

																}
															}

																// aelia currency switcher compatibility.
															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$is_product_discount_applied = true;

														} elseif ( 'percentage_increase' === $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

																$newprice = 0;

															} else {

																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice = $pro_price + $percent_price;
															}

															// aelia currency switcher compatibility.
															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$is_product_discount_applied = true;
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		} elseif ( ! is_user_logged_in() ) {


			foreach ( $cart_object->get_cart() as $key => $value ) {

				$customer_discount  = false;
				$role_discount      = false;
				$customer_discount1 = false;
				$role_discount1     = false;

				$quantity += $value['quantity'];

				if ( 0 !== $value['variation_id'] ) {

					$product_id = $value['variation_id'];
					$parent_id  = $value['product_id'];

				} else {

					$product_id = $value['product_id'];
					$parent_id  = 0;

				}

				if ( ! empty( $price_for_discount['guest'] ) && 'sale' === $price_for_discount['guest'] && ! empty( $value['data']->get_sale_price() ) ) {

					$pro_price = $value['data']->get_sale_price();

				} elseif ( ! empty( $price_for_discount['guest'] ) && 'regular' === $price_for_discount['guest'] && ! empty( $value['data']->get_regular_price() ) ) {

					$pro_price = $value['data']->get_regular_price();

				} else {

					$pro_price = $value['data']->get_price();
				}

						// Role Based Pricing for guest.
				if ( true ) {

							// get role base price for guest.
					$role_base_price = get_post_meta( $product_id, '_role_base_price', true );

					if ( ! empty( $role_base_price ) ) {

						foreach ( $role_base_price as $role_price ) {

							if ( isset( $role_price['user_role'] ) && 'guest' === $role_price['user_role'] ) {

								if ( ( $value['quantity'] >= $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									|| ( $value['quantity'] >= $role_price['min_qty'] && '' === $role_price['max_qty'] )
									|| ( $value['quantity'] >= $role_price['min_qty'] && 0 === $role_price['max_qty'] )
									|| ( '' === $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									|| ( 0 === $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
								) {

									if ( 'fixed_price' === $role_price['discount_type'] ) {

												// aelia currency switcher compatibility.
										$newprice = apply_filters( 'wc_aelia_cs_convert', $role_price['discount_value'], $base_currency, $active_currency );

										$role_discount               = true;
										$is_product_discount_applied = true;

									} elseif ( 'fixed_increase' === $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

											$newprice = 0;
											$newprice = $newprice + $role_price['discount_value'];

										} else {

											$newprice = $pro_price + $role_price['discount_value'];
										}

												// aelia currency switcher compatibility.
										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$role_discount               = true;
										$is_product_discount_applied = true;

									} elseif ( 'fixed_decrease' === $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

											$newprice = 0;

										} else {

											$newprice = $pro_price - $role_price['discount_value'];

											if ( 0 > $newprice ) {

												$newprice = 0;

											} else {

												$newprice = $newprice;

											}
										}

												// aelia currency switcher compatibility.
										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$role_discount               = true;
										$is_product_discount_applied = true;

									} elseif ( 'percentage_decrease' === $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price - $percent_price;

											if ( 0 > $newprice ) {

												$newprice = 0;

											} else {

												$newprice = $newprice;

											}
										}

												// aelia currency switcher compatibility.
										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$role_discount               = true;
										$is_product_discount_applied = true;

									} elseif ( 'percentage_increase' === $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;
										}

												// aelia currency switcher compatibility.
										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$role_discount               = true;
										$is_product_discount_applied = true;

									} else {

										$role_discount = false;
									}
								}
							}
						}
					} else {

						$role_discount = false;
					}

							// Rules - guest users.
					if ( false === $role_discount ) {

						if ( empty( $allfetchedrules->csp_load() ) ) {

							echo '';

						} else {

							$all_rules = $allfetchedrules->csp_load();

						}

						if ( ! empty( $all_rules ) ) {

							$rule_check = false;

							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
								$products                = get_post_meta( $rule->ID, 'csp_applied_on_products', true );
								$categories              = get_post_meta( $rule->ID, 'csp_applied_on_categories', true );
								$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

								if ( ! $rule_check ) {

									if ( 'yes' === $applied_on_all_products ) {
										$istrue = true;
									} elseif ( ! empty( $products ) && ( in_array( $product_id, $products ) || in_array( $parent_id, $products ) ) ) {
										$istrue = true;
									}

									if ( ! empty( $categories ) ) {
										foreach ( $categories as $cat ) {

											if ( ! empty( $cat ) && ( has_term( $cat, 'product_cat', $product_id )  ||  has_term( $cat, 'product_cat', $parent_id ) ) ) {

												$istrue = true;
											}
										}
									}

									if ( ! empty( $rbp_slected_brands ) ) {
										foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

											if ( ! empty( $rbp_brand_slect ) && ( has_term( $rbp_brand_slect, 'product_brand', $product_id )  ||  has_term( $rbp_brand_slect, 'product_brand', $parent_id ) ) ) {

												$istrue = true;
											}
										}
									}

									if ( $istrue ) {

												// get rule role base price for guest.
										$rule_role_base_price = get_post_meta( $rule->ID, 'rrole_base_price', true );

										if ( ! empty( $rule_role_base_price ) ) {

											foreach ( $rule_role_base_price as $rule_role_price ) {

												if ( isset( $rule_role_price['user_role'] ) && 'guest' === $rule_role_price['user_role'] ) {

													if ( ( $value['quantity'] >= $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
														|| ( $value['quantity'] >= $rule_role_price['min_qty'] && '' === $rule_role_price['max_qty'] )
														|| ( $value['quantity'] >= $rule_role_price['min_qty'] && 0 === $rule_role_price['max_qty'] )
														|| ( '' === $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
														|| ( 0 === $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
													) {

														$rule_check = true;

														if ( 'fixed_price' === $rule_role_price['discount_type'] ) {

																	// aelia currency switcher compatibility.
															$newprice = apply_filters( 'wc_aelia_cs_convert', $rule_role_price['discount_value'], $base_currency, $active_currency );

															$is_product_discount_applied = true;

														} elseif ( 'fixed_increase' === $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

																$newprice = 0;
																$newprice = $newprice + $rule_role_price['discount_value'];

															} else {

																$newprice = $pro_price + $rule_role_price['discount_value'];
															}

															// aelia currency switcher compatibility.
														$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

														$is_product_discount_applied = true;

														} elseif ( 'fixed_decrease' === $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

																		$newprice = 0;

															} else {

																$newprice = $pro_price - $rule_role_price['discount_value'];

																if ( 0 > $newprice ) {

																	$newprice = 0;

																} else {

																	$newprice = $newprice;

																}
															}

														// aelia currency switcher compatibility.
														$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

														$is_product_discount_applied = true;

														} elseif ( 'percentage_decrease' === $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

																				$newprice = 0;

															} else {

																				$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																				$newprice = $pro_price - $percent_price;

																if ( 0 > $newprice ) {

															$newprice = 0;

																} else {

			$newprice = $newprice;

																}
															}

																			// aelia currency switcher compatibility.
														$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

														$is_product_discount_applied = true;

														} elseif ( 'percentage_increase' === $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 === $pro_price ) ) {

																						$newprice = 0;

															} else {

																						$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																						$newprice = $pro_price + $percent_price;
															}

																					// aelia currency switcher compatibility.
														$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

														$is_product_discount_applied = true;
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
return $is_product_discount_applied;
	}
}


new AF_Discount_Cart_Front();
