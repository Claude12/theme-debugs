<?php

namespace Barn2\Plugin\WC_Product_Options\Util;

use Barn2\Plugin\WC_Product_Options\Model\Group as Group_Model;
use Barn2\Plugin\WC_Product_Options\Model\Option as Option_Model;
use Barn2\Plugin\WC_Product_Options\Dependencies\Illuminate\Database\Eloquent\ModelNotFoundException;
use Barn2\Plugin\WC_Product_Options\Util\Conditional_Logic;

/**
 * Cart utilities.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Cart {

	/**
	 * Handles validation on add to cart.
	 *
	 * @param bool $passed
	 * @param int $product_id
	 * @param int $quantity
	 * @param int|null $variation_id
	 * @param WC_Product_Variation $variation
	 * @param array|null $post_data
	 * @return bool $passed
	 */
	public static function handle_validation( $passed, $product_id, $quantity, $variation_id = null, $variation = null, $post_data = null ): bool {
		/**
		 * Filters whether to allow product option price calculation on a product.
		 *
		 * @param bool $enable Whether to handle validation on the product
		 * @param int $product_id The product ID which is being validated.
		 * @param int $quantity The quantity of the product being validated.
		 * @param int|null $variation_id The variation ID if this is a variation.
		 * @param WC_Product_Variation $variation The variation if this is a variation.
		 */
		$handle_add_to_cart_validation = apply_filters( 'wc_product_options_handle_add_to_cart_validation', true, $passed, $product_id, $quantity, $variation_id, $variation );

		if ( ! $handle_add_to_cart_validation ) {
			return $passed;
		}

		$object_id = is_numeric( $variation_id ) && $variation_id ? $variation_id : $product_id;
		$product   = wc_get_product( $object_id );

		if ( is_null( $product ) || $product === false ) {
			return $passed;
		}

		if ( ! Util::is_allowed_product_type( $product->get_type() ) ) {
			return $passed;
		}

		if ( $post_data ) {
			$submitted_options = $post_data['wpo-option'] ?? [];
		} else {
			$submitted_options = filter_input( INPUT_POST, 'wpo-option', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		}

		// $groups = Group_Model::get_groups_by_product( $product );
		$groups = Util::get_product_groups( $product );

		foreach ( $groups as $group ) {
			$options = $group->options;

			foreach ( $options as $option ) {
				$option_id          = $option->id;
				$prefixed_option_id = 'option-' . $option_id;
				$value              = $submitted_options[ $prefixed_option_id ] ?? null;

				// try {
				// 	$option = Option_Model::findOrFail( $option_id );
				// } catch ( ModelNotFoundException $exception ) {
				// 	continue;
				// }

				$field_class = Util::get_field_class( $option->type );

				if ( ! class_exists( $field_class ) ) {
					/* translators: %s: option name */
					wc_add_notice( esc_html( sprintf( __( 'The option "%s" is not supported.', 'woocommerce-product-options' ), $option->name ) ), 'error' );
					return false;
				}

				if ( is_array( $value ) ) {
					$value = array_filter( $value );
				}

				$field_object   = new $field_class( $option, $product );
				$field_validate = $field_object->validate( $value, $submitted_options );

				if ( is_wp_error( $field_validate ) ) {
					// a field validation error occurred with the submitted value.
					wc_add_notice( $field_validate->get_error_message(), 'error' );
					return false;
				}
			}
		}

		return $passed;
	}

	/**
	 * Add product options data to item inside the cart.
	 *
	 * @param array $cart_item_data
	 * @param int   $product_id
	 * @param int   $variation_id
	 * @param int   $quantity
	 * @param array|null $post_data This is used for the WPT integration.
	 * @return array|null
	 */
	public static function add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity, $post_data = null ) {
		$product = $variation_id === 0 ? wc_get_product( $product_id ) : wc_get_product( $variation_id );

		if ( ! $product || ! Util::is_allowed_product_type( $product->get_type() ) ) {
			return $cart_item_data;
		}

		$add_to_cart       = filter_input( INPUT_POST, 'add-to-cart', FILTER_VALIDATE_INT );
		$options           = filter_input( INPUT_POST, 'wpo-option', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$quantity_array    = filter_input( INPUT_POST, 'quantity', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY );
		$post_product_id   = filter_input( INPUT_POST, 'product_id', FILTER_VALIDATE_INT );
		$post_variation_id = filter_input( INPUT_POST, 'variation_id', FILTER_VALIDATE_INT );

		if ( $post_data ) {
			$add_to_cart       = $post_data['add-to-cart'] ?? $add_to_cart;
			$post_product_id   = $post_data['product_id'] ?? $post_product_id;
			$post_variation_id = $post_data['variation_id'] ?? $post_variation_id;
			$options           = $post_data['wpo-option'] ?? [];
		}

		$product_id_candidates = array_filter( [ $add_to_cart, $post_product_id, $post_variation_id ] );

		if ( ! empty( $quantity_array ) ) {
			if ( ! in_array( $product->get_id(), array_keys( $quantity_array ), true ) ) {
				// The request comes from wbv but the product ID is not in the quantity array.
				// It must be a product added via a "Products" field so skip processing here
				return $cart_item_data;
			}
		} elseif ( ! in_array( $product->get_id(), $product_id_candidates, true ) ) {
			// The product ID being added to the cart does not match the current product.
			// It must be a product added via a "Products" field so skip processing here.
			return $cart_item_data;
		}

		if ( ! $options ) {
			return $cart_item_data;
		}

		foreach ( $options as $prefixed_option_id => $input_value ) {
			$option_id = str_replace( 'option-', '', $prefixed_option_id );

			// Quantity pickers add number inputs with -qty suffix.
			// If option ID is not numeric (e.g. `1234-qty`), skip it here
			// as we handle quantity pickers below when getting cart item data.
			if ( ! is_numeric( $option_id ) ) {
				continue;
			}

			try {
				$option = Option_Model::findOrFail( $option_id );
			} catch ( ModelNotFoundException $exception ) {
				continue;
			}

			if ( $option->type !== 'price_formula' && empty( $option->choices ) ) {
				continue;
			}

			$field_class  = Util::get_field_class( $option->type );
			$field_object = new $field_class( $option, $product );

			if ( Conditional_logic::is_field_hidden( $field_object, $options ) || $field_object->is_variation_attribute_type_option() ) {
				continue;
			}

			$qty_pickers = $options[ "option-{$option_id}-qty" ] ?? null;

			if ( is_array( $qty_pickers ) ) {
				$qty_pickers = array_map( 'intval', $qty_pickers );
			}

			$sanitized_value = $field_object->sanitize( $input_value );

			$item_data = $field_object->get_cart_item_data( $sanitized_value, $product, $quantity, $options, $qty_pickers );

			if ( $item_data ) {
				$cart_item_data['wpo_options'][ $option->id ] = $item_data;
			}
		}

		return $cart_item_data;
	}

	/**
	 * Determine whether to use the product quantity for product add-ons.
	 *
	 * By default, product add-ons do not use the product quantity
	 * unless this filter is overridden to return true.
	 *
	 * @param WC_Product  $product The WooCommerce product instance.
	 * @return bool
	 */
	public static function shall_addons_use_cart_quantity( $product ): bool {
		return apply_filters( 'wc_product_options_addons_use_cart_quantity', false, $product );
	}

	/**
	 * Determines whether to update dependent addon items when the parent item's quantity changes.
	 *
	 * This is a shorthand of the 'wc_product_options_shall_update_addons_with_parent' filter
	 * which is used multiple times in the cart handling process.
	 *
	 * @param string $dependent_cart_item_key The cart item key of the dependent cart item.
	 * @return bool
	 */
	public static function shall_update_addons_with_parent( $dependent_cart_item_key ): bool {
		$dependent_cart_item  = WC()->cart->get_cart_item( $dependent_cart_item_key );
		$parent_cart_item_key = $dependent_cart_item['_wpo_parent_cart_item_key'] ?? null;

		if ( ! $parent_cart_item_key ) {
			return false;
		}

		$parent_cart_item = WC()->cart->get_cart_item( $parent_cart_item_key );

		/**
		 * Filters whether to link addon changes to their parent item.
		 *
		 * By default, dependent addon items will have their own quantities and remove buttons in the cart.
		 * However, if you want to link their quantities to the parent item (so they update automatically when the parent quantity changes),
		 * or you want them to be removed when the parent item is removed, you can enable this filter.
		 *
		 * You activate this behavior site-wide by returning true in the filter as in the following example:
		 *
		 * Example
		 * ```php
		 * add_filter( 'wc_product_options_update_addons_with_parent', '__return_true' );
		 * ```
		 *
		 * Alternatively, you can enable it conditionally based on the cart item or other criteria by using a custom callback function.
		 *
		 * @param bool $update Whether to update dependent addon items.
		 * @param string $dependent_cart_item_key The cart item key of the dependent cart item.
		 * @param array $dependent_cart_item The dependent cart item.
		 * @param string $parent_cart_item_key The key of the parent item.
		 * @param array $parent_cart_item The parent cart item.
		 */
		return apply_filters( 'wc_product_options_update_addons_with_parent', false, $dependent_cart_item_key, $dependent_cart_item, $parent_cart_item_key, $parent_cart_item );
	}
}
