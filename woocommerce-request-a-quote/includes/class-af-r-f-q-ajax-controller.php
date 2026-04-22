<?php
/**
 * Addify Add to Quote
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * AF_R_F_Q_Quote class.
 */
class AF_R_F_Q_Ajax_Controller {
	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $quote_contents = array();

	/**
	 * Constructor for the AF_R_F_Q_Ajax_Controller class. Loads quote contents.
	 */
	public function __construct() {

		add_action( 'wp_ajax_add_to_quote', array( $this, 'afrfq_add_to_quote_callback_function' ) );
		add_action( 'wp_ajax_nopriv_add_to_quote', array( $this, 'afrfq_add_to_quote_callback_function' ) );

		add_action( 'wp_ajax_add_to_quote_single', array( $this, 'afrfq_add_to_quote_single_callback_function' ) );
		add_action( 'wp_ajax_nopriv_add_to_quote_single', array( $this, 'afrfq_add_to_quote_single_callback_function' ) );

		add_action( 'wp_ajax_add_to_quote_single_vari', array( $this, 'afrfq_add_to_quote_single_vari_callback_function' ) );
		add_action( 'wp_ajax_nopriv_add_to_quote_single_vari', array( $this, 'afrfq_add_to_quote_single_vari_callback_function' ) );

		add_action( 'wp_ajax_remove_quote_item', array( $this, 'afrfq_remove_quote_item_callback_function' ) );
		add_action( 'wp_ajax_nopriv_remove_quote_item', array( $this, 'afrfq_remove_quote_item_callback_function' ) );

		add_action( 'wp_ajax_update_quote_items', array( $this, 'afrfq_update_quote_items' ) );
		add_action( 'wp_ajax_nopriv_update_quote_items', array( $this, 'afrfq_update_quote_items' ) );

		add_action( 'wp_ajax_check_availability_of_quote', array( $this, 'check_availability_of_quote' ) );
		add_action( 'wp_ajax_nopriv_check_availability_of_quote', array( $this, 'check_availability_of_quote' ) );

		add_action( 'wp_ajax_cache_quote_fields', array( $this, 'cache_quote_fields' ) );
		add_action( 'wp_ajax_nopriv_cache_quote_fields', array( $this, 'cache_quote_fields' ) );
		// Admin Ajax Hooks.
		add_action( 'wp_ajax_af_r_f_q_search_products', array( $this, 'af_r_f_q_search_products' ) );
		add_action( 'wp_ajax_afrfqsearch_product_and_variation', array( $this, 'afrfqsearch_product_and_variation' ) );
		add_action( 'wp_ajax_afrfq_insert_product_row', array( $this, 'afrfq_insert_product_row' ) );
		add_action( 'wp_ajax_afrfq_delete_quote_item', array( $this, 'afrfq_delete_quote_item' ) );
		add_action( 'wp_ajax_afrfq_search_users', array( $this, 'afrfq_search_users' ) );
		
		// pdf download at my account page
		add_action( 'wp_ajax_af_rfq_download_quote_pdf_account_page', array( $this, 'af_rfq_download_quote_pdf_account_page' ) );   

		//quote submission using popup
		add_action( 'wp_ajax_afrfq_submit_quote_via_popup', array( $this, 'afrfq_submit_quote_via_popup' ) );
		add_action( 'wp_ajax_nopriv_afrfq_submit_quote_via_popup', array( $this, 'afrfq_submit_quote_via_popup' ) );

		//add quote note
		add_action( 'wp_ajax_afrfq_add_quote_note', array( $this, 'afrfq_add_quote_note_cb' ) );
		// delete quote note
		add_action( 'wp_ajax_afrfq_delete_quote_note', array( $this, 'afrfq_delete_quote_note' ) );

		// clearing blocks cart on button click in case of cart is converted from quote
		add_action( 'wp_ajax_afrfq_clear_cart', array( $this, 'afrfq_clear_cart' ) );
		add_action( 'wp_ajax_nopriv_afrfq_clear_cart', array( $this, 'afrfq_clear_cart' ) );

		// verfiying adding to quote
		add_filter( 'addify_add_to_quote_validation', array( $this, 'validate_add_cart_item' ), 999, 4 );

		add_filter('addify_max_product_quantity_in_quote', array( $this, 'addify_max_product_quantity_in_quote' ));
	}

	/**
	 * AF_R_F_Q_Quote.
	 */
	public function cache_quote_fields() {


		$nonce = isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {
			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		if ( isset( $_POST['form_data'] ) ) {
			parse_str( sanitize_meta( '', wp_unslash( $_POST['form_data'] ), '' ), $form_data );
		}

		$quote_fields_obj = new AF_R_F_Q_Quote_Fields();
		$quote_fields     = (array) $quote_fields_obj->quote_fields;
		$fields_data      = array();

		foreach ( $quote_fields as $key => $value ) {

			$field_id         = $value->ID;
			$afrfq_field_name = get_post_meta( $field_id, 'afrfq_field_name', true );

			if ( isset( $form_data[ $afrfq_field_name ] ) ) {
				$fields_data[ $afrfq_field_name ] = $form_data[ $afrfq_field_name ];
			}
		}

		wc()->session->set( 'quote_fields_data', $fields_data );
	}

	/**
	 * Search users by Ajax.
	 */
	public function check_availability_of_quote() {

		$nonce = isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {
			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		$variation_id = isset( $_POST['variation_id'] ) ? sanitize_text_field( wp_unslash( $_POST['variation_id'] ) ) : '';

		if ( empty( $variation_id ) ) {
			die();
		}

		$variation = wc_get_product( $variation_id );

		$variation_avaiable = get_post_meta( $variation_id, 'disable_rfq', true );

		ob_start();

		if ( in_array( $variation_avaiable, array( 'disabled_swap', 'hide_swap' ) ) ) : ?>

			<button type="submit" class="single_add_to_cart_button afrfq_single_page_atc button alt">
				<?php echo esc_html( $variation->single_add_to_cart_text() ); ?>
			</button>

			<?php
		endif;

		$button = ob_get_clean();

		wp_send_json(
			array(
				'display' => $variation_avaiable,
				'button'  => $button,
			)
		);
	}


	/**
	 * Search users by Ajax.
	 */
	public function afrfq_search_users() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {
			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		$pro = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';

		$data_array  = array();
		$users       = new WP_User_Query(
			array(
				'search'         => '*' . esc_attr( $pro ) . '*',
				'search_columns' => array(
					'user_login',
					'user_nicename',
					'user_email',
					'user_url',
				),
			)
		);
		$users_found = $users->get_results();

		if ( ! empty( $users_found ) ) {
			foreach ( $users_found as $user ) {
				$title        = $user->display_name . '(' . $user->user_email . ')';
				$data_array[] = array( $user->ID, $title ); // array( User ID, User name and email ).
			}
		}

		wp_send_json( $data_array );
		die();
	}

	/**
	 * AF_R_F_Q_Quote.
	 */
	public function afrfq_delete_quote_item() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {

			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		$quote_item_key = isset( $_POST['quote_key'] ) ? sanitize_text_field( wp_unslash( $_POST['quote_key'] ) ) : '';
		$post_id        = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : 0;

		$post = get_post( intval( $post_id ) );

		if ( ! $post ) {
			die( esc_html__( 'Quote Item not found', 'addify_b2b' ) );
		}

		$quote_contents = get_post_meta( $post->ID, 'quote_contents', true );

		if ( isset( $quote_contents[ $quote_item_key ] ) ) {
			unset( $quote_contents[ $quote_item_key ] );
		}

		update_post_meta( $post->ID, 'quote_contents', $quote_contents );

		$af_quote       = new AF_R_F_Q_Quote( $quote_contents );
		$quote_contents = get_post_meta( $post->ID, 'quote_contents', true );

		$quote_totals = $af_quote->get_calculated_totals( $quote_contents, $post->ID );

		ob_start();
		include AFRFQ_PLUGIN_DIR . 'admin/meta-boxes/quotes/quote-details-table.php';
		$quote_table = ob_get_clean();

		wp_send_json(
			array(
				'quote-details-table' => $quote_table,
			)
		);
		// wp_send_json_success( $quote_contents );.
		die();
	}

	/**
	 * AF_R_F_Q_Quote.
	 */
	public function afrfq_insert_product_row() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {

			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		$product_id = isset( $_POST['product_id'] ) ? intval( wp_unslash( $_POST['product_id'] ) ) : 0;
		$quantity   = isset( $_POST['quantity'] ) ? intval( wp_unslash( $_POST['quantity'] ) ) : 0;
		$post_id    = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : 0;

		$post = get_post( intval( $post_id ) );

		if ( ! $post ) {
			die( 'post not found' );
		}

		$form_data['product_id'] = $product_id;
		$form_data['quantity']   = $quantity;

		$product = wc_get_product( $product_id );

		$quote_contents = (array) get_post_meta( $post->ID, 'quote_contents', true );
		$af_quote       = new AF_R_F_Q_Quote( $quote_contents );

		$quote_contents = $af_quote->add_to_quote( $form_data, $product_id, $quantity, 0, array(), array(), true );

		if ( is_array( $quote_contents ) ) {

			$quote_totals = $af_quote->get_calculated_totals( $quote_contents, $post_id );

			update_post_meta( $post_id, 'quote_totals', wp_json_encode( $quote_totals ) );
			update_post_meta( $post->ID, 'quote_contents', $quote_contents );

			ob_start();
			include AFRFQ_PLUGIN_DIR . 'admin/meta-boxes/quotes/quote-details-table.php';
			$quote_table = ob_get_clean();

			wp_send_json(
				array(
					'success'             => true,
					'quote-details-table' => $quote_table,
				)
			);

			die();

		} else {

			wp_send_json(
				array(
					'success' => false,
					/* translators: %s: Product name */
					'message' => sprintf( esc_html__( 'Quote is not permitted for “%s”.', 'addify_b2b' ), $product->get_name() ),
				)
			);
			die();
		}
	}

	/**
	 * AF_R_F_Q_Quote.
	 */
	public function afrfqsearch_product_and_variation() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {

			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		$pro = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';

		$data_array = array();
		$args       = array(
			'post_type'   => array( 'product', 'product_variation' ),
			'post_status' => 'publish',
			'numberposts' => -1,
			's'           => $pro,
		);
		$pros       = get_posts( $args );

		if ( ! empty( $pros ) ) {

			foreach ( $pros as $proo ) {

				$title        = ( mb_strlen( $proo->post_title ) > 50 ) ? mb_substr( $proo->post_title, 0, 49 ) . '...' : $proo->post_title;
				$data_array[] = array( $proo->ID, $title ); // array( Post ID, Post Title ).
			}
		}

		wp_send_json( $data_array );

		die();
	}

	/**
	 * AF_R_F_Q_Quote.
	 */
	public function af_r_f_q_search_products() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {
			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		$pro = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';

		$data_array = array();
		$args       = array(
			'post_type'   => 'product',
			'post_status' => 'publish',
			'numberposts' => -1,
			's'           => $pro,
		);
		$pros       = get_posts( $args );

		if ( ! empty( $pros ) ) {

			foreach ( $pros as $proo ) {

				$title        = ( mb_strlen( $proo->post_title ) > 50 ) ? mb_substr( $proo->post_title, 0, 49 ) . '...' : $proo->post_title;
				$data_array[] = array( $proo->ID, $title ); // array( Post ID, Post Title ).
			}
		}

		wp_send_json( $data_array );

		die();
	}

	/**
	 * Ajax add to quote controller.
	 */
	public function afrfq_update_quote_items() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {

			die( 'Failed Ajax security check!' );
		}

		if ( isset( $_POST['form_data'] ) ) {
			parse_str( sanitize_meta( '', wp_unslash( $_POST['form_data'] ), '' ), $form_data );
		} else {
			$form_data = '';
		}

		$form_type = isset( $_POST['form_type'] ) ? sanitize_text_field( wp_unslash( $_POST['form_type'] ) ) : 'page';

		$quotes = WC()->session->get( 'quotes' );
		$invalid_data = false;


		foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

			if (!isset($quote_item['composite_child_products']) && ( ( !isset($form_data['quote_qty'][ $quote_item_key ]) || $form_data['quote_qty'][ $quote_item_key ] < 0 ) || ( ( isset($form_data['offered_price'][ $quote_item_key ]) && $form_data['offered_price'][ $quote_item_key ] < 0 ) ) )) {
				$invalid_data = true;
				break;
			}

			$rule_id = isset($quote_item['afrfq_rule_id']) ? $quote_item['afrfq_rule_id'] : 0;
			$afrfq_apply_on_oos_products = get_post_meta($rule_id, 'afrfq_apply_on_oos_products', true);
			// $general

			$_product = $quote_item['data'];

			// checking stock availibility
			$max_product_quantity = apply_filters('addify_quote_product_quantity_maximum', $_product->get_max_purchase_quantity(), $_product, $quote_item);

			$is_in_stock = $_product && $_product->is_in_stock();
			$allow_oos = ( 'yes' === $afrfq_apply_on_oos_products || 'yes' === get_option( 'enable_o_o_s_products' ) );

			if ( $is_in_stock || ! $allow_oos ) {

				if ( ( -9999 != $max_product_quantity && -1 != $max_product_quantity )
					&& isset( $form_data['quote_qty'][ $quote_item_key ] )
					&& $max_product_quantity < $form_data['quote_qty'][ $quote_item_key ] ) {

					$invalid_data = true;
					break;
				}
			}

			if ( isset( $form_data['quote_qty'][ $quote_item_key ] ) ) {
				

				if ( 0 == $form_data['quote_qty'][ $quote_item_key ] ) {

					if (!isset($quote_item['af_cp_component_product']) ) {
						
						unset( $quotes[ $quote_item_key ] );
					}


				} else {

					$quotes[ $quote_item_key ]['quantity'] = intval( $form_data['quote_qty'][ $quote_item_key ] );
				}
			}

			if ( isset( $form_data['offered_price'][ $quote_item_key ] ) ) {
				$quotes[ $quote_item_key ]['offered_price'] = floatval( $form_data['offered_price'][ $quote_item_key ] );
			}
		}

		
		WC()->session->set( 'quotes', $quotes );
		
		$quotes = WC()->session->get( 'quotes' );

		foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

			if ( isset( $quote_item['quantity'] ) && empty( $quote_item['quantity'] ) ) {

				unset( $quotes[ $quote_item_key ] );
			}

			if ( ! isset( $quote_item['data'] ) ) {
				unset( $quotes[ $quote_item_key ] );
			}
		}

		WC()->session->set( 'quotes', array_filter( $quotes ) );

		do_action( 'addify_quote_session_changed' );

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), 'addify_quote', WC()->session->get( 'quotes' ) );
		}

		$af_quote = new AF_R_F_Q_Quote();

		ob_start();

		// if request is from popup then send the popup table as table strucutre is different for request page and popup
		if ('popup' == $form_type) {
			wc_get_template(
				'quote/quote-table-popup.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);
		} else {
			wc_get_template(
				'quote/quote-table.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);
		}

		$quote_table = ob_get_clean();

		ob_start();
			wc_get_template(
				'quote/mini-quote.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);
		$mini_quote = ob_get_clean();

		$message = wp_kses_post( '<div class="woocommerce-message" role="alert">' . esc_html__( 'Quote updated', 'addify_b2b' ) . '</div>' );
		if ($invalid_data) {
			$message = wp_kses_post( '<div class="woocommerce-error" role="alert">' . esc_html__( 'Unable to update quote. Some fields contain invalid information.', 'addify_b2b' ) . '</div>' );
		}

		ob_start();

			wc_get_template(
				'quote/quote-totals-table.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);

		$quote_totals = ob_get_clean();

		if ( empty( $quote_totals ) ) {
			$quote_totals = '';
		}

		$overstock_errors = apply_filters('addify_max_product_quantity_in_quote', $quotes);

		// Append stock error messages if any
		if (!empty($overstock_errors)) {
			foreach ($overstock_errors as $error) {
				$message .= wp_kses_post('<div class="woocommerce-error" role="alert">' . esc_html($error) . '</div>');
			}
		}

		wp_send_json(
			array(
				'quote_empty'  => empty( WC()->session->get( 'quotes' ) ) ? true : false,
				'quote-table'  => $quote_table,
				'message'      => $message,
				'mini-quote'   => $mini_quote,
				'quote-totals' => $quote_totals,
			)
		);
	}

	/**
	 * Ajax add to quote controller.
	 */
	public function afrfq_add_to_quote_callback_function() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {
			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		if ( isset( $_POST ) ) {
			$form_data = sanitize_meta( '', wp_unslash( $_POST ), '' );
		} else {
			$form_data = '';
		}

		$rule_id      = isset( $_POST['rule_id'] ) ? intval( sanitize_text_field(wp_unslash($_POST['rule_id'])) ) : '';
		$_POST      = $form_data;
		$product_id = isset( $form_data['product_id'] ) ? intval( $form_data['product_id'] ) : '';
		$quantity   = isset( $form_data['quantity'] ) ? intval( $form_data['quantity'] ) : 1;

		$popup_is_enabled = 'no';

		if ('' != $rule_id && 'yes' === get_post_meta($rule_id, 'afrfq_enable_add_to_quote_popup', true)) {
			$popup_is_enabled = 'yes';
		} else {
			$popup_is_enabled = 'no';
		}

		$ajax_add_to_quote = new AF_R_F_Q_Quote();

		$passed_validation = apply_filters( 'addify_add_to_quote_validation', true, $product_id, $quantity, $form_data );

		if ( ! $passed_validation ) {
			echo 'failed';
			die();
		}

		//if it is a price calculator object then redirect to product page
		if (class_exists('Addf_Price_Calculator_Helper') && Addf_Price_Calculator_Helper::addf_prc_check_rule_applied($product_id)) {
			$product_obj = wc_get_product($product_id);
			wp_send_json(
				array(
					'price_calculator_product'  =>  true,
					'redirect_to'               => $product_obj ? $product_obj->get_permalink() : '',
				)
			);
		}

		$quote_item_key = $ajax_add_to_quote->add_to_quote( $form_data, $product_id, $quantity );

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), 'addify_quote', WC()->session->get( 'quotes' ) );
		}

		$quote_contents = wc()->session->get( 'quotes' );
		$product        = '';
		$product_name   = 'Product';

		if ( isset( $quote_contents[ $quote_item_key ] ) ) {
			$product = $quote_contents[ $quote_item_key ]['data'];
		}

		if ( is_object( $product ) ) {
			$product_name = $product->get_name();
		}

		//handle add to quote via popup
		if ('yes' == $popup_is_enabled && false !== $quote_item_key) {

			ob_start();

			wc_get_template(
				'quote/mini-quote.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);

			$mini_quote = ob_get_clean();

			ob_start();
			?>
				<a href="<?php echo esc_url( get_page_link( get_option( 'addify_atq_page_id' ) ) ); ?>" class="added_to_cart added_to_quote wc-forward button" title="View Quote"><?php echo esc_html( get_option( 'afrfq_view_button_message' ) ); ?></a>
			<?php
			$view_quote_btn = ob_get_clean();

			wp_send_json(
				array(
					'mini-quote'  => $mini_quote,
					'view_button' => $view_quote_btn,
					'popup_is_enabled'  => $popup_is_enabled,
					'',
				)
			);
		}

		if ( 'yes' === get_option( 'enable_ajax_shop' ) && false !== $quote_item_key ) {

			ob_start();

			wc_get_template(
				'quote/mini-quote.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);

			$mini_quote = ob_get_clean();

			ob_start();
			?>
				<a href="<?php echo esc_url( get_page_link( get_option( 'addify_atq_page_id' ) ) ); ?>" class="added_to_cart added_to_quote wc-forward button" title="View Quote"><?php echo esc_html( get_option( 'afrfq_view_button_message' ) ); ?></a>
			<?php
			$view_quote_btn = ob_get_clean();

			wp_send_json(
				array(
					'mini-quote'  => $mini_quote,
					'view_button' => $view_quote_btn,
				)
			);
		} elseif ( false === $quote_item_key ) {
				/* translators: %s: Product name */
				wc_add_notice( sprintf( __( '“%s” has not been added to your quote.', 'addify_b2b' ), $product_name ), 'error' );
				echo 'success';
		} else {
			$button = '<a href="' . esc_url( get_page_link( get_option( 'addify_atq_page_id' ) ) ) . '" class="button wc-forward">' . __( 'View quote', 'addify_b2b' ) . '</a>';
			/* translators: %s: Product name */
			wc_add_notice( sprintf( __( '“%1$s” has been added to your quote. %2$s', 'addify_b2b' ), $product_name, wp_kses_post( $button ) ), 'success' );
			echo 'success';
		}

		die();
	}

	/**
	 * Ajax add to quote controller for variable.
	 */
	public function afrfq_add_to_quote_single_vari_callback_function() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {

			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		if ( isset( $_POST['form_data'] ) ) {
			parse_str( sanitize_meta( '', wp_unslash( $_POST['form_data'] ), '' ), $form_data );
		} else {
			$form_data = '';
		}

		$rule_id            = isset( $_POST['rule_id'] ) ? intval( sanitize_text_field(wp_unslash($_POST['rule_id'])) ) : '';

		// data attributes for button added via shortcode
		$popup_enabled              = isset( $_POST['popup_enabled'] ) ? sanitize_text_field(wp_unslash($_POST['popup_enabled'])) : '';
		$button_type                = isset( $_POST['button_type'] ) ? sanitize_text_field(wp_unslash($_POST['button_type'])) : '';
		$button_product_id          = isset( $_POST['product_id'] ) ? sanitize_text_field(wp_unslash($_POST['product_id'])) : '';
		$button_parent_product_id   = isset( $_POST['parent_product_id'] ) ? sanitize_text_field(wp_unslash($_POST['parent_product_id'])) : '';
		$button_product_quantity    = isset( $_POST['product_quantity'] ) ? sanitize_text_field(wp_unslash($_POST['product_quantity'])) : '';
		// adding this variation id as this array is passed to quote validation and that function is using variation id for stock calculation
		// only for add to quote via shortcode button
		if ('custom' == $button_type) {
			$form_data['variation_id']  = $button_product_id;
		}
		$form_data['afrfq_rule_id'] = $rule_id;

		$_POST              = $form_data;
		$product_id         = isset( $form_data['add-to-cart'] ) ? intval( $form_data['add-to-cart'] ) : '';
		$quantity           = isset( $form_data['quantity'] ) ? intval( $form_data['quantity'] ) : 1;
		$variation_id       = isset( $form_data['variation_id'] ) ? intval( $form_data['variation_id'] ) : '';

		$variation          = array();
		$popup_is_enabled   = 'no';

		if ('' != $rule_id && 'yes' === get_post_meta($rule_id, 'afrfq_enable_add_to_quote_popup', true)) {
			$popup_is_enabled = 'yes';
		} else {
			$popup_is_enabled = 'no';
		}

		
		//populating data when add to quote via shortcode button
		if ('custom' == $button_type) {
			//updating data from shortcode attributes
			$product_id = $button_parent_product_id;
			$quantity   = $button_product_quantity;
			
			$variation_id = $button_product_id;
			$variation_obj = wc_get_product( $variation_id );

			if ( $variation_obj && 'variation' === $variation_obj->get_type() ) {
				foreach ( $variation_obj->get_attributes() as $key => $value ) {
					$variation[ 'attribute_' . $key ] = $value;
				}
			}

		}
		if ('0' == $rule_id && 'yes' == $popup_enabled && 'custom' == $button_type) {
			$popup_is_enabled = 'yes';
		}

		foreach ( $form_data as $key => $value ) {

			if ( ! in_array( $key, array( 'add-to-cart', 'quantity', 'variation_id', 'product_id' ), true ) ) {

				$variation[ $key ] = $value;
			}
		}

		$ajax_add_to_quote = new AF_R_F_Q_Quote();

		$passed_validation = apply_filters( 'addify_add_to_quote_validation', true, $product_id, $quantity, $form_data );

		if ( ! $passed_validation ) {
			echo 'failed';
			die();
		}

		$quote_item_key = $ajax_add_to_quote->add_to_quote( $form_data, $product_id, $quantity, $variation_id, $variation );

		$quote_contents = wc()->session->get( 'quotes' );

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), 'addify_quote', WC()->session->get( 'quotes' ) );
		}

		$product = '';

		if ( isset( $quote_contents[ $quote_item_key ] ) ) {
			$product = $quote_contents[ $quote_item_key ]['data'];
		} else {
			$product = wc_get_product( $variation_id );
		}

		$product_name = 'Product';
		if ( is_object( $product ) ) {
			$product_name = $product->get_name();
		}

		if ('yes' == $popup_is_enabled && false !== $quote_item_key) {

			ob_start();

			wc_get_template(
				'quote/mini-quote.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);

			$mini_quote = ob_get_clean();

			ob_start();
			?>
				<a href="<?php echo esc_url( get_page_link( get_option( 'addify_atq_page_id' ) ) ); ?>" class="added_to_cart added_to_quote wc-forward button" title="View Quote"><?php echo esc_html( get_option( 'afrfq_view_button_message' ) ); ?></a>
			<?php
			$view_quote_btn = ob_get_clean();

			wp_send_json(
				array(
					'mini-quote'        => $mini_quote,
					'view_button'       => $view_quote_btn,
					'popup_is_enabled'  => $popup_is_enabled,
				)
			);
		}

		if ( 'yes' === get_option( 'enable_ajax_product' ) && false !== $quote_item_key ) {

			ob_start();

			wc_get_template(
				'quote/mini-quote.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);

			$mini_quote = ob_get_clean();

			ob_start();
			?>
				<a href="<?php echo esc_url( get_page_link( get_option( 'addify_atq_page_id' ) ) ); ?>" class="added_to_cart added_to_quote wc-forward button" title="View Quote"><?php echo esc_html( get_option( 'afrfq_view_button_message' ) ); ?></a>
			<?php
			$view_quote_btn = ob_get_clean();

			wp_send_json(
				array(
					'mini-quote'  => $mini_quote,
					'view_button' => $view_quote_btn,
				)
			);
		} elseif ( false === $quote_item_key ) {

				/* translators: %s: Product name */
				wc_add_notice( __( 'Quote is not available for selected variation.', 'addify_b2b' ), 'error' );
				echo 'success';
		} else {
			$button = '<a href="' . esc_url( get_page_link( get_option( 'addify_atq_page_id' ) ) ) . '" class="button wc-forward">' . __( 'View quote', 'addify_b2b' ) . '</a>';
			/* translators: %s: Product name */
			wc_add_notice( sprintf( __( '“%1$s” has been added to your quote. %2$s', 'addify_b2b' ), $product_name, wp_kses_post( $button ) ), 'success' );
			echo 'success';
		}

		die();
	}

	/**
	 * Ajax add to quote controller for single products.
	 */
	public function afrfq_add_to_quote_single_callback_function() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {

			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		if ( isset( $_POST['form_data'] ) ) {
			parse_str( sanitize_meta( '', wp_unslash( $_POST['form_data'] ), '' ), $form_data );
			if ( isset( $_POST['product_id'] ) ) {
				$form_data['add-to-cart'] = sanitize_text_field( wp_unslash( $_POST['product_id'] ) );
			}
		} else {
			$form_data = array();
		}

	
		$rule_id            = isset( $_POST['rule_id'] ) ? intval( sanitize_text_field(wp_unslash($_POST['rule_id'])) ) : '';

		$form_data['afrfq_rule_id'] = $rule_id;

		$popup_enabled      = isset( $_POST['popup_enabled'] ) ? sanitize_text_field(wp_unslash($_POST['popup_enabled'])) : '';
		$button_type        = isset( $_POST['button_type'] ) ? sanitize_text_field(wp_unslash($_POST['button_type'])) : '';
		$button_product_quantity   = isset( $_POST['product_quantity'] ) ? sanitize_text_field(wp_unslash($_POST['product_quantity'])) : '';
		
		$_POST              = $form_data;
		$product_id         = isset( $form_data['add-to-cart'] ) ? intval( $form_data['add-to-cart'] ) : '';
		$quantity           = isset( $form_data['quantity'] ) ? $form_data['quantity'] : 1;
		$variation_id       = isset( $form_data['variation_id'] ) ? intval( $form_data['variation_id'] ) : '';
		$variation          = array();
		$popup_is_enabled   = 'no';

		if ('0' != $rule_id && 'yes' === get_post_meta($rule_id, 'afrfq_enable_add_to_quote_popup', true)) {
			$popup_is_enabled = 'yes';
		} else {
			$popup_is_enabled = 'no';
		}

		if ('custom' == $button_type) {
			$quantity   = $button_product_quantity;
		}
		

		if ('0' == $rule_id && 'yes' == $popup_enabled && 'custom' == $button_type) {
			$popup_is_enabled = 'yes';
		}

		$ajax_add_to_quote = new AF_R_F_Q_Quote();

		$product        = wc_get_product( $product_id );
		$added_products = array();

		if ( $product->is_type( 'simple' ) ) {
			
			$passed_validation = apply_filters( 'addify_add_to_quote_validation', true, $product_id, $quantity, $form_data );

			if ( ! $passed_validation ) {
				die( 'failed' );
			}

			$quote_item_key = $ajax_add_to_quote->add_to_quote( $form_data, $product_id, $quantity );

		} elseif ($product->is_type( 'af_composite_product' )) {

			$passed_validation = apply_filters( 'addify_add_to_quote_validation', true, $product_id, $quantity, $form_data );

			if ( ! $passed_validation ) {
				die( 'failed' );
			}

			$parent_quote_item_key = $ajax_add_to_quote->add_to_quote( $form_data, $product_id, $quantity );

			$composite_product_components = isset($form_data['af_cp_component_product']) ? $form_data['af_cp_component_product'] : array();
			$composite_product_components_qty = isset($form_data['af_cp_component_product_qty']) ? $form_data['af_cp_component_product_qty'] : array();

			foreach ($composite_product_components as $key => $value) {
				// finding max value
				$max      = '';
				$max_arr  = (array) get_post_meta( $product_id , 'af_composite_product_max_qty' , true );
				$max_keys = (array) array_keys( $max_arr );
				if ( in_array( $key , $max_keys ) ) {
					if ( $max_arr[ $key ] ) {
						$max = $max_arr[ $key ];
					}
				}

				$variation_id = 0;
				$variation = array();

				if (wc_get_product($value)->is_type('variable')) {
					$variation_id = $form_data['af_cp_variation_id'][ $key ];
					$variation_obj = wc_get_product( $variation_id );
	
					if ( $variation_obj && 'variation' === $variation_obj->get_type() ) {
						foreach ( $variation_obj->get_attributes() as $attribute_key => $attribute_value ) {
							$variation[ 'attribute_' . $attribute_key ] = $attribute_value;
						}
					}
				}



				$component_product_quantity = isset($composite_product_components_qty[ $key ]) ? $composite_product_components_qty[ $key ] : 1;
				$component_product_quantity = $component_product_quantity * $quantity;

				$cart_item_meta_data_for_component['composite_child_products'] = 
																				array(
																					'parent_id'             => $parent_quote_item_key,
																					'parent_quantity'       => $quantity,
																					'parent_product_id'     => $product_id,
																					'type'                  => 'component',     
																					'qty'                   => isset($composite_product_components_qty[ $key ]) ? $composite_product_components_qty[ $key ] : 1,
																					'min'                   => isset($composite_product_components_qty[ $key ]) ? $composite_product_components_qty[ $key ] : 1,
																					'max'                   => floatval($max),
																					'comp_key'              => $key,
																					'price_type'            => 'component',     
																				);
				 
				$passed_validation = apply_filters( 'addify_add_to_quote_validation', true, $value, $component_product_quantity, $form_data );

				if ( ! $passed_validation ) {
					die( 'failed' );
				}

				$quote_item_key = $ajax_add_to_quote->add_to_quote( $form_data, $value, $component_product_quantity, $variation_id, $variation, $cart_item_meta_data_for_component );
				
			
			}

			$quote_item_key = $parent_quote_item_key;
			
		} elseif ( $product->is_type( 'grouped' ) ) {

			foreach ( $quantity as $product_id => $qty ) {

				if ( empty( $qty ) ) {
					continue;
				}

				$passed_validation = apply_filters( 'addify_add_to_quote_validation', true, $product_id, $qty, $form_data );

				if ( ! $passed_validation ) {
					die( 'failed' );
				}

				$quote_item_key = $ajax_add_to_quote->add_to_quote( $form_data, $product_id, $qty );

				if ( $quote_item_key ) {
					$added_products[] = $product_id;
				}
			}
		}
		
		$quote_contents = wc()->session->get( 'quotes' );

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), 'addify_quote', WC()->session->get( 'quotes' ) );
		}

		$product = '';
		if ( isset( $quote_contents[ $quote_item_key ] ) ) {
			$product = $quote_contents[ $quote_item_key ]['data'];
		} else {
			$product = wc_get_product( $product_id );
		}

		$product_name = 'Product';

		if ( ! empty( $added_products ) ) {

			$product_name = array();
			foreach ( $added_products as $product_id ) {
				$product        = wc_get_product( $product_id );
				$product_name[] = $product->get_name();
			}

			$product_name = implode( ', ', $product_name );

		} elseif ( is_object( $product ) ) {

			$product_name = $product->get_name();
		}

		if ('yes' == $popup_is_enabled && false !== $quote_item_key) {
			ob_start();

			wc_get_template(
				'quote/mini-quote.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);

			$mini_quote = ob_get_clean();

			ob_start();
			?>
				<a href="<?php echo esc_url( get_page_link( get_option( 'addify_atq_page_id' ) ) ); ?>" class="added_to_cart added_to_quote wc-forward button" title="View Quote"><?php echo esc_html( get_option( 'afrfq_view_button_message' ) ); ?></a>
			<?php
			$view_quote_btn = ob_get_clean();

			wp_send_json(
				array(
					'mini-quote'        => $mini_quote,
					'view_button'       => $view_quote_btn,
					'popup_is_enabled'  => $popup_is_enabled,
				)
			);
		}

		if ( 'yes' === get_option( 'enable_ajax_product' ) && false !== $quote_item_key ) {

			ob_start();

			wc_get_template(
				'quote/mini-quote.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);

			$mini_quote = ob_get_clean();

			ob_start();
			?>
				<a href="<?php echo esc_url( get_page_link( get_option( 'addify_atq_page_id' ) ) ); ?>" class="added_to_cart added_to_quote wc-forward button" title="View Quote"><?php echo esc_html( get_option( 'afrfq_view_button_message' ) ); ?></a>
			<?php
			$view_quote_btn = ob_get_clean();

			wp_send_json(
				array(
					'mini-quote'        => $mini_quote,
					'view_button'       => $view_quote_btn,
				)
			);
		} elseif ( false === $quote_item_key ) {

				/* translators: %s: Product name */
				wc_add_notice( sprintf( __( 'Quote is not available for “%s”.', 'addify_b2b' ), $product_name ), 'error' );
				echo 'success';
		} else {
			$button = '<a href="' . esc_url( get_page_link( get_option( 'addify_atq_page_id' ) ) ) . '" class="button wc-forward">' . __( 'View quote', 'addify_b2b' ) . '</a>';
			/* translators: %s: Product name */
			wc_add_notice( sprintf( __( '“%1$s” has been added to your quote. %2$s', 'addify_b2b' ), $product_name, wp_kses_post( $button ) ), 'success' );
			echo 'success';
		}

		die();
	}

	/**
	 * Ajax remove item from quote.
	 */
	public function afrfq_remove_quote_item_callback_function() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {

			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		$quote_key = isset( $_POST['quote_key'] ) ? sanitize_text_field( wp_unslash( $_POST['quote_key'] ) ) : '';

		if ( empty( $quote_key ) ) {
			die( 'Quote key is empty' );
		}

		$form_type = isset($_POST['form_type']) ? sanitize_text_field( wp_unslash( $_POST['form_type'] ) ) : 'page';

		$quotes = WC()->session->get( 'quotes' );

		// removing child of a composite product
		$af_cp_component_product = isset($quotes[ $quote_key ]['af_cp_component_product']) ? $quotes[ $quote_key ]['af_cp_component_product'] : array();

		$af_cp_variation_id = isset($quotes[ $quote_key ]['af_cp_variation_id']) ? $quotes[ $quote_key ]['af_cp_variation_id'] : array();

		foreach ($af_cp_variation_id as $index => $variation_id) {
			if (!empty($variation_id) && isset($af_cp_component_product[ $index ])) {
				$af_cp_component_product[ $index ] = $variation_id;
			}
		}
		
		if (!empty($af_cp_component_product)) {
			foreach ($af_cp_component_product as $key => $value) {
				foreach ($quotes as $selected_key=>$selected_value) {
					$product_id = isset($selected_value['variation_id']) && 0 != $selected_value['variation_id'] ? $selected_value['variation_id'] : $selected_value['product_id'];
					if (isset($selected_value['af_cp_component_product']) && !empty($selected_value['af_cp_component_product']) && $product_id == $value ) {
						unset($quotes[ $selected_key ]);
						break;
					}
				}
			}
		}

		$product = $quotes[ $quote_key ]['data'];

		unset( $quotes[ $quote_key ] );

		WC()->session->set( 'quotes', $quotes );

		do_action( 'addify_quote_session_changed' );

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), 'addify_quote', WC()->session->get( 'quotes' ) );
		}

		do_action( 'addify_quote_item_removed', $quote_key, $product );

		ob_start();

		if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-table.php' ) ) {

			include get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-table.php';

		} elseif ('popup' == $form_type) {

				wc_get_template(
					'quote/quote-table-popup.php',
					array(),
					'/woocommerce/addify/rfq/',
					AFRFQ_PLUGIN_DIR . 'templates/'
				);
		} else {
			wc_get_template(
				'quote/quote-table.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);
		}

		$quote_table = ob_get_clean();

		ob_start();

		wc_get_template(
			'quote/mini-quote.php',
			array(),
			'/woocommerce/addify/rfq/',
			AFRFQ_PLUGIN_DIR . 'templates/'
		);

		$mini_quote = ob_get_clean();

		/* translators: %s: Product name */
		$message      = sprintf( __( '“%s” has been removed from quote basket.', 'addify_b2b' ), $product->get_name() );
		$message_html = '<div class="woocommerce-message" role="alert">' . $message . '</div>';

		ob_start();

		if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-totals-table.php' ) ) {

			include get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-totals-table.php';

		} else {

			wc_get_template(
				'quote/quote-totals-table.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);
		}

		$quote_totals = ob_get_clean();

		if ( empty( $quote_totals ) ) {
			$quote_totals = '';
		}

		wp_send_json(
			array(
				'quote_empty'  => empty( WC()->session->get( 'quotes' ) ) ? true : false,
				'quote-table'  => $quote_table,
				'message'      => $message_html,
				'mini-quote'   => $mini_quote,
				'quote-totals' => $quote_totals,
			)
		);

		die();
	}

	public function af_rfq_download_quote_pdf_account_page() {
		
		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {
			wp_die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		$hashed_quote_id = isset( $_GET['quote_id'] ) ? sanitize_text_field( wp_unslash( $_GET['quote_id'] ) ) : '';

		list( $afrfq_qoute_id, $hash ) = explode( '|', base64_decode( rawurldecode( $hashed_quote_id ) ) );
		if ( !hash_equals( hash_hmac( 'sha256', $afrfq_qoute_id, wp_salt( 'auth' ) ), $hash ) ) {
			wp_die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		$user_id      = get_post_meta(  $afrfq_qoute_id , '_customer_user', true );
		$current_user_id = get_current_user_id();
	
		if ( $user_id != $current_user_id ) {
			wp_die( esc_html__( 'You are not authorized to download this quote.', 'addify_b2b' ) );
		}

		af_rfq_gernate_pdf_of_order(false, false, false, false, false, false, false, array( $afrfq_qoute_id ) );

		exit();
	}



	/**
	 * AF_R_F_Q_Quote.
	 */

	public function afrfq_submit_quote_via_popup() {

		if ( empty( $_POST['afrfq_nonce'] ) || ! wp_verify_nonce( esc_attr( sanitize_text_field( wp_unslash( $_POST['afrfq_nonce'] ) ) ), 'save_afrfq' ) ) {
			die( esc_html__( 'Site security violated1.', 'addify_b2b' ) );
		}

		$data = (array) sanitize_meta( '', wp_unslash( $_POST ), '' );

		$popup_template = isset($data['popup_template'])?$data['popup_template']:'template_one';
		$current_page   = isset($data['current_page'])?$data['current_page']:'';

		if ('yes' === get_option('afrfq_redirect_after_submission') && !empty(get_option('afrfq_redirect_url'))) {
			$redirect_url = get_option('afrfq_redirect_url');
		} else {
			$redirect_url = '';
		}

		$notices_html = '';

		// quantity validation
		$invalid_data = false;

		foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

			if (!isset($quote_item['composite_child_products']) && ( !isset($_POST['quote_qty'][ $quote_item_key ]) || $_POST['quote_qty'][ $quote_item_key ] < 0 || ( ( isset($_POST['offered_price'][ $quote_item_key ]) && $_POST['offered_price'][ $quote_item_key ] < 0 ) ) )) {
				$invalid_data = true;
				break;
			}

			$rule_id = isset($quote_item['afrfq_rule_id']) ? $quote_item['afrfq_rule_id'] : 0;
			$afrfq_apply_on_oos_products = get_post_meta($rule_id, 'afrfq_apply_on_oos_products', true);

			$_product = $quote_item['data'];

			// checking stock availibility
			$max_product_quantity = apply_filters('addify_quote_product_quantity_maximum', $_product->get_max_purchase_quantity(), $_product, $quote_item);

			$is_in_stock = $_product && $_product->is_in_stock();
			$allow_oos = ( 'yes' === $afrfq_apply_on_oos_products || 'yes' === get_option( 'enable_o_o_s_products' ) );

			if ( $is_in_stock || ! $allow_oos ) {

				if ( ( -9999 != $max_product_quantity && -1 != $max_product_quantity )
					&& isset( $_POST['quote_qty'][ $quote_item_key ] )
					&& $max_product_quantity < $_POST['quote_qty'][ $quote_item_key ] ) {

					$invalid_data = true;
					break;
				}
			}

		}

		if ($invalid_data) {
			$notices_html .= '<ul class="woocommerce-error">';
			$notices_html .= '<li>' . esc_html__('Please enter valid quantity.', 'addify_b2b') . '</li>';
			$notices_html .= '</ul>';
			wp_send_json( 
				array(
					'status' => 'fail',
					'notices_html'     => $notices_html,
				)
			);
		}

		
		$af_fields_obj = new AF_R_F_Q_Quote_Fields();

		$validation    = $af_fields_obj->afrfq_validate_fields_data( array_merge( $data, (array) $_FILES ) );

		if ('template_two' == $popup_template && '2' == $current_page) {
			//field validation
			if ( is_array( $validation ) ) {

				foreach ( $validation as $key => $message ) {

					if ( empty( $message ) ) {
						continue;
					}
					wc_add_notice( $message, 'error' );
				}

				$notices = wc_get_notices('error');
				wc_clear_notices(); 

				$notices_html = '';
				if (!empty($notices)) {
					$notices_html .= '<ul class="woocommerce-error">';
					foreach ($notices as $notice) {
						$notices_html .= '<li>' . esc_html($notice['notice']) . '</li>';
					}
					$notices_html .= '</ul>';
				}

				wp_send_json( 
						array(
							'status' => 'fail',
							'notices_html'     => $notices_html,
						)
				);
			} else {   
				//temporarily uploading file to server to show on review page in popup template 2
				if ( ! empty( $_FILES ) ) {
					foreach ( $_FILES as $file ) {
						// Ensure it's a valid upload
						if ( isset( $file['tmp_name'] ) && is_uploaded_file( $file['tmp_name'] ) ) {
							
							$timestamp = time();
				
							// Get file extension
							$extension = pathinfo( $file['name'], PATHINFO_EXTENSION );
							$extension = strtolower( sanitize_text_field( $extension ) );
				
							// Final file name with extension
							// $file_name   = 'temp_file_upload.' . $extension;
							$file_name   = $file['name'];
							$upload_path = AFRFQ_TEMP_UPLOAD_DIR . $file_name;
				
							move_uploaded_file( $file['tmp_name'], $upload_path );

							wc()->session? wc()->session->set('quote_fields_file_name', $file_name ):'';

						} else {
							wc()->session? wc()->session->set('quote_fields_file_name', '' ):'';
						}
						
					}
				} else {

					wc()->session? wc()->session->set('quote_fields_file_name', '' ):'';

					$upload_path = AFRFQ_UPLOAD_DIR . 'temp_file_upload.*'; 
					foreach ( glob( $upload_path ) as $file_to_delete ) {
						if ( is_file( $file_to_delete ) ) {
							unlink( $file_to_delete );
						}
					}
				}

				ob_start();

				wc_get_template(
					'quote/addify-quote-request-popup-review-page.php',
					array(),
					'/woocommerce/addify/rfq/',
					AFRFQ_PLUGIN_DIR . 'templates/'
				);

				$data = ob_get_clean();

				wp_send_json( 
					array(
						'status'        => 'pass',
						'redirect_url'  => $redirect_url,
						'data'          => $data,
					)
				);

				die();
			}
		} else if ('template_two' == $popup_template && '1' == $current_page) {
			$overstock_errors = apply_filters('addify_max_product_quantity_in_quote', array());

			$notices_html = '';
			if (!empty($overstock_errors)) {
				$notices_html .= '<ul class="woocommerce-error">';
				foreach ($overstock_errors as $error) {
					$notices_html .= '<li>' . esc_html($error) . '</li>';
				}
				$notices_html .= '</ul>';

				wp_send_json( 
					array(
						'status'           => 'fail',
						'notices_html'     => $notices_html,
					)
				);
			} else {

				wp_send_json( 
					array(
						'status'        => 'pass',
					)
				);

				die();
			}
		} else if ( is_array( $validation ) ) {

			foreach ( $validation as $key => $message ) {

				if ( empty( $message ) ) {
					continue;
				}
				wc_add_notice( $message, 'error' );
			}

			$notices = wc_get_notices('error');
			wc_clear_notices(); 

			$notices_html = '';
			if (!empty($notices)) {
				$notices_html .= '<ul class="woocommerce-error">';
				foreach ($notices as $notice) {
					$notices_html .= '<li>' . esc_html($notice['notice']) . '</li>';
				}
				$notices_html .= '</ul>';
			}

			wp_send_json( 
					array(
						'status'            => 'fail',
						'notices_html'      => $notices_html,
					)
			);
		} else {


			$overstock_errors = apply_filters('addify_max_product_quantity_in_quote', array());

			$notices_html = '';
			if (!empty($overstock_errors)) {
				$notices_html .= '<ul class="woocommerce-error">';
				foreach ($overstock_errors as $error) {
					$notices_html .= '<li>' . esc_html($error) . '</li>';
				}
				$notices_html .= '</ul>';

				wp_send_json( 
					array(
						'status'           => 'fail',
						'notices_html'     => $notices_html,
					)
				);
			}
	
			ob_start();
			$af_quote = new AF_R_F_Q_Quote();
			$af_quote->insert_new_quote( array_merge( $data, (array) $_FILES ));
			ob_get_clean();

			ob_start();

			wc_get_template(
				'quote/addify-quote-request-popup-thankyou.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);

			$data = ob_get_clean();


			wp_send_json( 
				array(
					'status'        => 'pass',
					'redirect_url' => $redirect_url,
					'data'          => $data,
				)
			);

			die();


		}
	}


	/**
	 * Ajax add quote note from .admin quote post type
	*/
	public function afrfq_add_quote_note_cb() {
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) ), 'afquote-ajax-nonce' ) ) {
			die( esc_html__( 'Site security violated.', 'addify_b2b' ) );
		}

		$quote_id = isset($_POST['quote_id'])?sanitize_text_field(wp_unslash($_POST['quote_id'])):'';
		$quote_note = isset($_POST['quote_note'])?sanitize_text_field(wp_unslash($_POST['quote_note'])):'';
		$note_type = isset($_POST['note_type']) && 'customer' == sanitize_text_field(wp_unslash($_POST['note_type']))?true:false;


		$date_format = get_option('date_format');
		$time_format = get_option('time_format');

		// parameters -- quote id, quote message,is customer not -- true for customer note,false for private note
		$message_added = afrfq_add_quote_note($quote_id, $quote_note, $note_type);

		if ($message_added) {
			$afrfq_quote_notes   =  get_post_meta( $quote_id, 'afrfq_quote_notes', true )?get_post_meta( $quote_id, 'afrfq_quote_notes', true ):array();

			ob_start();
			
			if (empty($afrfq_quote_notes)) :
				?>
				<li><?php echo esc_html__('There are no notes yet.', 'addify_b2b'); ?></li>
			<?php else : ?>
					<?php
					foreach ($afrfq_quote_notes as $key => $note) : 
						$note_class = $note['is_customer_note'] ? 'afrfq_customer_note' : 'afrfq_system_note';
						?>
						<li data-note_id="<?php echo esc_attr($key); ?>" class="<?php echo esc_attr($note_class); ?>">
							<div class="afrfq_note_content">
								<p><?php echo wp_kses_post($note['message']); ?></p>
							</div>
							<p class="afrfq_message_meta">
								<abbr class="exact-date" title="<?php echo esc_attr($note['datetime']); ?>">
									<?php echo esc_html($note['date'] . ' at ' . $note['time']); ?>
								</abbr>
								<span data-quote_id=<?php echo esc_attr($quote_id); ?> data-note_id="<?php echo esc_attr($key); ?>" class="afrfq_delete_note"><u><?php esc_html_e('Delete note', 'addify_b2b'); ?></u></span>
								</p>
						</li>
					<?php endforeach; ?>
			<?php
			endif; 

			$output_html = ob_get_clean();

			wp_send_json_success(array( 'output_html' => $output_html ));
			exit();
		}

		wp_send_json_error();
		exit();
	}

	/**
	 * Ajax add quote note from .admin quote post type
	*/
	public function afrfq_delete_quote_note() {
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( esc_attr( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) ), 'afquote-ajax-nonce' ) ) {
			die( esc_html__( 'Site security violated.', 'addify_b2b' ) );
		}

		$quote_id = isset($_POST['quote_id'])?sanitize_text_field(wp_unslash($_POST['quote_id'])):'';
		$note_id = isset($_POST['note_id'])?sanitize_text_field(wp_unslash($_POST['note_id'])):'';
		$message_removed = false;

		if ('' != $quote_id) {
			$quote_notes = get_post_meta($quote_id, 'afrfq_quote_notes', true);       

			if (!empty($quote_notes)) {
				unset($quote_notes[ $note_id ]);
				update_post_meta($quote_id, 'afrfq_quote_notes', $quote_notes);
				$message_removed= true;
			}

		}

		if ($message_removed) {
			$afrfq_quote_notes   =  get_post_meta( $quote_id, 'afrfq_quote_notes', true )?get_post_meta( $quote_id, 'afrfq_quote_notes', true ):array();

			ob_start();
			?>

			<?php if (empty($afrfq_quote_notes)) : ?>
				<li><?php echo esc_html__('There are no notes yet.', 'addify_b2b'); ?></li>
			<?php else : ?>
					<?php
					foreach ($afrfq_quote_notes as $key => $note) : 
						$note_class = $note['is_customer_note'] ? 'afrfq_customer_note' : 'afrfq_system_note';
						?>
						<li data-note_id="<?php echo esc_attr($key); ?>" class="<?php echo esc_attr($note_class); ?>">
							<div class="afrfq_note_content">
								<p><?php echo wp_kses_post($note['message']); ?></p>
							</div>
							<p class="afrfq_message_meta">
								<abbr class="exact-date" title="<?php echo esc_attr($note['datetime']); ?>">
									<?php echo esc_html($note['date'] . ' at ' . $note['time']); ?>
								</abbr>
								<span data-quote_id=<?php echo esc_attr($quote_id); ?> data-note_id="<?php echo esc_attr($key); ?>" class="afrfq_delete_note"><u><?php esc_html_e('Delete note', 'addify_b2b'); ?></u></span>
								</p>
						</li>
					<?php endforeach; ?>
			<?php endif; ?>
			

			<?php

			$output_html = ob_get_clean();

			wp_send_json_success(array( 'output_html' => $output_html ));
			exit();
		}

		wp_send_json_error();
		exit();
	}

	/**
	 * Ajax clear cart for blocks on button click
	*/
	public function afrfq_clear_cart() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'afquote-ajax-nonce' ) ) {

			die( esc_html__( 'Failed ajax security check!', 'addify_b2b' ) );
		}

		WC()->cart? WC()->cart->empty_cart():'';

		wp_send_json_success();
		exit();
	}

	public function validate_add_cart_item( $passed, $product_id, $qty, $post_data = array() ) {

		if (empty($post_data)) {
			return $passed;
		}
		
		$variation_id = isset($post_data['variation_id']) ? $post_data['variation_id'] : 0;

		$is_variation = false;

		if ('' != $variation_id && 0 != $variation_id) {
			$is_variation = true;
			$product_id   = $variation_id;
		}

		$product = wc_get_product($product_id);
		$stock = '';

	
		$is_in_stock = $product->is_in_stock();
		$global_allow_oos = ( 'yes' === get_option( 'enable_o_o_s_products' ) );

		if ( ! $is_in_stock && ( $global_allow_oos ) ) {
			return $passed;
		}
		
		// if product is on backorder then return
		if ($product->is_on_backorder() || $product->backorders_allowed()) {
			return $passed;
		}


		$product_id_to_compare = '';

		if ($is_variation) {
			$parent_id      = $product->get_parent_id();
			$parent_product = wc_get_product($parent_id);
			if (1 == $product->managing_stock() ) {
				$stock = $product->get_stock_quantity();
				$product_id_to_compare = $product_id;
			} else if ('parent' === $product->managing_stock()) {
				$stock = $parent_product ? $parent_product->get_stock_quantity():0;
				$product_id_to_compare = $parent_id;
			}
		} elseif (1 == $product->managing_stock() ) {
			$stock = $product->get_stock_quantity();
		} 

		if ('' == $stock) {
			return $passed;
		}

		$stock_already_in_quote = 0;

		$items = (array) WC()->session->get('quotes');


		foreach ( $items as $item => $values ) {
			$cart_product = $values['data'];

			$product_id_in_cart = $values['data']->get_id();
			if ($is_variation) {
				if ('parent' === $product->managing_stock() && 'parent' === $values['data']->managing_stock()) {
					$product_id_in_cart = $product->get_parent_id();
				}

				if ($product_id_in_cart == $product_id_to_compare) {
					$stock_already_in_quote += (float) $values['quantity'];
				}
			} elseif ($product_id_in_cart == $product_id) {
					$stock_already_in_quote += $values['quantity'];
			}

		}

		$total_old_and_new_qty  =  $qty + $stock_already_in_quote;
		
		if ( ( $stock - $total_old_and_new_qty ) < 0 ) {
			if (0 < $stock_already_in_quote) {
				// Translators: %1$s is stock quantity and %2$s is stock already in quote
				wc_add_notice( sprintf( __( 'You cannot add that amount to the quote -- we have %1$s in stock and you already have %2$s in your quote.', 'addify_b2b' ), wc_format_decimal( $stock ), wc_format_decimal( $stock_already_in_quote ) ), 'error' );
				return false;
			} else {
				// Translators: %1$s is stock quantity
				wc_add_notice( sprintf( __( 'You cannot add that amount to the quote -- we have %1$s in stock.', 'addify_b2b' ), wc_format_decimal( $stock ) ), 'error' );
				return false;
			}
		}      

		

		return $passed;
	}

	public function addify_max_product_quantity_in_quote() {
		$errors = array();
		$product_quantities = array();

		$quotes = WC()->session->get( 'quotes' );

		if (empty($quotes)) {
			return array();
		}

		$structured_data = $this->addify_compute_composite_product_min_max($quotes);
		$composite_min_max_errors = $this->addify_form_composite_product_min_max_errors($structured_data);
	
		foreach ($quotes as $quote_item_key => $quote_item) {

			if (!isset($quote_item['product_id']) || !isset($quote_item['quantity'])) {
				continue;
			}
	
			$product_id = !empty($quote_item['variation_id']) ? intval($quote_item['variation_id']) : intval($quote_item['product_id']);
			$qty        = intval($quote_item['quantity']);
	
			if (!isset($product_quantities[ $product_id ])) {
				$product_quantities[ $product_id ] = 0;
			}
			$product_quantities[ $product_id ] += $qty;

		}

	
		foreach ($product_quantities as $product_id => $total_qty) {
			$product = wc_get_product($product_id);
	
			if (!$product) {
				continue;
			}
	
			$max_qty = $product->get_max_purchase_quantity();
	
			if ($max_qty > 0 && $total_qty > $max_qty) {
				$errors[] = sprintf(
					/* translators: 1: product name, 2: requested qty, 3: available stock */
					__('Not enough stock for "%1$s". Requested: %2$d, Available: %3$d', 'addify_b2b'),
					$product->get_name(),
					$total_qty,
					$max_qty
				);
			}
		}

		$errors = array_merge($errors, $composite_min_max_errors);
	
		return $errors;
	}

	private function addify_compute_composite_product_min_max( $quotes ) {
		$structured_data = array();
		if ( ! empty( $quotes ) && is_array( $quotes ) ) {
			foreach ( $quotes as $quote ) {
				// Detect configurable (composite) parent products
				if ( isset( $quote['af_cp_component_product']) && !isset( $quote['composite_child_products'] ) ) {
		
					$parent_id = isset( $quote['product_id'] ) ? $quote['product_id'] : 0;
		
					// Get parent min/max from meta
					$parent_min = get_post_meta( $parent_id, 'af_comp_product_adj_min_qty', true );
					$parent_max = get_post_meta( $parent_id, 'af_comp_product_adj_max_qty', true );
		
					// Set default if empty
					$parent_min = ( '' === $parent_min ) ? -1 : (int) $parent_min;
					$parent_max = ( '' === $parent_max ) ? -1 : (int) $parent_max;

					$parent_quantity = isset( $quote['quantity'] ) ? (int) $quote['quantity'] : 1;
		
					$data = array(
						'parent' => array(
							'min' => $parent_min,
							'max' => $parent_max,
							'product_id' => $parent_id,
							'quantity' => $parent_quantity,
						),
					);
		
					// Now handle components
					foreach ( $quotes as $child_quote ) {
						if (
							isset( $child_quote['composite_child_products']['parent_id'] )
							&& $child_quote['composite_child_products']['parent_id'] === $quote['key']
						) {
							$comp_key = isset( $child_quote['composite_child_products']['comp_key'] ) ? $child_quote['composite_child_products']['comp_key'] : null;
		
							if ( $comp_key ) {
								$min = isset( $child_quote['composite_child_products']['min'] )
									? (int) $child_quote['composite_child_products']['min']
									: -1;
								$max = isset( $child_quote['composite_child_products']['max'] )
									? (int) $child_quote['composite_child_products']['max']
									: -1;
		
								$product_id = !empty($child_quote['variation_id']) ? intval($child_quote['variation_id']) : intval($child_quote['product_id']);
								$child_quantity = isset( $child_quote['quantity'] ) ? (int) $child_quote['quantity'] : 1;
								$component_name = isset( $child_quote['component_name'] ) ? $child_quote['component_name'] : '';
								$data[ $comp_key ] = array(
									'min' => $min,
									'max' => $max,
									'product_id' => $product_id,
									'quantity' => $child_quantity,
									'component_name' => $component_name,
								);
							}
						}
					}
		
					$structured_data[] = $data;
				}
			}
		}

		return $structured_data;
	}

	private function addify_form_composite_product_min_max_errors( $quantity_data ) {
		$errors = array();
	
		foreach ($quantity_data as $group) {
	
			// Handle parent product
			$parent = $group['parent'];
			$parent_name = get_the_title($parent['product_id']);
			$parent_qty  = (int) $parent['quantity'];
			$min = (int) $parent['min'];
			$max = (int) $parent['max'];
	
			$message = $this->get_quantity_error_message($parent_name, $parent_name, $min, $max, $parent_qty, false);
			if ($message) {
				$errors[] = $message;
			}
	
			// Handle child components
			foreach ($group as $key => $component) {
				if ('parent' === $key) {
					continue;
				}
	
				$comp_qty = (int) $component['quantity'];
				$min = (int) $component['min'];
				$max = (int) $component['max'];
	
				$message = $this->get_quantity_error_message(
					$component['component_name'],
					$parent_name,
					$min,
					$max,
					$comp_qty,
					true
				);
	
				if ($message) {
					$errors[] = $message;
				}
			}
		}
	
		return $errors;
	}
	
	private function get_quantity_error_message( $item_name, $parent_name, $min, $max, $current_qty, $is_component = false ) {
	
		// Normalize unset values
		if ($min <= 0) {
			$min = 0;
		}
		if ($max <= 0) {
			$max = 0;
		}
	
		// skip if no limits at all
		if (0 === $min && 0 === $max) {
			return '';
		}
	
		$prefix = $is_component
			? sprintf('The quantity of "%s" in "%s"', $item_name, $parent_name)
			: sprintf('The quantity of "%s"', $item_name);
	
		// determine rule
		if ($min > 0 && $max > 0) {
			if ($min === $max && $current_qty !== $min) {
				return sprintf('%s must be exactly %d. You currently have %d.', $prefix, $min, $current_qty);
			} elseif ($current_qty < $min || $current_qty > $max) {
				return sprintf('%s must be between %d and %d. You currently have %d.', $prefix, $min, $max, $current_qty);
			}
		} elseif ($min > 0 && $current_qty < $min) {
			return sprintf('%s must be at least %d. You currently have %d.', $prefix, $min, $current_qty);
		} elseif ($max > 0 && $current_qty > $max) {
			return sprintf('%s cannot be more than %d. You currently have %d.', $prefix, $max, $current_qty);
		}
	
		return '';
	}
}

new AF_R_F_Q_Ajax_Controller();
