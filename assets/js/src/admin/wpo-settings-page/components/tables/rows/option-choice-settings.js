/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';

/**
 * External dependencies
 */
import { RadioControl } from '@barn2plugins/components';
import { Controller } from 'react-hook-form';

/**
 * Internal dependencies
 */
import OptionFormRow from '../option-form-row';
import NumberInput from '../../fields/number-input';
import WCTableTooltip from '../../wc-table-tooltip';

const ChoiceSettingsRow = ( { formMethods, index, option } ) => {
	const optionType = formMethods.watch( `options.${ index }.type` );
	const choiceType = formMethods.watch( `options.${ index }.settings.choice_type` );
	const choices = formMethods.watch( `options.${ index }.choices` );
	const maxQtyLimit = formMethods.watch( `options.${ index }.settings.choice_qty.max` ) ?? '';
	const productSelection = formMethods.watch( `options.${ index }.settings.product_selection` );
	const dynamicProductsLimit = formMethods.watch( `options.${ index }.settings.dynamic_products.limit` );
	const productDisplayStyle = formMethods.watch( `options.${ index }.settings.product_display_style` );
	const qtyPickerStyle = formMethods.watch( `options.${ index }.settings.qty_pickers.style` );

	useEffect( () => {
		const qtyPickers = formMethods.getValues( `options.${ index }.settings.qty_pickers` );

		if ( ! qtyPickers || Array.isArray( qtyPickers ) && qtyPickers.length === 0 ) {
			formMethods.setValue( `options.${ index }.settings.qty_pickers`, {} );
		}
	}, [ formMethods ] );

	const getProductsLength = () => {
		if ( productSelection === 'dynamic' ) {
			return dynamicProductsLimit;
		}

		const products = [];
		const manualProducts = option.settings.manual_products;

		manualProducts?.forEach( ( product ) => {
			if ( product.type === 'simple' ) {
				products.push( product.product_id );
			} else if ( product?.variations?.length ) {
				products.push( ...product.variations.map( ( variation ) => variation.id ) );
			}
		} );

		return products?.length ?? 0;
	};

	const getChoicesLength = () => {
		if ( optionType === 'product' ) {
			return getProductsLength();
		}

		return choices?.length ?? 0;
	};

	const isProductDisplayStyle = ( style = 'product' ) => {
		return optionType === 'product' && [ style ].includes( productDisplayStyle );
	}

	const hasChoiceLimits = () => {
		return [ 'checkbox', 'dropdown', 'images', 'text_labels' ].includes( optionType ) ||
			( optionType === 'product' && [ 'checkbox', 'dropdown', 'image_buttons' ].includes( productDisplayStyle ) );
	}

	const showQuantityPickers = () => {
		if ( optionType === 'product' ) {
			return true;
		}

		return choiceType === 'custom_with_qty' ||
			( optionType === 'product' && ! isProductDisplayStyle( 'product' ) );
	}

	const hasQuantityLimits = () => {
		if ( optionType === 'product' && getQtyPickerStyle() === 'none' ) {
			return false;
		}

		return choiceType === 'custom_with_qty' || optionType === 'product';
	}

	const hasTotalQuantityLimits = () => {
		if ( optionType === 'product' && [ 'radio', 'dropdown' ].includes( productDisplayStyle ) ) {
			return false;
		}

		if ( maxQtyLimit !== '' && parseInt( maxQtyLimit ) === 1 ) {
			return false;
		}

		const choiceMax = option.settings?.choice_qty?.max ?? '';
		return ( [ 'checkbox', 'images', 'text_labels' ].includes( optionType ) && ( choiceMax === '' || parseInt( choiceMax ) > 1 ) )
			|| ( optionType === 'product' && ! isProductDisplayStyle( 'product' ) );
	}

	const getQtyPickerLabel = () => {
		if ( optionType === 'product' ) {
			return __( 'Quantity pickers', 'woocommerce-product-options' );
		}

		return __( 'Picker style', 'woocommerce-product-options' );
	}

	const getDefaultQtyPickerStyle = () => {
		const defaultStyle = optionType === 'product' && ! isProductDisplayStyle( 'product' ) ? 'none' : 'spinner';
		return defaultStyle;
	}

	const getAllowedQtyPickerStyles = () => {
		const styles = [ 'spinner', 'stepper' ];

		if ( optionType === 'product' && ! isProductDisplayStyle( 'product' ) ) {
			styles.push( 'none' );
		}

		return styles;
	}

	const getQtyPickerStyle = ( style = null ) => {
		style = style || qtyPickerStyle;

		return getAllowedQtyPickerStyles().includes( style ) ? style : getDefaultQtyPickerStyle();
	}

	if ( ! hasChoiceLimits() && ! showQuantityPickers() ) {
		return null;
	}

	return (
		<OptionFormRow
			className={ 'wpo-choice-settings-row' }
			name={ `options.${ index }.settings.choice_settings` }
			label={ __( 'Choice settings', 'woocommerce-product-options' ) }
		>
			{ hasChoiceLimits() && (
				<>
					<span className="qty-pickers-label">
						{ __( 'Number of choices', 'woocommerce-product-options' ) }
						&nbsp;
						<WCTableTooltip
							tooltip={ __( 'Set the minimum and maximum number of different choices that can be selected.', 'woocommerce-product-options' ) }
						/>
					</span>
					<div className="wpo-options-min-max-field">
						<div className="wpo-options-min-field">
							<label htmlFor="settings.choice_qty.min">
								{ __( 'Minimum', 'woocommerce-product-options' ) }
							</label>
							<input
								type="number"
								min={ 0 }
								max={ getChoicesLength() }
								step={ 1 }
								className="regular-input"
								placeholder={ 0 }
								{ ...formMethods.register( `options.${ index }.settings.choice_qty.min` ) }
							/>
						</div>

						<div className="wpo-options-max-field">
							<label htmlFor="settings.choice_qty.max">
								{ __( 'Maximum', 'woocommerce-product-options' ) }
							</label>
							<input
								type="number"
								min={ 0 }
								max={ getChoicesLength() }
								step={ 1 }
								className="regular-input"
								placeholder={ __( 'No limit', 'woocommerce-product-options' ) }
								defaultValue={ optionType === 'dropdown' || isProductDisplayStyle( 'dropdown' ) ? 1 : '' }
								{ ...formMethods.register( `options.${ index }.settings.choice_qty.max` ) }
							/>
						</div>
					</div>
				</>
			) }

			{ showQuantityPickers() &&  (
				<>
					<span className="qty-pickers-label">
						{ getQtyPickerLabel() }
						&nbsp;
						<WCTableTooltip
							tooltip={ __( 'Choose how quantity inputs appear to customers.', 'woocommerce-product-options' ) }
						/>
					</span>
					<Controller
						control={ formMethods.control }
						name={ `options.${ index }.settings.qty_pickers.style` }
						render={ ( { field } ) => (
							<div className="wpo-qty-pickers-style-selector">
								<RadioControl
									selected={ getQtyPickerStyle( field.value ) }
									options={ [
										optionType === 'product' && ! isProductDisplayStyle( 'product' ) && (
											{
												label: __( 'None (default)', 'woocommerce-product-options' ),
												value: 'none',
											}
										),
										{
											label: __( 'Spinner', 'woocommerce-product-options' ),
											value: 'spinner',
										},
										{
											label: __( 'Stepper', 'woocommerce-product-options' ),
											value: 'stepper',
										},
									].filter( Boolean ) }
									onChange={ ( value ) => field.onChange( value ) }
								/>

								{ ( [ 'spinner', 'stepper' ].includes( getQtyPickerStyle( field.value ) ) ) && (
									<div className="qty-pickers-style-preview">
										<span>{ __( 'Preview:', 'woocommerce-product-options' ) }</span>
										{ <NumberInput type={ getQtyPickerStyle( field.value ) } /> }
									</div>
								) }
							</div>
						) }
					/>
				</>
			) }

			{ hasQuantityLimits() && (
				<>
					<span className="qty-pickers-label">
						{ __( 'Individual choice quantity limits', 'woocommerce-product-options' ) }
						&nbsp;
						<WCTableTooltip
							tooltip={ __( 'Control the minimum and maximum quantity that can be selected for each individual choice.', 'woocommerce-product-options' ) }
						/>
					</span>

					<div className="wpo-options-min-max-field">
						<div className="wpo-options-min-field">
							<label
								htmlFor={ `options.${ index }.settings.qty_pickers.min` }
							>
								{ __( 'Minimum', 'woocommerce-product-options' ) }
							</label>
							<Controller
								control={ formMethods.control }
								name={ `options.${ index }.settings.qty_pickers.min` }
								render={ ( { field } ) => (
									<input
										type="number"
										className="regular-input"
										label= { __( 'Min quantity for a single choice', 'woocommerce-product-options' ) }
										placeholder={ __( '0', 'woocommerce-product-options' ) }
										min={ 0 }
										step={ 1 }
										onChange={ ( changeValue ) => field.onChange( changeValue ) }
										{ ...formMethods.register( `options.${ index }.settings.qty_pickers.min` ) }
									/>
								) }
							/>
						</div>
						<div className="wpo-options-max-field">
							<label
								htmlFor={ `options.${ index }.settings.qty_pickers.max` }
							>
								{ __( 'Maximum', 'woocommerce-product-options' ) }
							</label>
							<Controller
								control={ formMethods.control }
								name={ `options.${ index }.settings.qty_pickers.max` }
								render={ ( { field } ) => (
									<input
										type="number"
										className="regular-input"
										label= { __( 'Max quantity for a single choice', 'woocommerce-product-options' ) }
										placeholder={ __( 'No limit', 'woocommerce-product-options' ) }
										min={ 1 }
										step={ 1 }
										onChange={ ( changeValue ) => field.onChange( changeValue ) }
										{ ...formMethods.register( `options.${ index }.settings.qty_pickers.max` ) }
									/>
								) }
							/>
						</div>
					</div>

					{ hasTotalQuantityLimits() && (
						<>
							<span className="qty-pickers-label">
								{ __( 'Total quantity limits', 'woocommerce-product-options' ) }
								&nbsp;
								<WCTableTooltip
									tooltip={ __( 'Control the total quantity that can be selected across all choices.', 'woocommerce-product-options' ) }
								/>
							</span>

							<div className="wpo-options-min-max-field">
								<div className="wpo-options-min-field">
									<label
										htmlFor={ `options.${ index }.settings.qty_pickers.total_min` }
									>
										{ __( 'Minimum', 'woocommerce-product-options' ) }
									</label>
									<Controller
										control={ formMethods.control }
										name={ `options.${ index }.settings.qty_pickers.total_min` }
										render={ ( { field } ) => (
											<input
												type="number"
												className="regular-input"
												placeholder={ __( '0', 'woocommerce-product-options' ) }
												min={ 0 }
												step={ 1 }
												onChange={ ( changeValue ) => field.onChange( changeValue ) }
												{ ...formMethods.register( `options.${ index }.settings.qty_pickers.total_min` ) }
											/>
										) }
									/>
								</div>
								<div className="wpo-options-max-field">
									<label
										htmlFor={ `options.${ index }.settings.qty_pickers.total_max` }
									>
										{ __( 'Maximum', 'woocommerce-product-options' ) }
									</label>
									<Controller
										control={ formMethods.control }
										name={ `options.${ index }.settings.qty_pickers.total_max` }
										render={ ( { field } ) => (
											<input
												type="number"
												className="regular-input"
												placeholder={ __( 'No limit', 'woocommerce-product-options' ) }
												min={ 1 }
												step={ 1 }
												onChange={ ( changeValue ) => field.onChange( changeValue ) }
												{ ...formMethods.register( `options.${ index }.settings.qty_pickers.total_max` ) }
											/>
										) }
									/>
								</div>
							</div>
						</>
					) }
				</>
			) }
		</OptionFormRow>

	)
}

export default ChoiceSettingsRow;