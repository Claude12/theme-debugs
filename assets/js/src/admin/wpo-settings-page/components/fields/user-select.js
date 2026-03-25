/**
 * WordPress dependencies.
 */
import { useState, useEffect, useLayoutEffect } from '@wordpress/element';
import { __, _x } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

/**
 * External dependencies.
 */
import { SearchListControl } from '@barn2plugins/components';
import { useMultipleAdminNotifications } from '@barn2plugins/react-helpers';
import { useDebouncedValue } from 'rooks';

/**
 * Searchable user role multi-select control.
 *
 * @param {Object}   props
 * @param {Array}    props.prefilled
 * @param {Function} props.onChange
 */
const UserSelect = ( { prefilled, onChange = () => {} } ) => {
	const { setNotification } = useMultipleAdminNotifications();

	// Holds the current search results.
	const [ userRoles, setUserRoles ] = useState( [] );

	// Holds the list of selected user roles.
	const [ selectedUserRoles, setSelectedUserRoles ] = useState( [] );

	// Internal pending status of the component.
	const [ isPending, setPending ] = useState( false );

	// Track the search term.
	const [ searchTerm, setSearchTerm ] = useState( '' );

	// Track a debounced search term.
	const [ debouncedSearchTerm ] = useDebouncedValue( searchTerm, 500 );

	const userRoleMessages = {
		clear: __( 'Clear all selected user roles', 'woocommerce-product-options' ),
		noItems: __( 'No user roles found', 'woocommerce-product-options' ),
		/* translators: %s: user role select search query */
		noResults: _x( 'No results for %s', 'user roles', 'woocommerce-product-options' ),
		search: __( 'Search for user roles', 'woocommerce-product-options' ),
		selected: __( 'Selected user roles', 'woocommerce-product-options' ),
		placeholder: __( 'Search for user roles', 'woocommerce-product-options' ),
	};

	/**
	 * On component mount, fetch prefilled user roles if provided.
	 */
	useLayoutEffect( () => {
		if ( ! prefilled || ! Array.isArray( prefilled ) || prefilled.length < 1 ) {
			return;
		}

		const fetchUserRoles = async () => {
			setPending( true );

			// Fetch all user roles
			const wpUserRoles = await apiFetch( {
				path: `/wc-product-options/v1/groups/user-roles/`,
			} ).catch( () => {
				setNotification(
					'error',
					__( 'There was a problem fetching user roles.', 'woocommerce-product-options' )
				);
			} );

			// Filter to get only the prefilled ones
			const allRoles = wpUserRoles ?? [];
			const prefilledRoles = allRoles.filter( ( role ) => prefilled.includes( role.id ) );
			setSelectedUserRoles( prefilledRoles );

			setPending( false );
		};

		fetchUserRoles();
	}, [ prefilled ] );

	/**
	 * Fire onChange event when the selected user roles change.
	 */
	useEffect( () => {
		const roleIds = selectedUserRoles.map( ( role ) => role.id );
		onChange( roleIds );
	}, [ selectedUserRoles ] );

	/**
	 * When the debounced search term changes,
	 * trigger api request.
	 */
	useEffect( () => {
		if ( debouncedSearchTerm.length !== 0 ) {
			searchUserRoles();
		}
	}, [ debouncedSearchTerm ] );

	/**
	 * Search user roles from the API.
	 *
	 * @return {Array} wpUserRoles
	 */
	const searchUserRoles = async () => {
		setPending( true );

		const searchParams = new URLSearchParams( {
			search: searchTerm,
		} );

		const wpUserRoles = await apiFetch( {
			path: `/wc-product-options/v1/groups/user-roles/?${ searchParams.toString() }`,
		} ).catch( () => {
			setNotification(
				'error',
				__( 'There was a problem fetching user roles.', 'woocommerce-product-options' )
			);
		} );

		setUserRoles( wpUserRoles ?? [] );
		setPending( false );
	};

	return (
		<SearchListControl
			onChange={ ( changedValue ) => setSelectedUserRoles( changedValue ) }
			isLoading={ isPending }
			messages={ userRoleMessages }
			list={ userRoles }
			selected={ selectedUserRoles }
			onSearch={ ( searchValue ) => setSearchTerm( searchValue ) }
			didSearch={ debouncedSearchTerm.length !== 0 }
			isCompact
			searchOnly
		/>
	);
};

export default UserSelect;
