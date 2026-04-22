<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'AF_R_F_Q_Addify_Price_Calculator_Compatibilty' ) ) {

	class AF_R_F_Q_Addify_Price_Calculator_Compatibilty {

		public function __construct() {

			add_filter( 'addify_add_quote_item_data', array( $this, 'add_cart_item_data' ), 10, 5 );
			
			add_filter( 'addify_add_quote_item', array( $this, 'add_cart_item' ), 30, 1 );

			add_filter( 'addify_add_to_quote_validation', array( $this, 'validate_add_cart_item' ), 999, 4 );

			add_filter( 'addify_quote_product_quantity_maximum', array( $this, 'quote_product_quantity_maximum' ), 20, 3);
		}

		public function add_cart_item_data( $cart_item_data, $product_id, $variation_id = 0, $quantity = 1, $post_data = array() ) {

			if ( ! class_exists( 'Addf_Price_Calculator_Helper' ) ) {
				return $cart_item_data;
			}

			$data = Addf_Price_Calculator_Helper::add_price_calculator_add_cart_item_meta( $cart_item_data );


			if ( is_wp_error( $data ) ) {
				// Throw exception for add_to_cart to pickup.
				throw new Exception( esc_attr( $data->get_error_message() ) );
			} elseif ( $data ) {
				foreach ($data as $key => $value) {
					$cart_item_data[ $key ] = $value;
				}
			}
				

			return $cart_item_data;
		}

		public function add_cart_item( $cart_item_data, $post_data = array() ) {

			if ( ! class_exists( 'Addf_Price_Calculator_Helper' ) ) {
				return $cart_item_data;
			}

			$price = isset($cart_item_data['addf_prc_calculated_price']) ? $cart_item_data['addf_prc_calculated_price'] : $cart_item_data['data']->get_price( 'edit' );


			$cart_item_data['price_calculator_price'] = $price;
			$cart_item_data['offered_price'] = $price;

			return $cart_item_data;
		}

		public function validate_add_cart_item( $passed, $product_id, $qty, $post_data = array() ) {

			if ( ! class_exists( 'Addf_Price_Calculator_Helper' ) ) {
				return $passed;
			}

			$is_variation = false;


			if (!empty($post_data)) {
				$variation_id = isset($post_data['variation_id']) ? $post_data['variation_id'] : 0;
				$is_variation = 0 != $variation_id ? true : false;
				$product_id   = 0 != $variation_id ? $variation_id : $product_id;
			}

			$items = (array) WC()->session->get('quotes');

			$cart_item_data = array();
			

			$product = wc_get_product($product_id);
			$stock = '';

			// if product is on backorder then return
			if ($product->is_on_backorder() || $product->backorders_allowed()) {
				return $passed;
			}

			$stock_set_level = '';

			// checking which stock will be applied on this product
			if ($is_variation) {
				$parent_product = wc_get_product($product->get_parent_id());
				$parent_id = $product->get_parent_id();
				if (1 == $product->managing_stock() ) {
					$stock = $product->get_stock_quantity();
				} else if (1 == afrfq_is_price_calculator_product_managed($product_id)) {
					$stock = get_post_meta($product_id, 'addf_prc_stock_qty', true);
				} else if ('parent' == $product->managing_stock()) {
					$stock = $parent_product ? $parent_product->get_stock_quantity():0;

				} elseif ('parent' == afrfq_is_price_calculator_product_managed($product->get_id())) {
					$stock = get_post_meta($parent_id, 'addf_prc_stock_qty', true);
				}
			} elseif (1 == $product->managing_stock() ) {
					$stock = $product->get_stock_quantity();
			} else if (1 == afrfq_is_price_calculator_product_managed($product_id)) {
				$stock = get_post_meta($product_id, 'addf_prc_stock_qty', true);
				
			}

			if ('' == $stock) {
				return $passed;
			}

			$stock_already_in_quote = 0;

			$current_product_stock_set_level = Addf_Price_Calculator_Helper::check_stock_set_at_which_level($product);
			foreach ( $items as $item => $values ) {
				$cart_product = $values['data'];
				$stock_set_level = Addf_Price_Calculator_Helper::check_stock_set_at_which_level($cart_product);

				if ($is_variation) {
					$product_id_in_cart = $values['data']->get_id();
					$product_id_to_compare = $product_id;
					if (( 'parent_custom' == $stock_set_level || 'parent_default' == $stock_set_level ) && ( 'parent_custom' == $current_product_stock_set_level || 'parent_default' == $current_product_stock_set_level )) {
						$product_id_in_cart = $values['product_id'];
						$product_id_to_compare = $parent_product->get_id();
					}

					if ($product_id_in_cart == $product_id_to_compare) {
						if ( array_key_exists( 'addf_prc_price_calculated_weight', $values ) ) {
							$stock_already_in_quote += (float) $values['addf_prc_price_calculated_weight'] * $values['quantity'];
						}
					}
				} elseif ($values['data']->get_id() == $product_id) {
					if ( array_key_exists( 'addf_prc_price_calculated_weight', $values ) ) {
						$stock_already_in_quote += (float) $values['addf_prc_price_calculated_weight'] * $values['quantity'];
					}
				}

			}


			$post_data['called_from']            = 'afrfq_quote';
			$post_data['stock_already_in_quote'] = $stock_already_in_quote;
			
			if (!empty($post_data)) {
				$data = Addf_Price_Calculator_Helper::addf_price_calculator_add_to_cart_validator( $passed, $product_id, $qty , $variation_id, array(), $post_data);
	
				if ( is_wp_error( $data ) ) {
					wc_add_notice( $data->get_error_message(), 'error' );
					return false;
				}
			}
			
			return $passed;
		}

		public function quote_product_quantity_maximum( $value, $product, $quote_item ) {

			if (!isset($quote_item['product_id']) || 0 == $quote_item['product_id']  || !isset( $quote_item['addf_prc_price_calculated_weight'] )) {
				return $value;
			}

			if (!class_exists('Addf_Price_Calculator_Helper')) {
				return $value;
			}

			$items = WC()->session->get( 'quotes' ); 

			if (empty($items)) {
				return $value;
			}

			$product_id   = isset($quote_item['product_id']) ? $quote_item['product_id'] : 0;
			$variation_id = isset($quote_item['variation_id']) ? $quote_item['variation_id'] : 0;


			$is_variation = false;

			if ('' != $variation_id && 0 != $variation_id) {
				$is_variation = true;
				$product_id   = $variation_id;
			}

			$product = wc_get_product($product_id);
			$stock = '';

			// for backorder returning -1 so that no stock management logic triggers
			if ($product->is_on_backorder() || $product->backorders_allowed()) {
				return -9999;
			}

			$stock_set_level = '';

			// checking which stock will be applied on this product
			if ($is_variation) {
				$parent_product = wc_get_product($product->get_parent_id());
				$parent_id      = $product->get_parent_id();
				if (1 == $product->managing_stock() ) {
					$stock = $product->get_stock_quantity();
				} else if (1 == addf_prc_is_stock_managing($product_id)) {
					$stock = get_post_meta($product_id, 'addf_prc_stock_qty', true);
				} else if ('parent' == $product->managing_stock()) {
					$stock = $parent_product ? $parent_product->get_stock_quantity():0;

				} elseif ('parent' == addf_prc_is_stock_managing($product->get_id())) {
					$stock = get_post_meta($parent_id, 'addf_prc_stock_qty', true);
				}
			} elseif (1 == $product->managing_stock() ) {
					$stock = $product->get_stock_quantity();
			} else if (1 == addf_prc_is_stock_managing($product_id)) {
				$stock = get_post_meta($product_id, 'addf_prc_stock_qty', true);
			}

			if ('' == $stock) {
				return $value;
			}

			$calculated_weights = array(); 
			$individual_weights = array(); 

			$current_product_stock_set_level = Addf_Price_Calculator_Helper::check_stock_set_at_which_level($product);

			foreach ( $items as $quote_item_key => $quote_item_data ) {
				$id = $quote_item_data['variation_id'] && 0 != $quote_item_data['variation_id'] 
					? $quote_item_data['variation_id']
					: $quote_item_data['product_id']; 

				$product = wc_get_product($id);

				$stock_set_level = Addf_Price_Calculator_Helper::check_stock_set_at_which_level($product);

				$weight_per_unit = isset( $quote_item_data['addf_prc_price_calculated_weight'] ) ? floatval( $quote_item_data['addf_prc_price_calculated_weight'] ) : 0;
				$quantity = isset( $quote_item_data['quantity'] ) ? intval( $quote_item_data['quantity'] ) : 1;

				$total_weight = $weight_per_unit * $quantity;


				if (( 'parent_custom' == $stock_set_level || 'parent_default' == $stock_set_level ) && ( 'parent_custom' == $current_product_stock_set_level || 'parent_default' == $current_product_stock_set_level )) {
					// Store grouped sum per product/variation
					if ( ! isset( $calculated_weights[ $product->get_parent_id() ] ) ) {
						$calculated_weights[ $product->get_parent_id() ] = 0;
					}
					$calculated_weights[ $product->get_parent_id() ] += $total_weight;
				} else {
					if ( ! isset( $calculated_weights[ $id ] ) ) {
						$calculated_weights[ $id ] = 0;
					}
					$calculated_weights[ $id ] += $total_weight;
				}

				// Store per-item weight info using cart item key
				$individual_weights[ $quote_item_key ] = array(
					'product_id'       => $quote_item_data['product_id'],
					'variation_id'     => $quote_item_data['variation_id'],
					'quantity'         => $quantity,
					'weight_per_unit'  => $weight_per_unit,
					'total_weight'     => $total_weight,
				);
			}

			foreach ( $individual_weights as $key => &$item ) {
				$unique_id = $item['variation_id'] && 0 != $item['variation_id'] ? $item['variation_id'] : $item['product_id'];
				$unique_id = !isset($calculated_weights[ $unique_id ]) ? wc_get_product($unique_id)->get_parent_id() : $unique_id;
				
				if ( isset($calculated_weights[ $unique_id ]) ) {
					$total_weight = $calculated_weights[ $unique_id ];
					$item['total_weight_excluding_this_item'] = $total_weight - $item['total_weight'];
				}
			}
			unset($item);

			// $product_id   = isset($quote_item['product_id']) ? $quote_item['product_id'] : 0;
			// $variation_id = isset($quote_item['variation_id']) ? $quote_item['variation_id'] : 0;
			$key          = isset($quote_item['key']) ? $quote_item['key'] : 0; 
			

			if (0 != $product_id) {
				foreach ($individual_weights as $item_key => $item) {
					if ($key == $item_key) {
						$remaining_weight = $stock- $item['total_weight_excluding_this_item'];

						if ($remaining_weight < 0) {
							return $value;
						}

						$remaining_weight = floor($remaining_weight / $item['weight_per_unit']);
						return $remaining_weight;
					}
				}
			}

			return $value;
		}
	}

	new AF_R_F_Q_Addify_Price_Calculator_Compatibilty();
}
