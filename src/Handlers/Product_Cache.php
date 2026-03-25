<?php
namespace Barn2\Plugin\WC_Product_Options\Handlers;

use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Registerable;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\WC_Product_Options\Model\Group as Group_Model;
use Barn2\Plugin\WC_Product_Options\Util\Util;

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
class Product_Cache implements Registerable, Standard_Service {
	/**
	 * The product data (groups, image options, formula options, attribute options)
	 * attached to all the products loaded during a page request.
	 * The indexes are group IDs.
	 *
	 * @var array
	 */
	protected $all_groups = [];

	/**
	 * The image options attached to all products.
	 *
	 * @var array
	 */
	protected $all_options = [];

	/**
	 * The IDs of all products loaded during a page request.
	 *
	 * @var array
	 */
	protected $all_products = [];

	/**
	 * The current product.
	 *
	 * @var WC_Product|null
	 */
	protected $product;

	/**
	 * The groups attached to the current product.
	 *
	 * @var array
	 */
	protected $groups = [];

	/**
	 * Cache for image options to avoid repeated filtering.
	 *
	 * @var array|null
	 */
	protected $image_options_cache = null;

	/**
	 * Cache for formula options to avoid repeated filtering.
	 *
	 * @var array|null
	 */
	protected $formula_options_cache = null;

	/**
	 * Cache for attribute options to avoid repeated filtering.
	 *
	 * @var array|null
	 */
	protected $attribute_options_cache = null;

	/**
	 * Maximum number of products to cache before evicting old ones.
	 *
	 * @var int
	 */
	protected $max_cached_products = 50;

	/**
	 * {@inheritDoc}
	 */
	public function register() {
		add_action( 'the_post', [ $this, 'set_product_from_post' ], 11 );
	}

	/**
	 * Sync the data of the current product when the post changes.
	 *
	 * @param WP_Post $post The current post.
	 *
	 * @return void
	 */
	public function set_product_from_post( $post ) {
		if ( ( $post->post_type ?? '' ) !== 'product' ) {
			return;
		}

		$this->set_product();
	}

	/**
	 * Sets the current product.
	 *
	 * @param WC_Product $product The product to set.
	 *
	 * @return void
	 */
	public function set_product( $product = null ) {
		if ( is_null( $product ) ) {
			$product = wc_get_product();

			// If no product found, reset and return.
			if ( ! $product ) {
				$this->reset_product();
				return;
			}
		}

		$this->prepare_product( $product );
	}

	/**
	 * Load the groups for the current product.
	 *
	 * @param WC_Product $product The product to prepare.
	 *
	 * @return void
	 */
	protected function prepare_product( $product ) {
		if ( is_a( $product, 'WC_Product_Variation' ) ) {
			$product = wc_get_product( $product->get_parent_id() );
		}

		if ( $product === $this->product && $this->has_option_groups() ) {
			// The same product is already set as the current product
			// and groups are loaded, so avoid reloading.
			return;
		}

		if (
			! $product ||
			! is_a( $product, 'WC_Product' ) ||
			! Util::is_allowed_product_type( $product->get_type() )
		) {
			$this->reset_product();
			return;
		}

		$this->product = $product;

		// Check cache first
		if ( isset( $this->all_products[ $product->get_id() ] ) ) {
			$this->groups = $this->get_product_groups( $product->get_id() );
			return;
		}

		$this->load_groups_for_product( $product );
	}

	/**
	 * Load groups for a product from the database.
	 *
	 * This method only loads groups and options that are not already cached.
	 * To do that, a two-step approach is used: first, group IDs are fetched with a cheap query,
	 * then only missing groups are loaded with a more expensive JOIN query.
	 * Since groups and options are cached globally, this minimizes database load
	 * when multiple products share the same groups.
	 *
	 * @param WC_Product $product The product to load groups for.
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

		// Get group IDs (cheap query)
		$group_ids = wp_list_pluck( Group_Model::get_groups_by_product( $this->product, 'id' ), 'id' );

		if ( empty( $group_ids ) ) {
			$this->all_products[ $product->get_id() ] = [];
			return;
		}

		// Find which groups we DON'T have cached yet
		$cached_group_ids  = array_keys( $this->all_groups );
		$missing_group_ids = array_diff( $group_ids, $cached_group_ids );

		// Only load missing groups with expensive JOIN query
		if ( ! empty( $missing_group_ids ) ) {
			$new_groups = Group_Model::get_groups_with_options_by_ids( $missing_group_ids );

			// Process and cache new groups
			foreach ( $new_groups as $group ) {
				// Extract and transform options
				if ( ! empty( $group->options ) ) {
					foreach ( $group->options as $option ) {
						$this->all_options[ $option->id ] = $this->transform_option( $option );
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

				$this->all_groups[ $group->id ] = $group;
			}

			$this->clear_option_caches();
		}

		// Assign groups to current product (from cache)
		$this->groups = array_map(
			function ( $id ) {
				return $this->all_groups[ $id ];
			},
			$group_ids
		);

		$this->all_products[ $product->get_id() ] = $group_ids;
		$this->maybe_evict_old_products();
	}

	/**
	 * Transform a raw option object by decoding JSON fields.
	 *
	 * @param object $option The raw option object from the database.
	 *
	 * @return object The transformed option object.
	 */
	protected function transform_option( $option ) {
		// Decode choices if it's a JSON string
		$option->choices = is_string( $option->choices )
			? json_decode( $option->choices, true )
			: $option->choices;

		// Decode settings if it's a JSON string
		$option->settings = is_string( $option->settings )
			? json_decode( $option->settings, true )
			: $option->settings;

		// Handle the conditional_logic field with its quirky format
		if ( is_string( $option->conditional_logic ) ) {
			$clean                     = preg_replace( '/^"(.*)"$/', '$1', $option->conditional_logic );
			$option->conditional_logic = json_decode( $clean, true );
		}

		return $option;
	}

	/**
	 * Clear cached filtered option results.
	 *
	 * @return void
	 */
	protected function clear_option_caches() {
		$this->image_options_cache     = null;
		$this->formula_options_cache   = null;
		$this->attribute_options_cache = null;
	}

	/**
	 * Check if we should evict old products to manage memory.
	 *
	 * @return void
	 */
	protected function maybe_evict_old_products() {
		$non_empty_products = array_filter( $this->all_products );

		if ( count( $non_empty_products ) > $this->max_cached_products ) {
			// Remove oldest product data (first key in the array)
			$oldest_product_id = array_key_first( $non_empty_products );
			$this->evict_product( $oldest_product_id );
		}
	}

	/**
	 * Evict a product from the cache to free up memory.
	 *
	 * @param int $product_id The product ID to evict.
	 *
	 * @return void
	 */
	protected function evict_product( $product_id ) {
		if ( ! isset( $this->all_products[ $product_id ] ) ) {
			return;
		}

		// Check each group used by this product
		foreach ( $this->all_products[ $product_id ] as $group_id ) {
			// Determine if this group is used by other products
			$used_elsewhere = false;

			foreach ( $this->all_products as $pid => $gids ) {
				if ( $pid !== $product_id && in_array( $group_id, $gids, true ) ) {
					$used_elsewhere = true;
					break;
				}
			}

			// Only remove the group if no other product uses it
			if ( ! $used_elsewhere ) {
				// Remove options belonging to this group
				if ( isset( $this->all_groups[ $group_id ]->options ) ) {
					foreach ( $this->all_groups[ $group_id ]->options as $option ) {
						unset( $this->all_options[ $option->id ] );
					}
				}

				unset( $this->all_groups[ $group_id ] );
			}
		}

		unset( $this->all_products[ $product_id ] );

		// Clear option caches after eviction
		$this->clear_option_caches();
	}

	/**
	 * Return the groups assigned to the current product.
	 *
	 * @return array
	 */
	public function get_groups() {
		if ( empty( $this->groups ) ) {
			return [];
		}

		return $this->groups;
	}

	/**
	 * Get the current product.
	 *
	 * @return WC_Product|null
	 */
	public function get_product() {
		return $this->product;
	}

	/**
	 * Retrieve groups assigned to a product by its ID.
	 *
	 * @param int $product_id The product ID.
	 *
	 * @return array The groups assigned to the product.
	 */
	public function get_product_groups( $product_id ) {
		if ( ! isset( $this->all_products[ $product_id ] ) ) {
			return [];
		}

		return array_map(
			function ( $group_id ) {
				return $this->all_groups[ $group_id ];
			},
			$this->all_products[ $product_id ]
		);
	}

	/**
	 * Get the options for a specific group assigned to the current product.
	 *
	 * @param int $group_id The group ID.
	 *
	 * @return array
	 */
	public function get_group_options( $group_id ) {
		if ( ! isset( $this->all_groups[ $group_id ] ) ) {
			return [];
		}

		return $this->all_groups[ $group_id ]->options ?? [];
	}

	/**
	 * Get the image options attached to all loaded products.
	 *
	 * @return array
	 */
	public function get_image_options() {
		if ( $this->image_options_cache !== null ) {
			return $this->image_options_cache;
		}

		$this->image_options_cache = array_filter(
			$this->all_options,
			function ( $option ) {
				return isset( $option->settings['show_in_product_gallery'] )
					&& $option->settings['show_in_product_gallery'];
			}
		);

		return $this->image_options_cache;
	}

	/**
	 * Get the formula options attached to all loaded products.
	 *
	 * @return array
	 */
	public function get_formula_options() {
		if ( $this->formula_options_cache !== null ) {
			return $this->formula_options_cache;
		}

		$this->formula_options_cache = array_filter(
			$this->all_options,
			function ( $option ) {
				return $option->type === 'price_formula'
					&& isset( $option->settings['price_suffix'] )
					&& ! is_null( $option->settings['price_suffix'] );
			}
		);

		return $this->formula_options_cache;
	}

	/**
	 * Get the attribute options attached to all loaded products.
	 *
	 * @return array
	 */
	public function get_attribute_options() {
		if ( $this->attribute_options_cache !== null ) {
			return $this->attribute_options_cache;
		}

		$this->attribute_options_cache = array_filter(
			$this->all_options,
			function ( $option ) {
				return isset( $option->settings['choice_type'] )
					&& $option->settings['choice_type'] === 'variation_attributes'
					&& isset( $option->settings['selected_attribute'] )
					&& ! is_null( $option->settings['selected_attribute'] )
					&& $option->settings['selected_attribute'] !== '';
			}
		);

		return $this->attribute_options_cache;
	}

	/**
	 * Resets the current product and related data.
	 *
	 * @return void
	 */
	public function reset_product() {
		$this->product = null;
		$this->groups  = [];
	}

	/**
	 * Determine whether the current product has option groups.
	 *
	 * @return bool
	 */
	public function has_option_groups() {
		$groups = $this->get_groups();

		return ! empty( $groups );
	}
}
