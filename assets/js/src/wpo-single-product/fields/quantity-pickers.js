const quantityPickers = () => {
	let fields;
	let intervalId = null;
	let timeoutId = null;

	const init = () => {
		fields = Array.from(
			document.querySelectorAll( '.wpo-field-with-quantity-pickers' )
		).map( ( element ) => {
			return {
				element,
				qtyPickers: element.querySelectorAll( 'input.wpo-qty-picker[type="number"]' ),
			};
		} );

		bindEvents();
	}

	const bindEvents = () => {
		fields.forEach( ( field ) => {
			const form = field.element.closest( 'form' );

			field.qtyPickers.forEach( ( qtyPicker ) => {
				if ( qtyPicker.closest( '.wpo-product-option__list-product' ) ) {
					qtyPicker.addEventListener( 'change', updateQuantityInCartURL );
				} else {
					qtyPicker.addEventListener( 'change', ( e ) => {
						handleQuantityPickerChange( e, field );
					} );
				}

				const wrapper = qtyPicker.closest( '.wpo-quantity-picker' );

				// Bind events to the spin buttons
				const spinButtons = wrapper.querySelectorAll( '.wpo-quantity-button' );
				spinButtons.forEach( ( button ) => {
					button.addEventListener( 'mousedown', ( e ) => {
						const direction = button.getAttribute( 'data-direction' );
						startStepping( qtyPicker, direction );
					} );

					button.addEventListener( 'mouseup', stopStepping );
					button.addEventListener( 'mouseleave', stopStepping );
					// For touch devices
					button.addEventListener( 'touchstart', ( e ) => {
						e.preventDefault(); // Prevent mouse events
						const direction = button.getAttribute( 'data-direction' );
						startStepping( qtyPicker, direction );
					} );
					button.addEventListener( 'touchend', stopStepping );
				} );

				// Bind events to the selection input
				// When the selection changes, update the qty picker accordingly
				let selectionInputs = wrapper.closest( 'li' )?.querySelectorAll( 'input:not(.wpo-qty-picker[type="number"])' );

				if ( ! selectionInputs ) {
					// if the quantity picker is not inside a list item, it means it's a single-selection field
					// so we look for the selection input inside the whole field wrapper
					selectionInputs = wrapper.closest( '.wpo-field' )?.querySelectorAll( 'input:not(.wpo-qty-picker[type="number"]), select' );
				}

				if ( selectionInputs.length > 0 ) {
					selectionInputs.forEach( ( input ) => {
						input.addEventListener( 'change', ( event ) => {
							const input = event.target;
							const selected = input.tagName.toLowerCase() === 'select' ? ( input?.selectedOptions?.length > 0 ) : input?.checked;

							if ( ! selected ) {
								qtyPicker.value = 0;
								form.dispatchEvent( new CustomEvent( 'wpo:recalculate' ) );
							} else if ( parseInt( qtyPicker.value ) === 0 ) {
								qtyPicker.value = parseInt( qtyPicker.min ) > 1 ? parseInt( qtyPicker.min ) : 1;
								form.dispatchEvent( new CustomEvent( 'wpo:recalculate' ) );
							}
						} );
					} );
				}
			} );
		} );
	};

	const handleQuantityPickerChange = ( event, field ) => {
		const input = event.target;
		const selection = input?.closest( 'li' )?.querySelector( 'input:not(.wpo-qty-picker[type="number"])' );

		if ( selection ) {
			selection.checked = ( input?.value > 0 );
		}

		const form = field.element.closest( 'form' );
		form.dispatchEvent( new CustomEvent( 'wpo:recalculate' ) );
	}

	const startStepping = ( input, direction ) => {
		if (direction === 'up') {
			input.stepUp();
		} else {
			input.stepDown();
		}

		// Initial delay before continuous stepping
		timeoutId = setTimeout(() => {
			intervalId = setInterval(() => {
				if ( direction === 'up' ) {
					input.stepUp();
				} else {
					input.stepDown();
				}
			}, 60 ); // Step every 60ms while held
		}, 300) ; // Wait 300ms before starting continuous stepping
	}

	const stopStepping = ( event ) => {
		const target = event.target;
		const wrapper = target.closest( '.wpo-quantity-picker' );
		const input = wrapper.querySelector( 'input.wpo-qty-picker[type="number"]' );

		if ( timeoutId || intervalId ) {
			input.dispatchEvent( new Event( 'change' ) );
		}

		if ( timeoutId ) {
			clearTimeout( timeoutId );
			timeoutId = null;
		}
		if ( intervalId ) {
			clearInterval( intervalId );
			intervalId = null;
		}
	}

	const updateQuantityInCartURL = ( event ) => {
		const target = event.target;
		const qty = target.value;
		const addonProduct = target.closest( '.wpo-product-option__list-product' );
		const cartButton = addonProduct.querySelector( '.single_add_to_cart_button' );
		const newUrl = new URLSearchParams( cartButton.getAttribute( 'href' ) );
		newUrl.set( 'quantity', qty );
		cartButton.setAttribute( 'data-quantity', qty );
		cartButton.setAttribute( 'href', `?${newUrl.toString()}` );
	};

	return { init };
};

export default quantityPickers();
