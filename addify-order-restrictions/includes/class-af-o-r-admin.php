<?php

defined( 'ABSPATH' ) || exit;

class AF_O_R_Admin {

	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 100 );

		// Meta boxes
		add_action( 'add_meta_boxes', array( $this, 'add_rule_metaboxes' ) );

		add_action('save_post_af_order_rule', array( $this, 'save_rule_meta' ), 100 , 1 );
	}

	

	public function save_rule_meta( $post_id ) {

		$exclude_statuses = array(
			'auto-draft',
			'trash',
		);

		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

		if ( !in_array( get_post_status($post_id), $exclude_statuses ) && !is_ajax() && 'untrash' != $action ) {
			include_once AFOR_PLUGIN_DIR . 'includes/admin/meta-boxes/rule/save-rule.php';
		}
	}

	public function add_rule_metaboxes() {

		add_meta_box(
			'af_order_rule_general', 
			__('Order Rule', 'addify_b2b'),
			array( $this, 'order_restriction_metabox_callback' ),
			'af_order_rule', 
			'advanced'
		);
	}

	public function order_restriction_metabox_callback() {

		include_once AFOR_PLUGIN_DIR . 'includes/admin/meta-boxes/rule/general.php';
	}

	public function enqueue_scripts() {

		$screen = get_current_screen();

		wp_enqueue_style('afor_admin', AFOR_URL . 'assets/css/admin.css', array(), '1.0.0');

		if ( 'af_order_rule' == $screen->post_type ) {
			wp_enqueue_style('select2', plugins_url( 'assets/css/select2.css', WC_PLUGIN_FILE ) , array(), '5.7.2' );
			wp_enqueue_script('select2', plugins_url( 'assets/js/select2/select2.min.js', WC_PLUGIN_FILE ), array( 'jquery' ), '4.0.3', true );
		}

		wp_enqueue_script('afor_admin_js', AFOR_URL . 'assets/js/admin.js', array(), '1.0.0', true );

		$data = array(
			'admin_url' => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'af-o-r-ajax-nonce' ),
		);
		
		wp_localize_script( 'afor_admin_js', 'php_vars', $data );
	}
}
new AF_O_R_Admin();
