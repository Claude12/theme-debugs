<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'AF_R_F_Q_Addify_Composite_Product_Builder_Compatibilty' ) ) {

	class AF_R_F_Q_Addify_Composite_Product_Builder_Compatibilty {

		public function __construct() {


			add_filter( 'addify_add_quote_item_data', array( $this, 'add_cart_item_data' ), 10, 5 );
			
			add_filter( 'addify_add_quote_item', array( $this, 'add_cart_item' ), 30, 1 );

			add_filter( 'addify_add_to_quote_validation', array( $this, 'validate_add_cart_item' ), 999, 4 );

			// handle product quantity change of main composite product
			add_action( 'addify_quote_session_changed', array( $this, 'handle_composite_product_quantity_change' ), 20 );
		}

		public function handle_composite_product_quantity_change() {

			$quote = (array) wc()->session->get( 'quotes' );
			$updated_quote = $quote;

			foreach ($quote as $quote_value) {
				if ( array_key_exists( 'composite_child_products' , $quote_value ) ) {

					if ( isset($quote[ $quote_value['composite_child_products']['parent_id'] ]) ) {
						$parent_quantity   = $quote[ $quote_value['composite_child_products']['parent_id'] ]['quantity'];
						$own_quantity      = $quote_value['quantity'];
						$child_array       = $quote_value['composite_child_products'];
						$child_parent_qty  = $child_array['parent_quantity'];
						$child_own_qty     = $child_array['qty'];
						$check_qty_change  = false;

						$child_max_qty     =  isset($child_array['max']) ? $child_array['max'] : 0;

						

						if ( ( $child_parent_qty != $parent_quantity ) && ( $child_own_qty != $own_quantity ) ) {
							$check_qty_change = true;
							$child_own_qty    = $own_quantity / $child_parent_qty;
						} elseif ( ( $child_parent_qty != $parent_quantity ) && ( $child_own_qty == $own_quantity ) ) {
							$check_qty_change = true;
							$child_own_qty    = $own_quantity / $child_parent_qty;
						} elseif ( ( $child_parent_qty == $parent_quantity ) && ( $child_own_qty != $own_quantity ) ) {
							$check_qty_change = true;
							$child_own_qty    = $own_quantity / $child_parent_qty;
						}

						$child_own_qty   = ceil($child_own_qty);
						$update_quantity = ceil( $child_own_qty *  $parent_quantity );
						
						if ( $check_qty_change ) {
							$updated_quote[ $quote_value['key'] ]['quantity'] = $update_quantity;
							$updated_quote[ $quote_value['key'] ]['composite_child_products']['parent_quantity'] = $parent_quantity;
							$updated_quote[ $quote_value['key'] ]['composite_child_products']['qty'] = $child_own_qty;
						}
					}
				}
			}

			wc()->session->set( 'quotes' , $updated_quote );

			$quote_content = wc()->session->get( 'quotes' );
			wc()->session->set( 'quotes' , $this->af_compute_the_price($quote_content) );
		}

		public function add_cart_item_data( $cart_item_data, $product_id, $variation_id = 0, $quantity = 1, $post_data = array() ) {

			if ( ! class_exists( 'Af_Composite_Product_Helper' ) ) {
				return $cart_item_data;
			}
			
			$data = Af_Composite_Product_Helper::afcpb_add_cart_item_meta( $cart_item_data );

			if ( is_wp_error( $data ) ) {
				// Throw exception for add_to_cart to pickup.
				throw new Exception( esc_attr( $data->get_error_message() ) );
			} elseif ( $data ) {
				$cart_item_data = $data;
			}
				

			return $cart_item_data;
		}

		public function add_cart_item( $cart_item_data, $post_data = array() ) {

			if ( ! class_exists( 'Af_Composite_Product_Helper' ) ) {
				return $cart_item_data;
			}


			//price calculation for parent composite product
			if ($cart_item_data['data']->get_type() == 'af_composite_product') {
				$price_type     = get_post_meta( $cart_item_data['data']->get_id() , 'af_comp_product_price_type' , true );

				if (isset($cart_item_data['af_cp_component_product']) && !empty($cart_item_data['af_cp_component_product'])) {
					$product_ids    = isset($cart_item_data['af_cp_component_product']) && !empty($cart_item_data['af_cp_component_product']) ? $cart_item_data['af_cp_component_product'] : array();
					$quantities     = isset($cart_item_data['af_cp_component_product_qty']) && !empty($cart_item_data['af_cp_component_product_qty']) ? $cart_item_data['af_cp_component_product_qty'] : array();
					$variation_ids  = isset($cart_item_data['af_cp_variation_id']) && !empty($cart_item_data['af_cp_variation_id']) ? $cart_item_data['af_cp_variation_id'] : array();
					
					$computed_price = $this->compute_component_total_price( $cart_item_data['data']->get_id(), $product_ids, $quantities, $variation_ids );
					$cart_item_data['offered_price']            = $computed_price;
					$cart_item_data['composite_product_price']  = $computed_price;

					$cart_item_data['calculation_method'] = 'calculated_price' == $price_type ? 'per_component' : 'as_whole';
				}
			}

			//price calcualtion for composite products childern
			if (isset($cart_item_data['composite_child_products']) && !empty($cart_item_data['composite_child_products'])) {
				$parent_id = isset($cart_item_data['composite_child_products']) && !empty($cart_item_data['composite_child_products']) && isset($cart_item_data['composite_child_products']['parent_product_id']) ? $cart_item_data['composite_child_products']['parent_product_id'] : '';
				$comp_key = isset($cart_item_data['composite_child_products']) && !empty($cart_item_data['composite_child_products']) && isset($cart_item_data['composite_child_products']['comp_key']) ? $cart_item_data['composite_child_products']['comp_key'] : '';
				$price   = $cart_item_data['data']->get_price();

				$price_type     = get_post_meta( $parent_id , 'af_comp_product_price_type' , true );
				$component_name = get_post_meta( $parent_id, 'af_comp_product_component_name' , true );
				

				if ('calculated_price' == $price_type) {
					$cart_item_data['offered_price']            = $this->afrfq_cp_component_product_price($parent_id, $comp_key, $price);
					$cart_item_data['composite_product_price']  = $this->afrfq_cp_component_product_price($parent_id, $comp_key, $price);
					$cart_item_data['calculation_method']       = 'per_component';
					$cart_item_data['component_name']           = isset($component_name[ $comp_key ]) ? $component_name[ $comp_key ] : '';
				} else {
					$cart_item_data['offered_price']            = 0;
					$cart_item_data['composite_product_price']  = 0;
					$cart_item_data['calculation_method']       = 'as_whole';
					$cart_item_data['component_name']           = isset($component_name[ $comp_key ]) ? $component_name[ $comp_key ] : '';

				}

			}
			
			return $cart_item_data;
		}


		public function af_compute_the_price( $quote_data ) {
			// Find the composite product
			$composite_product_data = array();
			foreach ($quote_data as $item_key => $item_data) {
				if (isset($item_data['af_cp_component_product']) && !isset($item_data['composite_child_products'])) {
					if (!in_array($item_key, $composite_product_data)) {
						$composite_product_data[] = $item_key;
					}
				}
			}

			foreach ($composite_product_data as $cp_key) {
				$composite_product = $quote_data[ $cp_key ];

				// Return if no composite product found or not per_component calculation
				if (!$composite_product || 'per_component' != $composite_product['calculation_method']) {
					// return $quote_data; 
					continue;
				}
			
				// Get all component IDs from the composite product
				$component_ids = array_keys($composite_product['af_cp_component_product']);
				$parent_product_id = $composite_product['product_id'];
				// $found_components = [];
				$total_child_price = 0;
				$parent_quantity = $composite_product['quantity'];
			
				// Find all child products of this composite
				foreach ($quote_data as $item_key => $item_data) {
					if (isset($item_data['composite_child_products'])) {
						$child_info = $item_data['composite_child_products'];
						
						// Verify this is a child of our composite product
						if ($child_info['parent_id'] === $composite_product['key']) {
							// Check if this is one of our components
							if (in_array($child_info['comp_key'], $component_ids)) {
								// $found_components[] = $child_info['comp_key'];
								
								$child_price = $item_data['offered_price'];

								$child_quantity = $item_data['quantity'];
								$total_child_price += ( $child_price * $child_quantity );
							}
						}
					}
				}
			
				// Calculate new price if all components are present
				if ($parent_quantity > 0) {
					$new_price = $total_child_price / $parent_quantity;

					// appplying discount of configurable product after recalculation
					$adjust_type   = get_post_meta( $parent_product_id , 'af_comp_product_price_adjustment' , true );
					$adjust_value  = floatval(get_post_meta( $parent_product_id , 'af_comp_product_adj_value' , true ));
					// $product_price = $product_price/$item_product_qty;
					if ( ( 0 != $adjust_value ) && ( '' != $adjust_value ) ) {
						$percentage = ( $new_price/100 ) * $adjust_value;

						if ( 'fixed_increase' == $adjust_type ) {
							$new_price += $adjust_value;

						} elseif ( 'fixed_decrease' == $adjust_type ) {
							$new_price = $new_price - $adjust_value;

						} elseif ( 'percentage_increase' == $adjust_type ) {
							$new_price = $new_price + $percentage;

						} elseif ( 'percentage_decrease' == $adjust_type ) {
							$new_price = $new_price - $percentage;

						}
					}
					$quote_data[ $composite_product['key'] ]['composite_product_price'] = $new_price;
				}   
			}       
			return $quote_data;
		}



		public function compute_component_total_price( $parent_id, $product_ids, $quantities, $variation_ids = array() ) {


			$product_price    = wc_get_product($parent_id)->get_price();
			$quote            = (array) WC()->session->get( 'quotes' );
			$price_type       = get_post_meta( $parent_id , 'af_comp_product_price_type' , true );
			$item_product_qty = 1;
			if ( 'calculated_price' == $price_type ) {
				$product_price = 0;

				foreach ( $product_ids as $comp_key => $product_id ) {
					$qty = isset( $quantities[ $comp_key ] ) ? (int) $quantities[ $comp_key ] : 1;
			
					// Check if variation exists for this comp_key
					if ( isset( $variation_ids[ $comp_key ] ) && $variation_ids[ $comp_key ] ) {
						$variation = wc_get_product( (int) $variation_ids[ $comp_key ] );
						if ( $variation && $variation->is_type( 'variation' ) ) {
							$price = floatval( $this->afrfq_cp_component_product_price( $parent_id , $comp_key , $variation->get_price() ));
						} else {
							continue;
						}
					} else {
						$product = wc_get_product( (int) $product_id );
						if ( $product ) {
							$price = floatval( $this->afrfq_cp_component_product_price( $parent_id , $comp_key , $product->get_price() ));
						} else {
							continue; 
						}
					}

					$product_price += (float) $price * $qty;

				}
			}
			$adjust_type   = get_post_meta( $parent_id , 'af_comp_product_price_adjustment' , true );
			$adjust_value  = floatval(get_post_meta( $parent_id , 'af_comp_product_adj_value' , true ));
			$product_price = $product_price/$item_product_qty;
			if ( ( 0 != $adjust_value ) && ( '' != $adjust_value ) ) {
				$percentage = ( $product_price/100 ) * $adjust_value;

				if ( 'fixed_increase' == $adjust_type ) {
					$product_price += $adjust_value;

				} elseif ( 'fixed_decrease' == $adjust_type ) {
					$product_price = $product_price - $adjust_value;

				} elseif ( 'percentage_increase' == $adjust_type ) {
					$product_price = $product_price + $percentage;

				} elseif ( 'percentage_decrease' == $adjust_type ) {
					$product_price = $product_price - $percentage;

				}
			}
			if ( 0 > $product_price ) {
				$product_price = 0;
			}
			return $product_price;
		}
		
		public function validate_add_cart_item( $passed, $product_id, $qty, $post_data = array() ) {

			if ( ! class_exists( 'Af_Composite_Product_Helper' ) ) {
				return $passed;
			}

			if (!empty($post_data)) {
				$variation_id = isset($post_data['variation_id']) ? $post_data['variation_id'] : 0;
			}

			$data = Af_Composite_Product_Helper::afcpb_add_to_cart_validator( $passed, $product_id, $qty , $variation_id, array(), $post_data);

			if ( is_wp_error( $data ) ) {
				wc_add_notice( $data->get_error_message(), 'error' );
				return false;
			}
			
			
			return $passed;
		}

		public function compute_product_quantities( $items ) {

			$product_quantities = array();
			foreach ($items as $item_key => $item_data) {
				if (!isset($item_data['product_id']) || empty($item_data['product_id'])) {
					continue;
				}
				
				$product_id = $item_data['product_id'];
				$quantity = isset($item_data['quantity']) ? (int) $item_data['quantity'] : 0;
				
				if (isset($item_data['composite_child_products'])) {

					$parent_quantity = isset($item_data['composite_child_products']['parent_quantity']) ? 
						(int) $item_data['composite_child_products']['parent_quantity'] : 0;
					$component_qty = isset($item_data['composite_child_products']['qty']) ? 
						(int) $item_data['composite_child_products']['qty'] : 1;
					
					
					$actual_quantity = $parent_quantity * $component_qty;
					
					if (!isset($product_quantities[ $product_id ])) {
						$product_quantities[ $product_id ] = 0;
					}
					$product_quantities[ $product_id ] += $actual_quantity;
				} else {
					if (!isset($product_quantities[ $product_id ])) {
						$product_quantities[ $product_id ] = 0;
					}
					$product_quantities[ $product_id ] += $quantity;
				}
			}
			return $product_quantities;
		}

		public function compute_product_quantities_of_current_product( $quote_item ) {

			$product_quantities = array();

			if ( ! empty( $quote_item['af_cp_component_product'] ) && is_array( $quote_item['af_cp_component_product'] ) ) {
				foreach ( $quote_item['af_cp_component_product'] as $index => $product_id ) {
					$qty_per_composite = isset( $quote_item['af_cp_component_product_qty'][ $index ] ) ? $quote_item['af_cp_component_product_qty'][ $index ] : 1;
					$total_qty = $qty_per_composite * $quote_item['quantity'];
			
					$product_quantities[ $product_id ] = $total_qty;
				}
				$product_quantities[ $quote_item['product_id'] ] = $quote_item['quantity'];
			} else {
				$product_quantities[ $quote_item['product_id'] ] = $quote_item['quantity'];
			}

			return $product_quantities;
		}
			


		public function afrfq_cp_component_product_price( $parent_id, $comp_key, $price ) {
			$adj_type_arr = (array) get_post_meta( $parent_id , 'af_cp_comp_adj_type' , true );
			$value_arr    = (array) get_post_meta( $parent_id , 'af_cp_comp_adj_price_value' , true );
			$value        = 0;
			$price        = floatval($price);
			$value_keys   = (array) array_keys( $value_arr );
			if ( in_array( $comp_key , $value_keys ) ) {
				$value = $value_arr[ $comp_key ];
				if ( ( '' != $value ) || ( 0 != $value ) ) {
					$adj_type      = '';
					$adj_type_keys = (array) array_keys( $adj_type_arr );
					if ( in_array( $comp_key , $adj_type_keys ) ) {
						$adj_type = $adj_type_arr[ $comp_key ];
					}
					$percentage = ( floatval($price) / 100 ) * floatval( $value );
					if ( 'fixed_increase' == $adj_type ) {
						$price = ( $price + floatval($value) );
					} elseif ( 'fixed_decrease' == $adj_type ) {
						$price = ( $price - floatval($value) );
					} elseif ( 'percentage_increase' == $adj_type ) {
						$price = ( $price + floatval($percentage) );
					} elseif ( 'percentage_decrease' == $adj_type ) {
						$price = ( $price - floatval($percentage) );
					} elseif ('same_price'== $adj_type) {
						$price=$price;
					}
				}
			}
			if ( 0 > $price ) {
				$price = 0;
			}
			return $price;
		}
	}

	new AF_R_F_Q_Addify_Composite_Product_Builder_Compatibilty();
}
