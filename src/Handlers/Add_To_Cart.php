<?php

namespace Barn2\Plugin\WC_Product_Options\Handlers;

use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\WC_Product_Options\Util\Util;
use Barn2\Plugin\WC_Product_Options\Util\Cart as Cart_Util;
use Barn2\Plugin\WC_Product_Options\Util\Price as Price_Util;
use Barn2\Plugin\WC_Product_Options\Model\Group as Group_Model;
use WC_Product;

use function Barn2\Plugin\WC_Product_Options\wpo;

/**
 * Add to Cart Handler
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Add_To_Cart implements Registerable, Standard_Service {

	/**
	 * The current product listing.
	 *
	 * @var array
	 */
	private $products = [];

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		// Set priority to 20 to ensure compatibility with WQM
		add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'handle_validation' ], 20, 5 );

		add_filter( 'woocommerce_product_add_to_cart_url', [ $this, 'loop_add_to_cart_url' ], 20, 2 );
		add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'loop_add_to_cart_text' ], 20, 2 );
		add_filter( 'woocommerce_product_add_to_cart_aria_describedby', [ $this, 'loop_add_to_cart_text' ], 20, 2 );
		add_filter( 'woocommerce_product_add_to_cart_description', [ $this, 'loop_add_to_cart_text' ], 20, 2 );
		add_filter( 'woocommerce_product_supports', [ $this, 'loop_ajax_add_to_cart_support' ], 20, 3 );

		add_filter( 'wc_add_to_cart_message_html', [ $this, 'add_to_cart_message_html' ], 10, 3 );

		add_action( 'woocommerce_add_to_cart', [ $this, 'push_products_to_cart_bottom' ], PHP_INT_MAX, 6 );
		add_action( 'woocommerce_cart_item_removed', [ $this, 'on_parent_product_removed' ], 10, 2 );

		/**
		 * Filters the priority for the `woocommerce_get_price_html` filter used to modify the main product price display.
		 *
		 * Many components modify the price HTML, so we use the highest priority to ensure our modification runs last
		 * but also allow developers to adjust this if needed.
		 *
		 * @param int $priority The priority for the filter.
		 */
		$get_price_html_priority = apply_filters( 'wc_product_options_get_price_html_priority', PHP_INT_MAX );
		add_filter( 'woocommerce_get_price_html', [ $this, 'loop_price_display' ], $get_price_html_priority, 2 );
	}

	/**
	 * Handles validation on add to cart.
	 *
	 * @param bool $passed
	 * @param int $product_id
	 * @param int $quantity
	 * @param int|null $variation_id
	 * @param WC_Product_Variation $variation
	 * @return bool $passed
	 */
	public function handle_validation( $passed, $product_id, $quantity, $variation_id = null, $variation = null ): bool {
		return Cart_Util::handle_validation( $passed, $product_id, $quantity, $variation_id, $variation );
	}

	/**
	 * Add to cart URL.
	 *
	 * @param string $url URL.
	 * @param WC_Product $product Product.
	 * @return string
	 */
	public function loop_add_to_cart_url( string $url, WC_Product $product ): string {
		if ( did_action( 'wc_product_options_before_option_container' ) > did_action( 'wc_product_options_after_option_container' ) ) {
			// Don't modify the URL while inside the product options container.
			return $url;
		}

		if ( ! Util::is_allowed_product_type( $product->get_type() ) ) {
			return $url;
		}

		$groups = Util::get_product_groups( $product );

		if ( empty( $groups ) ) {
			return $url;
		}

		$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

		return get_permalink( $product_id );
	}

	/**
	 * Add to cart text.
	 *
	 * @param string $text Text.
	 * @param WC_Product $product Product.
	 * @return string
	 */
	public function loop_add_to_cart_text( string $text, WC_Product $product ): string {
		if ( ! Util::is_allowed_product_type( $product->get_type() ) || ! $product->is_purchasable() ) {
			return $text;
		}

		$groups = Util::get_product_groups( $product );

		if ( empty( $groups ) ) {
			return $text;
		}

		switch ( true ) {
			case doing_filter( 'woocommerce_product_add_to_cart_text' ):
				return esc_html__( 'Select options', 'woocommerce-product-options' );
			case doing_filter( 'woocommerce_product_add_to_cart_aria_describedby' ):
				return __( 'This product has options that may be chosen on the product page', 'woocommerce-product-options' );
			case doing_filter( 'woocommerce_product_add_to_cart_description' ):
				// translators: %s: product name
				return sprintf( __( 'Select options for &ldquo;%s&rdquo;', 'woocommerce' ), $product->get_name() );
		}

		return esc_html__( 'Select options', 'woocommerce-product-options' );
	}

	/**
	 * Remove AJAX add to cart support if the product has options.
	 *
	 * @param bool $supported
	 * @param string $feature
	 * @param WC_Product $product
	 * @return bool
	 */
	public function loop_ajax_add_to_cart_support( bool $supported, string $feature, WC_Product $product ): bool {
		if ( 'ajax_add_to_cart' !== $feature ) {
			return $supported;
		}

		if ( $supported === false ) {
			return $supported;
		}

		$groups = Util::get_product_groups( $product );

		if ( ! empty( $groups ) ) {
			$supported = false;
		}

		return $supported;
	}

	/**
	 * Filter the add to cart message to include the additional products from all the Products option type fields.
	 *
	 * @param  string $message
	 * @param  array $products
	 * @param  bool $show_qty
	 * @return string
	 */
	public function add_to_cart_message_html( $message, $products, $show_qty ) {
		$new_message = '';
		$wpo_options = $_POST['wpo-option'] ?? [];

		foreach ( $products as $product_id => $qty ) {
			$addon_list = [];

			if ( isset( $_POST['cart_data'] ) && isset( $_POST['cart_data'][ 'p' . $product_id ] ) ) {
				$product_type_options = $_POST['cart_data'][ 'p' . $product_id ]['wpo-option'] ?? [];
				$list_template        = '<br><span>%s</span><br><br>';
				$list_separator       = ',<br></span><span>';
			} else {
				$product_type_options = $wpo_options;
				$list_template        = '<ul><li>%s</li></ul>';
				$list_separator       = '</li><li>';
			}

			$product_type_options = array_filter(
				$product_type_options,
				function ( $option ) {
					return isset( $option['product_ids'] );
				}
			);

			foreach ( $product_type_options as $prefixed_option_id => $value ) {
				$quantities  = $wpo_options[ $prefixed_option_id . '-qty' ] ?? [];
				$product_ids = $value['product_ids'];

				if ( is_string( $product_ids ) ) {
					$product_ids = [ $product_ids ];
				}

				$linted_product_ids = $product_ids;

				if ( array_keys( $product_ids ) === range( 0, count( $product_ids ) - 1 ) ) {
					$linted_product_ids = [];
					// the data comes from a dropdown, so we need to explode it
					// to get the product and, possibly, variation IDs
					foreach ( $product_ids as $pid_string ) {
						$new_product_ids                             = explode( ',', $pid_string );
						$linted_product_ids[ $new_product_ids[0] ][] = $new_product_ids[1] ?? 0;
					}
				}

				$linted_product_ids = array_filter( $linted_product_ids );

				foreach ( $linted_product_ids as $addon_product_id => $variations ) {
					$addon_product = wc_get_product( $addon_product_id );

					if ( ! $addon_product ) {
						continue;
					}

					if ( is_array( $quantities ) ) {
						$product_qty = intval( $quantities[ $addon_product->get_id() ] ?? 1 );
					} else {
						$product_qty = intval( $quantities ?? 1 );
					}

					if ( Cart_Util::shall_addons_use_cart_quantity( $addon_product->get_id() ) ) {
						$product_qty *= $qty;
					}

					if ( is_string( $variations ) ) {
						$variations = explode( ',', $variations );

						if ( count( $variations ) > 1 ) {
							$variations = array_slice( $variations, 1 );
						}
					}

					$addon_name = '';

					if ( ! empty( $variations ) ) {
						$variations = array_values( array_unique( array_filter( array_map( 'absint', $variations ) ) ) );

						foreach ( $variations as $addon_variation_id ) {
							$addon_variation = wc_get_product( $addon_variation_id );

							if ( ! $addon_variation && ! $addon_product ) {
								continue;
							}

							if ( ! $addon_product ) {
								$addon_product_id = $addon_variation->get_parent_id();
								$addon_product    = wc_get_product( $addon_product_id );
							}

							$addon      = $addon_variation ?: $addon_product;
							$addon_name = $addon->get_name();

							if ( is_array( $quantities ) ) {
								$product_qty = intval( $quantities[ $addon->get_id() ] ?? 1 );
							} else {
								$product_qty = intval( $quantities ?? 1 );
							}

							if ( Cart_Util::shall_addons_use_cart_quantity( $addon_product->get_id() ) ) {
								$product_qty *= $qty;
							}

							$i = 0;

							while ( $addon_name && $i < $product_qty ) {
								$addon_list[] = $addon_name;
								++$i;
							}
						}
					} else {
						if ( ! $addon_product ) {
							continue;
						}

						$addon_name = $addon_product->get_name();

						$i = 0;

						while ( $addon_name && $i < $product_qty ) {
							$addon_list[] = $addon_name;
							++$i;
						}
					}
				}
			}

			if ( ! empty( $addon_list ) ) {
				$addon_list = array_count_values( $addon_list );
				$addon_list = array_map(
					function ( $count, $name ) {
						return sprintf( __( '%1$s &times; %2$s', 'woocommerce-product-options' ), $count, $name );
					},
					$addon_list,
					array_keys( $addon_list )
				);

				$addon_list = empty( $addon_list )
					? ''
					: sprintf( $list_template, implode( $list_separator, $addon_list ) );
			}

			if ( ! empty( $addon_list ) ) {
				$product_title = apply_filters( 'woocommerce_add_to_cart_qty_html', ( $qty > 1 ? absint( $qty ) . ' &times; ' : '' ), $product_id ) . apply_filters( 'woocommerce_add_to_cart_item_name_in_quotes', sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce-product-options' ), wp_strip_all_tags( get_the_title( $product_id ) ) ), $product_id );

				$new_message .= sprintf(
					// translators: %1$s: link to the shop/cart, %2$s: product title, %3$s: list of additional products
					__( '%1$s has been added to your cart, together with the following additional products: %2$s', 'woocommerce-product-options' ),
					$product_title,
					$addon_list
				);
			}
		}

		if ( $new_message ) {
			// get the cart link from the notice message...
			preg_match( '/<a.*?<\/a>/', $message, $matches );
			$message_link = $matches ? $matches[0] : '';

			// ...and add it back with the products from the Products option type
			$message = sprintf( '%1$s%2$s', $new_message, $message_link );
		}

		return $message;
	}

	/**
	 * Push products added with options to the bottom of the cart.
	 *
	 * Products added with a Products option type field are added during the cart validation process,
	 * which results in them appearing above the main product when that is finally added to the cart.
	 * This method removes and re-adds those products so they appear at the bottom of the cart.
	 *
	 * @param string $cart_item_key
	 * @param int    $product_id
	 * @param int    $quantity
	 * @param int    $variation_id
	 * @param array  $variation
	 * @param array  $cart_item_data
	 * @return void
	 */
	public function push_products_to_cart_bottom( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ): void {
		$cart_item_keys             = $GLOBALS['wpo_last_added_cart_item_keys'] ?? [];
		$add_to_cart                = filter_input( INPUT_POST, 'add-to-cart', FILTER_VALIDATE_INT );
		$list_addons_before_product = apply_filters( 'wc_product_options_list_addons_before_main_product', false );

		if ( empty( $cart_item_keys ) || $add_to_cart !== $product_id || $list_addons_before_product ) {
			return;
		}

		$cart_items = array_combine(
			array_column( $cart_item_keys, 'key' ),
			array_map(
				function ( $item ) {
					return [
						'cart_item' => WC()->cart->get_cart_item( $item['key'] ),
						'qty'       => $item['qty'],
					];
				},
				$cart_item_keys
			)
		);

		// First, remove from the cart all the items that were just added
		foreach ( $cart_items as $key => $item ) {
			if ( $item['cart_item']['quantity'] > $item['qty'] ) {
				WC()->cart->set_quantity( $key, $item['cart_item']['quantity'] - $item['qty'], false );
			} else {
				WC()->cart->remove_cart_item( $key );
			}
		}

		// Then, re-add them so they go to the bottom of the cart
		// Also pass the main product's cart item key as cart item data to maintain the relationship
		foreach ( $cart_items as $key => $item ) {
			WC()->cart->add_to_cart( $item['cart_item']['product_id'], $item['qty'], $item['cart_item']['variation_id'], [], [ '_wpo_parent_cart_item_key' => $cart_item_key ] );
		}

		// Finally, clear the global
		unset( $GLOBALS['wpo_last_added_cart_item_keys'] );
	}

	/**
	 * Remove addon products when the main product is removed from the cart.
	 *
	 * @param string $cart_item_key
	 * @param WC_Cart $cart
	 * @return void
	 */
	public function on_parent_product_removed( $cart_item_key, $cart ): void {
		foreach ( $cart->get_cart() as $dependent_cart_item_key => $dependent_cart_item ) {
			if ( isset( $dependent_cart_item['_wpo_parent_cart_item_key'] ) && $dependent_cart_item['_wpo_parent_cart_item_key'] === $cart_item_key ) {
				if ( Cart_Util::shall_update_addons_with_parent( $dependent_cart_item_key ) ) {
					// Remove the addon product from the cart
					$cart->remove_cart_item( $dependent_cart_item_key );
				} else {
					// Remove the parent reference so the addon product remains in the cart independently
					unset( $cart->cart_contents[ $dependent_cart_item_key ]['_wpo_parent_cart_item_key'] );
				}
			}
		}
	}

	/**
	 * Outputs the main product price according to the price display settings.
	 *
	 * The price display format is determined by the price display setting of the groups assigned to the product.
	 * When more than one group is assigned to the product, the price display format with the highest priority is used.
	 * The priority order, from lowest to highest, is 'default', 'extend' and 'hide'.
	 *
	 * @param string $price_html The original price HTML.
	 * @param \WC_Product $product The product.
	 * @return string The modified price HTML.
	 */
	public function loop_price_display( $price_html, $product ) {
		if ( strpos( $price_html, 'wpo-price-display' ) !== false ) {
			// Already modified
			return $price_html;
		}

		if ( did_action( 'wc_product_options_before_option_container' ) > did_action( 'wc_product_options_after_option_container' ) ) {
			return $price_html;
		}

		/**
		 * Filters whether to use the product price display in loops.
		 *
		 * @param bool $use_price_display Whether to use the product price display.
		 * @param WC_Product $product The product.
		 */
		$use_price_display = apply_filters( 'wc_product_options_use_loop_price_display', true, $product );

		if ( ! $use_price_display ) {
			return $price_html;
		}

		// if ( $product->is_type( 'variation' ) ) {
		// 	// variation cannot be assigned groups directly, get the parent product
		// 	$parent_id = $product->get_parent_id();
		// 	$parent    = wc_get_product( $parent_id );

		// 	if ( ! $parent ) {
		// 		return $price_html;
		// 	}

		// 	$product = $parent;
		// }

		$price_display_format = Price_Util::get_price_display_format( Util::get_product_groups( $product ) );

		if ( (float) $product->get_price() === 0 ) {
			/**
			 * Filters the price display format when the product price is zero.
			 *
			 * This filter allows to override the price display format when the product price is zero,
			 * regardless of the group settings.
			 *
			 * @param string $price_display_format The price display format.
			 * @param WC_Product $product The product.
			 */
			$price_display_format = apply_filters( 'wc_product_options_price_display_when_zero', $price_display_format, $product );
		}

		return sprintf(
			$price_display_format,
			$price_html
		);
	}

}
