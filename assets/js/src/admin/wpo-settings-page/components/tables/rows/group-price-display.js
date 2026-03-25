/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEffect } from 'react';
import { Controller } from 'react-hook-form';

/**
 * Internal dependencies
 */
import { SelectControl } from '@barn2plugins/components';

/**
 * Price Display Row Component
 */
const PriceDisplayRow = ( { formMethods, group } ) => {
	const priceDisplay = formMethods.watch( 'settings.price_display' );

	useEffect( () => {
		if ( ! group ) {
			return;
		}

		if ( ! group?.settings || Array.isArray( group.settings ) ) {
			group.settings = {};
		}

		if ( ! group?.settings?.price_display ) {
			group.settings.price_display = 'default';
		}
	}, [ group ] );

	return (
		<tr valign="top" className="wpo-group-settings-price_display">
			<th scope="row" className="titledesc ">
				<span className={ 'group-form-label' }>
					{ __( 'Price display', 'woocommerce-product-options' ) }
				</span>
			</th>
			<td>
				<fieldset>
					<Controller
						control={ formMethods.control }
						name={ 'settings.price_display' }
						defaultValue="default"
						render={ ( { field } ) => (
							<select
								className="wpo-price-display-select"
								{ ...field }
							>
								<option value="default">
									{__('Default', 'woocommerce-product-options')}
								</option>
								<option value="extend">
									{__('Add a prefix or a suffix to the main product price', 'woocommerce-product-options')}
								</option>
								<option value="hide">
									{__('Hide the main product price', 'woocommerce-product-options')}
								</option>
							</select>
						)}
					/>

					{ priceDisplay === 'extend' && (
						<span className="wpo-price-display">
							<Controller
								control={ formMethods.control }
								name={ `settings.price_display_prefix` }
								render={ ( { field } ) => (
									<div className="wpo-price-display-prefix-field">
										<label htmlFor="price_display_prefix">
											{ __( 'Prefix', 'woocommerce-product-options' ) }
										</label>
										<input
											type="text"
											id="price_display_prefix"
											{ ...field }
											placeholder={ __( 'e.g. Starting from ', 'woocommerce-product-options' ) }
										/>
									</div>
								) }
							/>
							<Controller
								control={ formMethods.control }
								name={ `settings.price_display_suffix` }
								render={ ( { field } ) => (
									<div className="wpo-price-display-suffix-field">
										<label htmlFor="price_display_suffix">
											{ __( 'Suffix', 'woocommerce-product-options' ) }
										</label>
										<input
											type="text"
											id="price_display_suffix"
											{ ...field }
											placeholder={ __( 'e.g. per square meter', 'woocommerce-product-options' ) }
										/>
									</div>
								) }
							/>
						</span>
					) }
				</fieldset>
			</td>
		</tr>
	);
}

export default PriceDisplayRow;