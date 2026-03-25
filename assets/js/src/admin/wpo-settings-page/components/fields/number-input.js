const NumberInput = ( props ) => {
	const { type = 'spinner', className = '' } = props;
	const glyphs = {
		spinner: {
			increase: <svg width="8" height="6" viewBox="0 0 8 6" xmlns="http://www.w3.org/2000/svg"><path d="M1.04907e-06 6L4 -6.99382e-07L8 6L1.04907e-06 6Z" /></svg>,
			decrease: <svg width="8" height="6" viewBox="0 0 8 6" xmlns="http://www.w3.org/2000/svg"><path d="M8 3.51264e-07L4 6L-6.50838e-07 -3.48118e-07L8 3.51264e-07Z" /></svg>,

		},
		stepper: {
			increase: <svg width="10" height="10" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg"><path d="M4 10V6H0V4H4V0H6V4H10V6H6V10H4Z" /></svg>,
			decrease: <svg width="10" height="2" viewBox="0 0 10 2" xmlns="http://www.w3.org/2000/svg"><path d="M0 2V0H10V2H0Z" /></svg>,
		},
	};

	const inputRef = React.useRef();
	let intervalId = null;
	let timeoutId = null;

	const startStepping = ( input, direction ) => {
		// Immediate step
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

	const stopStepping = () => {
		if ( timeoutId ) {
			clearTimeout( timeoutId );
			timeoutId = null;
		}
		if ( intervalId ) {
			clearInterval( intervalId );
			intervalId = null;
		}
	}

	return Object.keys(glyphs).includes(type) && 
		<div className={ `wpo-${type} ${className}` }>
			<button
				type="button"
				className={ `wpo-${type}-button wpo-decrease` }
				onMouseDown={ () => startStepping( inputRef.current, 'down' ) }
				onMouseUp={ stopStepping }
				onMouseLeave={ stopStepping }
			>
				{glyphs[type].decrease}
			</button>
			<input type="number" min="0" defaultValue="1" className={ `wpo-${type}-input` } ref={inputRef} />
			<button
				type="button"
				className={ `wpo-${type}-button wpo-increase` }

				onMouseDown={ () => startStepping( inputRef.current, 'up' ) }
				onMouseUp={ stopStepping }
				onMouseLeave={ stopStepping }
			>
				{glyphs[type].increase}
			</button>
		</div>;
}

export default NumberInput;