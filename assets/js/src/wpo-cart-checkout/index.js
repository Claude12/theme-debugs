import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';

window.addEventListener( 'DOMContentLoaded', () => {
    __webpack_public_path__ = wpoSettings.module_path_url;

    const wpoLightbox = new PhotoSwipeLightbox( {
        gallery: '.woocommerce-page .cart_item, .woocommerce-page .order_item',
        children: 'a[href*="wpo-uploads"]',
        showHideAnimationType: 'zoom',
        showAnimationDuration: 150,
        hideAnimationDuration: 150,
        
        pswpModule: () => import(
            /*webpackChunkName: "photoswipe"*/
            'photoswipe'
        ),
    } )

    wpoLightbox.init();

    window.jQuery( document.body ).on( 'updated_checkout updated_cart_totals', () => {
        wpoLightbox.init();
    } );

	const wpoCheckoutLink = document.querySelectorAll( '.wpo-checkout-link button.copy-button' );

	wpoCheckoutLink?.forEach( button => {
		const container = button.closest('.wpo-checkout-link');
		const input = container.querySelector('.copy-url');
		const wrapper = container.querySelector('.copy-wrapper');
		const tooltip = container.querySelector('.copy-tooltip');
		button.addEventListener( 'click', () => {
			navigator.clipboard.writeText(input.value).then(() => {
				wrapper.classList.add('show-tooltip');

				setTimeout(() => {
					wrapper.classList.remove('show-tooltip');
				}, 1500);
			}).catch(() => {
				tooltip.textContent = 'Failed to copy';
				wrapper.classList.add('show-tooltip');

				setTimeout(() => {
					tooltip.textContent = 'Copied!';
					wrapper.classList.remove('show-tooltip');
				}, 2000);
			});
		});
	});

} );
