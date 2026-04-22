<?php
/**
 * Request a quote Extend Store API.
 *
 * @package woocommerce-request-a-quote
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;

class AF_R_F_Q_Extend_Store_Endpoint {
	/**
	 * Stores Rest Extending instance.
	 *
	 * @var ExtendSchema
	 */
	private static $extend;

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'addify_rfq';

	/**
	 * Bootstraps the class and hooks required data.
	 *
	 * @param ExtendSchema $extend_rest_api An instance of the ExtendSchema class.
	 *
	 * @since 3.1.0
	 */
	public static function init( ExtendSchema $extend_rest_api ) {
		self::$extend = $extend_rest_api;
		self::extend_store();
	}

	/**
	 * Registers the actual data into each endpoint.
	 */
	public static function extend_store() {
		// Register into `cart/items`
		self::$extend->register_endpoint_data(
			array(
				'endpoint'        => CartSchema::IDENTIFIER,
				'namespace'       => self::IDENTIFIER,
				'data_callback'   => array( 'AF_R_F_Q_Extend_Store_Endpoint', 'extend_cart_data' ), 
				'schema_callback' => array( 'AF_R_F_Q_Extend_Store_Endpoint', 'extend_cart_schema' ), 
			)
		);
	}

	/**
	 * Register quote product data into cart endpoint.
	 *
	 * @return array $item_data Registered data or empty array if condition is not satisfied.
	 */
	public static function extend_cart_data() {
		$has_conversion = false;
		$quote_id = null;

		if ( is_null( WC()->cart ) ) {
			return array( 'quote_conversion' => $has_conversion );
		}

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( ! empty( $cart_item['quote_conversion'] ) ) {
				$has_conversion = true;
				$quote_id = $cart_item['quote_id'];
				break;
			}
		}

		return array(
			'quote_conversion' => $has_conversion,
			'quote_id' => $quote_id,
		);
	}

	/**
	 * Register quote product schema into cart endpoint.
	 *
	 * @return array Registered schema.
	 */
	public static function extend_cart_schema() {
		return array(
			'quote_conversion'      => array(
				'description'   => __( 'Is quote converted to cart?', 'addify_b2b' ),
				'type'          => array( 'bool' ),
			),   
			'quote_id'      => array(
				'description'   => __( 'Quote ID.', 'addify_b2b' ),
				'type'          => array( 'int' ),
			),   
		);
	}
}
