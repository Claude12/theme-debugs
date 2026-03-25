<?php

namespace Barn2\Plugin\WC_Product_Options\Fields\Traits;

use Barn2\Plugin\WC_Product_Options\Fields\Abstract_Field;

/**
 * Trait for quantity pickers functionality in fields.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 */
trait With_Quantity_Pickers {

	/**
	 * The option quantity picker settings.
	 *
	 * @var array
	 */
	protected $qty_pickers;

	/**
	 * Returns the valid choices for the field.
	 *
	 * @return null|array
	 */
	abstract public function get_choices(): ?array;

	/**
	 * Returns a specific choice by index.
	 *
	 * @param int $index The choice index.
	 * @return null|array
	 */
	abstract public function get_choice( $index ): ?array;

	/**
	 * Whether the option supports a multiselection of choices
	 *
	 */
	abstract public function stores_multiple_values(): bool;

	/**
	 * Run setup hooks for quantity pickers functionality.
	 *
	 * @return void
	 */
	public function run_qty_picker_setup_hooks(): void {
		add_filter( 'wc_product_options_field_css_classes', [ $this, 'add_quantity_picker_field_class' ], 10, 2 );
		add_filter( 'wc_product_options_is_choice_preselected', [ $this, 'is_choice_preselected_through_quantity_picker' ], 10, 3 );
		add_filter( 'wc_product_options_field_attributes', [ $this, 'add_quantity_picker_field_attributes' ], 1, 2 );

		if ( $this->is_single_selection_field() ) {
			/**
			 * Filters the inline position of the quantity picker for single-selection fields.
			 *
			 * @param string $inline_position The inline position ('after' or 'before').
			 * @param Abstract_Field $field The current field object.
			 */
			$inline_position = apply_filters( 'wc_product_options_quantity_picker_inline_position', 'after', $this ) === 'before' ? 'prefix' : 'suffix';
			add_filter( "wc_product_options_option_name_{$inline_position}", [ $this, 'get_quantity_picker_option_name_suffix' ], 10, 3 );
		}
	}

	/**
	 * Indicates whether the field is required or not.
	 *
	 * In addition to the parent requirement, if quantity pickers are enabled with a minimum
	 * value or a minimum total greater than zero, the field is considered required.
	 *
	 * @return bool
	 */
	public function is_required() {
		return parent::is_required() || $this->get_quantity_pickers_min() > 0 || $this->get_quantity_pickers_total_min() > 0;
	}

	/**
	 * Determines if the field is a single-selection type.
	 *
	 * @return bool
	 */
	public function is_single_selection_field(): bool {
		if ( in_array( $this->type, [ 'radio', 'color_swatches', 'dropdown' ], true ) ) {
			return true;
		}

		if ( $this->type === 'product' ) {
			return in_array( $this->get_setting( 'product_display_style' ), [ 'radio', 'dropdown' ], true );
		}

		return false;
	}

	/**
	 * Gets the name attribute for the field input.
	 *
	 * @return string
	 */
	public function get_qty_input_name(): string {
		return sprintf( 'wpo-option[option-%d-qty]', $this->option->id );
	}

	/**
	 * Get the quantity picker settings.
	 *
	 * @return array
	 */
	public function get_quantity_pickers_settings(): array {
		if ( ! isset( $this->qty_pickers ) ) {
			$this->qty_pickers = $this->get_setting( 'qty_pickers', [] );
		}

		return $this->qty_pickers ?? [];
	}

	/**
	 * Determines if the field has quantity pickers enabled.
	 *
	 * @return bool
	 */
	public function has_quantity_pickers(): bool {
		$has_qty_pickers = isset( $this->option->settings['choice_type'] ) && $this->option->settings['choice_type'] === 'custom_with_qty';

		return apply_filters( 'wc_product_options_field_has_quantity_pickers', $has_qty_pickers, $this );
	}

	/**
	 * Get a specific quantity picker setting.
	 *
	 * @param string $key The setting key.
	 * @param string $default The default value if the setting is not found.
	 * @return string
	 */
	public function get_quantity_pickers_setting( string $key, $default = '' ) {
		return $this->qty_pickers[ $key ] ?? $default;
	}

	/**
	 * Return the style of the quantity pickers for the current option.
	 *
	 * @return string
	 */
	public function get_quantity_pickers_style(): string {
		return $this->get_quantity_pickers_setting( 'style', 'spinner' );
	}

	/**
	 * Return the minimum value of the quantity picker for individual choices.
	 *
	 * @return int
	 */
	public function get_quantity_pickers_min(): int {
		if ( ! $this->has_quantity_pickers() ) {
			return 0;
		}

		return (int) $this->get_quantity_pickers_setting( 'min', 0 );
	}

	/**
	 * Return the maximum value of the quantity picker for individual choices.
	 *
	 * @return int
	 */
	public function get_quantity_pickers_max(): int {
		if ( ! $this->has_quantity_pickers() ) {
			return 0;
		}

		return (int) $this->get_quantity_pickers_setting( 'max', 0 );
	}

	/**
	 * Return the step of the quantity picker for individual choices.
	 *
	 * @return int
	 */
	public function get_quantity_pickers_step(): int {
		if ( ! $this->has_quantity_pickers() ) {
			return 1;
		}

		return (int) $this->get_quantity_pickers_setting( 'step', 1 );
	}

	/**
	 * Return the minimum value for the quantity pickers across the entire option.
	 *
	 * @return int
	 */
	public function get_quantity_pickers_total_min(): int {
		if ( ! $this->has_quantity_pickers() || $this->is_single_selection_field() ) {
			return 0;
		}

		return (int) $this->get_quantity_pickers_setting( 'total_min', 0 );
	}

	/**
	 * Return the maximum value for the quantity pickers across the entire option.
	 *
	 * @return int
	 */
	public function get_quantity_pickers_total_max(): int {
		if ( ! $this->has_quantity_pickers() ) {
			return 0;
		}

		return (int) $this->get_quantity_pickers_setting( 'total_max', 0 );
	}

	/**
	 * Return the default value of the quantity picker for an individual choice.
	 *
	 * @param int $index The choice index.
	 * @return int
	 */
	public function get_choice_quantity_picker_default_value( $index = 0 ): int {
		if ( ! $this->has_quantity_pickers() ) {
			return 0;
		}

		$choice_qty_picker = intval( $this->get_choice( $index )['default_qty'] ?? 0 );

		// Dropdowns store multiple values but we treat them as single-selection
		// in the context of quantity pickers because it is unpractical
		// to include individual quantity pickers for each choice
		// In case multiple choices are selected, each choice will be multiplied
		// by the same quantity specified in the option name
		if ( ! $this->stores_multiple_values() || $this->type === 'dropdown' ) {
			return $choice_qty_picker;
		}

		return max( $choice_qty_picker, $this->get_quantity_pickers_min() );
	}

	/**
	 * Get the HTML markup for a quantity picker spin button.
	 *
	 * @param string $direction The direction of the spin button ('up' or 'down').
	 * @return string
	 */
	public function get_quantity_picker_spin_button_html( $direction = 'up' ): string {
		$glyphs = [
			'spinner' => [
				'up'   => '<svg width="8" height="6" viewBox="0 0 8 6" xmlns="http://www.w3.org/2000/svg"><path d="M1.04907e-06 6L4 -6.99382e-07L8 6L1.04907e-06 6Z" /></svg>',
				'down' => '<svg width="8" height="6" viewBox="0 0 8 6" xmlns="http://www.w3.org/2000/svg"><path d="M8 3.51264e-07L4 6L-6.50838e-07 -3.48118e-07L8 3.51264e-07Z" /></svg>',
			],
			'stepper' => [
				'up'   => '<svg width="10" height="10" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg"><path d="M4 10V6H0V4H4V0H6V4H10V6H6V10H4Z" /></svg>',
				'down' => '<svg width="10" height="2" viewBox="0 0 10 2" xmlns="http://www.w3.org/2000/svg"><path d="M0 2V0H10V2H0Z" /></svg>',
			],
		];

		return sprintf(
			'<button type="button" class="%1$s wpo-quantity-button" data-direction="%2$s" tabindex="-1">%3$s</button>',
			esc_attr( $direction === 'up' ? 'wpo-quantity-increase' : 'wpo-quantity-decrease' ),
			esc_attr( $direction ),
			$glyphs[ $this->get_quantity_pickers_style() ][ $direction ]
		);
	}

	/**
	 * Get the HTML markup of the quantity picker for the entire option.
	 *
	 * This is used for single-selection fields like radios, color swatches and dropdowns.
	 * NOTE: About dropdowns - quantity pickers for dropdowns only support the single-selection mode.
	 *
	 * @return string
	 */
	public function get_option_quantity_picker_html(): string {
		if ( ! $this->has_quantity_pickers() ) {
			return '';
		}

		$default_value = 0;

		foreach ( $this->get_choices() as $index => $choice ) {
			$choice_default = $this->get_choice_quantity_picker_default_value( $index );

			if ( $choice_default ) {
				$default_value = $choice_default;
				break;
			}
		}

		return sprintf(
			'<span class="wpo-quantity-picker wpo-quantity-picker-%2$s">
				%1$s
				<input class="wpo-qty-picker" type="number" name="%3$s" value="%4$s" min="%5$s" max="%6$s" step="1" />
				%7$s
			</span>',
			$this->get_quantity_picker_spin_button_html( 'down' ),
			esc_attr( $this->get_quantity_pickers_style() ),
			esc_attr( $this->get_qty_input_name() ),
			esc_attr( $default_value ),
			esc_attr( $this->get_quantity_pickers_min() ),
			esc_attr( $this->get_quantity_pickers_max() > 0 ? $this->get_quantity_pickers_max() : '' ),
			$this->get_quantity_picker_spin_button_html( 'up' )
		);
	}

	/**
	 * Get the HTML markup of the quantity picker for a specific choice.
	 *
	 * @param int $index The choice index.
	 * @return string
	 */
	public function get_choice_quantity_picker_html( $index = 0 ): string {
		if ( ! $this->has_quantity_pickers() ) {
			return '';
		}

		$choice = $this->get_choice( $index );

		return sprintf(
			'<div class="wpo-quantity-picker wpo-quantity-picker-%2$s">
				%1$s
				<input class="wpo-qty-picker" type="number" name="%3$s" value="%4$s" min="%5$s" max="%6$s" step="%7$s" />
				%8$s
			</div>',
			$this->get_quantity_picker_spin_button_html( 'down' ),
			esc_attr( $this->get_quantity_pickers_style() ),
			esc_attr( $this->get_qty_input_name() . '[' . $choice['id'] . ']' ),
			esc_attr( $this->get_choice_quantity_picker_default_value( $index ) ),
			esc_attr( $this->get_quantity_pickers_min() ),
			esc_attr( $this->get_quantity_pickers_max() > 0 ? $this->get_quantity_pickers_max() : '' ),
			esc_attr( $this->get_quantity_pickers_step() ),
			$this->get_quantity_picker_spin_button_html( 'up' )
		);
	}

	/**
	 * Adds a custom CSS class to fields that have quantity pickers enabled.
	 *
	 * @param array $classes The existing CSS classes.
	 * @return array
	 */
	public function add_quantity_picker_field_class( array $classes, Abstract_Field $field ): array {
		if ( $this !== $field || ! $this->has_quantity_pickers() ) {
			return $classes;
		}

		$classes[] = 'wpo-field-with-quantity-pickers';

		return $classes;
	}

	/**
	 * Determines if a choice is preselected based on its quantity picker value.
	 *
	 * @param bool  $is_preselected Whether the choice is preselected.
	 * @param array $choice The choice data.
	 * @param Abstract_Field $option The field option.
	 * @return bool
	 */
	public function is_choice_preselected_through_quantity_picker( bool $is_preselected, array $choice, $option ): bool {
		if ( $option !== $this || ! $this->has_quantity_pickers() ) {
			return $is_preselected;
		}

		$index               = $choice['index'] ?? 0;
		$quantity_picker_val = $this->get_choice_quantity_picker_default_value( $index );

		return $quantity_picker_val > 0;
	}

	/**
	 * Adds data attributes to fields that have quantity pickers enabled.
	 *
	 * @param array $attributes The existing field attributes.
	 * @param Abstract_Field $field The current field object.
	 * @return array
	 */
	public function add_quantity_picker_field_attributes( array $attributes, Abstract_Field $field ): array {
		if ( $field->get_id() !== $this->get_id() || ! $this->has_quantity_pickers() ) {
			return $attributes;
		}

		$attributes['data-qty-total-min'] = $this->get_quantity_pickers_total_min();
		$attributes['data-qty-total-max'] = $this->get_quantity_pickers_total_max();

		return $attributes;
	}

	/**
	 * Append the quantity picker HTML to the option name suffix.
	 *
	 * @param string $suffix The suffix string.
	 * @param Abstract_Field $field The current field object.
	 * @return string
	 */
	public function get_quantity_picker_option_name_suffix( $suffix, $field ) {
		if ( $field->get_id() !== $this->get_id() ) {
			return $suffix;
		}

		if ( ! $this->has_quantity_pickers() ) {
			return $suffix;
		}

		if ( strpos( $suffix, 'wpo-quantity-picker' ) !== false ) {
			return $suffix;
		}

		return $suffix . $this->get_option_quantity_picker_html();
	}
}
