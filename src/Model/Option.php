<?php
namespace Barn2\Plugin\WC_Product_Options\Model;

use Barn2\Plugin\WC_Product_Options\Dependencies\Illuminate\Database\Eloquent\Model;
use Barn2\Plugin\WC_Product_Options\Dependencies\Sematico\FluentQuery\Concerns\HasUniqueIdentifier;
use Barn2\Plugin\WC_Product_Options\Model\Group;
use Barn2\Plugin\WC_Product_Options\Plugin;
use WC_Product;

/**
 * Representation of an individual group and it's options.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Option extends Model {

	use HasUniqueIdentifier;

	protected $table   = Plugin::META_PREFIX . 'options';
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
		'group_id',
		'menu_order',
		'display_name',
		'description',
		'required',
		'type',
		'choices',
		'settings',
		'conditional_logic',
	];

	/**
	 * Defaults
	 *
	 * @var array
	 */
	protected $attributes = [
		'name'              => '',
		'group_id'          => 0,
		'menu_order'        => 0,
		'display_name'      => 1,
		'description'       => '',
		'required'          => 0,
		'type'              => 'text',
		'choices'           => 'null',
		'settings'          => 'null',
		'conditional_logic' => 'null',
	];

	/**
	 * Automatically cast attributes in specific ways.
	 *
	 * @var array
	 */
	protected $casts = [
		'choices'           => 'array',
		'settings'          => 'array',
		'conditional_logic' => 'array',
	];

	public static function clean_option_data( $data ) {
		$type = $data['type'];

		foreach ( $data as $key => $value ) {
			if ( self::type_supports_property( $key, $type ) ) {
				$cleaned_data[ $key ] = $value;
			}
		}

		return $cleaned_data;
	}

	public static function type_supports_property( $property, $type ) {
		$property_support = [
			'checkbox' => [
				'choices'           => [],
				'settings'          => [ 'choice_qty' ],
				'conditional_logic' => [],
			],
			'radio'    => [
				'choices'           => [],
				'settings'          => [ 'choice_qty' ],
				'conditional_logic' => [],
			],
		];

		return in_array( $type, $property_support[ $property ], true );
	}

	/**
	 * Whether the price formula includes the [product_quantity]
	 *
	 * @param int $option_id
	 * @return bool
	 */
	public static function formula_includes_product_quantity( $option_id ): bool {
		$option_settings = self::getQuery()
			->where( 'id', $option_id )
			->where( 'type', 'price_formula' )
			->get( 'settings' );

		if ( $option_settings->isEmpty() ) {
			return false;
		}

		$option_settings = json_decode( $option_settings->first()->settings );

		return array_reduce(
			$option_settings->formula->customVariables ?? [],
			function ( $carry, $custom_variable ) {
				return $carry || str_contains( $custom_variable->formula, '[product_quantity]' );
			},
			str_contains( $option_settings->formula->formula, '[product_quantity]' )
		);
	}

	/**
	 * Retrieve all custom attribute options for the given attribute, which do not have a choice set for the given term.
	 */
	public static function get_missing_attribute_options( $attribute, $term_id ) {
		$term = get_term( $term_id );

		if ( ! $term ) {
			return [];
		}

		$term_slug = $term->slug;

		$attribute_options = self::getQuery()
			->where( 'settings->choice_type', 'variation_attributes' )
			->where( 'settings->selected_attribute', $attribute )
			->whereJsonDoesntContain( 'choices', [ 'term' => $term_slug ] )
			->get();

		if ( $attribute_options->isEmpty() ) {
			return [];
		}

		return $attribute_options->toArray();
	}
}
