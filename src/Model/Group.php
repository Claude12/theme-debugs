<?php
namespace Barn2\Plugin\WC_Product_Options\Model;

use Barn2\Plugin\WC_Product_Options\Dependencies\Illuminate\Database\Eloquent\Model;
use Barn2\Plugin\WC_Product_Options\Dependencies\Sematico\FluentQuery\Concerns\HasUniqueIdentifier;
use Barn2\Plugin\WC_Product_Options\Plugin;

/**
 * Representation of an individual group and it's options.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Group extends Model {

	use HasUniqueIdentifier;

	protected $table   = Plugin::META_PREFIX . 'groups';
	public $timestamps = false;

	// phpcs:ignore WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
	protected $primaryKey = 'id';


	/**
	 * Fields which can be mass assigned.
	 *
	 * @var array
	 */
	public $fillable = [
		'name',
		'display_name',
		'menu_order',
		'visibility',
		'categories',
		'exclude_categories',
		'products',
		'exclude_products',
		'user_roles',
		'exclude_user_roles',
		'settings',
	];

	/**
	 * Defaults
	 *
	 * @var array
	 */
	protected $attributes = [
		'name'               => '',
		'menu_order'         => 0,
		'display_name'       => 0,
		'visibility'         => 'global',
		'categories'         => '[]',
		'exclude_categories' => '[]',
		'products'           => '[]',
		'exclude_products'   => '[]',
		'user_roles'         => '[]',
		'exclude_user_roles' => '[]',
		'settings'           => '{}',
	];

	/**
	 * Automatically cast attributes in specific ways.
	 *
	 * @var array
	 */
	protected $casts = [
		'products'           => 'array',
		'exclude_products'   => 'array',
		'categories'         => 'array',
		'exclude_categories' => 'array',
		'user_roles'         => 'array',
		'exclude_user_roles' => 'array',
		'settings'           => 'object',
	];

	/**
	 * Get the groups for a particular product.
	 *
	 * @param \WC_Product $product
	 * @param string|array $select_columns Columns to select. Default is '*'.
	 *
	 * @return array
	 */
	public static function get_groups_by_product( $product, $select_columns = '*' ) {
		if ( ! $product instanceof \WC_Product ) {
			return [];
		}

		$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
		$product_id = (int) apply_filters( 'wc_product_options_get_product_id', $product_id, $product );
		$term_ids   = wc_get_product_term_ids( $product_id, 'product_cat' );

		// We need to get the ancestors of the categories as well
		// otherwise a product that belongs to a subcategory will not be
		// included in the group that applies to the parent category.
		$deep_category_ids = [];

		foreach ( $term_ids as $term_id ) {
			$ancestors         = get_ancestors( $term_id, 'product_cat' );
			$deep_category_ids = array_merge( $deep_category_ids, $ancestors, [ $term_id ] );
		}

		$term_ids = array_unique( $deep_category_ids );

		// Get the roles of the current user.
		$current_user = wp_get_current_user();
		$user_roles   = $current_user->roles;

		// If the user is not logged in, we use the "guest" role.
		if ( empty( $user_roles ) ) {
			$user_roles = [ 'guest' ];
		}

		// We finally get the groups that apply to the product.
		$collection = self::getQuery()
			->select( $select_columns )

			// We exclude groups that are not enabled.
			->where( 'visibility', 'NOT LIKE', 'disabled-%' )

			// We exclude groups that have the current product listed in the product exclusion list.
			->whereJsonDoesntContain( 'exclude_products', [ $product_id ] )

			// We exclude groups that have the current product in one of the categories
			// listed in the category exclusion list or their ancestors.
			->where(
				function ( $query ) use ( $term_ids ) {
					foreach ( $term_ids as $term_id ) {
						$query->whereJsonDoesntContain( 'exclude_categories', [ $term_id ] );
					}
				}
			)

			// We exclude groups that have the role of the current user in the role exclusion list.
			->where(
				function ( $query ) use ( $user_roles ) {
					// Add a condition for each user role
					foreach ( $user_roles as $user_role ) {
						$query
						->orWhereJsonDoesntContain( 'exclude_user_roles', [ $user_role ] );
					}
				}
			)

			->where(
				function ( $query ) use ( $term_ids, $product_id, $user_roles ) {
					$query
					// 1. The option group applies to all products.
					->orWhere( 'visibility', 'global' )

					// 2. The option group applies to specific products, categories or roles.
					->orWhere(
						function ( $query ) use ( $term_ids, $product_id, $user_roles ) {
							$query

							// 2a. The option group applies to the current user's role AND...
							->where(
								function ( $query ) use ( $user_roles ) {
									// Add a condition for each user role
									foreach ( $user_roles as $user_role ) {
										$query
										->orWhereJsonLength( 'user_roles', 0 )
										->orWhereJsonContains( 'user_roles', [ $user_role ] );
									}
								}
							)
							// 2b. ...the option group applies to the current product or its categories.
							->where(
								function ( $query ) use ( $term_ids, $product_id ) {
									$query
									// The option group applies to the current product OR...
									->orWhereJsonContains( 'products', [ $product_id ] )
									// ...the option group applies to one of the current product's categories OR...
									->orWhere(
										function ( $query ) use ( $term_ids ) {
											foreach ( $term_ids as $term_id ) {
												$query->orWhereJsonContains( 'categories', [ $term_id ] );
											}
										}
									)
									// ...the option group applies to all products and categories.
									// This condition is necessary to include groups
									// that only have user roles defined in the inclusion list.
									->orWhere(
										function ( $query ) {
											$query
											->whereJsonLength( 'products', 0 )
											->whereJsonLength( 'categories', 0 );
										}
									);
								}
							);
						}
					);
				}
			)

			->orderBy( 'menu_order', 'asc' )
			->get();

		if ( $collection->isEmpty() ) {
			return [];
		}

		return $collection->toArray();
	}

	/**
	 * Get groups with their options for a particular product in a single query.
	 * This method uses a LEFT JOIN to load both groups and options at once,
	 * significantly reducing database queries.
	 *
	 * @param \WC_Product $product
	 * @return array Array of groups with nested options
	 */
	public static function get_groups_with_options_by_product( $product ) {
		if ( ! $product instanceof \WC_Product ) {
			return [];
		}

		$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
		$product_id = (int) apply_filters( 'wc_product_options_get_product_id', $product_id, $product );
		$term_ids   = wc_get_product_term_ids( $product_id, 'product_cat' );

		// Get ancestors of categories
		$deep_category_ids = [];
		foreach ( $term_ids as $term_id ) {
			$ancestors         = get_ancestors( $term_id, 'product_cat' );
			$deep_category_ids = array_merge( $deep_category_ids, $ancestors, [ $term_id ] );
		}
		$term_ids = array_unique( $deep_category_ids );

		// Get user roles
		$current_user = wp_get_current_user();
		$user_roles   = $current_user->roles;
		if ( empty( $user_roles ) ) {
			$user_roles = [ 'guest' ];
		}

		$groups_table  = Plugin::META_PREFIX . 'groups';
		$options_table = Plugin::META_PREFIX . 'options';

		// Build the JOIN query
		$results = self::getQuery()
			// LEFT JOIN to include groups even if they have no options
			->leftJoin(
				$options_table . ' as options',
				$groups_table . '.id',
				'=',
				'options.group_id'
			)

			// Select all group columns with table prefix
			->select(
				$groups_table . '.*',
				// Select option columns with prefixes to avoid conflicts
				'options.id as option_id',
				'options.name as option_name',
				'options.description as option_description',
				'options.display_name as option_display_name',
				'options.required as option_required',
				'options.type as option_type',
				'options.menu_order as option_menu_order',
				'options.choices as option_choices',
				'options.settings as option_settings',
				'options.conditional_logic as option_conditional_logic',
				'options.group_id as option_group_id'
			)

			// All the existing WHERE clauses for groups
			->where( $groups_table . '.visibility', 'NOT LIKE', 'disabled-%' )
			->whereJsonDoesntContain( $groups_table . '.exclude_products', [ $product_id ] )

			->where(
				function ( $query ) use ( $term_ids, $groups_table ) {
					foreach ( $term_ids as $term_id ) {
						$query->whereJsonDoesntContain( $groups_table . '.exclude_categories', [ $term_id ] );
					}
				}
			)

			->where(
				function ( $query ) use ( $user_roles, $groups_table ) {
					foreach ( $user_roles as $user_role ) {
						$query->orWhereJsonDoesntContain( $groups_table . '.exclude_user_roles', [ $user_role ] );
					}
				}
			)

			->where(
				function ( $query ) use ( $term_ids, $product_id, $user_roles, $groups_table ) {
					$query
					->orWhere( $groups_table . '.visibility', 'global' )
					->orWhere(
						function ( $query ) use ( $term_ids, $product_id, $user_roles, $groups_table ) {
							$query
							->where(
								function ( $query ) use ( $user_roles, $groups_table ) {
									foreach ( $user_roles as $user_role ) {
										$query
										->orWhereJsonLength( $groups_table . '.user_roles', 0 )
										->orWhereJsonContains( $groups_table . '.user_roles', [ $user_role ] );
									}
								}
							)
							->where(
								function ( $query ) use ( $term_ids, $product_id, $groups_table ) {
									$query
									->orWhereJsonContains( $groups_table . '.products', [ $product_id ] )
									->orWhere(
										function ( $query ) use ( $term_ids, $groups_table ) {
											foreach ( $term_ids as $term_id ) {
												$query->orWhereJsonContains( $groups_table . '.categories', [ $term_id ] );
											}
										}
									)
									->orWhere(
										function ( $query ) use ( $groups_table ) {
											$query
											->whereJsonLength( $groups_table . '.products', 0 )
											->whereJsonLength( $groups_table . '.categories', 0 );
										}
									);
								}
							);
						}
					);
				}
			)

			// Order by group menu_order, then option menu_order
			->orderBy( $groups_table . '.menu_order', 'asc' )
			->orderBy( 'options.menu_order', 'asc' )
			->get();

		if ( $results->isEmpty() ) {
			return [];
		}

		// Transform the flat result set into nested groups with options
		return self::transform_joined_results( $results->toArray() );
	}

	/**
	 * Transform the flat JOIN results into nested group/option structure.
	 *
	 * @param array $results Flat array of joined group+option rows
	 * @return array Nested array with groups containing their options
	 */
	private static function transform_joined_results( $results ) {
		$grouped_data = [];

		foreach ( $results as $row ) {
			$group_id = $row->id;

			// Initialize group if first time seeing it
			if ( ! isset( $grouped_data[ $group_id ] ) ) {
				$grouped_data[ $group_id ] = (object) [
					'id'                 => $row->id,
					'name'               => $row->name,
					'display_name'       => $row->display_name,
					'menu_order'         => $row->menu_order,
					'visibility'         => $row->visibility,
					'categories'         => $row->categories,
					'exclude_categories' => $row->exclude_categories,
					'products'           => $row->products,
					'exclude_products'   => $row->exclude_products,
					'user_roles'         => $row->user_roles,
					'exclude_user_roles' => $row->exclude_user_roles,
					'settings'           => $row->settings,
					'options'            => [],
				];
			}

			// Add option if it exists (LEFT JOIN means some groups might have no options)
			if ( $row->option_id ) {
				$grouped_data[ $group_id ]->options[] = (object) [
					'id'                => $row->option_id,
					'name'              => $row->option_name,
					'description'       => $row->option_description,
					'display_name'      => $row->option_display_name,
					'required'          => $row->option_required,
					'type'              => $row->option_type,
					'menu_order'        => $row->option_menu_order,
					'group_id'          => $row->option_group_id,
					'choices'           => $row->option_choices,
					'settings'          => $row->option_settings,
					'conditional_logic' => $row->option_conditional_logic,
				];
			}
		}

		return array_values( $grouped_data );
	}

	/**
	 * Get specific groups with options by their IDs (for loading missing groups).
	 * 
	 * @param array $group_ids Array of group IDs to load
	 * @return array Array of groups with their options
	 */
	public static function get_groups_with_options_by_ids( $group_ids ) {
		if ( empty( $group_ids ) ) {
			return [];
		}

		$groups_table  = Plugin::META_PREFIX . 'groups';
		$options_table = Plugin::META_PREFIX . 'options';

		$results = self::getQuery()
			->leftJoin(
				$options_table . ' as options',
				$groups_table . '.id',
				'=',
				'options.group_id'
			)
			->select(
				$groups_table . '.*',
				'options.id as option_id',
				'options.name as option_name',
				'options.description as option_description',
				'options.display_name as option_display_name',
				'options.required as option_required',
				'options.type as option_type',
				'options.menu_order as option_menu_order',
				'options.choices as option_choices',
				'options.settings as option_settings',
				'options.conditional_logic as option_conditional_logic',
				'options.group_id as option_group_id'
			)
			->whereIn( $groups_table . '.id', $group_ids )
			->orderBy( $groups_table . '.menu_order', 'asc' )
			->orderBy( 'options.menu_order', 'asc' )
			->get();

		return self::transform_joined_results( $results->toArray() );
	}
}
