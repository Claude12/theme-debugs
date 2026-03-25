/**
 * WordPress dependencies.
 */
import { Fragment } from '@wordpress/element';
import { __, _n } from '@wordpress/i18n';

/**
 * Displays the content of the visiblity column.
 *
 * @param {Object} props
 * @param {Object} props.table
 * @param {Object} props.visibilityObjects
 * @return {Object} JSX
 */
const VisibilityCell = ( { table } ) => {
	const { visibility, products, exclude_products, categories, exclude_categories, user_roles, exclude_user_roles, visibility_objects } =
		table.row.original;
	const visibilityObjects = visibility_objects;


	/**
	 * On component mount, trigger an automated search for selected products.
	 */
	const formattedProducts = visibilityObjects?.products?.filter( ( object ) => {
		return products?.includes( object.id );
	} );

	const formattedCategories = visibilityObjects?.categories?.filter( ( object ) => {
		return categories?.includes( object.term_id );
	} );

	const formattedRoles = visibilityObjects?.user_roles?.filter( ( object ) => {
		return user_roles?.includes( object.key );
	} );

	const formattedExcludedProducts = visibilityObjects?.products?.filter( ( object ) => {
		// eslint-disable-next-line camelcase
		return exclude_products?.includes( object.id );
	} );

	const formattedExcludedCategories = visibilityObjects?.categories?.filter( ( object ) => {
		// eslint-disable-next-line camelcase
		return exclude_categories?.includes( object.term_id );
	} );

	const formattedExcludedRoles = visibilityObjects?.user_roles?.filter( ( object ) => {
		return exclude_user_roles?.includes( object.key );
	} );

	/**
	 * Get the formatted list of renderable visibilities.
	 *
	 * @return {React.ReactElement} Formatted list of products and categories
	 */
	const getItemsFormatted = () => {
		if ( visibilityObjects === null ) {
			return '';
		}

		const productCount = formattedProducts?.length ?? 0;
		const categoryCount = formattedCategories?.length ?? 0;
		const roleCount = formattedRoles?.length ?? 0;
		const excludedProductCount = formattedExcludedProducts?.length ?? 0;
		const excludedCategoryCount = formattedExcludedCategories?.length ?? 0;
		const excludedRoleCount = formattedExcludedRoles?.length ?? 0;
		const allProductCount = productCount + categoryCount + excludedProductCount + excludedCategoryCount;
		const allRoleCount = roleCount + excludedRoleCount;

		return (
			<div className="wpo-visibility-cell">
				{ ( visibility === 'global' || allProductCount === 0 ) && (
					<span className="barn2-selection-item" key={ 'all-product-list' }>
						{ __( 'All products', 'woocommerce-product-options' ) }
					</span>
				) }
				{
					<>
						{ productCount > 0 && (
							<span className="barn2-selection-item" key={ 'products-list' }>
								<strong>
									{ _n( 'Product: ', 'Products: ', productCount, 'woocommerce-product-options' ) }
								</strong>
								<span className="barn2-selection-list">
									{ formattedProducts.map( ( product, index ) => {
										return (
											<Fragment key={ product.id }>
												<a href={ product.href }>{product.name}</a>
												{ index < formattedProducts.length - 1 && <>, </> }
											</Fragment>
										);
									} ) }
								</span>
							</span>
						) }
						{ categoryCount > 0 && (
							<span className="barn2-selection-item" key={ 'category-list' }>
								<strong>
									{ _n( 'Category: ', 'Categories: ', categoryCount, 'woocommerce-product-options' ) }
								</strong>
								<span className="barn2-selection-list">
									{ formattedCategories.map( ( category, index ) => {
										return (
											<Fragment key={ category.term_id }>
												<a href={ category.href }>{category.name}</a>
												{ index < formattedCategories.length - 1 && <>, </> }
											</Fragment>
										);
									} ) }
								</span>
							</span>
						) }
						{ excludedProductCount > 0 && (
							<span className="barn2-selection-item" key={ 'excluded-products-list' }>
								<strong>
									{ _n(
										'Excluding product: ',
										'Excluding products: ',
										excludedProductCount,
										'woocommerce-product-options'
									) }
								</strong>
								<span className="barn2-selection-list">
									{ formattedExcludedProducts.map( ( product, index ) => {
										return (
											<Fragment key={ product.id }>
												<a href={ product.href }>{product.name}</a>
												{ index < formattedExcludedProducts.length - 1 && <>, </> }
											</Fragment>
										);
									} ) }
								</span>
							</span>
						) }
						{ excludedCategoryCount > 0 && (
							<span className="barn2-selection-item" key={ 'excluded-category-list' }>
								<strong>
									{ _n(
										'Excluding category: ',
										'Excluding categories: ',
										excludedCategoryCount,
										'woocommerce-product-options'
									) }
								</strong>
								<span className="barn2-selection-list">
									{ formattedExcludedCategories.map( ( category, index ) => {
										return (
											<Fragment key={ category.term_id }>
												<a href={ category.href }>{category.name}</a>
												{ index < formattedExcludedCategories.length - 1 && <>, </> }
											</Fragment>
										);
									} ) }
								</span>
							</span>
						) }
					</>
				}
				{ ( visibility === 'global' || allRoleCount === 0 ) && (
					<span className="barn2-selection-item" key={ 'all-role-list' }>
						{ __( 'All user roles', 'woocommerce-product-options' ) }
					</span>
				) }
				{ roleCount > 0 && (
					<span className="barn2-selection-item" key={ 'roles-list' }>
						<strong>
							{ _n( 'User role: ', 'User roles: ', roleCount, 'woocommerce-product-options' ) }
						</strong>
						<span className="barn2-selection-list">
							{ formattedRoles.map( ( role, index ) => {
								return (
									<Fragment key={ `role-${ role.key }-${ index }` }>
										{ role.name }
										{ index < formattedRoles.length - 1 && <>, </> }
									</Fragment>
								);
							} ) }
						</span>
					</span>
				) }
				{ excludedRoleCount > 0 && (
					<span className="barn2-selection-item" key={ 'excluded-role-list' }>
						<strong>
							{ _n( 'User role: ', 'User roles: ', excludedRoleCount, 'woocommerce-product-options' ) }
						</strong>
						<span className="barn2-selection-list">
							{ formattedExcludedRoles.map( ( role, index ) => {
								return (
									<Fragment key={ `role-${ role.key }-${ index }` }>
										{ role.name }
										{ index < formattedExcludedRoles.length - 1 && <>, </> }
									</Fragment>
								);
							} ) }
						</span>
					</span>
				) }

			</div>
		);
	};

	return <>{ getItemsFormatted() }</>;
};

export default VisibilityCell;
