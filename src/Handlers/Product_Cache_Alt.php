<?php
namespace Barn2\Plugin\WC_Product_Options\Handlers;

use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\WC_Product_Options\Model\Group as Group_Model;

use WC_Product;

/**
 * Class to handle the groups of a product.
 *
 * This class is used by all other handlers to get the groups for the current product
 * and centralizes the logic for loading and caching the groups.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Cache_Alt extends Product_Cache implements Registerable, Standard_Service {
	/**
	 * Load groups for a product from the database.
	 *
	 * This method runs only one query per product to load all its groups and options.
	 * Nevertheless, this may result in running more JOIN queries than necessary.
	 * Compared to the parent method, this implementation may result in running more JOIN queries,
	 * which are more time-consuming.
	 *
	 * @param WC_Product $product The product to prepare.
	 *
	 * @return void
	 */
	protected function load_groups_for_product( $product ) {
		$json_columns = [
			'products',
			'exclude_products',
			'categories',
			'exclude_categories',
			'user_roles',
			'exclude_user_roles',
			'settings',
		];

		// This is the only case groups are loaded from the database.
		$this->groups = Group_Model::get_groups_with_options_by_product( $this->product );

		if ( empty( $this->groups ) ) {
			return;
		}

		foreach ( $this->groups as &$group ) {
			if ( ! empty( $group->options ) ) {
				foreach ( $group->options as $option ) {
					// Transform option JSON fields if needed
					$transformed_option               = $this->transform_option( $option );
					$this->all_options[ $option->id ] = $transformed_option;
				}
			}

			$converted_values = (object) array_map(
				function ( $value, $key ) use ( $json_columns ) {
					if ( in_array( $key, $json_columns, true ) && is_string( $value ) ) {
						return json_decode( $value, true );
					}
					return $value;
				},
				(array) $group,
				array_keys( (array) $group )
			);

			$group = (object) array_combine(
				array_keys( (array) $group ),
				array_values( (array) $converted_values )
			);
		}

		// Clear option caches when new options are loaded
		$this->clear_option_caches();

		// Store groups and product mapping
		$group_keys                               = array_map( 'intval', wp_list_pluck( $this->groups, 'id' ) );
		$this->all_groups                         = $this->all_groups + array_combine( $group_keys, $this->groups );
		$this->all_products[ $product->get_id() ] = $group_keys;

		// Check if we need to evict old products to manage memory
		$this->maybe_evict_old_products();
	}
}
