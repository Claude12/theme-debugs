<?php

defined( 'ABSPATH' ) || exit;

class AF_Order_Restrictions {

	public function __construct() {

		$this->add_plugin_files();

		// Register Post Types.
		add_action( 'init', array( $this, 'register_post_types' ), 5 );
	}

	public function add_plugin_files() {

		include_once AFOR_PLUGIN_DIR . 'includes/class-af-o-r-ajax.php';

		if ( is_admin() ) {
			include_once AFOR_PLUGIN_DIR . 'includes/class-af-o-r-admin.php';
		} else {
			include_once AFOR_PLUGIN_DIR . 'includes/class-af-o-r-front.php';
		}
	}

	public function register_post_types() {

		$labels = array(
			'name'               => esc_html__( 'Order Restrictions', 'addify_b2b' ),
			'singular_name'      => esc_html__( 'Order Restriction', 'addify_b2b' ),
			'add_new'            => esc_html__( 'Add New Rule', 'addify_b2b' ),
			'add_new_item'       => esc_html__( 'Add New Rule', 'addify_b2b' ),
			'edit_item'          => esc_html__( 'Edit Rule', 'addify_b2b' ),
			'new_item'           => esc_html__( 'New Rule', 'addify_b2b' ),
			'view_item'          => esc_html__( 'View Rule', 'addify_b2b' ),
			'search_items'       => esc_html__( 'Search Rule', 'addify_b2b' ),
			'not_found'          => esc_html__( 'No Rule found', 'addify_b2b' ),
			'not_found_in_trash' => esc_html__( 'No rule found in trash', 'addify_b2b' ),
			'parent_item_colon'  => '',
			'all_items'          => esc_html__( 'Order Restrictions', 'addify_b2b' ),
			'menu_name'          => esc_html__( 'Order Restrictions', 'addify_b2b' ),
			'attributes'         => esc_html__( 'Rule Priority', 'addify_b2b' ),
			'item_published'     => esc_html__( 'Rule published', 'addify_b2b' ),
			'item_updated'       => esc_html__( 'Rule updated', 'addify_b2b' ),
		);

		$args = array(
			'labels'              => $labels,
			'menu_icon'           => '',
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => 'addify-b2b',
			'query_var'           => true,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'capability_type'     => 'post',
			'capabilities'        => array(),
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 100,
			'rewrite'             => array(
				'slug'       => 'af_order_rule',
				'with_front' => false,
			),
			'supports'            => array( 'title' ),
		);

		register_post_type( 'af_order_rule', $args );
	}
}

new AF_Order_Restrictions();
