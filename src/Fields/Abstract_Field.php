<?php

namespace Barn2\Plugin\WC_Product_Options\Fields;

use Barn2\Plugin\WC_Product_Options\Fields\Traits\Cart_Item_Data;
use Barn2\Plugin\WC_Product_Options\Util\Price as Price_Util;
use Barn2\Plugin\WC_Product_Options\Util\Conditional_Logic as Conditional_Logic_Util;
use Barn2\Plugin\WC_Product_Options\Util\Util;
use WP_Error;
use WC_Product;
use WC_Product_Variable;

defined( 'ABSPATH' ) || exit;
/**
 * Abstract field class.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
#[\AllowDynamicProperties]
abstract class Abstract_Field {

	use Cart_Item_Data;

	protected $type;
	protected $option;
	protected $product;

	/**
	 * Whether the field has a user-defined value.
	 *
	 * @var bool
	 */
	protected $has_user_value = false;

	/**
	 * Whether the field has conditional logic.
	 *
	 * @var bool
	 */
	protected $has_conditional_logic = false;

	/**
	 * Whether the field supports multiple values (e.g checkboxes, radios, select).
	 *
	 * @var bool
	 */
	protected $supports_multiple_values = true;

	/**
	 * Whether the field can store multiple value selections (e.g checkboxes).
	 *
	 * @var bool
	 */
	protected $stores_multiple_values = false;


	/**
	 * Whether the field is for display only.
	 *
	 * @var bool
	 */
	protected $is_display_field = false;

	/**
	 * An array of settings being used by the field.
	 *
	 * @var array
	 */
	protected $used_settings = [];

	/**
	 * The choice quantity setting.
	 *
	 * @var array
	 */
	protected $choice_qty;

	/**
	 * The choice character setting.
	 *
	 * @var array
	 */
	protected $choice_char;

	/**
	 * Whether all choices have equal pricing.
	 *
	 * @var bool
	 */
	protected $equal_pricing;

	/**
	 * Constructor.
	 *
	 * @param mixed $option
	 * @param WC_Product $product
	 */
	public function __construct( $option, $product ) {
		$this->option  = $option;
		$this->product = $product;

		$used_settings = array_unique(
			array_merge(
				$this->used_settings,
				[
					'choice_qty',
					'qty_pickers',
					'choice_char',
				]
			)
		);

		if ( $this->option->settings ) {
			foreach ( $used_settings as $used_setting ) {
				$this->{$used_setting} = $this->get_setting( $used_setting );
			}
		}

		if ( $this->option->display_name && $this->supports_multiple_values && count( $this->option->choices ?? [] ) > 0 ) {
			$base_price_type = $this->option->choices[0]['price_type'] ?? '';
			$base_pricing    = $this->option->choices[0]['pricing'] ?? 0;

			$this->equal_pricing = array_reduce(
				$this->option->choices,
				function ( $carry, $choice ) use ( $base_price_type, $base_pricing ) {
					$choice_pricing    = $choice['pricing'] ?? 0;
					$choice_price_type = $choice['price_type'] ?? '';

					return $carry && $choice_pricing === $base_pricing && $choice_price_type === $base_price_type;
				},
				true
			);
		} else {
			$this->equal_pricing = false;
		}

		$this->init_conditional_logic();
	}

	/**
	 * The set magic method.
	 *
	 * This is defined and empty to allow the use of dynamic properties.
	 *
	 * @see https://www.php.net/manual/en/language.oop5.properties.php#language.oop5.properties.dynamic-properties
	 *
	 * @param  string $name
	 * @param  mixed $value
	 * @return void
	 */
	public function __set( $name, $value ): void {}

	/**
	 * Determine whether the field has conditional logic.
	 */
	private function init_conditional_logic() {
		$this->has_conditional_logic = ! is_null( $this->option->conditional_logic ) && ! empty( $this->option->conditional_logic['conditions'] );
	}

	/**
	 * Get the array with the field settings.
	 *
	 * @return array|null
	 */
	public function get_settings() {
		/**
		 * Filters the array with the field settings.
		 *
		 * @param array $settings The array with the field settings.
		 * @param Abstract_Field $field The field object.
		 * @param WC_Product $product The product object.
		 */
		return apply_filters( 'wc_product_options_get_settings', $this->option->settings, $this, $this->get_product() );
	}

	/**
	 * Get the value of a specific field setting.
	 *
	 * @param  string $setting_name The key of the setting to retrieve.
	 * @param  mixed $default The default value to return if the setting is not found. Defaults to null.
	 * @return string|array|null
	 */
	public function get_setting( $setting_name, $default = null ) {
		$settings   = $this->get_settings();
		$field_type = $this->type;
		$value      = isset( $settings[ $setting_name ] ) ? $settings[ $setting_name ] : $default;

		/**
		 * Filters the value of a specific field setting.
		 *
		 * @param string|array $value The value of the setting being filtered.
		 * @param string $setting_name The key of the setting being filtered.
		 * @param Abstract_Field $field The field object.
		 * @param WC_Product $product The product object.
		 */
		$value = apply_filters( 'wc_product_options_get_setting', $value, $setting_name, $this, $this->get_product() );

		/**
		 * Filters the value of a specific field setting.
		 *
		 * The variable portions of the filter name refer to:
		 * - the field type (e.g., `text` or `customer_price`), and
		 * - the setting name (e.g., `default_value`).
		 *
		 * Example 1
		 * ```
		 * add_filter( 'wc_product_options_get_text_default_value_setting', 'my_filter_text_default_value', 10, 3 );
		 * function my_filter_text_default_value( $value, $field, $product ) {
		 *     return 'Custom default value';
		 * }
		 * ```
		 *
		 * Example 2
		 * ```
		 * add_filter( 'wc_product_options_get_customer_price_min_price_setting', 'my_filter_customer_price_min_price', 10, 3 );
		 * function my_filter_customer_price_min_price( $value, $field, $product ) {
		 *     return 'Custom min price';
		 * }
		 * ```
		 *
		 * @param string $value The value of the setting being filtered.
		 * @param string $setting_name The key of the setting being filtered.
		 * @param Abstract_Field $field The field object.
		 * @param WC_Product $product The product object.
		 */
		return apply_filters( "wc_product_options_get_{$field_type}_{$setting_name}_setting", $value, $setting_name, $this, $this->get_product() );
	}

	/**
	 * Validate the field value.
	 *
	 * @param mixed $value
	 * @param array $option_data
	 * @return WP_Error|true
	 */
	public function validate( $value, $option_data ) {
		// wpt and the serializeObject plugin pass through display fields.
		if ( $this->is_display_field ) {
			return $this->validate_filters( $value, $option_data );
		}

		if ( ! $this->is_valid_attribute_option_for_product() ) {
			// if this is a variation attribute option that doesn't apply to this product,
			// the validation succeeds.
			return $this->validate_filters( $value, $option_data );
		}

		if ( $this->is_required() && strlen( $value ) === 0 && ! Conditional_Logic_Util::is_field_hidden( $this, $option_data ) ) {
			/* translators: %1$s: Option name %2$s: Product name*/
			return new WP_Error( 'wpo-validation-error', esc_html( sprintf( __( '"%1$s" is a required field for "%2$s".', 'woocommerce-product-options' ), $this->option->name, $this->product->get_name() ) ) );
		}

		$values = array_filter( is_array( $value ) ? $value : [ $value ] );

		foreach ( $values as $choice ) {
			$key = $this->has_user_value ? 0 : array_search( $choice, array_column( $this->option->choices, 'id' ), true );

			if ( ! $this->has_user_value && ! isset( $this->option->choices[ $key ] ) ) {
				/* translators: %1$s: Option name %2$s: Product name*/
				return new WP_Error( 'wpo-validation-error', esc_html( sprintf( __( 'Invalid option choice for "%1$s" on "%2$s".', 'woocommerce-product-options' ), $this->option->name, $this->product->get_name() ) ) );
			}
		}

		return $this->validate_filters( $value, $option_data );
	}

	/**
	 * Run the validation filters.
	 *
	 * @param mixed $value
	 * @param array $option_data
	 * @param bool $is_valid
	 * @return WP_Error|true
	 */
	public function validate_filters( $value, $option_data, $is_valid = true ) {
		return apply_filters( 'wc_product_options_field_validate', $is_valid, $value, $option_data, $this );
	}

	/**
	 * Sanitize the field input value.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function sanitize( $value ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Returns the valid choices for the field.
	 *
	 * @return null|array
	 */
	public function get_choices(): ?array {
		if ( $this->is_valid_attribute_option_for_product() ) {
			return $this->get_attribute_choices();
		}

		return $this->option->choices;
	}

	/**
	 * Retrieves a specific choice by its index.
	 *
	 * @param int $index The index of the choice to retrieve. Default is 0.
	 * @return null|array The choice array if found, null otherwise.
	 */
	public function get_choice( $index = 0 ): ?array {
		$choices = $this->get_choices();

		if ( ! isset( $choices[ $index ] ) ) {
			return null;
		}

		return $choices[ $index ];
	}

	/**
	 * Retrieves the valid choices for the attribute option.
	 *
	 * @return null|array
	 */
	protected function get_attribute_choices(): ?array {
		$global_attributes = $this->get_global_attributes();

		if ( empty( $global_attributes ) ) {
			return null;
		}

		if ( ! isset( $global_attributes[ $this->option->settings['selected_attribute'] ] ) ) {
			return null;
		}

		$attribute_terms = $global_attributes[ $this->option->settings['selected_attribute'] ];

		if ( empty( $attribute_terms ) ) {
			return null;
		}

		$choices = $this->option->choices;

		$choices = array_filter(
			$choices,
			function ( $choice ) use ( $attribute_terms ) {
				return in_array( $choice['term'], $attribute_terms, true );
			}
		);

		return $choices;
	}

	/**
	 * Retrieves the choice from the option DB based on the user value provided.
	 *
	 * @param mixed $value
	 * @return array|null
	 */
	public function get_choice_for_value( $value ): ?array {
		$index = $this->has_user_value ? 0 : array_search( $value, array_column( $this->option->choices, 'id' ), true );

		if ( $index === false || ! isset( $this->option->choices[ $index ] ) ) {
			return null;
		}

		return array_merge( [ 'index' => $index ], $this->option->choices[ $index ] );
	}

	/**
	 * Determines whether the option has enough data to display.
	 *
	 * @return bool
	 */
	protected function has_display_prerequisites(): bool {
		if ( $this->is_display_field ) {
			return true;
		}

		if ( $this->type !== 'price_formula' && is_null( $this->option->choices ) ) {
			return false;
		}

		if ( $this->is_variation_attribute_type_option() && ! $this->is_valid_attribute_option_for_product() ) {
			return false;
		}

		if ( in_array( $this->product->get_type(), [ 'simple' ], true ) && ! Conditional_Logic_Util::check_product_conditions( $this ) ) {
			// Options of a simple product can be safely hidden
			// if conditions on product attributes are not met in the conditional logic.
			return false;
		}

		/**
		 * Filters whether the field should be displayed.
		 *
		 * @param bool $display True if the field should be displayed, false otherwise.
		 * @param Abstract_Field $field The current field object.
		 */
		$display = apply_filters( 'wc_product_options_field_has_display_prerequisites', true, $this );

		return $display;
	}

	/**
	 * Render the HTML for the field.
	 */
	public function render(): void {}

	/**
	 * Renders the field wrap.
	 */
	protected function render_field_wrap_open(): void {
		$field_type = $this->type;

		/**
		 * Fires before the field wrap is rendered.
		 *
		 * @param Abstract_Field $field The current field object.
		 * @param WC_Product $product The current product object.
		 */
		do_action( 'wc_product_options_before_field_wrap', $this, $this->get_product() );

		/**
		 * Fires before the field wrap is rendered.
		 *
		 * The variable part of the hook name refers to the field type (i.e. `$field->option->type`)
		 *
		 * @param Abstract_Field $field The current field object.
		 * @param WC_Product $product The current product object.
		 */
		do_action( "wc_product_options_before_{$field_type}_field_wrap", $this, $this->get_product() );

		// phpcs:reason All the parameters are properly escaped in the methods.
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<div %s>',
			$this->get_field_attributes()
		);

		/**
		 * Fires before the field is rendered.
		 *
		 * @param Abstract_Field $field The current field object.
		 * @param WC_Product $product The current product object.
		 */
		do_action( 'wc_product_options_before_field', $this, $this->get_product() );

		/**
		 * Fires before the field is rendered.
		 *
		 * The variable part of the hook name refers to the field type (i.e. `$field->option->type`)
		 *
		 * @param Abstract_Field $field The current field object.
		 * @param WC_Product $product The current product object.
		 */
		do_action( "wc_product_options_before_{$field_type}_field", $this, $this->get_product() );
	}

	/**
	 * Renders the field wrap closing tag.
	 */
	protected function render_field_wrap_close(): void {
		$field_type = $this->type;

		/**
		 * Fires after the field is rendered.
		 *
		 * @param Abstract_Field $field The current field object.
		 * @param WC_Product $product The current product object.
		 */
		do_action( 'wc_product_options_after_field', $this, $this->get_product() );

		/**
		 * Fires after the field is rendered.
		 *
		 * The variable part of the hook name refers to the field type (i.e. `$field->option->type`)
		 *
		 * @param Abstract_Field $field The current field object.
		 * @param WC_Product $product The current product object.
		 */
		do_action( "wc_product_options_after_{$field_type}_field", $this, $this->get_product() );

		print( '</div>' );

		/**
		 * Fires after the field wrap is rendered.
		 *
		 * @param Abstract_Field $field The current field object.
		 * @param WC_Product $product The current product object.
		 */
		do_action( 'wc_product_options_after_field_wrap', $this, $this->get_product() );

		/**
		 * Fires after the field wrap is rendered.
		 *
		 * The variable part of the hook name refers to the field type (i.e. `$field->option->type`)
		 *
		 * @param Abstract_Field $field The current field object.
		 * @param WC_Product $product The current product object.
		 */
		do_action( "wc_product_options_after_{$field_type}_field_wrap", $this, $this->get_product() );
	}

	/**
	 * Render the HTML for the field label.
	 */
	protected function render_option_name(): void {
		if ( $this->option->display_name ) {
			$name = sprintf(
				'<span class="wpo-option-name__text">%s</span>',
				esc_html( apply_filters( 'wc_product_options_get_output_string', $this->option->name, $this->option, 'option_name' ) )
			);

			/**
			 * Filters the prefix added to the option name.
			 *
			 * @param string $prefix The prefix string.
			 * @param Abstract_Field $field The current field object.
			 * @param WC_Product $product The current product object.
			 */
			$name_prefix = apply_filters( 'wc_product_options_option_name_prefix', '', $this, $this->get_product() );

			/**
			 * Filters the suffix added to the option name.
			 *
			 * @param string $suffix The suffix string.
			 * @param Abstract_Field $field The current field object.
			 * @param WC_Product $product The current product object.
			 */
			$name_suffix = apply_filters( 'wc_product_options_option_name_suffix', '', $this, $this->get_product() );

			$edit_button = '';

			if ( current_user_can( 'manage_woocommerce' ) ) {
				$edit_button = sprintf(
					'<span class="wpo-field-edit">%s</span>',
					$this->get_edit_option_link()
				);
			}

			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			printf(
				'<p class="wpo-option-name">%1$s%2$s%3$s%4$s%5$s%6$s</p>',
				$edit_button,
				$name_prefix,
				$name,
				$this->choices_have_equal_pricing() ? ' ' . $this->get_choice_pricing_string( [], true ) : '',
				$this->get_required_symbol(),
				$name_suffix
			);
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Render the HTML for the field description.
	 */
	protected function render_description(): void {
		if ( ! empty( $this->option->description ) ) {
			$description = apply_filters( 'wc_product_options_get_output_string', $this->option->description, $this->option, 'option_description' );
			printf( '<p class="wpo-field-description">%s</p>', esc_html( $description ) );
		}
	}

	/**
	 * Gets the id of the field wrapper element.
	 *
	 * The ID is used to uniquely identify the field in the DOM
	 * and is based on the option ID and the product ID
	 * so that when two products with the same option are displayed on the same page,
	 * the field IDs are still unique.
	 *
	 * @return string
	 */
	public function get_field_id(): string {
		return sprintf( 'wpo-field-%d-%d', $this->option->id, $this->product->get_id() );
	}

	/**
	 * Gets the name attribute for the field input.
	 *
	 * @return string
	 */
	public function get_input_name(): string {
		return sprintf( 'wpo-option[option-%d]', $this->option->id );
	}

	/**
	 * Gets the id attribute for the field input.
	 *
	 * @return string
	 */
	public function get_input_id(): string {
		return sprintf( 'wpo-option-%d-%d-%d', $this->option->group_id, $this->option->id, $this->product->get_id() );
	}

	/**
	 * Gets the CSS class for the field.
	 *
	 * @return string
	 */
	public function get_field_class(): string {
		$classes = [ 'wpo-field', 'wpo-field-%s' ];

		if ( $this->has_conditional_logic && $this->option->conditional_logic['visibility'] === 'show' ) {
			$classes[] = 'wpo-field-hide';
		}

		if ( $this->is_required() ) {
			$classes[] = 'wpo-field-required';
		}

		if ( in_array( $this->get_type(), [ 'color_swatches', 'text_labels' ] ) ) {
			$classes[] = 'wpo-field-flex';
		}

		if ( ! $this->option->display_name && $this->has_user_value ) {
			$classes[] = 'wpo-label-is-option-name';
		}

		if ( $this->option->settings['show_in_product_gallery'] ?? '' ) {
			$classes[] = 'wpo-field-with-images';
		}

		if ( $this->option->settings['display_label'] ?? '' ) {
			$classes[] = 'wpo-field-with-labels';
		}

		if ( ! empty( $this->option->settings['custom_css_class'] ) ) {
			$classes[] = esc_attr( $this->option->settings['custom_css_class'] );
		}

		if ( $this->option->settings['hide_prices'] ?? '' ) {
			$classes[] = 'wpo-field-with-hidden-prices';
		}

		/**
		 * Filters the array with the CSS classes for the field.
		 *
		 * @param array $classes The array with the CSS classes.
		 * @param Abstract_Field $field The current field object.
		 * @param WC_Product $product The current product object.
		 */
		$classes = apply_filters( 'wc_product_options_field_css_classes', $classes, $this, $this->get_product(), $this->type );

		/**
		 * Filters the CSS classes for the field.
		 *
		 * @param string $class_string
		 * @param Abstract_Field $field
		 */
		return apply_filters( 'wc_product_options_field_css_class', sprintf( implode( ' ', $classes ), $this->type ), $this, $this->get_product(), $this->type );
	}

	/**
	 * Gets the HTML attributes as an array of key/value pairs.
	 *
	 * @return array
	 */
	public function get_field_attributes_array(): array {
		$attributes = [
			'id'             => $this->get_field_id(),
			'class'          => $this->get_field_class(),
			'data-type'      => $this->type,
			'data-group-id'  => $this->option->group_id,
			'data-option-id' => $this->option->id,
			'data-clogic'    => $this->has_conditional_logic ? 'true' : 'false',
		];

		if ( $this->has_conditional_logic ) {
			$attributes = array_merge(
				$attributes,
				[
					'data-clogic-relation'   => $this->option->conditional_logic['relation'],
					'data-clogic-visibility' => $this->option->conditional_logic['visibility'],
					'data-clogic-conditions' => wp_json_encode( $this->option->conditional_logic['conditions'] ),
				]
			);
		}

		if ( $this->stores_multiple_values && $this->choice_qty ) {
			$min = $this->choice_qty['min'] ?? '';

			if ( $min !== '' ) {
				$attributes['data-min-qty'] = $min;
			}

			$max = $this->choice_qty['max'] ?? '';

			if ( $max !== '' ) {
				$attributes['data-max-qty'] = $max;
			}
		}

		if ( ! $this->is_display_field ) {
			$attributes = array_merge( $attributes, $this->get_pricing_attributes() );
		}

		if ( $this->is_variation_attribute_type_option() ) {
			$attributes['data-variation-attribute'] = $this->option->settings['selected_attribute'];
		}

		/**
		 * Filter the field attributes.
		 *
		 * Field attributes are added to the `.wpo-field` wrapper element.
		 *
		 * @param array $attributes The array of attributes.
		 * @param Abstract_Field $field The current field object.
		 * @param WC_Product $product The current product object.
		 */
		return apply_filters( 'wc_product_options_field_attributes', $attributes, $this, $this->get_product() );
	}

	/**
	 * Gets the string with the `name=value` pairs of the attributes for the field wrapper element.
	 *
	 * @return string
	 */
	protected function get_field_attributes(): string {
		return Util::get_html_attribute_string( $this->get_field_attributes_array() );
	}

	/**
	 * Get the label for the field or its choices.
	 *
	 * The $index parameter is used to get the label for a specific choice.
	 * For single-choice fields, the $index can be omitted.
	 *
	 * @param int $index The index of the choice (or 0 for single-choice fields)
	 * @param bool $unformatted Whether to skip escaping the label text. Default is false.
	 * @return string
	 */
	public function get_label( $index = 0, $unformatted = false ): string {
		$choice = $this->get_choice( $index );

		if ( $choice['label'] === '' ) {
			return '';
		}

		$label = apply_filters( 'wc_product_options_get_output_string', $choice['label'], $this->option, 'choice_label', $index );

		$label = sprintf(
			$unformatted ? '%s' : '<span class="wpo-choice-label">%s</span>',
			$unformatted ? esc_attr( $label ) : esc_html( $label )
		);

		if ( ! $this->supports_multiple_values && ! $this->option->display_name && ! $unformatted ) {
			$edit_button = '';

			if ( current_user_can( 'manage_woocommerce' ) ) {
				$edit_button = sprintf(
					'<span class="wpo-field-edit">%s</span>',
					$this->get_edit_option_link()
				);
			}

			$label = $label . $edit_button . $this->get_required_symbol();
		}

		return $label;
	}

	/**
	 * Gets the required symbol for the field.
	 *
	 * @return string
	 */
	protected function get_required_symbol() {
		return $this->is_required() ? '<span class="wpo-field-required-symbol">*</span>' : '';
	}

	/**
	 * Gets the label for choice quantity limits if applicable.
	 */
	protected function render_choice_quantity_limits_label(): void {
		if ( ! $this->stores_multiple_values || ! $this->choice_qty ) {
			return;
		}

		$limits = [];

		$min = $this->choice_qty['min'] ?? '';

		if ( $min !== '' ) {
			$limits[] = sprintf( 'Minimum %d', esc_html( $min ) );
		}

		$max = $this->choice_qty['max'] ?? '';

		if ( $max !== '' ) {
			$limits[] = sprintf( 'Maximum %d', esc_html( $max ) );
		}

		if ( empty( $limits ) ) {
			return;
		}

		printf( '<span class="wpo-info-label">Quantities: %s.</span>', esc_html( implode( ', ', $limits ) ) );
	}

	/**
	 * Gets the label for choice character limits if applicable.
	 */
	protected function render_choice_character_limits_label(): void {
		if ( ! $this->has_user_value() || ! $this->choice_char ) {
			return;
		}

		$limits = [];

		$min = $this->choice_char['min'] ?? '';

		if ( $min !== '' ) {
			$limits[] = sprintf( 'Minimum %d', esc_html( $min ) );
		}

		$max = $this->choice_char['max'] ?? '';

		if ( $max !== '' ) {
			$limits[] = sprintf( 'Maximum %d', esc_html( $max ) );
		}

		if ( empty( $limits ) ) {
			return;
		}

		printf( '<span class="wpo-info-label">Characters: %s.</span>', esc_html( implode( ', ', $limits ) ) );
	}

	/**
	 * Retrieves top level pricing attributes for the field.
	 *
	 * @return array
	 */
	protected function get_pricing_attributes(): array {
		$has_pricing   = false;
		$default_price = 0;

		if ( empty( $this->option->choices ) || $this->type === 'product' ) {
			return [];
		}

		foreach ( $this->option->choices as $choice ) {
			$price_type = $choice['price_type'] ?? 'no_cost';

			if ( $price_type === 'no_cost' ) {
				continue;
			}

			$has_pricing = true;
			$selected    = $choice['selected'] ?? false;

			if ( $selected && $this->supports_multiple_values ) {
				$default_price += $choice['pricing'];
			}

			if ( ! $this->supports_multiple_values ) {
				$default_price += $choice['pricing'];
			}
		}

		$attributes = [
			'data-pricing'      => $has_pricing ? 'true' : 'false',
			'data-option-price' => (string) $default_price ?? '0',
		];

		return $attributes;
	}

	/**
	 * Gets the data attributes to handle JS pricing calculation.
	 *
	 * @param array $choice
	 * @return string
	 */
	protected function get_choice_pricing_attributes( array $choice = [] ): string {
		$choice     = empty( $choice ) ? $this->option->choices[0] : $choice;
		$price_type = $choice['price_type'] ?? 'no_cost';

		$attributes = [
			'data-price-type' => $price_type,
			'data-index'      => $choice['index'] ?? '',
		];

		$choice_price = Price_Util::get_user_choice_pricing( $choice );

		if ( in_array( $price_type, [ 'percentage_inc', 'percentage_dec' ], true ) ) {
			$attributes['data-price-amount'] = ! empty( $choice_price ) ? $choice_price : '0';
		} else {
			$attributes['data-price-amount'] = ! empty( $choice_price ) ? Price_Util::get_choice_display_price( $this->product, $choice_price, null ) : '0';
		}

		if ( $this->is_valid_attribute_option_for_product() ) {
			$attributes['data-attribute-term'] = $choice['term'];
		}

		$attribute_string = Util::get_html_attribute_string( $attributes );

		return $attribute_string;
	}

	/**
	 * Retrieves the pricing string for a choice
	 *
	 * @param array $choice
	 * @param bool $skip_parenthesis
	 */
	protected function get_choice_pricing_string( array $choice = [], $skip_parenthesis = true ): string {
		$currency_data = Price_Util::get_currency_data();
		$price_string  = '';
		$choice        = empty( $choice ) ? $this->option->choices[0] : $choice;
		$choice_price  = Price_Util::get_user_choice_pricing( $choice );
		$space         = str_contains( get_option( 'woocommerce_currency_pos' ), 'space' ) ? '&nbsp;' : '';

		switch ( $choice['price_type'] ?? '' ) {
			case 'no_cost':
				return '';
			case 'flat_fee':
				$price_string = sprintf( esc_html_x( '%s', 'one-time fee', 'woocommerce-product-options' ), Price_Util::get_price_html( Price_Util::get_choice_display_price( $this->product, $choice_price, null ) ) );
				break;
			case 'percentage_inc':
				/* translators: %1$s: A space or an empty string, %2$s: Option choice percentage increase */
				$price_string = sprintf( esc_html__( '+%1$s%2$s%1$s%%', 'woocommerce-product-options' ), $space, number_format( $choice_price, $currency_data['precision'], $currency_data['decimalSeparator'], $currency_data['thousandSeparator'] ) );
				break;
			case 'percentage_dec':
				/* translators: %1$s: A space or an empty string, %2$s: Option choice percentage decrease */
				$price_string = sprintf( esc_html__( '-%1$s%2$s%1$s%%', 'woocommerce-product-options' ), $space, number_format( $choice_price, $currency_data['precision'], $currency_data['decimalSeparator'], $currency_data['thousandSeparator'] ) );
				break;
			case 'quantity_based':
				$price_string = sprintf( esc_html_x( '%s', 'quantity-based fee', 'woocommerce-product-options' ), Price_Util::get_price_html( Price_Util::get_choice_display_price( $this->product, $choice_price, null ) ) );
				break;
			case 'char_count':
				/* translators: %s: Option choice price per character */
				$price_string = sprintf( esc_html__( '%s per character', 'woocommerce-product-options' ), Price_Util::get_price_html( Price_Util::get_choice_display_price( $this->product, $choice_price, null ) ) );
				break;
			case 'file_count':
				/* translators: %s: Option choice price per character */
				$price_string = sprintf( esc_html__( '%s per uploaded file', 'woocommerce-product-options' ), Price_Util::get_price_html( Price_Util::get_choice_display_price( $this->product, $choice_price, null ) ) );
				break;
			default:
				return '';
		}

		if ( ! empty( $price_string ) ) {
			if ( ! $skip_parenthesis ) {
				$price_string = "($price_string)";
			}
			if ( $this->option->settings['hide_prices'] ?? false ) {
				return '';
			}
			return sprintf( '<span class="price wpo-price-container">%s</span>', $price_string );
		}
	}

	/**
	 * Get the formula value associated with a choice of the field.
	 *
	 * @param int $index The index of the choice
	 * @return string
	 */
	public function get_choice_formula_value( $index = 0 ): string {
		$choice        = $this->get_choice( $index );
		$formula_value = $choice['value'] ?? '';

		if ( ! $formula_value ) {
			return '';
		}

		return $choice['value'] ?? '';
	}

	/**
	 * Gets the choice image for the field.
	 *
	 * @param int $index
	 * @return string
	 */
	public function get_choice_image( $index = 0 ): string {
		$choice = $this->get_choice( $index );
		$image  = $choice['media'] ?? '';

		if ( ! $image ) {
			return '';
		}

		return $image;
	}

	/**
	 * Get the choice image HTML markup
	 *
	 * @param int $index
	 * @return string
	 */
	protected function get_choice_image_html( $index = 0 ): string {
		$choice = $this->get_choice( $index );

		if ( empty( $this->option->settings['display_choice_image'] ) ) {
			return '';
		}

		$image     = $this->get_choice_image( $index );
		$image_src = wp_get_attachment_image_url( $image, 'thumbnail' );

		if ( ! $image_src ) {
			return '';
		}

		$image_srcset = wp_get_attachment_image_srcset( $image, 'thumbnail' );
		$alt          = $choice['label'] ?? '';
		$html         = sprintf(
			'<img src="%1$s" srcset="%2$s" alt="%3$s" class="wpo-choice-image" />',
			esc_url( $image_src ),
			esc_attr( $image_srcset ),
			esc_attr( $alt )
		);

		return $html;
	}

	/**
	 * Get the srcset attribute for the image.
	 *
	 * @param int $image_id
	 * @param string $size
	 * @return string
	 */
	protected function get_image_srcset( $image_id, $size ): string {
		if ( ! is_numeric( $image_id ) ) {
			return '';
		}

		return wp_get_attachment_image_srcset( $image_id, $size );
	}

	/**
	 * Return the `data-image` attribute of the image button.
	 *
	 * The attribute contains the image data in JSON format
	 * as returned by the `wc_get_product_attachment_props` function.
	 *
	 * @since 1.6.4
	 * @param  string|int $attachment_id The ID of the image attachment.
	 * @return string
	 */
	protected function get_image_data( $attachment_id ) {
		if ( ! $this->is_update_main_image_enabled() ) {
			return '';
		}

		return sprintf(
			'data-image="%1$s"',
			htmlspecialchars( wp_json_encode( wc_get_product_attachment_props( $attachment_id ) ) )
		);
	}

	/**
	 * Get the `sizes` attribute for the image element.
	 *
	 * @since 1.6.4
	 * @param string|int $attachment_id The ID of the image attachment.
	 * @param int $width
	 * @return string
	 */
	protected function get_attachment_image_sizes( $attachment_id, $width ) {
		if ( ! wp_get_attachment_image_src( $attachment_id, 'full' ) ) {
			return '';
		}

		return sprintf(
			'(max-width: 480px) 480px, %1$dpx',
			$width
		);
	}

	/**
	 * Checks if the field has choice images.
	 *
	 * @return bool
	 */
	protected function has_choice_images(): bool {
		if ( ! $this->supports_multiple_values ) {
			return false;
		}

		if ( $this->type === 'product' ) {
			return true;
		}

		foreach ( $this->option->choices ?? [] as $choice ) {
			if ( ! empty( $choice['media'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether the option has the "Update main image" setting enabled.
	 *
	 * @since 1.6.4
	 * @return bool
	 */
	protected function is_update_main_image_enabled() {
		if ( ! $this->has_choice_images() ) {
			return false;
		}

		$show_in_product_gallery = (bool) ( $this->option->settings['show_in_product_gallery'] ?? false );

		/**
		 * Filter whether the option has the "Update main image" setting enabled.
		 *
		 * Thanks to this filter, the "Update main image" can be forced to be enabled/disabled site-wide
		 * or on specific products without any need to manually change any of the existing options.
		 *
		 * Example:
		 * ```
		 * function my_wc_product_options_is_update_main_image_enabled( $is_enabled, $option, $product ) {
		 *     if ( $product->get_id() === 123 ) {
		 *         return true;
		 *     }
		 *
		 *     return $is_enabled;
		 * }
		 * ```
		 *
		 * @param bool          $show_in_product_gallery Whether the option has the "Update main image" setting enabled.
		 * @param Image_Buttons $option                  The current option object.
		 * @param WC_Product    $product                 The current product object.
		 */
		return apply_filters( 'wc_product_options_is_update_main_image_enabled', $show_in_product_gallery, $this, $this->get_product() );
	}


	/**
	 * Get the field conditional logic config.
	 *
	 * @return object|null
	 */
	public function get_conditional_logic_config(): ?object {
		if ( ! $this->has_conditional_logic ) {
			return null;
		}

		return (object) $this->option->conditional_logic;
	}

	/**
	 * Get the field type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Get the field id.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return $this->option->id;
	}

	/**
	 * Get the product for the field.
	 *
	 * @return WC_Product
	 */
	public function get_product(): WC_Product {
		return $this->product;
	}

	/**
	 * Whether the field has a user inputted value.
	 *
	 * @return bool
	 */
	public function has_user_value(): bool {
		return $this->has_user_value;
	}

	/**
	 * Get the default value for the field.
	 *
	 * @return string
	 */
	public function get_value_attribute() {
		$default_value = $this->get_setting( 'default_value', '' );

		if ( $default_value === '' ) {
			return '';
		}

		return sprintf( 'value="%s"', esc_attr( $default_value ) );
	}

	/**
	 * Whether the field has conditional logic.
	 *
	 * @return bool
	 */
	public function has_conditional_logic(): bool {
		return $this->has_conditional_logic;
	}

	/**
	 * Whether the field stores multiple values.
	 *
	 * @return bool
	 */
	public function stores_multiple_values(): bool {
		return $this->stores_multiple_values;
	}

	/**
	 * Whether the field is required.
	 *
	 * @return boolean
	 */
	public function is_required() {
		return ! empty( $this->option->required );
	}

	/**
	 * Determines whether the option is a variation attribute type option.
	 *
	 * @return boolean
	 */
	public function is_variation_attribute_type_option() {
		if ( ! isset( $this->option->settings['choice_type'] ) || ! isset( $this->option->settings['selected_attribute'] ) ) {
			return false;
		}

		return $this->option->settings['choice_type'] === 'variation_attributes' && ! empty( $this->option->settings['selected_attribute'] );
	}

	/**
	 * Determmine whether the selected attribute should be displayed in the product page.
	 *
	 * @return ?string
	 */
	public function is_valid_attribute_option_for_product() {
		if ( ! $this->is_variation_attribute_type_option() ) {
			return null;
		}

		if ( ! $this->product instanceof WC_Product_Variable ) {
			return null;
		}

		$global_attributes = $this->get_global_attributes();

		if ( empty( $global_attributes ) ) {
			return null;
		}

		$selected_attribute    = $this->option->settings['selected_attribute'];
		$global_attribute_keys = array_keys( $global_attributes );

		if ( empty( $selected_attribute ) || ! in_array( $selected_attribute, $global_attribute_keys, true ) ) {
			return null;
		}

		return $selected_attribute;
	}

	/**
	 * Determine whether all choices share the same price.
	 * 
	 * @since 2.6.3
	 *
	 * @return bool
	 */
	protected function choices_have_equal_pricing(): bool {
		/**
		 * Filter whether the option has equal pricing.
		 *
		 * By default, the price label of choices that share the same price is displayed only once
		 * to the right of the option name. This avoids the redundancy of having the same price
		 * repeated for each choice. For certain use cases, developers may want to override this
		 * behaviour and display the price for each choice anyway. This can be achieved by returning
		 * false from the filter callback (or by simply using the `__return_false` function).
		 *
		 * Example:
		 * ```php
		 * add_filter( 'wc_product_options_choices_have_equal_pricing', '__return_false' );
		 * ```
		 *
		 * @param bool $equal_pricing Whether the option has equal pricing.
		 * @param self $option The current option object.
		 * @param WC_Product $product The current product object.
		 */
		return apply_filters( 'wc_product_options_choices_have_equal_pricing', $this->equal_pricing, $this, $this->get_product() );
	}

	/**
	 * Retrieves the global attributes for the product variation.
	 *
	 * @return null|array
	 */
	protected function get_global_attributes(): ?array {
		if ( ! $this->is_variation_attribute_type_option() ) {
			return null;
		}

		if ( ! $this->product instanceof WC_Product_Variable ) {
			return null;
		}

		$variation_attributes = $this->product->get_variation_attributes();

		$global_attributes = array_filter(
			$variation_attributes,
			function ( $attribute_name ) {
				return strpos( $attribute_name, 'pa_' ) === 0;
			},
			ARRAY_FILTER_USE_KEY
		);

		if ( empty( $global_attributes ) ) {
			return null;
		}

		return $global_attributes;
	}

	/**
	 * Determine whether the choice is preselected.
	 *
	 * @param array $choice
	 * @return bool
	 */
	protected function is_choice_preselected( $choice ): bool {
		if ( $this->is_variation_attribute_type_option() ) {
			// the choice is a term of a product attribute, check if it is the default choice.
			$default_attributes = $this->product->get_default_attributes();
			$default_term       = $default_attributes[ $this->option->settings['selected_attribute'] ] ?? null;

			// An attribute term is preselected if it is the default term for the product
			// or if the choice is selected in the product options configuration and no default term is selected for the product.
			// The default term of a product has higher priority than the selected choice.
			// This avoids that an attributes has two selected choices, which is not allowed.
			return ( ! $default_term && (bool) $choice['selected'] ) || $choice['term'] === $default_term;
		}

		/**
		 * Filters whether the choice is preselected.
		 *
		 * @param bool $is_selected True if the choice is preselected, false otherwise.
		 * @param array $choice The choice array.
		 * @param Abstract_Field $field The current field object.
		 * @param WC_Product $product The current product object.
		 */
		return apply_filters( 'wc_product_options_is_choice_preselected', (bool) ( $choice['selected'] ?? false ), $choice, $this, $this->get_product() );
	}

	/**
	 * Gets the edit option link for the field.
	 *
	 * @return string
	 */
	public function get_edit_option_link() {
		$edit_link    = sprintf(
			admin_url( 'admin.php?post_type=products&page=wpo_options#edit/%d/%d' ),
			$this->option->group_id,
			$this->option->id
		);
		$button_title = esc_attr__( 'Edit this option', 'woocommerce-product-options' );

		return sprintf(
			'<a href="%1$s" class="wpo-edit-option-link" title="%2$s" aria-label="%2$s" rel="noopener noreferrer">%3$s</a>',
			esc_url( $edit_link ),
			$button_title,
			'<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="24" height="24">
				<path d="M.75,17.5A.751.751,0,0,1,0,16.75V12.569a.755.755,0,0,1,.22-.53L11.461.8a2.72,2.72,0,0,1,3.848,0L16.7,2.191a2.72,2.72,0,0,1,0,3.848L5.462,17.28a.747.747,0,0,1-.531.22ZM1.5,12.879V16h3.12l7.91-7.91L9.41,4.97ZM13.591,7.03l2.051-2.051a1.223,1.223,0,0,0,0-1.727L14.249,1.858a1.222,1.222,0,0,0-1.727,0L10.47,3.91Z" transform="translate(4.5,3.5),scale(.9)"></path>
			</svg>'
		);
	}
}
