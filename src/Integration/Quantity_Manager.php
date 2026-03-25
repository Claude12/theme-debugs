<?php
namespace Barn2\Plugin\WC_Product_Options\Integration;

use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Util as Lib_Util;
use Barn2\Plugin\WC_Quantity_Manager\Util\Quantity as WQM_Quantity;

/**
 * Handles the WooCommerce Quantity Manager integration.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Quantity_Manager implements Registerable, Standard_Service {

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		if ( ! Lib_Util::is_barn2_plugin_active( '\Barn2\Plugin\WC_Quantity_Manager\wqm' ) ) {
			return;
		}

		add_filter( 'wc_product_options_product_quantity_picker_restrictions', [ $this, 'get_quantity_picker_restrictions' ], 10, 4 );
	}

	/**
	 * Get the quantity restriction for an addon product.
	 *
	 * This method is used as a callback function for multiple filter hooks.
	 * For this reason, the value being returned depends on the current filter.
	 *
	 * @param int $value The current minimum quantity.
	 * @param Abstract_Field $field The field.
	 * @param WC_Product $product The current product.
	 * @param WC_Product $addon_product The current addon product.
	 * @return int The updated quantity restriction.
	 */
	public function get_quantity_picker_restrictions( $args, $field, $product, $addon_product ) {
		$wqm_args = WQM_Quantity::get_calculated_quantity_restrictions( $addon_product );

		$wqm_args['value'] = $wqm_args['input_value'];
		unset( $wqm_args['input_value'] );

		foreach ( $args as $key => $value ) {
			$wqm_value = $wqm_args[ $key ] ?? '';
			switch ( $key ) {
				case 'max':
					if ( empty( $value ) ) {
						$args[ $key ] = $wqm_value;
					} else {
						$args[ $key ] = min( $value, $wqm_value );
					}
					break;
				case 'min':
				case 'step':
				default:
					$args[ $key ] = max( $value, $wqm_value );
					break;
			}
		}

		return $args;
	}
}
