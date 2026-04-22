<?php
/**
 * Front class start.
 *
 * @package woocommerce-request-a-quote
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'AF_R_F_Q_Front' ) ) {
	/**
	 * AF_R_F_Q_Front.
	 */
	class AF_R_F_Q_Front extends Addify_Request_For_Quote {
		/**
		 * Construct.
		 */
		public function __construct() {


			add_action( 'wp_enqueue_scripts', array( $this, 'afrfq_front_script' ) );
			add_action( 'wp_loaded', array( $this, 'addify_convert_to_order_customer' ) );
			add_action( 'wp_loaded', array( $this, 'addify_convert_to_cart_customer' ) );
			add_action( 'wp_loaded', array( $this, 'addify_insert_customer_quote' ) );
			add_action( 'wp_nav_menu_items', array( $this, 'afrfq_quote_basket' ), 10, 2 );
			add_shortcode( 'addify-quote-request-page', array( $this, 'addify_quote_request_page_shortcode_function' ) );
			add_shortcode( 'addify-mini-quote', array( $this, 'addify_mini_quote_shortcode_function' ) );
			add_shortcode( 'addify-quote-request-popup', array( $this, 'addify_quote_request_popup_shortcode_function' ) );
			add_shortcode( 'AFRFQ_QUOTE_BUTTON', array( $this, 'addify_quote_button_shortcode_function' ) );
			add_action( 'wp_loaded', array( $this, 'addify_add_endpoints' ) );
			add_filter( 'woocommerce_account_menu_items', array( $this, 'addify_new_menu_items' ) );
			add_action( 'woocommerce_account_request-quote_endpoint', array( $this, 'addify_endpoint_content' ) );
			add_filter( 'query_vars', array( $this, 'addify_add_query_vars' ), 0 );
			add_filter( 'the_title', array( $this, 'addify_endpoint_title' ) );
			add_action( 'wp_loaded', array( $this, 'afrfq_start_customer_session' ) );
			add_action( 'wp_footer', array( $this, 'afrfq_js_pdf_loader' ) );
			add_action( 'wp_head', array( $this, 'afrfq_css_load' ) );

			add_filter( 'render_block', array( $this, 'afrfq_woocommerce_cart_block_do_actions' ), 9999, 2 );

			add_action( 'wp_loaded', array( $this, 'afrfq_custom_start_wc_session' ), 10 );
		}


		/**
		 * AF_R_F_Q_Front.
		 */
		public function afrfq_js_pdf_loader() {
			?>
			<div id="loader-wrapper">
			</div>

			<!-- Custom Popup Modal -->
			<?php
			if (!is_page('request-a-quote') && !is_cart()) {
				?>
				<div class="afrfq-quote-popup-modal" style="display:none">
					<?php
					if ('template_one' == get_option('afrfq_select_popup_template')) {
						?>
						<div class="afrfq-quote-popup-content" style="padding:20px;overflow-y: scroll;overflow-x:hidden">
							<span class="afrfq-close-popup">&times;</span>
							<div class="afrfq-quote-inner-content">
								<h2><?php echo esc_html__('Quote Request', 'addify_b2b'); ?></h2>
								<div id="popup-notice-container"></div>
								<?php echo do_shortcode('[addify-quote-request-popup]'); ?>
							</div>
						</div>	
						<?php
					} else {
						?>
							<div class="afrfq-quote-popup-content" style="overflow: hidden;">
								<span class="afrfq-close-popup">&times;</span>
								
								<div class="afrfq-popup-form-inner">
									<div class="afrfq-popup-form-steps" data-current-page="1">
										<div class="afrfq-popup-form-step" data-active="true">
											<div class="afrfq-popup-form-step-label">1</div>
											<div class="afrfq-popup-form-step-description"><?php echo esc_html__('Product selection', 'addify_b2b'); ?></div>
										</div>
										<div class="afrfq-popup-form-step">
											<div class="afrfq-popup-form-step-label">2</div>
											<div class="afrfq-popup-form-step-description"><?php echo esc_html__('Contact information', 'addify_b2b'); ?></div>
										</div>
										<div class="afrfq-popup-form-step">
											<div class="afrfq-popup-form-step-label">3</div>
											<div class="afrfq-popup-form-step-description"><?php echo esc_html__('Review information', 'addify_b2b'); ?></div>
										</div>
									</div>
									
									<div class="afrfq-product-selection-section">
										<div class="afrfq-product-selection-section-inner">
											<div id="popup-notice-container"></div>
										<?php echo do_shortcode('[addify-quote-request-popup]'); ?>
										</div>
									</div>
									
									<div class="afrfq-popup-form-footer">
										<div class="afrfq-popup-form-footer-buttons">
											<button class="afrfq-left-button afrfq-popup-form-button"><?php echo esc_html__('Continue Shopping', 'addify_b2b'); ?></button>
											<button class="afrfq-right-button afrfq-popup-form-button-primary"><?php echo esc_html__('Next step', 'addify_b2b'); ?></button>
										</div>
									</div>
								</div>
							</div>	
							<?php
					}
					?>
					
				</div>

			<?php
			}

			// popup styles
			// Active step button styles
			if ('yes' == get_option('afrfq_popup_enable_active_step_button_color')) {
				$active_step_button_text_color = $this->afrfq_get_color_option('afrfq_popup_active_step_button_fg_color', '#0073aa');
				$active_step_button_color      = $this->afrfq_get_color_option('afrfq_popup_active_step_button_bg_color', '#FFFFFF');
			} else {
				$active_step_button_text_color = '#FFFFFF';
				$active_step_button_color      = '#0073aa';
			}
			
			// Previous step button styles
			if ('yes' == get_option('afrfq_popup_enable_previous_step_button_color')) {
				$previous_step_button_text_color = $this->afrfq_get_color_option('afrfq_popup_previous_step_button_fg_color', '#0073aa');
				$previous_step_button_color      = $this->afrfq_get_color_option('afrfq_popup_previous_step_button_bg_color', '#FFFFFF');
				$previous_step_button_border     = '1px solid black';
			} else {
				$previous_step_button_text_color = '#000000';
				$previous_step_button_color      = '#FFFFFF';
				$previous_step_button_border     = '1px solid black';
			}
			
			// Next step button styles
			if ('yes' == get_option('afrfq_popup_enable_next_step_button_color')) {
				$next_step_button_text_color = $this->afrfq_get_color_option('afrfq_popup_next_step_button_fg_color', '#0073aa');
				$next_step_button_color      = $this->afrfq_get_color_option('afrfq_popup_next_step_button_bg_color', '#FFFFFF');
			} else {
				$next_step_button_text_color = '#FFFFFF';
				$next_step_button_color      = '#0073aa';
			}

			
			?>
			<style>
				.afrfq-popup-form-step[data-active="true"] .afrfq-popup-form-step-label {
					background: <?php echo esc_attr($active_step_button_color); ?>;
					color: <?php echo esc_attr($active_step_button_text_color); ?>;
				}
				.afrfq-popup-form-button,
				.afrfq-popup-form-button:hover {
					background: <?php echo esc_attr($previous_step_button_color); ?>;
					color: <?php echo esc_attr($previous_step_button_text_color); ?>;
					border: <?php echo esc_attr($previous_step_button_border); ?>;
				}
				.afrfq-popup-form-button-primary,
				.afrfq-popup-form-button-primary:hover {
					background: <?php echo esc_attr($next_step_button_color); ?>;
					color: <?php echo esc_attr($next_step_button_text_color); ?>;
					border-color: #006799;
				}
			</style>
			<?php
		}

		/**
		 * AF_R_F_Q_Front.
		 */
		public function afrfq_load_quote_from_session() {

			if ( isset( wc()->session ) && empty( wc()->session->get( 'quotes' ) ) ) {

				if ( is_user_logged_in() ) {

					$quotes = get_user_meta( get_current_user_id(), 'addify_quote', true );

					if ( ! empty( $quotes ) ) {
						wc()->session->set( 'quotes', $quotes );
					}
				}
			}
		}

		/**
		 * AF_R_F_Q_Front.
		 */
		public function addify_insert_customer_quote() {

			if ( ! isset( $_POST['afrfq_action'] ) ) {
				return;
			}

			if ( !isset($_POST['addify_checkout_place_quote'])) {
				return;
			}

			if ( empty( $_REQUEST['afrfq_nonce'] ) || ! wp_verify_nonce( esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['afrfq_nonce'] ) ) ), 'save_afrfq' ) ) {
				die( esc_html__( 'Site security violated.', 'addify_b2b' ) );
			}


			$invalid_data =false;

			//checking product quantity
			foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

				if (!isset($quote_item['composite_child_products']) && ( !isset($_POST['quote_qty'][ $quote_item_key ]) || $_POST['quote_qty'][ $quote_item_key ] < 0 || ( ( isset($_POST['offered_price'][ $quote_item_key ]) && $_POST['offered_price'][ $quote_item_key ] < 0 ) ) )) {
					$invalid_data = true;
					break;
				}
	
				$rule_id = isset($quote_item['afrfq_rule_id']) ? $quote_item['afrfq_rule_id'] : 0;
				$afrfq_apply_on_oos_products = get_post_meta($rule_id, 'afrfq_apply_on_oos_products', true);

				$_product = $quote_item['data'];
	
				// checking stock availibility
				// Incase of product on backorder it will return -1
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

			$overstock_errors = apply_filters('addify_max_product_quantity_in_quote', array());

			foreach ($overstock_errors as $error) {
				wc_add_notice( $error, 'error' );
				$invalid_data = true;
			}


			if ($invalid_data) {
				wc_add_notice( esc_html__( 'Unable to place quote. Some fields contain invalid information.', 'addify_b2b' ), 'error' );
				return;
			}

			unset( $_POST['afrfq_action'] );

			$data = (array) sanitize_meta( '', wp_unslash( $_POST ), '' );

			$af_quote = new AF_R_F_Q_Quote();

			$af_quote->insert_new_quote( array_merge( $data, (array) $_FILES ) );
		}

		/**
		 * AF_R_F_Q_Front.
		 */
		public function addify_convert_to_order_customer() {

			if ( ! isset( $_POST['addify_convert_to_order_customer'] ) ) {
				return;
			}

			if ( empty( $_REQUEST['_afrfq__wpnonce'] ) || ! wp_verify_nonce( esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['_afrfq__wpnonce'] ) ) ), '_afrfq__wpnonce' ) ) {
				wp_die( esc_html__( 'Site security violated.', 'addify_b2b' ) );
			}

			$quote_id = sanitize_text_field( wp_unslash( $_POST['addify_convert_to_order_customer'] ) );

			if ( empty( intval( $quote_id ) ) ) {
				return;
			}

			$af_quote = new AF_R_F_Q_Quote();

			$af_quote->convert_quote_to_order( $quote_id );
		}

		/**
		 * AF_R_F_Q_Front.
		 */
		public function addify_convert_to_cart_customer() {

			if ( ! isset( $_POST['addify_convert_to_cart_customer'] ) ) {
				return;
			}

			if ( empty( $_REQUEST['_afrfq__wpnonce'] ) || ! wp_verify_nonce( esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['_afrfq__wpnonce'] ) ) ), '_afrfq__wpnonce' ) ) {
				wp_die( esc_html__( 'Site security violated.', 'addify_b2b' ) );
			}

			$quote_id = sanitize_text_field( wp_unslash( $_POST['addify_convert_to_cart_customer'] ) );

			if ( empty( intval( $quote_id ) ) ) {
				return;
			}

			$af_quote = new AF_R_F_Q_Quote();

			$af_quote->convert_quote_to_cart( $quote_id );
		}



		

		/**
		 * AF_R_F_Q_Front.
		 */
		public function afrfq_start_customer_session() {

			if ( is_user_logged_in() || is_admin() ) {
				return;
			}

			if ( isset( WC()->session ) ) {
				if ( ! WC()->session->has_session() ) {
					WC()->session->set_customer_session_cookie( true );
				}
			}
		}

		/**
		 * AF_R_F_Q_Front.
		 */
		public function afrfq_front_script() {

			wp_enqueue_style( 'afrfq-front', AFRFQ_URL . 'assets/css/afrfq_front.css', false, '1.1' );
			wp_enqueue_style( 'select2-front', AFRFQ_URL . '/assets/css/select2.css', false, '1.0' );
			wp_enqueue_script( 'jquery' );

			wp_enqueue_script('wp-api-fetch');

			wp_enqueue_script( 'afrfq-frontj', AFRFQ_URL . 'assets/js/afrfq_front.js', array( 'jquery' ), '1.3.2', true );
			wp_enqueue_script( 'select2-front', AFRFQ_URL . '/assets/js/select2.js', array( 'jquery' ), '1.0', true );

			$afrfq_redirect_url = 'yes' == get_option( 'afrfq_redirect_after_submission' ) ? get_option( 'afrfq_redirect_url' ) : '';
			
			$afrfq_data = array(
				'admin_url'             => admin_url( 'admin-ajax.php' ),
				'nonce'                 => wp_create_nonce( 'afquote-ajax-nonce' ),
				'redirect'              => get_option( 'afrfq_redirect_to_quote' ),
				'redirect_to_url'       => $afrfq_redirect_url,
				'pageurl'               => get_page_link( get_option( 'addify_atq_page_id', true ) ),
			);
			if (wc()->session->get('afrfq_quote_to_cart_data')) {
				$afrfq_data['quote_to_cart_data'] = array_merge(array( 'quote_message' =>__('The email address must match the one used for the quote.', 'addify_b2b') ), wc()->session->get('afrfq_quote_to_cart_data'));
			}
			wp_localize_script( 'afrfq-frontj', 'afrfq_phpvars', $afrfq_data );
			wp_enqueue_style( 'dashicons' );

			if ( 'yes' === get_option( 'afrfq_enable_captcha' ) ) {
				wp_enqueue_script( 'Google reCaptcha JS', '//www.google.com/recaptcha/api.js', array( 'jquery' ), '1.0', true );
			}
		}

		/**
		 * AF_R_F_Q_Front.
		 */
		public function addify_mini_quote_shortcode_function() {

			ob_start();
			wc_get_template(
				'quote/mini-quote.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);

			return ob_get_clean();
		}

		/**
		 * AF_R_F_Q_Front.
		 *
		 * @param object $items returns items.
		 *
		 * @param object $args returns args.
		 */
		public function afrfq_quote_basket( $items, $args ) {

			if ( is_user_logged_in() ) {
				$user_role = current( wp_get_current_user()->roles );
			} else {
				$user_role = 'guest';
			}

			if ( ! empty( get_option( 'afrfq_customer_roles' ) ) && in_array( $user_role, (array) get_option( 'afrfq_customer_roles' ) ) ) {
				return $items;
			}

			$menu_ids = is_serialized( get_option( 'quote_menu' ) ) ? unserialize( get_option( 'quote_menu' ) ) : get_option( 'quote_menu' );

			if ( empty( $menu_ids ) ) {
				return $items;
			}

			if ( isset( $args->menu->term_id ) ) {

				$menu_id = $args->menu->term_id;

			} elseif ( isset( $args->term_id ) ) {

				$menu_id = $args->term_id;

			} elseif ( isset( $args->menu ) ) {

				$menu_id = $args->menu;

			} else {

				$menu_id = 0;
			}

			$menu_match = in_array( (string) $menu_id, (array) $menu_ids, true ) ? true : false;

			if ( ! $menu_match ) {
				return $items;
			}

			ob_start();

			wc_get_template(
				'quote/mini-quote.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);

			return $items . ob_get_clean();
		}

		/**
		 * AF_R_F_Q_Front.
		 */
		public function addify_quote_request_page_shortcode_function() {

			ob_start();

			do_action( 'addify_rfg_success_message' );

			if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/rfq/front/addify-quote-request-page.php' ) ) {

				require_once get_stylesheet_directory() . '/woocommerce/addify/rfq/front/addify-quote-request-page.php';

			} else {

				wc_get_template(
					'quote/addify-quote-request-page.php',
					array(),
					'/woocommerce/addify/rfq/',
					AFRFQ_PLUGIN_DIR . 'templates/'
				);
			}

			return ob_get_clean();
		}

		public function addify_quote_button_shortcode_function( $attr ) {

			$button_text        = isset($attr['text'])?$attr['text']:'Add Quote';
			$product_id         = ( isset($attr['product-id']) && wc_get_product($attr['product-id']) ) ? $attr['product-id'] : '';
			$product_quantity   = isset($attr['product-quantity'])?$attr['product-quantity']:'1';

			$product_sku        = '';
			$product_type       = '';
			$parent_product_id  = '';

			if ('' != $product_id) {
				$product        = wc_get_product($product_id);
				$product_sku    = $product->get_sku();
				$product_type   = $product->get_type();
				if ('variation' == $product_type) {
					$parent_product_id = $product->get_parent_id();
				}
				$product_type   = 'variation' == $product_type ? 'variable':$product_type;
			}
			
			$button_class  = isset($attr['class'])?$attr['class']:'';
			$popup_enabled = isset($attr['popup-enabled'])?$attr['popup-enabled']:'no'; 

			ob_start();
			
				echo '<a href="javascript:void(0)" rel="nofollow" data-rule_id="" data-popup_enabled="' . esc_attr($popup_enabled) . '" data-button_type="custom" data-product_id="' . intval( $product_id ) . '" data-parent_product_id="' . intval($parent_product_id) . '" data-product_quantity="' . intval($product_quantity) . '" data-product_sku="' . esc_attr( $product_sku ) . '" class="afrfqbt_single_page wp-element-button button single_add_to_cart_button alt product_type_' . esc_attr( $product_type ) . ' ' . esc_attr($button_class) . '">' . esc_attr( $button_text ) . '</a>';

			return ob_get_clean();
		}

		public function addify_quote_request_popup_shortcode_function() {
			
			ob_start();

			do_action( 'addify_rfg_success_message' );

			if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/rfq/front/addify-quote-request-popup.php' ) ) {

				require_once get_stylesheet_directory() . '/woocommerce/addify/rfq/front/addify-quote-request-popup.php';

			} else {

				wc_get_template(
					'quote/addify-quote-request-popup.php',
					array(),
					'/woocommerce/addify/rfq/',
					AFRFQ_PLUGIN_DIR . 'templates/'
				);
			}

			return ob_get_clean();
		}

		/**
		 * AF_R_F_Q_Front.
		 */
		public function addify_add_endpoints() {

			add_rewrite_endpoint( 'request-quote', EP_ROOT | EP_PAGES );
			flush_rewrite_rules();
		}

		/**
		 * AF_R_F_Q_Front.
		 *
		 * @param object $vars returns vars.
		 */
		public function addify_add_query_vars( $vars ) {
			$vars[] = 'request-quote';
			return $vars;
		}

		/**
		 * AF_R_F_Q_Front.
		 *
		 * @param object $title returns title.
		 */
		public function addify_endpoint_title( $title ) {
			global $wp_query;
			$is_endpoint = isset( $wp_query->query_vars['request-quote'] );
			if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				$title = esc_html__( 'Quotes', 'addify_b2b' );
				remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
			}
			return $title;
		}

		/**
		 * AF_R_F_Q_Front.
		 *
		 * @param object $items returns items.
		 */
		public function addify_new_menu_items( $items ) {
			$logout = $items['customer-logout'];
			unset( $items['customer-logout'] );
			$items['request-quote']   = esc_html__( 'Quotes', 'addify_b2b' );
			$items['customer-logout'] = $logout;
			return $items;
		}

		/**
		 * AF_R_F_Q_Front.
		 */
		public function addify_endpoint_content() {

			$statuses = array(
				'af_pending'                => __( 'Pending', 'addify_b2b' ),
				'af_in_process'             => __( 'In Process', 'addify_b2b' ),
				'af_accepted'               => __( 'Accepted', 'addify_b2b' ),
				'af_converted_to_cart'      => __( 'Converted to Cart', 'addify_b2b' ),
				'af_converted'              => __( 'Converted to Order', 'addify_b2b' ),
				'af_declined'               => __( 'Declined', 'addify_b2b' ),
				'af_cancelled'              => __( 'Cancelled', 'addify_b2b' ),
			);

			$afrfq_id = get_query_var( 'request-quote' );

			$quote = get_post( $afrfq_id );
		
			if ( ! empty( $afrfq_id ) && is_a( $quote, 'WP_Post' ) && 'addify_quote'  == $quote->post_type && ! str_contains($afrfq_id, 'paged=') ) {
				

				$quotedataid = get_post_meta( $afrfq_id, 'quote_proid', true );

				if ( ! empty( $quotedataid ) ) {

					wc_get_template(
						'my-account/quote-details-my-account-old-quotes.php',
						array(
							'afrfq_id' => $afrfq_id,
							'quote'    => $quote,
						),
						'/woocommerce/addify/rfq/',
						AFRFQ_PLUGIN_DIR . 'templates/'
					);

				} else {

					wc_get_template(
						'my-account/quote-details-my-account.php',
						array(
							'afrfq_id' => $afrfq_id,
							'quote'    => $quote,
							'statuses' => $statuses,
						),
						'/woocommerce/addify/rfq/',
						AFRFQ_PLUGIN_DIR . 'templates/'
					);

				}
			} else {

				$afrfq_id = str_replace('paged=', '', $afrfq_id);
				$afrfq_id = str_replace('page/', '', $afrfq_id);

				if ( empty( $afrfq_id ) || 0 == $afrfq_id ) {
					$afrfq_id = 1;
				}
				$quotes_args = array(
					'numberposts' => 5, // Show 5 posts per page
					'meta_key'    => '_customer_user',
					'meta_value'  => get_current_user_id(),
					'post_type'   => 'addify_quote',
					'post_status' => 'publish',
					'paged'       => $afrfq_id,
				);

				// Fetch the posts
				$customer_quotes = get_posts($quotes_args);

				wc_get_template(
					'my-account/quote-list-table.php',
					array(
						'customer_quotes' => $customer_quotes,
						'statuses'        => $statuses,
					),
					'/woocommerce/addify/rfq/',
					AFRFQ_PLUGIN_DIR . 'templates/'
				);

				// Pagination
				$pagination_links = paginate_links(array(
					'format'    => '?paged=%#%', //P's here
					'current'   => max(1, $afrfq_id),
					'total'     => ceil(wp_count_posts('addify_quote')->publish / 5), // Calculate total pages
					'prev_text' => '&laquo; Previous', // Previous button text
					'next_text' => 'Next &raquo;', // Next button text
					'type'      => 'plain', // Output plain pagination without numbers

				));

				// Display pagination links
				if ($pagination_links) {
					$pagination_links = '<div class="af-request-a-quote pagination">' . $pagination_links . '</div>';

					echo wp_kses($pagination_links, wp_kses_allowed_html('post')) ;
				}

			}
		}

		private function afrfq_get_color_option( $option_name, $default ) {
			$option = get_option($option_name);
			if (empty($option)) {
				return $default;
			}
			return $option;
		}
		

		public function afrfq_css_load() {
			$current_theme = wp_get_theme();
			$parent_theme  = $current_theme->parent();
			?>
			<style type="text/css">
			<?php
			if ($current_theme->get('Name') === 'Divi' || ( $parent_theme && $parent_theme->get('Name') === 'Divi' )) {
				?>
				.mini-quote-dropdown{
					box-shadow: 0 2px 5px rgba(0,0,0,.1);
				}
				.addify-rfq-mini-cart__buttons #view-quote{
					color: #fff!important;
					display: block;
					margin-top: 10px!important;
				}
				.mini-quote-dropdown .addify-rfq-mini-cart{
					box-shadow: none!important;
				}
				.woocommerce .addify-quote-form__contents .product-quantity input.qty{
					background-color: #fff!important;
					color: #000!important;
					font-size: 13px!important;
					border: 1px solid #99939333!important;
				}
				.template_two .product-price.offered-price, .template_two .product-subtotal,
				.template_two .product-subtotal.offered-subtotal{
					min-width: auto!important;
				}
				.afrfqbt_single_page + .added_to_quote{
					padding: 12px 20px !important;
					line-height: 23px !important;
					font-size: 17px !important;
					float: left;
				}
				.afrfq-quote-actions {
					display: flex!important;
					align-items: start!important; 
					gap: 8px!important; 
				}
			<?php
			}
			if ($current_theme->get('Name') === 'Astra' || ( $parent_theme && $parent_theme->get('Name') === 'Astra' )) {
				?>
					.afrq-menu-item{
							height: 100%;
						display: flex;
						text-decoration: none!important;
						align-items: center;
					}
					.afrfqbt + .added_to_quote{
						display: inline-block!important;
					}
				<?php
			}
			if ($current_theme->get('Name') === 'Avada' || ( $parent_theme && $parent_theme->get('Name') === 'Avada' )) {
				?>
				.addify-quote-form__contents .product-name{
					width: auto!important;
				}
				.addify-quote-form__contents .product-thumbnail{
					float: none!important;
				}
				.addify-quote-form__contents.template-two .product-quantity .quantity{
					width: 89px;
					margin-left: 0;
				}
				.afrfqbt_single_page + .added_to_quote{
					padding: 9px 20px!important;
					line-height: 18px!important;
					font-size: 14px!important;
					display: block!important;
				}
				.afrfqbt + .added_to_quote{
					display: block!important;
					padding-left: 7px ! Important;
				}
				.afrfqbt{
						margin-left: 5px;
				}
				.add_to_cart_button + .afrfqbt + .added_to_quote + .show_details_button{
					float: none !important;
				}
			<?php
			}
			if ($current_theme->get('Name') === 'Woodmart' || ( $parent_theme && $parent_theme->get('Name') === 'Woodmart' )) {
				?>
				form.addify-quote-form.template_one .addify-quote-form__contents .product-quantity .minus,
				form.addify-quote-form.template_one .addify-quote-form__contents .product-quantity .plus{
					background: no-repeat;
					border: 1px solid #99939333;
					line-height: 1.618;
					min-height: auto;
					width: 27px !important;
					padding: 3px 0;
				}
				form.addify-quote-form.template_two .addify-quote-form__contents .product-quantity .minus,
				form.addify-quote-form.template_two .addify-quote-form__contents .product-quantity .plus{
					width: 28px !important;
				}
				.addify-quote-form__contents.template-two th{
					font-size: 13px;
					line-height: 22px;
				}
				.template_two .product-price.offered-price, .template_two .product-subtotal,
				.template_two .product-subtotal.offered-subtotal{
					min-width: 100%!important;
				}
				.afrfqbt + .added_to_quote{
					display: inline-block!important;
					background: no-repeat!important;
					color: inherit!important;
					margin-top: 6px;
					box-shadow: none!important;
				}
				.afrfqbt_single_page + .added_to_quote{
						display: inline-flex;
						align-items: center;
						flex: 0 0 auto!important;
				}
				.afrfqbt.button{
					margin-left: 10px;
					min-height: 36px!important;
				}
			<?php
			} 
			
			if ($current_theme->get('Name') === 'Twenty Twenty-Five') {
				?>
				.wp-block-button__link {
					height: auto !important;
				}
				.afrfqbt_single_page{
					margin: 10px 0px !important;
				}
				<?php
			}
			?>
			</style>
			<?php
		}

		public function afrfq_woocommerce_cart_block_do_actions( $block_content, $block ) {
			$blocks = array(
				'woocommerce/cart-line-items-block',
			);
			if ( in_array( $block['blockName'], $blocks ) ) {
				ob_start();
				do_action( 'addify_before_' . $block['blockName'] );
				echo wp_kses_post($block_content);
				do_action( 'addify_after_' . $block['blockName'] );
				$block_content = ob_get_contents();
				ob_end_clean();
			}
			return $block_content;
		}

		// fix for product not adding to quote in woocommerce version after 10.0.1
		public function afrfq_custom_start_wc_session() {
			if ( function_exists( 'WC' ) && WC()->session ) {
				WC()->session->set_customer_session_cookie( true );
			}
		}
	}
	new AF_R_F_Q_Front();
}


