<?php

namespace Barn2\Plugin\WC_Product_Options\Handlers;

use Barn2\Plugin\WC_Product_Options\Util\Util;
use Barn2\Plugin\WC_Product_Options\Util\Price as Price_Util;
use Barn2\Plugin\WC_Product_Options\Util\Cart as Cart_Util;
use Barn2\Plugin\WC_Product_Options\Model\Option as Option_Model;
use Barn2\Plugin\WC_Product_Options\Dependencies\Illuminate\Database\Eloquent\ModelNotFoundException;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Service\Standard_Service as ServiceStandard_Service;

use WC_Order;
use WC_Meta_Data;
use WC_Order_Item_Product;

/**
 * Handles adding and displaying the product options item data.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Item_Data implements Registerable, ServiceStandard_Service {

	/**
	 * Register hooks and filters.
	 */
	public function register() {
		add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 10, 4 );
		add_filter( 'woocommerce_get_item_data', [ $this, 'display_cart_item_data' ], 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'add_order_item_data' ], 10, 4 );
		add_filter( 'woocommerce_cart_item_class', [ $this, 'cart_item_class' ], 10, 2 );

		add_filter( 'woocommerce_order_again_cart_item_data', [ $this, 'order_again_cart_item_data' ], 10, 3 );

		add_filter( 'woocommerce_order_item_display_meta_key', [ $this, 'format_item_data_key' ], 10, 3 );
		add_filter( 'woocommerce_order_item_display_meta_value', [ $this, 'format_item_data_value' ], 10, 3 );
	}

	/**
	 * Add product options data to item inside the cart.
	 *
	 * @param array $cart_item_data
	 * @param int   $product_id
	 * @param int   $variation_id
	 * @param int   $quantity
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity ): array {
		return Cart_Util::add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity );
	}

	/**
	 * Display product options data in the cart and checkout.
	 *
	 * @param array $item_data
	 * @param array $cart_item
	 *
	 * @return array
	 */
	public function display_cart_item_data( $item_data, $cart_item ): array {
		if ( ! isset( $cart_item['wpo_options'] ) ) {
			return $item_data;
		}

		$wpo_options = $cart_item['wpo_options'];

		foreach ( $wpo_options as $option_data ) {
			do_action( 'wc_product_options_before_cart_item_data_option', $item_data, $cart_item, $option_data, $wpo_options );

			$item_data[] = $this->format_item_data( $option_data, $cart_item['data'], $cart_item['quantity'], $cart_item );

			do_action( 'wc_product_options_after_cart_item_data_option', $item_data, $cart_item, $option_data, $wpo_options );
		}

		/**
		 * Filter the item data displayed in the cart and checkout.
		 *
		 * @param array $item_data The array of metadata attached to each cart item
		 * @param array $cart_item The cart item
		 */
		return apply_filters( 'wc_product_options_get_item_data', $item_data, $cart_item );
	}

	/**
	 * Add product options data to order item.
	 *
	 * @param WC_Order_Item_Product $item
	 * @param string                $cart_item_key
	 * @param array                 $cart_item
	 * @param WC_Order              $order
	 */
	public function add_order_item_data( $item, $cart_item_key, $cart_item, $order ): void {

		if ( ! isset( $cart_item['wpo_options'] ) ) {
			return;
		}

		$wpo_options = $cart_item['wpo_options'];
		$item->add_meta_data( '_wpo_options', $wpo_options );
		$files = [];

		do_action( 'wc_product_options_before_order_item_data', $item, $cart_item, $order, $wpo_options );

		foreach ( $cart_item['wpo_options'] as $option_data ) {
			if ( $option_data['type'] === 'file_upload' && ! empty( $option_data['value'] ) ) {
				$files = array_merge( $files, $option_data['value'] );
			}

			do_action( 'wc_product_options_before_order_item_data_option', $item, $cart_item, $order, $option_data, $wpo_options );

			$item_data = $this->format_item_data( $option_data, $item->get_product(), $item->get_quantity(), $cart_item, true );

			$item->add_meta_data( $item_data['key'], $item_data['value'] );

			do_action( 'wc_product_options_after_order_item_data_option', $item, $cart_item, $order, $option_data, $wpo_options );
		}

		do_action( 'wc_product_options_after_order_item_data', $item, $cart_item, $order, $wpo_options );

		if ( ! empty( $files ) ) {
			// remove from unlinked files option
			$unlinked_files = get_option( 'wpo_unlinked_files', [] );

			foreach ( $files as $file ) {
				if ( ( $key = array_search( $file, $unlinked_files, true ) ) !== false ) {
					unset( $unlinked_files[ $key ] );
				}
			}

			update_option( 'wpo_unlinked_files', $unlinked_files );

			// add to current files order option
			$current_files = $order->get_meta( '_wpo_files' );

			if ( ! empty( $current_files ) ) {
				$files = array_merge( $current_files, $files );
			}

			$order->update_meta_data( '_wpo_files', $files );
			$order->save();
		}
	}

	/**
	 * Format the item data for display in cart/checkout/order.
	 *
	 * @param array $option_data
	 * @param WC_Product $product
	 * @param int $quantity
	 * @param array $cart_item
	 * @param bool $skip_filter Whether to skip filtering the option and choice labels.
	 * @return string
	 */
	private function format_item_data( $option_data, $product, $quantity, $cart_item, $skip_filter = false ): array {
		$choice_data       = $option_data['choice_data'];
		$option_object     = Option_Model::find( $option_data['option_id'] );
		$formatted_strings = [];

		foreach ( $choice_data as $choice ) {
			if ( isset( $choice['quantity'] ) && $choice['quantity'] === 0 ) {
				continue;
			}

			$choice_label = $choice['label'];
			$choice_index = $choice['index'] ?? 0;

			if ( ! $skip_filter && ! Util::option_has_user_value( $option_object, $product ) ) {
				/**
				 * Filter the choice label before formatting.
				 *
				 * @param string $choice_label
				 * @param Option_Model $option_object
				 * @param string $context
				 * @param int|null $choice_index
				 */
				$choice_label = apply_filters( 'wc_product_options_get_output_string', $choice_label, $option_object, 'choice_label', $choice_index );
			}

			if ( isset( $choice['quantity'] ) ) {
				$quantity_picker = $choice['quantity'];
				$choice_label    = sprintf( '<strong>%d x</strong> %s', $quantity_picker, $choice_label );
			}

			if ( ! isset( $choice['pricing'] ) ) {
				$formatted_string = $choice_label;
			} else {
				if ( isset( $cart_item['wholesale_pro']['is_wholesale_price'] ) && $cart_item['wholesale_pro']['is_wholesale_price'] === true ) {
					$product_price = $cart_item['wholesale_pro']['wholesale_price'];
				} else {
					$product_price = $product->is_on_sale() ? $product->get_sale_price() : $product->get_regular_price();
				}

				$hide_prices = $choice['pricing']['hide'] ?? false;

				$formatted_price  = Price_Util::get_price_html( Price_Util::calculate_option_display_price( $choice['pricing'], $product, $quantity, 'cart', $product_price ) );
				$formatted_string = $choice['pricing']['amount'] === 0 || $hide_prices ? $choice_label : sprintf( '%s <strong>%s</strong>', $choice_label, $formatted_price );
			}

			$formatted_strings[] = $formatted_string;
		}

		/**
		 * Filter the formatted item data string.
		 *
		 * Used in the cart, checkout, orders, and emails. Default ' | ' separator.
		 *
		 * @param string $seperator
		 */
		$seperator = apply_filters( 'wc_product_options_multiple_cart_item_data_seperator', ' | ' );

		$key = $option_data['name'];

		if ( ! $skip_filter ) {
			/**
			 * Filter the option name before formatting.
			 *
			 * @param string $option_name
			 * @param Option_Model $option_object
			 * @param string $context
			 */
			$key = apply_filters( 'wc_product_options_get_output_string', $key, $option_object, 'option_name' );
		}

		return [
			'key'   => $key,
			'value' => implode( $seperator, $formatted_strings ),
		];
	}

	/**
	 * Add product options data to cart item when ordering again.
	 *
	 * @param array $cart_item_data
	 * @param WC_Order_Item_Product $item
	 * @param WC_Order $order
	 * @return array $cart_item_data
	 */
	public function order_again_cart_item_data( $cart_item_data, $item, $order ) {
		$options = $item->get_meta( '_wpo_options' );

		if ( empty( $options ) ) {
			return $cart_item_data;
		}

		/**
		 * Whether to add product options data to cart item when ordering again.
		 *
		 * Note that this filter is used for WC Subscriptions to determine whether to prevent recalculating the renewals.
		 *
		 * @param bool $enabled
		 * @param WC_Order_Item_Product $item
		 * @param WC_Order $order
		 */
		$is_add_order_again_item_data_enabled = apply_filters( 'wc_product_options_add_order_again_item_data', true, $item, $order );

		if ( ! $is_add_order_again_item_data_enabled ) {
			return $cart_item_data;
		}

		foreach ( $options as $option_data ) {
			try {
				$option = Option_Model::findOrFail( $option_data['option_id'] );
			} catch ( ModelNotFoundException $exception ) {
				continue;
			}

			$field_class  = Util::get_field_class( $option->type );
			$field_object = new $field_class( $option, $item->get_product() );

			$item_data = $field_object->get_cart_item_data( $option_data['value'], $item->get_product(), $item->get_quantity(), $options );

			if ( $item_data ) {
				$cart_item_data['wpo_options'][ $option->id ] = $item_data;
			}
		}

		return $cart_item_data;
	}

	/**
	 * Add custom class to cart item row if it has WPO options.
	 *
	 * @param string $classname
	 * @param array  $cart_item
	 * @return string
	 */
	public function cart_item_class( $classname, $cart_item ) {
		if ( isset( $cart_item['wpo_options'] ) ) {
			$classname .= ' has-wpo-cart-item-data';
		}

		return $classname;
	}

	/**
	 * Format order item data key for display.
	 *
	 * @param string $key
	 * @param WC_Meta_Data $meta
	 * @param WC_Order_Item_Product $item
	 * @return string
	 */
	public function format_item_data_key( $key, $meta, $item ) {
		$_wpo_options = $item->get_meta( '_wpo_options' );

		if ( ! $_wpo_options ) {
			return $key;
		}

		$option_data = null;

		foreach ( $_wpo_options as $option ) {
			if ( $option['name'] === $key ) {
				$option_data = $option;
				break;
			}
		}

		if ( ! $option_data ) {
			return $key;
		}

		try {
			$option_object = Option_Model::findOrFail( $option_data['option_id'] );
		} catch ( ModelNotFoundException $exception ) {
			return $key;
		}

		/**
		 * Filter the option name before formatting.
		 *
		 * @param string $option_name
		 * @param Option_Model $option_object
		 * @param string $context
		 */
		$key = apply_filters( 'wc_product_options_get_output_string', $key, $option_object, 'option_name' );

		return wc_clean( $key );
	}

	/**
	 * Format order item data value for display.
	 *
	 * @param string $value
	 * @param WC_Meta_Data $meta
	 * @param WC_Order_Item_Product $item
	 * @return string
	 */
	public function format_item_data_value( $value, $meta, $item ) {
		if ( ! method_exists( $item, 'get_product' ) ) {
			return $value;
		}

		$product      = $item->get_product();
		$_wpo_options = $item->get_meta( '_wpo_options' );

		if ( ! $product || ! $_wpo_options ) {
			return $value;
		}

		$option_data = null;

		foreach ( $_wpo_options as $option ) {
			if ( $option['name'] === $meta->get_data()['key'] ) {
				$option_data = $option;
				break;
			}
		}

		if ( ! $option_data ) {
			return $value;
		}

		try {
			$option_object = Option_Model::findOrFail( $option_data['option_id'] );
		} catch ( ModelNotFoundException $exception ) {
			return $value;
		}

		if ( Util::option_has_user_value( $option_object, $product ) ) {
			return $value;
		}

		$choice_data = array_values(
			array_filter(
				$option_data['choice_data'],
				function ( $choice ) use ( $value ) {
					return strpos( $value, $choice['label'] ) !== false;
				}
			)
		);

		foreach ( $choice_data as $choice ) {
			$choice_label = $choice['label'];
			$choice_index = $choice['index'] ?? 0;

			/**
			 * Filter the choice label before formatting.
			 *
			 * @param string $choice_label
			 * @param Option_Model $option_object
			 * @param string $context
			 * @param int|null $choice_index
			 */
			$choice_label = apply_filters( 'wc_product_options_get_output_string', $choice_label, $option_object, 'choice_label', $choice_index );
			$value        = str_replace( $choice['label'], $choice_label, $value );
		}

		return wc_clean( $value );
	}
}
