<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;


if ( ! class_exists( 'Addify_Request_For_Quote' ) ) {

	class Addify_Request_For_Quote {

		/**
		 * Contains an array of quote items.
		 *
		 * @var array
		 */
		public $quote_fields_obj = array();

		public function __construct() {

			$this->afrfq_global_constents_vars();
			add_action( 'init', array( $this, 'afrfq_plugin_init' ) );
			
			add_action( 'before_woocommerce_init', array( $this, 'afrfq_h_o_p_s_compatibility' ) );

			add_action( 'woocommerce_blocks_loaded', array( $this, 'afrfq_init_store_api' ) );

			include_once AFRFQ_PLUGIN_DIR . '/includes/post-types/class-af-r-f-q-quote-controller.php';
			include_once AFRFQ_PLUGIN_DIR . '/includes/post-types/class-af-r-f-q-rule-controller.php';
			include_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-main.php';
			include_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-quote.php';
			include_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-quote-fields.php';
			include_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-email-controller.php';
			include_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-ajax-controller.php';
			include_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-product-addon.php';
			include_once AFRFQ_PLUGIN_DIR . '/af-rfq-general-functions.php';

			if ( is_admin() ) {
				include_once AFRFQ_PLUGIN_DIR . 'admin/class-af-r-f-q-admin.php';
			} else {
				include_once AFRFQ_PLUGIN_DIR . 'front/class-af-r-f-q-front.php';
				// Load the Convert Quote to Cart handler
				include_once AFRFQ_PLUGIN_DIR . 'includes/class-af-r-f-q-convert-to-cart-handler.php';

			}
		}

		public function afrfq_plugin_init() {

			if ( defined( 'WC_PLUGIN_FILE' ) ) {
				$this->afrfq_custom_post_type();
				$this->include_compatibility_classes();
				$this->load_rest_api();
				$this->afrfq_quote_default_settings();
			}
		}

		public function afrfq_quote_default_settings() {

			if (get_option( 'afrfq_select_popup_template', null ) === null) {
				$defaults = array(
					'afrfq_select_popup_template'              => 'template_two',
					'afrfq_enable_popup_table_header_color'    => 'yes',
					'afrfq_popup_table_header_color'           => '#ffffff',
					'afrfq_popup_table_header_bg_color'        => '#000000',
					'afrfq_popup_active_step_button_fg_color'  => '#ffffff',
					'afrfq_popup_active_step_button_bg_color'  => '#0073AA',
					'afrfq_popup_previous_step_button_fg_color'=> '#000000',
					'afrfq_popup_previous_step_button_bg_color'=> '#ffffff',
					'afrfq_popup_next_step_button_fg_color'    => '#ffffff',
					'afrfq_popup_next_step_button_bg_color'    => '#0073AA',
				);
			
				foreach ( $defaults as $option_name => $default_value ) {
					if ( get_option( $option_name, null ) === null ) {
						add_option( $option_name, $default_value );
					}
				}
			}
		}

		public function afrfq_init_store_api() {
			include_once AFRFQ_PLUGIN_DIR . 'includes/class-af-r-f-q-extend-store-endpoint.php';

			$extend = StoreApi::container()->get( ExtendSchema::class );
			AF_R_F_Q_Extend_Store_Endpoint::init( $extend );
		}

		
		public function afrfq_h_o_p_s_compatibility() {

			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		}

		public function load_rest_api() {
			require AFRFQ_PLUGIN_DIR . 'includes/rest-api/class-server.php';
			\Addify\Request_a_Quote\RestApi\Server::instance()->init();
		}

		public function include_compatibility_classes() {

			if ( defined( 'AFB2B_PLUGIN_DIR' ) ) {
				include_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-b2b-compatibility.php';
			}

			if ( defined( 'ADDIFY_WSP_PLUGINDIR' ) ) {
				include_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-whole-sale-compatibility.php';
			}

			if (defined('ADDF_PRC_DIR')) {
				include_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-addify-price-calculator-compatibilty.php';
			}

			if (defined('AFCPB_DIR_PATH')) {
				include_once AFRFQ_PLUGIN_DIR . '/includes/class-r-f-q-addify-composite-product-builder-compatiblity.php';
			}
		}

		public function afrfq_global_constents_vars() {

			if ( ! defined( 'AFRFQ_URL' ) ) {
				define( 'AFRFQ_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'AFRFQ_BASENAME' ) ) {
				define( 'AFRFQ_BASENAME', plugin_basename( __FILE__ ) );
			}

			if ( ! defined( 'AFRFQ_PLUGIN_DIR' ) ) {
				define( 'AFRFQ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			$upload_dir = wp_upload_dir();

			$upload_path = $upload_dir['basedir'] . '/addify-rfq/';

			if ( ! is_dir( $upload_path ) ) {
				mkdir( $upload_path );
			}

			$upload_url = $upload_dir['baseurl'] . '/addify-rfq/';

			define( 'AFRFQ_UPLOAD_DIR', $upload_path );
			define( 'AFRFQ_UPLOAD_URL', $upload_url );

			// a dedicated directory for temporarily store uploaded files via popup
			$upload_path = $upload_path . '/addify-rfq-temp/';

			if ( ! is_dir( $upload_path ) ) {
				mkdir( $upload_path );
			}

			$upload_url = $upload_url . '/addify-rfq-temp/';

			define( 'AFRFQ_TEMP_UPLOAD_DIR', $upload_path );
			define( 'AFRFQ_TEMP_UPLOAD_URL', $upload_url );

			// // temp file cleanup after 30 minutes
			if (!get_option('af_rfq_temp_file_cleanup') || get_option('af_rfq_temp_file_cleanup') < time() - 1800) {

				update_option('af_rfq_temp_file_cleanup', time());
				$files = glob( AFRFQ_TEMP_UPLOAD_DIR . '*' ); // Get all files in the directory

				$now = time();

				foreach ( $files as $file ) {
					if ( is_file( $file ) ) {
						if ( $now - filemtime( $file ) > 1800 ) {
							unlink( $file ); // Delete file if older than half hour
						}
					}
				}
			}
		}


		public function afrfq_custom_post_type() {

			$labels = array(
				'name'                => esc_html__( 'Request for Quote Rules', 'addify_b2b' ),
				'singular_name'       => esc_html__( 'Request for Quote Rule', 'addify_b2b' ),
				'add_new'             => esc_html__( 'Add New Rule', 'addify_b2b' ),
				'add_new_item'        => esc_html__( 'Add New Rule', 'addify_b2b' ),
				'edit_item'           => esc_html__( 'Edit Rule', 'addify_b2b' ),
				'new_item'            => esc_html__( 'New Rule', 'addify_b2b' ),
				'view_item'           => esc_html__( 'View Rule', 'addify_b2b' ),
				'search_items'        => esc_html__( 'Search Rule', 'addify_b2b' ),
				'exclude_from_search' => true,
				'not_found'           => esc_html__( 'No rule found', 'addify_b2b' ),
				'not_found_in_trash'  => esc_html__( 'No rule found in trash', 'addify_b2b' ),
				'parent_item_colon'   => '',
				'menu_name'           => esc_html__( 'Quote Rules', 'addify_b2b' ),
				'attributes'          => esc_html__( 'Rule Priority', 'addify_b2b' ),
				'item_published'      => esc_html__( 'Quote rule published', 'addify_b2b' ),
				'item_updated'        => esc_html__( 'Quote rule updated', 'addify_b2b' ),
			);

			$args = array(
				'labels'             => $labels,
				'menu_icon'          => plugin_dir_url( __FILE__ ) . 'assets/images/small_logo_white.png',
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => 'addify-b2b',
				'query_var'          => true,
				// 'rewrite'            => true,.
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 30,
				'rewrite'            => array(
					'slug'       => 'addify_rfq',
					'with_front' => false,
				),
				'supports'           => array( 'title', 'page-attributes' ),
			);

			register_post_type( 'addify_rfq', $args );

			$labels = array(
				'name'                => esc_html__( 'Fields for Request a Quote', 'addify_b2b' ),
				'singular_name'       => esc_html__( 'Field for Quote Rule', 'addify_b2b' ),
				'add_new'             => esc_html__( 'Add New Field', 'addify_b2b' ),
				'add_new_item'        => esc_html__( 'Add New Field', 'addify_b2b' ),
				'edit_item'           => esc_html__( 'Edit Field', 'addify_b2b' ),
				'new_item'            => esc_html__( 'New Field', 'addify_b2b' ),
				'view_item'           => esc_html__( 'View Field', 'addify_b2b' ),
				'search_items'        => esc_html__( 'Search Field', 'addify_b2b' ),
				'exclude_from_search' => true,
				'not_found'           => esc_html__( 'No Field found', 'addify_b2b' ),
				'not_found_in_trash'  => esc_html__( 'No Field found in trash', 'addify_b2b' ),
				'parent_item_colon'   => '',
				'menu_name'           => esc_html__( 'Quote Fields', 'addify_b2b' ),
				'attributes'          => esc_html__( 'Field Attributes', 'addify_b2b' ),
				'item_published'      => esc_html__( 'Quote field published', 'addify_b2b' ),
				'item_updated'        => esc_html__( 'Quote field updated', 'addify_b2b' ),
			);

			$args = array(
				'labels'             => $labels,
				'menu_icon'          => plugin_dir_url( __FILE__ ) . 'assets/images/small_logo_white.png',
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => 'addify-b2b',
				'query_var'          => true,
				// 'rewrite'            => true,.
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 30,
				'rewrite'            => array(
					'slug'       => 'addify_rfq_fields',
					'with_front' => false,
				),
				'supports'           => array( 'title', 'page-attributes' ),
			);

			register_post_type( 'addify_rfq_fields', $args );

			$labels = array(
				'name'                => esc_html__( 'Quotes', 'addify_b2b' ),
				'singular_name'       => esc_html__( 'Quote', 'addify_b2b' ),
				'add_new'             => esc_html__( 'New Quote', 'addify_b2b' ),
				'add_new_item'        => esc_html__( 'New Quote', 'addify_b2b' ),
				'edit_item'           => esc_html__( 'Edit Quote', 'addify_b2b' ),
				'new_item'            => esc_html__( 'New Quote', 'addify_b2b' ),
				'view_item'           => esc_html__( 'View Quote', 'addify_b2b' ),
				'search_items'        => esc_html__( 'Search Quote', 'addify_b2b' ),
				'exclude_from_search' => true,
				'not_found'           => esc_html__( 'No Quote found', 'addify_b2b' ),
				'not_found_in_trash'  => esc_html__( 'No quote found in trash', 'addify_b2b' ),
				'parent_item_colon'   => '',
				'menu_name'           => esc_html__( 'All Quotes', 'addify_b2b' ),
				'item_published'      => esc_html__( 'Quote published', 'addify_b2b' ),
				'item_updated'        => esc_html__( 'Quote updated', 'addify_b2b' ),
			);

			$args = array(
				'labels'             => $labels,
				'menu_icon'          => plugin_dir_url( __FILE__ ) . 'assets/images/small_logo_white.png',
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => 'addify-b2b',
				'query_var'          => true,
				// 'rewrite'            => true,.
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 30,
				'rewrite'            => array(
					'slug'       => 'addify_quote',
					'with_front' => false,
				),
				'supports'           => array( 'title' ),
			);

			register_post_type( 'addify_quote', $args );
		}
	}

	new Addify_Request_For_Quote();

}
