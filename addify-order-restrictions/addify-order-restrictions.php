<?php
/**
 * WooCommerce Order Restrictions
 */

defined( 'ABSPATH' ) || exit;


if ( ! defined( 'AFOR_URL' ) ) {
	define( 'AFOR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'AFOR_PLUGIN_DIR' ) ) {
	define( 'AFOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Include the main WooCommerce Registration form Builder class.
if ( ! class_exists( 'AF_Order_Restrictions', false ) ) {
	include_once AFOR_PLUGIN_DIR . 'includes/class-af-order-restrictions.php';
}
