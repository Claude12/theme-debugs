<?php
/**
 * Frontend handler for Convert Quote to Cart
 *
 * @package woocommerce-request-a-quote
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class AF_R_F_Q_Convert_To_Cart_Handler {

	public function __construct() {

		add_action( 'wp', array( $this, 'afrfq_maybe_handle_convert_to_cart' ) );
		// Filter payment gateways
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'afrfq_filter_payment_gateways' ) );

		// Disable cart modifications for quote items (Classic Cart)
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'afrfq_disable_cart_item_quantity' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'afrfq_disable_cart_item_remove' ), 10, 2 );

		//for blocks
		add_filter('woocommerce_store_api_product_quantity_editable', array( $this, 'afrfq_update_cart_quantity_validation_cart_item_editable' ), 10, 3 );        

	
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'afrfq_apply_custom_price_to_cart_item' ) , 999);
		// Update order meta when order is created (works with both classic and blocks checkout)
		add_action( 'woocommerce_checkout_create_order', array( $this, 'afrfq_update_order_quote_meta' ), 10, 2 );

		//for checkout blocks
		add_action( 'woocommerce_store_api_checkout_update_order_meta', array( $this, 'afrfq_update_order_quote_meta_blocks' ), 10, 1 );

		add_action('woocommerce_thankyou', array( $this, 'afrfq_update_order_quote_meta_thankyou_page' ), 10, 1 );

		//for guest user validation that the quote email is same as of user email at checkout
		add_action('woocommerce_checkout_process', array( $this, 'afrfq_place_order_validation_for_guest_user' ));

		// stop add to cart if there is already a quote converted to cart
		add_filter('woocommerce_add_to_cart_validation', array( $this, 'afrfq_handle_add_to_cart_if_cart_contain_quote_products' ), 10, 3);

		// clear cart button if cart contains quote products
		add_action('woocommerce_cart_contents', array( $this, 'afrfq_clear_cart_button_if_cart_contain_quote_products' ));
	
		//for cart blocks
		add_action( 'addify_after_woocommerce/cart-line-items-block', array( $this, 'afrfq_clear_cart_button_if_cart_contain_quote_products' ) );
		
		add_action( 'wp_loaded', array( $this, 'afrfq_handling_clearing_cart_on_button_click' ) );

		add_filter( 'woocommerce_cart_item_price', array( $this, 'afrfq_update_cart_item_price' ), 10, 3 );
	}

	public function afrfq_update_order_quote_meta_thankyou_page( $order_id ) {
		$order = wc_get_order( $order_id );
		
		// Return if order is already handled to prevent duplicate processing
		if ($order->get_meta('order_handled')) {
			return;
		}

		$afrfq_quote_data = $order->get_meta('afrfq_quote_data');
		$quote_id = isset($afrfq_quote_data['quote_id']) ? $afrfq_quote_data['quote_id'] : false;

		if ( $quote_id ) {
			// Add quote fields to order meta
			$af_fields_obj = new AF_R_F_Q_Quote_Fields();
			$quote_fields = (array) $af_fields_obj->afrfq_get_fields_enabled();

			foreach ( $quote_fields as $quote_field ) {
				$field_label = get_post_meta( $quote_field->ID, 'afrfq_field_label', true );
				$field_value = get_post_meta( $quote_id, 'afrfq_field_' . $quote_field->ID, true );
				
				if ( ! empty( $field_value ) ) {
					$order->update_meta_data( $field_label, $field_value );
				}
			}

			//adding messages to order
			$order_note = esc_html__( 'This order has been created from ', 'addify_b2b' ) . '<a href="post.php?post=' . $quote_id . '&action=edit">quote # ' . $quote_id . '</a>' . esc_html__( " via the 'Convert to Cart' link.", 'addify_b2b' );

			$my_account_url = wc_get_page_permalink( 'myaccount' );
			$request_quote_endpoint = 'request-quote';

			// In case endpoint is registered via add_rewrite_endpoint() and can be filtered
			$request_quote_endpoint = apply_filters( 'woocommerce_get_endpoint', $request_quote_endpoint, $request_quote_endpoint, $quote_id );

			$order_note_for_customer = esc_html__( 'This order has been created from ', 'addify_b2b' ) .
				'<a href="' . esc_url( trailingslashit( $my_account_url ) . trailingslashit( $request_quote_endpoint ) . $quote_id ) . '">quote # ' . $quote_id . '</a>';


			//order notes
			$order->add_order_note( $order_note );

			// Add note for customer with quote id
			$order->add_order_note( $order_note_for_customer, 1 );

			//quote notes
			$quote_note = sprintf(
				'%s <a href="%s">%s</a>%s',
				esc_html__('This quote has been converted to', 'addify_b2b'),
				$order->get_edit_order_url(),
				esc_html__(sprintf('order #%s', $order->get_id()), 'addify_b2b'),
				esc_html__(" via the 'Convert to Cart' link.", 'addify_b2b')
			);


			$quote_note_for_customer = esc_html__( 'Your quote has been converted to ', 'addify_b2b' ) .
				'<a href="' . esc_url( $order->get_view_order_url() ) . '">order # ' . esc_html( $order->get_id() ) . '</a>';

			afrfq_add_quote_note($quote_id, $quote_note_for_customer, true);

			afrfq_add_quote_note($quote_id, $quote_note);

			// Link quote to order
			$order->update_meta_data( 'quote_id_for_this_order', $quote_id );

			//updating quote meta for corresponding order
			update_post_meta( $quote_id, 'quote_status', 'af_converted' );
			update_post_meta( $quote_id, 'order_for_this_quote', $order->get_id() );

			$current_user = wp_get_current_user();

			$current_user = isset( $current_user->ID ) ? (string) $current_user->user_login : get_post_meta( $quote_id, 'afrfq_name_field', true );

			update_post_meta( $quote_id, 'converted_by_user', $current_user );
			update_post_meta( $quote_id, 'converted_by', __( 'Customer', 'addify_b2b' ) );

			// Mark order as handled
			$order->update_meta_data('order_handled', true);
			$order->save();
		}
	}

	public function afrfq_update_cart_quantity_validation_cart_item_editable( $value, $product, $cart_item ) {


		if ( isset( $cart_item['quote_conversion'] ) && $cart_item['quote_conversion'] ) {
			return false;
		}
		return $value;
	}

	/**
	 * Filter payment gateways for quote conversion
	 *
	 * @param array $available_gateways Array of available payment gateways.
	 * @return array Filtered payment gateways
	 */
	public function afrfq_filter_payment_gateways( $available_gateways ) {
		// Check if any cart item is from a quote
		$has_quote_items = false;
		if ( ! WC()->cart ) {
			return $available_gateways;
		}
		
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['quote_conversion'] ) && $cart_item['quote_conversion'] ) {
				$has_quote_items = true;
				break;
			}
		}

		// Only filter if cart has quote items
		if ( ! $has_quote_items ) {
			return $available_gateways;
		}

		// Get disabled payment methods
		$disabled_methods = (array) get_option('afrfq_disabled_payment_methods', array());
		if ( empty( $disabled_methods ) ) {
			return $available_gateways;
		}

		// Remove disabled payment methods
		foreach ( $disabled_methods as $method_id ) {
			unset( $available_gateways[ $method_id ] );
		}

		return $available_gateways;
	}

	/**
	 * Update order meta with quote information
	 *
	 * @param WC_Order $order The order object being created
	 * @param array $data The data being used to create the order
	 */
	public function afrfq_update_order_quote_meta( $order, $data ) {

		$quote_id = false;

		// Check cart items for quote conversion
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['quote_conversion'] ) && $cart_item['quote_conversion'] ) {
				$quote_id = $cart_item['quote_id'];
				break;
			}
		}

		// If this was a quote conversion, update the order and quote meta
		if ( $quote_id ) {
			$order->update_meta_data('afrfq_quote_data', array( 'quote_id' => $quote_id ));
		}
	}

	/**
	 * Handle quote meta for blocks checkout
	 *
	 * @param WC_Order $order The order object being created
	 */
	public function afrfq_update_order_quote_meta_blocks( $order ) {
		// Reuse the same logic by calling the main method
		// Pass empty array as data since we don't need it
		$this->afrfq_update_order_quote_meta( $order, array() );
	}

	/**
	 * Disable quantity input for quote items in cart
	 *
	 * @param string $product_quantity HTML of quantity input
	 * @param string $cart_item_key Cart item key
	 * @param array $cart_item Cart item data
	 * @return string Modified quantity HTML
	 */
	public function afrfq_disable_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
		// If this is a quote item, return quantity as text instead of input
		if ( isset( $cart_item['quote_conversion'] ) && $cart_item['quote_conversion'] ) {
			return sprintf( '<span class="quantity">%s</span>', $cart_item['quantity'] );
		}

		return $product_quantity;
	}

	/**
	 * Remove the ability to remove quote items from cart
	 *
	 * @param string $link HTML for remove link
	 * @param string $cart_item_key Cart item key
	 * @return string Empty string for quote items, original link otherwise
	 */
	public function afrfq_disable_cart_item_remove( $link, $cart_item_key ) {
		$cart_item = WC()->cart->get_cart_item( $cart_item_key );
		
		// If this is a quote item, return empty string to remove the delete link
		if ( isset( $cart_item['quote_conversion'] ) && $cart_item['quote_conversion'] ) {
			return '';
		}

		return $link;
	}


	public function afrfq_maybe_handle_convert_to_cart() {

		$afrfq_notice   = filter_input( INPUT_GET, 'afrfq_notice', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$redirect_to    = filter_input( INPUT_GET, 'redirect_to', FILTER_SANITIZE_URL );
		$afrfq_action   = filter_input( INPUT_GET, 'afrfq_action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$quote_id       = absint( filter_input( INPUT_GET, 'quote_id', FILTER_SANITIZE_NUMBER_INT ) );
		$token          = filter_input( INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$quote_status = get_post_meta( $quote_id, 'quote_status', true );


		if ('af_converted' == $quote_status) {
			wc_add_notice( __( 'This quote has already been converted to an order.', 'addify_b2b' ), 'error' );
			return;
		}

		// Handle notices and auto-redirect after login
		if ( is_account_page() && $afrfq_notice ) {
			if ( 'login_required' === $afrfq_notice ) {
				wc_add_notice( __( 'Please log in to your account to convert this quote to cart.', 'addify_b2b' ), 'notice' );
				// If just logged in, trigger conversion by redirecting back to the quote-to-cart link
				if ( is_user_logged_in() && $redirect_to ) {
					wp_redirect( esc_url_raw( $redirect_to ) );
					exit;
				}
				return;
			} elseif ( 'wrong_user' === $afrfq_notice ) {
				wc_add_notice( __( 'You are logged in, but not as the user who placed this quote. Please log in with the correct account.', 'addify_b2b' ), 'error' );
				return;
			}
		}
		
		if ( 'convert_to_cart' !== $afrfq_action ) {
			return;
		}

		if ( ! $quote_id || ! $token ) {
			wc_add_notice( __( 'Invalid quote conversion request.', 'addify_b2b' ), 'error' );
			wp_redirect( wc_get_cart_url() );
			exit;
		}

		$saved_token = get_post_meta( $quote_id, 'afrfq_convert_to_cart_token', true );
		if ( ! $saved_token || $saved_token !== $token ) {
			wc_add_notice( __( 'Invalid or expired conversion link.', 'addify_b2b' ), 'error' );
			wp_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit;
		}

		// Check if link expiration is enabled
		$expiration_enabled = 'expires_on_date' === get_post_meta($quote_id, 'afrfq_cart_link_restriction_type', true);
		$link_creation_date = get_post_meta($quote_id, 'afrfq_cart_link_creation_date', true);

		// Check expiration if enabled
		if ($expiration_enabled && $link_creation_date) {
			$expiry_days = get_post_meta($quote_id, 'afrfq_cart_link_expiry_time', true);
			if ('' ==  $expiry_days) {
			 $expiry_days = 7;
			}
			$expiry_date = strtotime($link_creation_date . ' + ' . $expiry_days . ' days');

			if (current_time('timestamp') > $expiry_date) {
				WC()->cart->empty_cart();
				wc_add_notice( __( 'This quote conversion link has expired.', 'addify_b2b' ), 'error' );
				wp_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}
		}

		$customer_id = get_post_meta( $quote_id, '_customer_user', true );

		if ('guest' != $customer_id) {
			// Registered user quote: require login as that user
			if ( ! is_user_logged_in() ) {
				// Not logged in at all
				$redirect_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( $_SERVER['REQUEST_URI'] ) : home_url( '/' );
				$login_url = wc_get_page_permalink( 'myaccount' );
				$login_url = add_query_arg( array(
					'redirect_to'  => urlencode( $redirect_url ),
					'afrfq_notice' => 'login_required',
				), $login_url );
				wp_redirect( $login_url );
				exit;
			} elseif ( get_current_user_id() != $customer_id ) {
				// Logged in, but as the wrong user
				$redirect_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( $_SERVER['REQUEST_URI'] ) : home_url( '/' );
				$login_url = wc_get_page_permalink( 'myaccount' );
				$login_url = add_query_arg( array(
					'redirect_to' => urlencode( $redirect_url ),
					'afrfq_notice' => 'wrong_user',
				), $login_url );
				wp_redirect( $login_url );
				exit;
			}
		} else {
			$quote_fields_obj = new AF_R_F_Q_Quote_Fields();
			$quote_fields     = (array) $quote_fields_obj->quote_fields;

			$email_field_id = '';

			foreach ( $quote_fields as $key => $field ) {

				$field_id   = $field->ID;
				$field_type = $field->post_name;

				if ('email' == $field_type) {
					$email_field_id = $field_id;
					break;
				}
			}

			// Guest user conversion - localize script data
			if ('' != $email_field_id) {
				$quote_email = get_post_meta($quote_id, 'afrfq_field_' . $email_field_id, true);

				wc()->session->set('afrfq_quote_to_cart_data', array(
					'quote_id' => $quote_id, 
					'quote_email' => $quote_email,
				));
			}
			
			
		}

		// Get quote contents
		$quote_contents = get_post_meta( $quote_id, 'quote_contents', true );
		if ( empty( $quote_contents ) || ! is_array( $quote_contents ) ) {
			wc_add_notice( __( 'No quote items found.', 'addify_b2b' ), 'error' );
			wp_redirect( wc_get_cart_url() );
			exit;
		}

		foreach ( $quote_contents as $quote_item_key => $quote_item ) {

			if ( isset( $quote_item['data'] ) ) {
				$product = $quote_item['data'];
			} else {
				continue;
			}

			if ( ! is_object( $product ) ) {
				continue;
			}

			// incase product type is custom and now the plugin which registers that product type is not active then it simply empties the quote
			if (is_object($product) && $product instanceof __PHP_Incomplete_Class) {
				$contain_invalid_product = true;
				break;
			}
		}

		if ($contain_invalid_product) {
			wc_add_notice( __( 'Some product data could not be loaded. Please verify that all required components are available.', 'addify_b2b' ), 'error' );
			wp_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit;
		}

		// Optionally: Empty the cart before adding quote items
		WC()->cart->empty_cart();

		foreach ( $quote_contents as $quote_item_key => $quote_item ) {
			$product_id = isset( $quote_item['product_id'] ) ? absint( $quote_item['product_id'] ) : 0;
			$quantity   = isset( $quote_item['quantity'] ) ? absint( $quote_item['quantity'] ) : 1;
			$variation_id = isset( $quote_item['variation_id'] ) ? absint( $quote_item['variation_id'] ) : 0;
			$variation = isset( $quote_item['variation'] ) && is_array( $quote_item['variation'] ) ? $quote_item['variation'] : array();
			$cart_item_data = array(
				'quote_conversion' => true,
				'quote_id' => $quote_id,
			);

			if ( isset( $quote_item['data'] ) ) {
				$product = $quote_item['data'];
			} else {
				continue;
			}

			if ( ! is_object( $product ) ) {
				continue;
			}
			

			$price         = $product->get_price();
			$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;

			if ( 0 < $offered_price ) {
				$cart_item_data['afrfq_offered_price'] = floatval( $offered_price );
			} else {
				$cart_item_data['afrfq_offered_price'] = floatval( $price );
			}

			// handling composite product -- component price is added up in main product
			if (isset($quote_item['composite_child_products']) && !empty($quote_item['composite_child_products'])) {
				$cart_item_data['afrfq_offered_price'] = 0;
			}


			if ( ! empty( $quote_item['addons'] ) && class_exists( 'WC_Product_Addons_Helper' ) ) {
				foreach ( $quote_item['addons'] as $addon ) {
					$key           = $addon['name'];
					$price_type    = $addon['price_type'];
					$product_price = $cart_item_data['afrfq_offered_price'];
		
					/*
					 * For percentage based price type we want
					 * to show the calculated price instead of
					 * the price of the add-on itself and in this
					 * case its not a price but a percentage.
					 * Also if the product price is zero, then there
					 * is nothing to calculate for percentage so
					 * don't show any price.
					 */
					if ( $addon['price'] && 'percentage_based' === $price_type && 0 != $product_price ) {
						$addon_price = $product_price * ( $addon['price'] / 100 );
					} else {
						$addon_price = $addon['price'];
					}
					$price = html_entity_decode(
						wp_strip_all_tags( wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $addon_price, $product ) ) ),
						ENT_QUOTES,
						get_bloginfo( 'charset' )
					);
		
					if ( $addon['price'] && apply_filters( 'woocommerce_addons_add_price_to_name', true ) ) {
						$key .= ' (' . $price . ')';
					}
		
					if ( 'custom_price' === $addon['field_type'] ) {
						$addon['value'] = $addon['price'];
					}               

					$data = array(
						'name'       => isset($addon['name']) ? $addon['name'] : '',
						'value'      => isset($addon['value']) ? $addon['value'] : '',
						'price'      => isset($addon['price']) ? $addon['price'] : '',
						'field_name' => isset($addon['field_name']) ? $addon['field_name'] : '',
						'field_type' => isset($addon['field_type']) ? $addon['field_type'] : '',
						'id'         => isset($addon['id']) ? $addon['id'] : '',
						'price_type' => isset($addon['price_type']) ? $addon['price_type'] : '',
					);

					$cart_item_data['addons'][] = $data;
					
				}
			}

			//adding price calculator meta to cart item
			if (isset($quote_item['addf_prc_calculated_price']) && !empty($quote_item['addf_prc_calculated_price'])) {
				if ( isset( $quote_item['addf_prc_calculated_price'] ) ) {
					$cart_item_data['addf_prc_calculated_price'] = $quote_item['addf_prc_calculated_price'];
				}
				if ( isset( $quote_item['addf_prc_unit_particulars'] ) ) {
					$cart_item_data['addf_prc_unit_particulars'] = $quote_item['addf_prc_unit_particulars'];
				}
				if ( isset( $quote_item['addf_prc_weight_input'] ) ) {
					$cart_item_data['addf_prc_weight_input'] = $quote_item['addf_prc_weight_input'];
				}
				if ( isset( $quote_item['addf_prc_price_calculated_weight'] ) ) {
					$cart_item_data['addf_prc_price_calculated_weight'] = $quote_item['addf_prc_price_calculated_weight'];
				}
				if ( isset( $quote_item['addf_prc_calc_weight_convert_value'] ) ) {
					$cart_item_data['addf_prc_calc_weight_convert_value'] = $quote_item['addf_prc_calc_weight_convert_value'];
				}
				if ( isset( $quote_item['addf_prc_weight_unit_input'] ) ) {
					$cart_item_data['addf_prc_weight_unit_input'] = $quote_item['addf_prc_weight_unit_input'];
				}
				if ( isset( $quote_item['addf_prc_weight_ttl_unit_input'] ) ) {
					$cart_item_data['addf_prc_weight_ttl_unit_input'] = $quote_item['addf_prc_weight_ttl_unit_input'];
				}
				if ( isset( $quote_item['addf_prc_unit_name'] ) ) {
					$cart_item_data['addf_prc_unit_name'] = $quote_item['addf_prc_unit_name'];
				}
				if ( isset( $quote_item['addf_prc_current_rule_id'] ) ) {
					$cart_item_data['addf_prc_current_rule_id'] = $quote_item['addf_prc_current_rule_id'];
				}
			}

			if ( $product_id ) {
				WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation, $cart_item_data );
			}
		}

		wc_add_notice( __( 'Your quote items have been added to the cart.', 'addify_b2b' ), 'success' );
		wp_redirect( wc_get_checkout_url() );
		exit;
	}

	/**
	 * Apply custom price to cart items that have an offered price
	 *
	 * @param object $cart WC_Cart object.
	 */
	public function afrfq_apply_custom_price_to_cart_item( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		// Avoid infinite loops
		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
			return;
		}

		// Loop through cart items
		foreach ( $cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['afrfq_offered_price'] ) ) {
				$cart_item['data']->set_price( $cart_item['afrfq_offered_price'] );
			}
		}
	}


	/**
	 * Validating user email before placing order
	 *
	 */
	public function afrfq_place_order_validation_for_guest_user() {

		$quote_fields_obj = new AF_R_F_Q_Quote_Fields();
		$quote_fields     = (array) $quote_fields_obj->quote_fields;

		$email_field_id = '';

		foreach ( $quote_fields as $key => $field ) {

			$field_id   = $field->ID;
			$field_type = $field->post_name;

			if ('email' == $field_type) {
				$email_field_id = $field_id;
				break;
			}
		}

		
		// Only validate guest users
		if (is_user_logged_in()) {
			return;
		}

		if ('' == $email_field_id) {
			return;
		}
	
		// Check if cart contains quote conversion items
		$has_quote_conversion = false;
		$quote_id = null;
		
		foreach (WC()->cart->get_cart() as $cart_item) {
			if (!empty($cart_item['quote_conversion'])) {
				$has_quote_conversion = true;
				$quote_id = $cart_item['quote_id'];
				break;
			}
		}
	
		if ($has_quote_conversion && $quote_id) {
			// Get quote email from post meta


			if (!isset($_POST['woocommerce-process-checkout-nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['woocommerce-process-checkout-nonce']), 'woocommerce-process_checkout')) {
				return; // Exit if nonce fails
			}
			$quote_email = get_post_meta($quote_id, 'afrfq_field_' . $email_field_id, true);
			$submitted_email = isset($_POST['billing_email']) ? sanitize_text_field($_POST['billing_email']) : '';
	
			if (!empty($quote_email) && $quote_email !== $submitted_email) {
				wc_add_notice(
					__('The email address must match the one used for the quote.', 'addify_b2b'),
					'error'
				);
			}
		}
	}

	/**
	 * Validating add to cart if cart contain quote products
	 *
	 */
	public function afrfq_handle_add_to_cart_if_cart_contain_quote_products( $passed, $product_id, $quantity ) {
		foreach (WC()->cart->get_cart() as $cart_item) {
			if (isset($cart_item['quote_conversion'])) {
				wc_add_notice(
					__('You cannot add items to cart while cart contains quote products.', 'addify_b2b'),
					'error'
				);
				return false; 
			}
		}
		return $passed;
	}

	/**
	 * Clear cart button if cart contain quote products
	 *
	 */
	public function afrfq_clear_cart_button_if_cart_contain_quote_products() {
		if ( WC()->cart) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if (isset($cart_item['quote_conversion'])) {
					wp_nonce_field( 'afrfq_clear_cart_nonce_action', 'afrfq_clear_cart_nonce' );
					?>
					<button name="afrfq_clear_cart" class="afrfq_clear_cart"><?php echo esc_html__('Clear Cart', 'addify_b2b'); ?></button>
					<?php
					break;
				}
			}
		}
	}
	
	/**
	 * Handling clear cart on button click
	 *
	 */
	public function afrfq_handling_clearing_cart_on_button_click() {
		
		if (!isset($_POST['afrfq_clear_cart_nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['afrfq_clear_cart_nonce']), 'afrfq_clear_cart_nonce_action')) {
			return; 
		}

		if ( isset( $_POST['afrfq_clear_cart'] ) ) {
			WC()->cart?WC()->cart->empty_cart():'';
		}
	}

	/**
	 * Setting offered price as price in cart mainly for composite products
	 *
	 */
	public function afrfq_update_cart_item_price( $price_html, $cart_item, $cart_item_key ) {
		if ( isset( $cart_item['afrfq_offered_price'] ) ) {
			$price_html = wc_price( $cart_item['afrfq_offered_price'] );
		}
		return $price_html;
	}
}

new AF_R_F_Q_Convert_To_Cart_Handler();
