import { getFieldInputType, isFieldType, isFieldCheckboxLike, isFieldRadioLike, isFieldHidden, isFieldTextLike } from './util';
import { getProductVariables } from './price-util';
import { isAfter, isBefore, isSameDay } from 'date-fns';
import { is } from 'date-fns/locale';
import { loop } from '@wordpress/icons';

const conditionalLogic = function ( addToCartForm ) {
	const form = addToCartForm;
	const productId = form?.dataset?.product_id;
	const variationFormClassnames = [ 'variations_form', 'wpt_variations_form' ];
	let fieldData;
	const $ = window.jQuery;
	let productVariables = getProductVariables( form );

	function init() {
		if ( ! ( form instanceof HTMLFormElement ) ) {
			return false;
		}

		fieldData = Array.from( form.querySelectorAll( '.wpo-field' ), ( field ) => {
			return {
				element: field,
				inputElements: field.querySelectorAll( getFieldInputType( field.dataset.type ) ),
				type: field.dataset?.type,
				groupId: field.dataset?.groupId ? parseInt( field.dataset.groupId ) : null,
				optionId: field.dataset?.optionId ? parseInt( field.dataset.optionId ) : null,
				clogic: field.dataset.clogic === 'true' ? true : false,
				clogicRelation: field.dataset?.clogicRelation ?? false,
				clogicVisibility: field.dataset?.clogicVisibility ?? false,
				clogicConditions: field.dataset?.clogicConditions
					? JSON.parse( field.dataset.clogicConditions )
					: false,
			};
		} );

		bindEvents();
		checkLogic();
	}

	function bindEvents() {
		// bind the listener for input changes
		fieldData.forEach( ( field ) => field?.element.addEventListener( 'change', checkLogic ) );

		// bind listener for quantity picker changes
		fieldData.forEach( ( field ) => field?.element.querySelectorAll( 'input.wpo-qty-picker[type="number"]' ).forEach( ( qtyPicker ) => {
			qtyPicker.addEventListener( 'change', checkLogic );
		} ) );

		variationFormBindEvents();
	}

	function variationFormBindEvents() {
		if ( variationFormClassnames.some( (className) => form.classList.contains( className ) ) === false ) {
			return;
		}

		$( form ).on( 'woocommerce_variation_select_change', checkLogic );
		$( form ).on( 'reset_variations', checkLogic );
		$( form ).on( 'found_variation', updateProductVariables );
	}

	function variationFormUnbindEvents() {
		$( form ).off( 'woocommerce_variation_select_change', checkLogic );
		$( form ).off( 'reset_variations', checkLogic );
		$( form ).off( 'found_variation', updateProductVariables );
	}

	/**
	 * Checks the conditional logic for the current form values.
	 */
	function updateProductVariables( event, foundVariation ) {
		const variationForm = event?.target;
		const totalsContainer = form.querySelector( '.wpo-totals-container' );

		if ( totalsContainer ) {
			totalsContainer.dataset.productPrice = parseFloat( foundVariation.display_price || 0 );
			totalsContainer.dataset.weight = parseFloat( foundVariation.weight || 0 );
			totalsContainer.dataset.width = parseFloat( foundVariation.width || 0 );
			totalsContainer.dataset.length = parseFloat( foundVariation.length || 0 );
			totalsContainer.dataset.height = parseFloat( foundVariation.height || 0 );
			productVariables = getProductVariables( variationForm );
		}

		checkLogic();
	}

	function checkLogic() {
		const totalsContainer = form.querySelector('.wpo-totals-container');
		variationFormUnbindEvents();

		fieldData
			.filter( ( field ) => field.clogic && field.clogicConditions )
			.forEach( ( field ) => {
				checkForConditions( field );
			} );

		const visibleFormulas = fieldData.filter( ( field ) => field.type === 'price_formula' && !isFieldHidden( field ) );

		totalsContainer.dataset.excludeProductPrice = visibleFormulas.reduce( ( acc, formula ) => {
			// If there is at least one formula with the "Ignore main product price" enabled,
			// set `data-exclude-product-pricePrice` to true
			return acc || formula.inputElements[0].dataset.excludeProductPrice === 'true';
		}, false );

		variationFormBindEvents();
	}

	function checkForConditions( field ) {
		const currentValues = getFormValues();
		currentValues.push( ...getAttributeValues() );
		currentValues.push( ...getShippingPropertyValues() );

        // we check for 'some' or 'every' item in the array
        // depending on the relation set in the conditional logic
        const clogicMethod = field.clogicRelation === 'or' ? 'some' : 'every';

        if ( field.clogicConditions[ clogicMethod ]( ( condition ) => checkCondition( currentValues, condition ) ) ) {
            toggleVisibility( field, true );
        } else {
            toggleVisibility( field, false );
        }
	}

	/**
	 * Check a condition against the current form values.
	 *
	 * @param {Array}  formValues
	 * @param {Object} condition
	 * @return {boolean} Whether the condition is satisfied.
	 */
	function checkCondition( formValues, condition ) {
		const field = formValues.find( ( formValue ) => String(formValue.optionId) === String(condition.optionID) );

		if ( ! field ) {
			return false;
		}

		if ( field.values.length === 1 ) {
			let value = field.values[ 0 ];

			if ( condition?.optionType === 'product' && typeof value === 'string' ) {
				const valueArray = value.split( ',' );
				// set value as the last item in the array
				value = valueArray[ valueArray.length - 1 ];
			}

			switch ( condition.operator ) {
				case 'contains':
					return condition.value === 'any' ? true : value === condition.value;
				case 'not_contains':
					return condition.value === 'any' ? false : value !== condition.value;
				case 'equals':
					return condition.value === 'any' ? true : value === condition.value;
				case 'not_equals':
					return condition.value === 'any' ? false : value !== condition.value;
				case 'greater':
					return parseFloat( value ) > parseFloat( condition.value );
				case 'less':
					return parseFloat( value ) < parseFloat( condition.value );
				case 'not_empty':
					return value.length > 0;
				case 'empty':
					return value.length === 0;
				case 'date_greater':
					return isAfter( new Date( value ), new Date( condition.value ) );
				case 'date_less':
					return isBefore( new Date( value ), new Date( condition.value ) );
				case 'date_equals':
					return isSameDay( new Date( value ), new Date( condition.value ) );
				case 'date_not_equals':
					return ! isSameDay( new Date( value ), new Date( condition.value ) );
			}
		} else {
			let values = field.values;

			if ( condition?.optionType === 'product' ) {
				values = values.map( ( value ) => {
					if ( typeof value === 'string' ) {
						const valueArray = value.split( ',' );
						// set value as the last item in the array
						return valueArray[ valueArray.length - 1 ];
					}
					return value;
				} );
			}

			switch ( condition.operator ) {
				case 'contains':
					return condition.value === 'any' && values.length > 0 ? true : values.includes( condition.value );
				case 'not_contains':
					return condition.value === 'any' ? values.length === 0 : ! values.includes( condition.value );
				case 'equals':
					return condition.value === 'any' && values.length > 0 ? true : values.includes( condition.value );
				case 'not_equals':
					return condition.value === 'any' ? values.length === 0 : ! values.includes( condition.value );
				case 'empty':
					return values.length === 0;
				case 'not_empty':
					return values.length > 0;
			}
		}

		return false;
	}

	/**
	 * Toggles field visibility based on the provided boolean.
	 *
	 * @param {Object}  field
	 * @param {boolean} passing
	 */
	function toggleVisibility( field, passing ) {
		let isHidden = false;

		if ( passing ) {
			if ( field.clogicVisibility === 'show' ) {
				field.element.classList.remove( 'wpo-field-hide' );
			}

			if ( field.clogicVisibility === 'hide' ) {
				field.element.classList.add( 'wpo-field-hide' );
				isHidden = true;
			}
		} else {
			if ( field.clogicVisibility === 'show' ) {
				field.element.classList.add( 'wpo-field-hide' );
				isHidden = true;
			}

			if ( field.clogicVisibility === 'hide' ) {
				field.element.classList.remove( 'wpo-field-hide' );
			}
		}

		if ( isHidden && field.element.dataset.variationAttribute ) {
			// if the field is hidden and is a variation attribute,
			// reset to the default value for that attribute
			// WARNING: variation attributes that are subject to conditional logic
			// MUST have a default value set in the variation editor
			// otherwise the form will trigger a validation error from the server side
			field.element.querySelectorAll( 'input[checked="checked"]' ).forEach( (input) => {
				input.checked = true;
				const coreVariationDropdown = form.querySelector( `select[name="attribute_${ field.element.dataset.variationAttribute }"]` );

				if ( coreVariationDropdown === null ) {
					return;
				}

				coreVariationDropdown.value = input.dataset.attributeTerm;
				$( coreVariationDropdown ).trigger( 'change' );
			});
		}

		form.dispatchEvent( new Event( 'wpo_run_frontend_calculation' ) );
	}

	/**
	 * Get the current input values for all fields.
	 *
	 * @return {Array} An array of objects containing the field option ID and values.
	 */
	function getFormValues() {
		const formValues = [];
		const visibleFields = fieldData.filter( ( field ) => ! field.element.classList.contains( 'wpo-field-hide' ) );

		visibleFields.forEach( ( field ) => {
			const { optionId } = field;
			const values = getInputValues( field );

			formValues.push( { optionId, values: [ ...values ] } );
		} );

		return formValues;
	}

	function getAttributeValues() {
		let variationSelects = Array.from( form.querySelectorAll( '.variations select' ) );

		variationSelects = variationSelects.map( ( select ) => {
			const re = new RegExp( `_${productId}$`, 'g' );
			return {
				optionId: select.id.replace( re, '' ),
				values: [ select.value ],
			};
		} ).filter( ( attribute ) => attribute.values[ 0 ] !== '' );

		const dataProductAttributes = form.querySelector( '.wpo-options-container' )?.dataset?.productAttributes;
		let productAttributes = Object.entries( JSON.parse( dataProductAttributes ?? '{}' ) );

		productAttributes = productAttributes.map( ( attribute ) => {
			return {
				optionId: attribute[ 0 ],
				values: attribute[ 1 ],
			};
		} );

		return [
			...variationSelects,
			...productAttributes,
		];
	}

	function getShippingPropertyValues() {
		return Object.keys( productVariables ).map( ( key ) => {
			return { optionId: `product_${key}`, values: [ productVariables[ key ] ] };
		} );
	}

	/**
	 * Get the current input values for a field.
	 *
	 * @param {Object} field
	 * @return {Array} An array of values.
	 */
	function getInputValues( field ) {
		let inputElements = false;

		switch ( true ) {
			case isFieldCheckboxLike( field ):
				inputElements = field.element.querySelectorAll( 'input[type="checkbox"]' );
				break;
			case isFieldRadioLike( field ):
				inputElements = field.element.querySelectorAll( 'input[type="radio"]' );
				break;
			case isFieldType( field, 'dropdown' ):
				inputElements = field.element.querySelector( 'select' );
				break;
			case isFieldType( field, [ 'text', 'datepicker', 'file_upload', 'customer_price', 'number' ] ):
				inputElements = field.element.querySelector( 'input' );
				break;
			case isFieldType( field, 'textarea' ):
				inputElements = field.element.querySelector( 'textarea' );
				break;
		}

		let values = [];

		if ( 'file_upload' === field.type ) {
			values = JSON.parse( inputElements.value );
		} else if ( 'dropdown' === field.type ) {
			values = Array.from( inputElements )
				.filter( ( inputElement ) => inputElement.selected )
				.map( ( inputElement ) => inputElement.value );
		} else {
			if ( inputElements instanceof NodeList ) {
				inputElements = Array.from( inputElements );
			} else {
				inputElements = [ inputElements ];
			}

			values = inputElements.map( ( inputElement ) => {
				if ( isFieldCheckboxLike( field ) || isFieldRadioLike( field ) ) {
					return inputElement.checked ? inputElement.value : '';
				}

				return inputElement.value;
			} );
		}

		return values.filter( Boolean );
	}

	return { init };
};

export default conditionalLogic;
