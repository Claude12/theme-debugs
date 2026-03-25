<?php
namespace Barn2\Plugin\WC_Product_Options\Integration;

use Barn2\Plugin\WC_Product_Options\Util\Util;
use Barn2\Plugin\WC_Product_Options\Util\Display as Display_Util;
use Barn2\Plugin\WC_Product_Options\Util\Price as Price_Util;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Util as Lib_Util;
use Barn2\Plugin\WC_Bulk_Variations\Util\Util as WBV_Util;

/**
 * Handles the Bulk Variations integration.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Bulk_Variations implements Registerable, Standard_Service {
	// An odd priority to ensure we only remove our own filters later.
	const PRIORITY = 1031;

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		if ( ! Lib_Util::is_barn2_plugin_active( '\Barn2\Plugin\WC_Bulk_Variations\wbv' ) ) {
			return;
		}

		add_filter( 'wc_product_options_field_has_display_prerequisites', [ $this, 'remove_attribute_option_fields' ], 10, 2 );
		add_action( 'wc_bulk_variations_before_totals_container', [ $this, 'add_product_options' ], 10, 1 );
		add_action( 'wc_bulk_variations_table_before_add_cells', [ $this, 'add_price_display_filter' ] );
		add_action( 'wc_bulk_variations_table_after_add_cells', [ $this, 'remove_price_display_filter' ] );
	}

	/**
	 * Removes the attribute option fields from the Bulk Variations table products.
	 *
	 * @param bool $display
	 * @param Abstract_Field $field
	 * @return bool
	 */
	public function remove_attribute_option_fields( $display, $field ) {
		if ( $display === false ) {
			return $display;
		}

		if ( WBV_Util::is_variations_grid_enabled( $field->get_product() ) && $field->is_valid_attribute_option_for_product() ) {
			return false;
		}

		return $display;
	}

	/**
	 * Add the product options after the Bulk Variations table ouput.
	 *
	 * @param int $product_id
	 * @return string
	 */
	public function add_product_options( $product_id ) {
		$product = wc_get_product( $product_id );
		$groups  = Util::get_product_groups( $product );

		if ( empty( $groups ) ) {
			return;
		}

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Display_Util::get_groups_html( $groups, $product );
		echo self::get_totals_container_html( $product );
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Retrives the totals HTML for the supplied product
	 *
	 * @param WC_Product $product
	 * @return string
	 */
	public static function get_totals_container_html( $product ) {
		$exclude_price = Price_Util::get_product_price_exclusion_status( $product );

		$html = sprintf(
			'<div class="wpo-totals-container" data-product-price="%1$s" data-exclude-product-price="%2$s"></div>',
			esc_attr( wc_get_price_to_display( $product, [ 'price' => $product->get_price() ] ) ),
			$exclude_price ? 'true' : 'false'
		);

		return $html;
	}

	/**
	 * Add the price display filter before rendering the Bulk Variations table.
	 *
	 * This method uses an odd priority to ensure it only removes its own filter later
	 * in case other plugins are also adding the same callback to this filter.
	 *
	 * @return void
	 */
	public function add_price_display_filter() {
		add_filter( 'wc_product_options_use_loop_price_display', '__return_false', self::PRIORITY );
	}

	/**
	 * Remove the price display filter after rendering the Bulk Variations table.
	 *
	 * @return void
	 */
	public function remove_price_display_filter() {
		remove_filter( 'wc_product_options_use_loop_price_display', '__return_false', self::PRIORITY );
	}
}
