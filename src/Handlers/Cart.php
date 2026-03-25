<?php
namespace Barn2\Plugin\WC_Product_Options\Handlers;

use Barn2\Plugin\WC_Product_Options\Model\Option as Option_Model;
use Barn2\Plugin\WC_Product_Options\Util\Price as Price_Util;
use Barn2\Plugin\WC_Product_Options\Util\Cart as Cart_Util;
use Barn2\Plugin\WC_Product_Options\Util\Util;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Service\Standard_Service;

use WC_Product;
use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;

/**
 * Cart Handler
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Cart implements Registerable, Standard_Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'option_price_calculation' ], 11, 1 );
		add_action( 'woocommerce_before_mini_cart', [ $this, 'force_mini_cart_calculation' ], 1 );

		add_filter( 'woocommerce_cart_item_name', [ $this, 'filter_cart_item_name' ], 10, 2 );
		add_filter( 'woocommerce_cart_item_class', [ $this, 'filter_cart_item_class' ], 10, 2 );
		add_filter( 'woocommerce_mini_cart_item_class', [ $this, 'filter_cart_item_class' ], 10, 2 );
		add_filter( 'wp_kses_allowed_html', [ $this, 'allow_svg_in_cart_item_names' ], 10, 2 );

		add_filter( 'wp_loaded', [ $this, 'update_dependent_item_quantities' ], 19 );
		add_filter( 'woocommerce_cart_item_remove_link', [ $this, 'filter_cart_item_remove_link' ], 10, 2 );
		add_filter( 'woocommerce_cart_item_quantity', [ $this, 'filter_cart_item_quantity' ], 10, 3 );
	}

	/**
	 * Calculates the product addon pricing in the cart.
	 *
	 * @param WC_Cart $cart
	 */
	public function option_price_calculation( $cart ) {
		// Prevent multiple recalculations in a single request
		// which might happen if WooCommerce or other third-party components
		// need to recalculate the cart multiple times
		if ( did_action( 'woocommerce_before_calculate_totals' ) > 1 ) {
			return;
		}

		do_action( 'wc_product_options_before_cart_items_calculation', $cart );

		foreach ( $cart->get_cart_contents() as $cart_item_key => $cart_item ) {
			do_action( 'wc_product_options_before_cart_item_calculation', $cart, $cart_item );

			if ( ! isset( $cart_item['wpo_options'] ) ) {
				continue;
			}

			$product = $cart_item['data'];

			if ( ! Util::is_allowed_product_type( $product->get_type() ) ) {
				continue;
			}

			/**
			 * Filters whether to allow product option price calculation on a product.
			 *
			 * @param bool $enable Whether to allow product option price calculation on a product.
			 * @param \WC_Product $product The product which is being calculated.
			 * @param array|null $cart_item The cart item if this is calculated in the cart.
			 */
			$calculation_enabled = apply_filters( 'wc_product_options_enable_price_calculation', true, $product, $cart_item );

			if ( ! $calculation_enabled ) {
				continue;
			}

			$product_price = $product->get_price();

			/**
			 * The following conditional clause was part of the initial release of the plugin.
			 * According to the semantic meaning of "regular price" and "sale price" in WooCommerce,
			 * it seems more appropriate to always use the product's current price for calculations,
			 * because WooCommerce already handles the condition of whether the product is on sale or not.
			 * Therefore, this clause has been commented out for now and replaced with a direct retrieval of the product price.
			 */

			// if ( isset( $cart_item['wholesale_pro']['is_wholesale_price'] ) && $cart_item['wholesale_pro']['is_wholesale_price'] === true ) {
			// 	$product_price = $product->get_price();
			// } else {
			// 	$product_price = $product->is_on_sale() ? $product->get_sale_price() : $product->get_regular_price();
			// }

			$options_price = $this->calculate_options_price( $cart_item, $product_price );

			// if $option_data was changed, update it in the cart
			$cart->cart_contents[ $cart_item_key ] = $cart_item;
			$cart->set_session();

			// Calculate the final price
			$final_price = Price_Util::get_product_price_exclusion_status( $product ) ? $options_price : (float) $product_price + $options_price;

			/**
			 * Filters the condition determing whether negative prices are allowed.
			 *
			 * @param bool $allow_negative_prices Whether negative prices are allowed.
			 * @param \WC_Product $product The product which is being calculated.
			 * @param array $cart_item The cart item.
			 */
			$allow_negative_prices = apply_filters( 'wc_product_options_allow_negative_prices', false, $product, $cart_item );

			if ( ! $allow_negative_prices ) {
				$final_price = max( 0, $final_price );
			}

			// Set the final price of the cart item product
			$product->set_price( $final_price );

			do_action( 'wc_product_options_after_cart_item_calculation', $cart, $cart_item );
		}

		do_action( 'wc_product_options_after_cart_items_calculation', $cart );
	}

	/**
	 * Workaround - https://github.com/woocommerce/woocommerce/issues/26422
	 */
	public function force_mini_cart_calculation() {
		if ( is_cart() || is_checkout() || ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return;
		}

		// if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
		// define( 'WOOCOMMERCE_CART', true );
		// }

		WC()->cart->calculate_totals();
	}

	/**
	 * Update the quantities of dependent cart items when the parent item's quantity is changed.
	 *
	 * @return void
	 */
	public function update_dependent_item_quantities() {
		if ( ! isset( $_REQUEST['update_cart'] ) || ! isset( $_REQUEST['cart'] ) ) {
			// This is not a cart update request
			return;
		}

		$nonce_value = wc_get_var( $_REQUEST['woocommerce-cart-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

		if ( ! wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) {
			// The nonce verification failed
			return;
		}

		$cart          = WC()->cart;
		$cart_contents = $cart->get_cart();

		// Get the submitted quantities from the POST data
		$cart_totals = isset( $_POST['cart'] ) ? $_POST['cart'] : [];

		// Process each submitted quantity change
		foreach ( $cart_totals as $parent_cart_item_key => $values ) {
			if ( ! isset( $cart_contents[ $parent_cart_item_key ] ) ) {
				continue;
			}

			$parent_cart_item = $cart_contents[ $parent_cart_item_key ];

			$new_quantity = isset( $values['qty'] ) ? intval( $values['qty'] ) : 0;
			$old_quantity = $parent_cart_item['quantity'];

			// Check if quantity actually changed
			if ( $new_quantity === $old_quantity ) {
				continue;
			}

			// Find dependent cart items for this parent
			foreach ( $cart_contents as $dependent_item_key => $dependent_item ) {
				if ( isset( $dependent_item['_wpo_parent_cart_item_key'] ) &&
					$dependent_item['_wpo_parent_cart_item_key'] === $parent_cart_item_key ) {

					if ( ! Cart_Util::shall_update_addons_with_parent( $dependent_item_key ) || ! Cart_Util::shall_addons_use_cart_quantity( $dependent_item['data'] ) ) {
						continue;
					}

					// Calculate the original ratio
					$dependent_old_qty = $dependent_item['quantity'];
					$original_ratio    = $dependent_old_qty / $old_quantity;

					// Calculate new quantity maintaining the ratio
					$new_dependent_qty = round( $new_quantity * $original_ratio );

					// Modify the POST data to include the updated dependent quantity
					if ( $new_dependent_qty > 0 ) {
						$_POST['cart'][ $dependent_item_key ]['qty'] = $new_dependent_qty;
					}
				}
			}
		}
	}

	/**
	 * Calculates the price of the product options.
	 *
	 * @param array $cart_item
	 * @param float $product_price
	 * @return float
	 */
	private function calculate_options_price( &$cart_item, $product_price ): float {
		$options_price  = 0;
		$options_data   = $cart_item['wpo_options'];
		$product        = $cart_item['data'];
		$quantity       = $cart_item['quantity'];
		$is_order_again = filter_input( INPUT_GET, 'order_again', FILTER_VALIDATE_INT ) > 0;

		foreach ( $options_data as $option_id => $option_data ) {
			if ( ! isset( $option_data['choice_data'] ) ) {
				continue;
			}

			$price_change = 0;
			$option_type  = $option_data['type'];

			foreach ( $option_data['choice_data'] as $choice_index => $choice_data ) {
				if ( ! isset( $choice_data['pricing'] ) ) {
					continue;
				}

				if ( $option_type === 'price_formula' && ( $is_order_again || Option_Model::formula_includes_product_quantity( $option_id ) ) ) {
					$choice_data['pricing']['amount']                           = Price_Util::evaluate_cart_item_formula( $option_data['option_id'], $cart_item ) ?? 0;
					$options_data[ $option_id ]['choice_data'][ $choice_index ] = $choice_data;
				}

				$price_change += Price_Util::calculate_option_cart_price( $choice_data, $product, $quantity, $product_price );
			}

			$options_price += $price_change;
		}

		$cart_item['wpo_options'] = $options_data;

		return $options_price;
	}

	/**
	 * Filters the cart item remove link.
	 *
	 * Product addons are part of the main product, so removing them individually is not allowed.
	 *
	 * @param string $link
	 * @param string $dependent_cart_item_key
	 * @return string
	 */
	public function filter_cart_item_remove_link( $link, $dependent_cart_item_key ): string {
		$dependent_cart_item = WC()->cart->get_cart_item( $dependent_cart_item_key );

		if ( Cart_Util::shall_update_addons_with_parent( $dependent_cart_item_key ) ) {
			$link = apply_filters( 'wc_product_options_dependent_item_remove_link', '', $dependent_cart_item_key, $dependent_cart_item );
		}

		return $link;
	}

	/**
	 * Filters the cart item quantity display.
	 *
	 * Product addons are part of the main product, so their quantity is disabled.
	 *
	 * @param string $quantity_html
	 * @param string $dependent_cart_item_key
	 * @param array $dependent_cart_item
	 * @return string
	 */
	public function filter_cart_item_quantity( $quantity_html, $dependent_cart_item_key, $dependent_cart_item ): string {
		if ( Cart_Util::shall_update_addons_with_parent( $dependent_cart_item_key ) ) {
			$dependent_quantity_html = sprintf(
				'<span class="wpo-cart-addon-quantity">%s</span>',
				$dependent_cart_item['quantity']
			);
			$quantity_html           = apply_filters( 'wc_product_options_dependent_item_quantity', $dependent_quantity_html, $dependent_cart_item_key, $dependent_cart_item );
		}

		return $quantity_html;
	}

	/**
	 * Filters the cart item quantity display.
	 *
	 * Product addons are part of the main product, so their quantity is disabled.
	 *
	 * @param string $name The product name displayed in the cart item.
	 * @param array $dependent_cart_item
	 * @return string
	 */
	public function filter_cart_item_name( $name, $dependent_cart_item ): string {
		if ( isset( $dependent_cart_item['_wpo_parent_cart_item_key'] ) ) {
			$dep_icon = '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor"><path d="m576-172.78-69.09-69.09L623.48-359h-402.7v-476.22h98V-457h304.7L506.91-573.57l69.66-69.08L811.22-408 576-172.78Z"/></svg>';
			$name     = "$dep_icon $name";
		}

		return $name;
	}

	/**
	 * Filters the cart item class.
	 *
	 * Product addons are part of the main product, so we add a custom class for styling.
	 *
	 * @param string $classname
	 * @param array $dependent_cart_item
	 * @return string
	 */
	public function filter_cart_item_class( $classname, $dependent_cart_item ): string {
		if ( isset( $dependent_cart_item['_wpo_parent_cart_item_key'] ) ) {
			$wpo_cart_classname = 'wpo-addon-';

			if ( doing_filter( 'woocommerce_mini_cart_item_class' ) ) {
				$wpo_cart_classname .= 'mini_cart_item';
			} else {
				$wpo_cart_classname .= 'cart-item';
			}

			$classname .= ' ' . $wpo_cart_classname;
		}

		return $classname;
	}

	/**
	 * Allows SVG in cart item names.
	 *
	 * @param array  $allowedtags
	 * @param string $context
	 * @return array
	 */
	public function allow_svg_in_cart_item_names( $allowedtags, $context ): array {
		if ( $context === 'post' ) {
			$allowedtags['svg']  = [
				'width'       => true,
				'height'      => true,
				'viewbox'     => true,
				'xmlns'       => true,
				'fill'        => true,
				'aria-hidden' => true,
				'role'        => true,
			];
			$allowedtags['path'] = [
				'd'    => true,
				'fill' => true,
			];
		}

		return $allowedtags;
	}
}
