<?php
namespace Barn2\Plugin\WC_Product_Options\Integration;

use Barn2\Plugin\WC_Product_Options\Util\Util;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Util as Lib_Util;

use function Barn2\Plugin\WC_Product_Options\wpo;

/**
 * Handles the WooCommerce Quick View Pro integration.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Quick_View_Pro implements Registerable, Standard_Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		if ( ! Lib_Util::is_barn2_plugin_active( '\Barn2\Plugin\WC_Quick_View_Pro\wqv' ) ) {
			return;
		}

		add_action( 'wc_quick_view_pro_load_scripts', [ $this, 'load_scripts' ] );
		add_action( 'wc_quick_view_pro_before_quick_view', [ $this, 'setup_hooks' ] );
	}

	/**
	 * Set the global product for WPO to use in the quick view.
	 *
	 * @param WC_Product $product The product being displayed in the quick view.
	 */
	public function setup_hooks( $product ) {
		$single_product_handler = wpo()->get_service( 'handlers/single_product' );

		if ( is_null( $single_product_handler ) ) {
			return;
		}

		$single_product_handler->setup_hooks();
	}

	/**
	 * Load frontend scripts.
	 */
	public function load_scripts() {
		wp_enqueue_script( 'wpo-quick-view' );
		wp_enqueue_style( 'wpo-frontend-fields' );
	}
}
