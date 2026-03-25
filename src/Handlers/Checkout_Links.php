<?php
namespace Barn2\Plugin\WC_Product_Options\Handlers;

use Automattic\WooCommerce\Blocks\BlockTypes\Checkout;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\WC_Product_Options\Model\Checkout_Link as Checkout_Link_Model;

use Exception;
use Automattic\WooCommerce\StoreApi\Utilities\CartTokenUtils;
use WC_Cache_Helper;
use WC_Blocks_Utils;

use const WC_SESSION_CACHE_GROUP;

/**
 * Checkout links handler
 *
 * Handles the creation and access of checkout links for sharing carts with customers
 * by leveraging WooCommerce Store API sessions and Checkout Links.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Checkout_Links implements Registerable, Standard_Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_filter( 'the_content', [ $this, 'add_checkout_link' ] );

		add_action( 'woocommerce_load_cart_from_session', [ $this, 'handle_checkout_link_access' ], 5 );
	}

	/**
	 * Adds a checkout link above the cart for shop managers to share the cart with customers.
	 *
	 * This feature requires WooCommerce Store API to be active.
	 * The link includes a session token that allows customers to access the cart without logging in.
	 * Only shop managers can see this link.
	 *
	 * @return void
	 */
	public function add_checkout_link( $content ) {
		if (
			is_admin() ||
			( ! is_cart() && ! WC_Blocks_Utils::has_block_in_page( get_post(), 'woocommerce/cart' ) ) ||
			! class_exists( 'Automattic\WooCommerce\StoreApi\Utilities\CartTokenUtils' ) ||
			! current_user_can( 'manage_woocommerce' ) ||
			empty( WC()->cart ) || WC()->cart->is_empty() ||
			did_action( 'wc_product_options_before_checkout_link' )
		) {
			return $content;
		}

		global $wpdb;

		// Get the current cart session data
		$customer_id  = wc()->session->get_customer_id();
		$session_data = array_intersect_key( wc()->session->get_session( $customer_id, [] ), [ 'cart' => true ] );

		$serialized_data = $session_data['cart'] ?? '';

		// Serialize the session data for storage and comparison
		if ( ! is_serialized( $serialized_data ) ) {
			$serialized_data = maybe_serialize( $serialized_data );
		}

		$existing_link = Checkout_Link_Model::where( 'session_value', $serialized_data )
			->where( 'session_expiry', '>', time() )
			->first();

		$expiration = apply_filters( 'wc_product_options_checkout_link_expiration', time() + 10 * YEAR_IN_SECONDS );

		if ( $existing_link ) {
			// Reuse existing link
			$link_key = $existing_link->getAttribute('session_key');
		} else {
			// Generate a new unique key
			$link_key = wc_rand_hash( 't_', 30 );

			Checkout_Link_Model::create(
				[
					'session_key'    => $link_key,
					'session_value'  => $serialized_data,
					'session_expiry' => $expiration,
					'created_at'     => current_time( 'mysql' ),
					'created_by'     => get_current_user_id(),
				]
			);
		}

		// Also ensure the session exists in WooCommerce's sessions table
		$wc_session_exists = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT session_key FROM {$wpdb->prefix}woocommerce_sessions WHERE session_key = %s",
				$link_key
			)
		);

		if ( ! $wc_session_exists ) {
			// Create the session in WooCommerce's table
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}woocommerce_sessions 
					(session_key, session_value, session_expiry) 
					VALUES (%s, %s, %d)",
					$link_key,
					maybe_serialize( [ 'cart' => $serialized_data ] ),
					$expiration
				)
			);

			// Update cache
			$session_data_unserialized = maybe_unserialize( $serialized_data );
			wp_cache_set(
				WC_Cache_Helper::get_cache_prefix( WC_SESSION_CACHE_GROUP ) . $link_key,
				$session_data_unserialized,
				WC_SESSION_CACHE_GROUP,
				$expiration - time()
			);
		}

		// Generate the checkout link using WooCommerce's token system
		$session_token     = CartTokenUtils::get_cart_token( $link_key );
		$checkout_link_url = add_query_arg( 'session', $session_token, wc_get_checkout_url() );

		do_action( 'wc_product_options_before_checkout_link' );

		ob_start();
		?>

		<div class="wpo-checkout-link woocommerce-info">
			<div class="notice-title">Shop managers only!</div>
			<div class="notice-description">Share this cart with your customers by sending them the checkout link below:</div>
			<div class="copy-wrapper" title="Share this checkout link with your customers.">
				<input type="text" class="copy-url" value="<?php echo esc_url( $checkout_link_url ); ?>" readonly>
				<button type="button" class="copy-button button alt">
					<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="currentColor"><path d="M360-240q-29.7 0-50.85-21.15Q288-282.3 288-312v-480q0-29.7 21.15-50.85Q330.3-864 360-864h384q29.7 0 50.85 21.15Q816-821.7 816-792v480q0 29.7-21.15 50.85Q773.7-240 744-240H360Zm0-72h384v-480H360v480ZM216-96q-29.7 0-50.85-21.15Q144-138.3 144-168v-552h72v552h456v72H216Zm144-216v-480 480Z"/></svg>
					Copy
				</button>
				<div class="copy-tooltip">Copied!</div>
			</div>
		</div>
		<?php

		return ob_get_clean() . $content;
	}

	/**
	 * Handles access to checkout links by recreating the session if it doesn't exist.
	 *
	 * @return void
	 */
	public function handle_checkout_link_access() {
		// Check if this is a checkout link access
		if ( ! filter_input( INPUT_GET, 'session' ) || ! class_exists( 'Automattic\WooCommerce\StoreApi\Utilities\CartTokenUtils' ) ) {
			return;
		}

		$session_token = sanitize_text_field( $_GET['session'] );

		try {
			// Get the payload from the token
			$payload = CartTokenUtils::get_cart_token_payload( $session_token );

			// Get the first session from the user id
			$session_key = $payload['user_id'] ?? 0;

			if ( ! $session_key || strpos( $session_key, 't_' ) !== 0 ) {
				return;
			}

			$customer_id = (int) wc()->session->get_customer_id();

			if ( ! $customer_id ) {
				$customer_id = $session_key;
			}

			$checkout_link = Checkout_Link_Model::find( $session_key );

			global $wpdb;

			// Check if this session key exists in WooCommerce sessions
			$existing_session = WC()->session->get_session( $customer_id );

			// If session doesn't exist, recreate it from our custom table
			if ( ! $existing_session ) {
				$wpdb->query(
					$wpdb->prepare(
						"INSERT INTO {$wpdb->prefix}woocommerce_sessions 
						(session_key, session_value, session_expiry) 
						VALUES (%s, %s, %d)",
						$checkout_link->session_key,
						$checkout_link->session_value,
						$checkout_link->session_expiry
					)
				);

				// Update cache
				$session_data = maybe_unserialize( $checkout_link->session_value );
				wp_cache_set(
					\WC_Cache_Helper::get_cache_prefix( WC_SESSION_CACHE_GROUP ) . $session_key,
					$session_data,
					WC_SESSION_CACHE_GROUP,
					$checkout_link->session_expiry - time()
				);
			} else {
				// If session exists and user is currently logged in,
				// we need to check the current session data:
				// * if it is empty (no cart content), we can load the session data from our table
				// * if it has data, we need to merge the carts

				$current_session_data = wc()->session->get_session( $customer_id, [] );
				$session_cart         = maybe_unserialize( $current_session_data['cart'] );

				// try to merge carts if the current customer session has cart data
				$linked_cart = maybe_unserialize( $checkout_link->session_value );
				$merged_cart = array_merge( (array) $session_cart, (array) $linked_cart );

				WC()->session->set( 'cart', $merged_cart );
				WC()->session->__unset( 'previous_customer_id' );
				WC()->session->save_data();
			}

			++$checkout_link->access_count;
			$checkout_link->session_expiry = time() + 10 * YEAR_IN_SECONDS;
			$checkout_link->last_accessed  = current_time( 'mysql' );
			$checkout_link->save();
		} catch ( Exception $e ) {
			// Token validation failed or other error - silently fail
			return;
		}
	}
}
