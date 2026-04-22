<?php
/**
 * Cart Based Discount for WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Discount_Cart_Main' ) ) {

	/**
	 * Discount main class.
	 */
	class Discount_Cart_Main {
		/**
		 * Constructor.
		 * */
		public function __construct() {

			$this->af_dcv_global_constents_vars();

			add_action( 'init', array( $this, 'af_dcv_discount_custompostype' ) );

			include DCV_PLUGIN_DIR . 'include/class-af-discount-cart-ajax.php';

			if ( is_admin() ) {

				include DCV_PLUGIN_DIR . 'include/admin/class-dcv-discount-cart-admin.php';

			} else {

				include DCV_PLUGIN_DIR . 'include/front/class-af-discount-cart-front.php';
			}
		}
		/**
		 * Custom post.
		 * */
		public function af_dcv_discount_custompostype() {

			$labels = array(
				'exclude_from_search' => true,
				'name'                => esc_html__( 'Cart Discounts Rules', 'addify_b2b' ),
				'singular_name'       => esc_html__( 'Cart Discounts Rules', 'addify_b2b' ),
				'add_new'             => esc_html__( 'Add New', 'addify_b2b' ),
				'add_new_item'        => esc_html__( 'Add New', 'addify_b2b' ),
				'edit_item'           => esc_html__( 'Edit Rule', 'addify_b2b' ),
				'new_item'            => esc_html__( 'Cart Discounts', 'addify_b2b' ),
				'view_item'           => esc_html__( 'View Cart Discounts', 'addify_b2b' ),
				'search_items'        => esc_html__( 'Search Cart Discounts', 'addify_b2b' ),
				'not_found'           => esc_html__( 'No Cart found', 'addify_b2b' ),
				'not_found_in_trash'  => esc_html__( 'No cart found in trash', 'addify_b2b' ),
				'parent_item_colon'   => '',
				'menu_name'           => esc_html__( 'Cart Discounts', 'addify_b2b' ),
				'item_published'      => esc_html__( 'Cart published', 'addify_b2b' ),
				'item_updated'        => esc_html__( 'Cart updated', 'addify_b2b' ),
			);

			$supports = array( 'title' );

			$options = array(
				'labels'            => $labels,
				'public'            => false,
				'publicly_querable' => false,
				'query_var'         => true,
				'capability_type'   => 'post',
				'can_export'        => true,
				'show_ui'           => true,
				'show_in_menu'      => 'addify-b2b',
				'menu_position'     => 30,
				'show_in_admin_bar' => true,
				'show_in_nav_menus' => true,
				'has_archive'       => true,
				'rewrite'           => array(
					'slug'       => 'discount_cart',
					'with_front' => false,
				),
				'show_in_rest'      => true,
				'supports'          => $supports,
			);
			register_post_type( 'discount_cart', $options );
		}
		/**
		 * Directories path.
		 * */
		public function af_dcv_global_constents_vars() {

			if ( ! defined( 'DCV_URL' ) ) {

				define( 'DCV_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'DCV_BASENAME' ) ) {

				define( 'DCV_BASENAME', plugin_basename( __FILE__ ) );
			}

			if ( ! defined( 'DCV_PLUGIN_DIR' ) ) {

				define( 'DCV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}
		}
	}

	new Discount_Cart_Main();
}
