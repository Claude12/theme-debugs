<?php

namespace Barn2\Plugin\WC_Product_Options\Fields\Traits;

use Barn2\Plugin\WC_Product_Options\Util\Price as Price_Util;
use Barn2\Plugin\WC_Product_Options\Util\Conditional_Logic as Conditional_Logic_Util;

trait Cart_Item_Data_Multi {

	/**
	 * Retrieves the cart item data for the selected value(s) of the field.
	 *
	 * @param mixed       $values
	 * @param WC_Product $product
	 * @param int $quantity
	 * @param array $options
	 * @param int[]|int|null $qty_pickers
	 * @return array
	 */
	public function get_cart_item_data( $values, $product, $quantity, $options, $qty_pickers = null ): ?array {
		// wpt multicart can provide empty values inside the array
		if ( is_array( $values ) ) {
			$values = array_filter( $values );
		}

		if ( empty( $values ) ) {
			return null;
		}

		if ( Conditional_Logic_Util::is_field_hidden( $this, $options ) ) {
			return null;
		}

		$item_data = [
			'name'        => $this->option->name,
			'type'        => $this->option->type,
			'option_id'   => $this->option->id,
			'group_id'    => $this->option->group_id,
			'value'       => $values,
			'choice_data' => [],
		];

		foreach ( $values as $value ) {
			$choice = $this->get_choice_for_value( $value );

			if ( ! $choice ) {
				continue;
			}

			$choice_data = [
				'index' => $choice['index'],
				'label' => $choice['label'],
			];

			if ( ! empty( $qty_pickers ) ) {
				if ( is_array( $qty_pickers ) ) {
					// Multi-selection fields always have quantity pickers as an array
					$choice_data['quantity'] = $qty_pickers[ $choice['id'] ] ?? 0;
				} else {
					// This case handles dropdowns with quantity pickers
					// Although dropdowns are multiselect, they behave as single-selection
					// when it comes to quantity pickers
					$choice_data['quantity'] = (int) $qty_pickers;
				}
			}

			if ( $choice['pricing'] && $choice['price_type'] !== 'no_cost' ) {
				$pricing = Price_Util::get_user_choice_pricing( $choice );

				$choice_data['pricing'] = [
					'type'   => $choice['price_type'],
					'amount' => (float) $pricing,
					'hide'   => $this->option->settings['hide_prices'] ?? false,
				];
			}

			$item_data['choice_data'][] = $choice_data;
		}

		return $item_data;
	}
}
