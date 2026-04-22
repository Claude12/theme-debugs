jQuery(document).ready(function () {
	"use strict";
	var ajaxUrl  = afrfq_phpvars.admin_url;
	var nonce    = afrfq_phpvars.nonce;
	var redirect = afrfq_phpvars.redirect;
	var pageurl  = afrfq_phpvars.pageurl;


	jQuery('#loader-wrapper').hide();

	jQuery('.adf-multi-select').select2();
	jQuery('.adf-rqst-select').select2();

	jQuery(document).on('click', '.afrfq-proceed-btn', function(){
        window.location.href = jQuery(this).data('url');
    });
	
	
	jQuery('div.menu ul').append( '<li>' + jQuery('li.quote-li a:eq(1)').text() + '</li>' );

	jQuery(document).on( 'change', '.variation_id', function (e) {

		jQuery(this).closest('form').find('button.afrfq_single_page_atc').remove();
		jQuery( '.afrfqbt_single_page' ).addClass( 'disabled wc-variation-is-unavailable' );
		jQuery( '.afrfqbt_single_page' ).show();

		if ( !jQuery(this).val() ) {
			return;
		}

		var variation_id   = parseInt( jQuery(this).val() );
		var current_button = jQuery(this);

		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action       : 'check_availability_of_quote',
				nonce        : nonce,
				variation_id : variation_id,
			},
			success: function ( response ) {

				if ( 'disabled' == response['display'] ) {
					jQuery( '.afrfqbt_single_page' ).addClass( 'disabled wc-variation-is-unavailable' );
					jQuery( '.afrfqbt_single_page' ).show();

					if ( jQuery('button.single_add_to_cart_button').length < 1 ) {
						jQuery( '.afrfqbt_single_page' ).before( response['button'] );
					}

				} else if ( 'disabled_swap' == response['display'] ) {
					jQuery( '.afrfqbt_single_page' ).addClass( 'disabled wc-variation-is-unavailable' );
					jQuery( '.afrfqbt_single_page' ).show();
					if ( jQuery('button.single_add_to_cart_button').length < 1  ) {
						jQuery( '.afrfqbt_single_page' ).before( response['button'] );
					}
				} else if ( 'hide' == response['display'] ) {
					jQuery( '.afrfqbt_single_page' ).addClass( 'disabled wc-variation-is-unavailable' );
					jQuery( '.afrfqbt_single_page' ).hide();
					if ( jQuery('button.single_add_to_cart_button').length < 1  ) {
						jQuery( '.afrfqbt_single_page' ).before( response['button'] );
					}
				} else if ( 'hide_swap' == response['display'] ) {
					jQuery( '.afrfqbt_single_page' ).addClass( 'disabled wc-variation-is-unavailable' );
					jQuery( '.afrfqbt_single_page' ).hide();

					if ( jQuery('button.single_add_to_cart_button').length < 1 ) {
						jQuery( '.afrfqbt_single_page' ).before( response['button'] );
					}
				} else {
					jQuery( '.afrfqbt_single_page' ).removeClass( 'disabled' );
					jQuery( '.afrfqbt_single_page' ).show();
					if ( jQuery('button.single_add_to_cart_button').length < 1  ) {
						jQuery( '.afrfqbt_single_page' ).before( response['button'] );
					}
				}
			},
			error: function (response) {
				current_button.removeClass('loading');
				current_button.css('opacity', '1' );
				current_button.css('border', '1px solid red' );
			}
		});

	});
	
	jQuery('.addify_converty_to_order_button button').click( function (e) {
		jQuery(this).addClass('loading');
		jQuery('table.quote_details').css( 'opacity', '0.67' );
	});

	jQuery('div.af_quote_fields input:not([type="submit"]), div.af_quote_fields textarea, div.af_quote_fields select').each( function(){

		var current_button = jQuery(this);

		if ( !current_button.val() || current_button.val().length < 1 ) {

			if ( 'required' === current_button.attr('required')  ) {
				current_button.css('border-left', '2px solid #ca1010');
			}
		} else {
			current_button.css('border-left', '2px solid green');
		}
	});

	jQuery( document ).on( 'focusout', 'div.af_quote_fields input, div.af_quote_fields textarea, div.af_quote_fields select', function(ev) {
				
		var current_button = jQuery(this);
		if ( !current_button.val() || current_button.val().length < 1 ) {
			if ( 'required' === current_button.attr('required')  ) {
				current_button.css('border-left', '2px solid #ca1010');
			}
			return;
		}
		
		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action   : 'cache_quote_fields',
				nonce    : nonce,
				form_data : jQuery(this).closest('form').serialize(),
			},
			success: function (response) {
				current_button.css('border-left', '2px solid green');
			},
			error: function (response) {
				current_button.css('border-left', '2px solid #ca1010');
			}
		});
	});

	jQuery(document).on('change', 'div.af_quote_fields input[type="checkbox"], div.af_quote_fields select[multiple]', function(ev) {
		
		var current_button = jQuery(this);
		if ( !current_button.val() || current_button.val().length < 1 ) {
			if ( 'required' === current_button.attr('required')  ) {
				current_button.css('border-left', '2px solid #ca1010');
			}
			return;
		}		

		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action   : 'cache_quote_fields',
				nonce    : nonce,
				form_data : jQuery(this).closest('form').serialize(),
			},
			success: function (response) {
				current_button.css('border-left', '2px solid green');
			},
			error: function (response) {
				current_button.css('border-left', '2px solid #ca1010');
			}
		});
	});

	(function ($) {
		let ajaxRequest = null; 
		let debounceTimer = null;
	
		$(document).on('click', 'button.afrfq_update_quote_btn', function (e) {
			e.preventDefault();
	
			const current_button = $(this);
			const form_type = $('form.addify-quote-form').data('form-type');
			
			var form_data = $(this).closest('form.addify-quote-form').serialize();
				
			current_button.addClass('loading');
	
			clearTimeout(debounceTimer);
	
			debounceTimer = setTimeout(function () {

				if (ajaxRequest && ajaxRequest.readyState !== 4) {
					ajaxRequest.abort();
				}
	
				ajaxRequest = $.ajax({
					url: ajaxUrl,
					type: 'POST',
					dataType: 'JSON',
					data: {
						action: 'update_quote_items',
						nonce: nonce,
						form_data: form_data,
						quote_id: current_button.data('quote_id'),
						form_type: form_type
					},
	
					success: function (response) {
						current_button.removeClass('loading').addClass('disabled');
						$('.error, .woocommerce-error').remove();
	
						if (response['quote_empty']) {
							location.reload();
							return;
						}
	
						$('div.woocommerce-notices-wrapper').html(response['message']);
						$('table.addify-quote-form__contents').replaceWith(response['quote-table']);
						$('table.table_quote_totals').replaceWith(response['quote-totals']);
						$('li.quote-li').replaceWith(response['mini-quote']);
	
						$('body').animate({
							scrollTop: $('div.woocommerce-notices-wrapper').offset().top
						}, 500);
	
						$('.error, .woocommerce-error').remove();
					},
	
					error: function (xhr, status) {
						if (status !== 'abort') {
							console.error('Quote update failed:', status);
						}
						current_button.removeClass('loading').addClass('disabled');
					}
				});
			}, 200); 
		});
	})(jQuery);

	jQuery(document).on('click', '.afrfqbt', function () {				

		jQuery(this).closest('li').find('a.added_to_quote').remove();
		var redirect = afrfq_phpvars.redirect;


		if (jQuery(this).is('.product_type_simple')) {

			var productId = jQuery(this).attr('data-product_id');
			var ruleId = jQuery(this).attr('data-rule_id');
			var quantity  = 1;

			jQuery(this).addClass('loading');
			var current_button = jQuery(this);
			jQuery.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					action: 'add_to_quote',
					product_id: productId,
					rule_id:ruleId,
					quantity: quantity,
					nonce: nonce
				},
				success: function (response) {
					if(response['price_calculator_product']){
						window.location.href = response['redirect_to'];						
						return;
					}

					if (response['popup_is_enabled'] === 'yes') {
						jQuery(".afrfq-quote-popup-content").load(location.href + " .afrfq-quote-popup-content > *",function(){
							jQuery('.adf-multi-select').select2();
							//render captcha in popup
							renderAfrfqCaptchaForPopup();
						});
						
						jQuery(".afrfq-quote-popup-modal").css("display", "flex");
						
					 }else{
						jQuery(".afrfq-quote-popup-modal").css("display", "none");
					 }

					if ( 'success' == jQuery.trim(response) ) {
						if ( "yes" == redirect ) {
							window.location.href = pageurl;
						} else {

							window.location = location.href;
						}
						
					} else if ( 'failed' == jQuery.trim(response) ) {

						window.location = location.href;
						
					} else {

						current_button.removeClass('loading');
						current_button.after( response['view_button'] );
						jQuery('.quote-li').replaceWith(response['mini-quote']);

					}	
				}
			});

		}
		return false;
	});

});


function renderAfrfqCaptchaForPopup() {
    const captchaEl = jQuery('#afrfq-recaptcha');

    if (!captchaEl.is(':visible')) {
        setTimeout(renderAfrfqCaptchaForPopup, 1000);
        return;
    }

    // If already rendered, don't re-render
    if (captchaEl.find('.g-recaptcha').length > 0) {
        return;
    }

    const freshEl = jQuery('<div class="g-recaptcha"></div>')
        .attr('data-sitekey', captchaEl.data('sitekey'));

    captchaEl.append(freshEl);

    try {
        grecaptcha.render(freshEl.get(0), {
            sitekey: freshEl.data('sitekey')
        });
    } catch (e) {
        console.error("Failed to render reCAPTCHA", e);
    }
}


jQuery(document).ready(function () {

	"use strict";
	var ajaxUrl  = afrfq_phpvars.admin_url;
	var nonce    = afrfq_phpvars.nonce;
	var redirect = afrfq_phpvars.redirect;
	var pageurl  = afrfq_phpvars.pageurl;
	var required = false;

	jQuery(document).on('click','.afrfqbt_single_page', function ($) {				
		
		var current_button = jQuery(this);

		jQuery(this).closest('form').find('a.added_to_quote').remove();

		if(jQuery(this).data('button_type') && 'custom' == jQuery(this).data('button_type')){
			jQuery('a.added_to_quote').remove();
		}

		if (jQuery(this).is('.product_type_variable')) {

			if ( jQuery(this).hasClass('disabled') ) {
				return;
			}

			jQuery(this).addClass('loading');

			jQuery.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					action: 'add_to_quote_single_vari',
					form_data: jQuery(this).closest('form').serialize(),
					product_id: jQuery(this).data('product_id'),
					rule_id: jQuery(this).data('rule_id'),
					popup_enabled: jQuery(this).data('popup_enabled'),
					button_type: jQuery(this).data('button_type'),
					product_quantity:jQuery(this).data('product_quantity'),
					parent_product_id:jQuery(this).data('parent_product_id'),
					nonce: nonce
				},
				success: function (response) {

					
					if (response['popup_is_enabled'] === 'yes') {
						jQuery(".afrfq-quote-popup-content").load(location.href + " .afrfq-quote-popup-content > *",function(){
							jQuery('.adf-multi-select').select2();
							//render captcha in popup
							renderAfrfqCaptchaForPopup();
						});

						jQuery(".afrfq-quote-popup-modal").css("display", "flex");

						
						
					 }else{
						jQuery(".afrfq-quote-popup-modal").css("display", "none");
					 }

					if ('success' == jQuery.trim(response)) {
						if ("yes" == redirect) {

							window.location.href = pageurl;
						} else {

							window.location = location.href; 
						}
					} else if ( 'failed' == jQuery.trim(response) ) {

						window.location = location.href;
						
					} else {

						current_button.removeClass('loading');
						if (response['popup_is_enabled'] !== 'yes') {
							current_button.after( response['view_button'] );
						}
						jQuery('.quote-li').replaceWith(response['mini-quote']);
					}
				}
			});

		} else {
			
			var productId = jQuery(this).attr('data-product_id');
			var quantity  = jQuery('.qty').val();				

			jQuery(this).addClass('loading');

			jQuery.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					action: 'add_to_quote_single',
					form_data : jQuery(this).closest('form').serialize(),
					product_id: jQuery(this).data('product_id'),
					rule_id: jQuery(this).data('rule_id'),
					popup_enabled: jQuery(this).data('popup_enabled'),
					button_type: jQuery(this).data('button_type'),
					product_quantity:jQuery(this).data('product_quantity'),
					nonce: nonce
				},
				success: function (response) {				


					if (response['popup_is_enabled'] === 'yes') {
						jQuery(".afrfq-quote-popup-content").load(location.href + " .afrfq-quote-popup-content > *",function(){
							jQuery('.adf-multi-select').select2();
							//render captcha in popup
							renderAfrfqCaptchaForPopup();
						});

						jQuery(".afrfq-quote-popup-modal").css("display", "flex");

						
					 }else{
						jQuery(".afrfq-quote-popup-modal").css("display", "none");
					 }

					if ( 'success' == jQuery.trim(response) ) {
						if ( "yes" == redirect ) {
							window.location.href = pageurl;
						} else {							
							window.location = location.href;
						}
						
					} else if ( 'failed' == jQuery.trim(response) ) {
						window.location = location.href;	
					} else {
						current_button.removeClass('loading');
						if(response['popup_is_enabled'] !== 'yes'){
							current_button.after( response['view_button'] );
						}
						jQuery('.quote-li').replaceWith(response['mini-quote']);
					}
				}
			});

		}
		return false;
	});

});

// remove from quote
jQuery(document).on('click', '.remove-quote-item', function (e) {
	"use strict";
	e.preventDefault();
	var quoteKey = jQuery(this).data('cart_item_key');
	var ajaxUrl  = afrfq_phpvars.admin_url;
	var nonce    = afrfq_phpvars.nonce;
	
	jQuery(this).closest('tr').css('opacity', '0.5' );

	const form_type = jQuery('form.addify-quote-form').data('form-type');

	jQuery.ajax({
		url: ajaxUrl,
		type: 'POST',

		data: {
			action: 'remove_quote_item',
			quote_key: jQuery(this).data('cart_item_key'),
			nonce: nonce,
			form_type: form_type,
		},
		success: function (response) {

			if ( response['quote_empty'] ) {
				location.reload();
			}

			jQuery('div.woocommerce-notices-wrapper').html(response['message'] );
			jQuery('table.addify-quote-form__contents').replaceWith( response['quote-table'] );
			jQuery('table.table_quote_totals').replaceWith( response['quote-totals'] );
			jQuery('li.quote-li').replaceWith( response['mini-quote'] );
			jQuery('body').animate({
				scrollTop: jQuery('div.woocommerce-notices-wrapper').offset().top,
				}, 500
			);
		}
	});
});

// removal from mini quote
jQuery(document).on('click', '.quote-remove', function (event) {
	"use strict";
	event.preventDefault();
	var quoteKey = jQuery(this).data('cart_item_key');
	var ajaxUrl  = afrfq_phpvars.admin_url;
	var nonce    = afrfq_phpvars.nonce;
	
	jQuery(this).closest('li.mini_quote_item').css('opacity', '0.5' );

	jQuery.ajax({
		url: ajaxUrl,
		type: 'POST',
		data: {
			action: 'remove_quote_item',
			quote_key: jQuery(this).data('cart_item_key'),
			nonce: nonce
		},
		success: function (response) {

			jQuery('div.woocommerce-notices-wrapper').html(response['message'] );
			jQuery('table.addify-quote-form__contents').replaceWith( response['quote-table'] );
			jQuery('table.table_quote_totals').replaceWith( response['quote-totals'] );
			jQuery('li.quote-li').replaceWith( response['mini-quote'] );
		}
	});

	// Check if the WooCommerce shop block exists

});

// Update quantity
jQuery(document).on('change', 
  '.addify-quote-form__contents .product-quantity .qty, .offered-price .offered-price-input', 
  function (e) {
    "use strict";
    e.preventDefault();
    
    var quoteKey = jQuery(this).val(); // yeh current input ka value leta hai.
    var updateButton = jQuery(this).closest('.addify-quote-form').find('.afrfq_update_quote_btn'); // update button select karta hai.

    
      updateButton.trigger('click'); // 5 seconds baad update button par click trigger karta hai.
     
});

jQuery(document).ready(function($){
	setTimeout(function(){
		if ($('.wp-block-woocommerce-product-template').length || $('.wp-block-group.woocommerce.product ').length) {
			 $('.afrfqbt.button').each(function() {
	            $(this).parent('li').css('text-align', 'center');
	        });
	        $('.afrfqbt.button').addClass('wp-block-button__link wp-element-button wc-block-components-product-button__button has-font-size has-small-font-size has-text-align-center wc-interactive');
	    }
	    if ($('.woocommerce .wp-block-column').length) {
	        $('.afrfqbt_single_page.button').addClass('wp-element-button');
	        $('.afrfqbt_single_page.button').css({'float': 'none', 'margin-bottom': '10px', 'display': 'inline-block'});
	    }
	}, 2000);


	//quote popup setting
	$(document).on('click','.afrfq-close-popup,.afrfq-continue-button',function(){
		$(".afrfq-quote-popup-modal").hide();
	})

	function popup_checkout_place_quote(formData,popupTemplate,currentPage=''){

		formData.append('action', 'afrfq_submit_quote_via_popup');
		formData.append('popup_template',popupTemplate);
		formData.append('current_page',currentPage);


		var ajaxUrl  = afrfq_phpvars.admin_url;

			return new Promise((resolve, reject) => {
				jQuery.ajax({
					url: ajaxUrl,
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function(response) {
						$('.addify_popup_checkout_place_quote').removeClass('loading');
						$('.addify_popup_checkout_place_quote').removeClass('disabled');
						if (response.status === 'fail') {
							var noticeContainer = $('#popup-notice-container');
							noticeContainer.html(response.notices_html).show();
							$('.afrfq-quote-popup-content').animate({ scrollTop: 0 }, 'slow');
							// $('.afrfq-product-selection-section').animate({ scrollTop: 0 }, 'slow');
							
							resolve(false); 
						} else if (response.status === 'pass') {
							if('template_two' == popupTemplate && '1' == currentPage){
								resolve(true); 
							}
							else if('template_two' == popupTemplate && '2' == currentPage){
								$('.popup-form-template_two').append(response.data);
								$('.af_quote_fields_template_two').toggle();
							}
							else{
								$('#popup-notice-container').hide();
								$('.afrfq-quote-inner-content').remove();
								$('.afrfq-popup-form-inner').remove();
								$('.afrfq-quote-popup-content').append(response.data);
							}
							resolve(true); 
							
							
						}
					},
					error: function(xhr, status, error) {
						console.error("Error submitting form", error);
						resolve(false); 
					}
				});
			});
		

		
	}

	//handling quote submission via ajax
	jQuery(document).on('click', '.addify_popup_checkout_place_quote', function(event) {
		event.preventDefault();
		
		var form = jQuery(this).closest('form')[0];
		
		if (form.checkValidity()) {
			var formData = new FormData(form);
			$(this).addClass('loading disabled');
			popup_checkout_place_quote(formData, 'template_one');			
			if(afrfq_phpvars.redirect_to_url){
				window.location.href = afrfq_phpvars.redirect_to_url;
			}
		} else {
			
			form.reportValidity();
		}
	});

	function popup_data_saved(){
		return new Promise((resolve, reject) => {
			jQuery.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					action   : 'cache_quote_fields',
					nonce    : nonce,
					form_data : jQuery('form.popup-form-template_two').serialize(),
				},
				success: function (response) {
					resolve(true); 				
				},
				error: function (response) {
					resolve(false); 
				}
			});
		});
	}

	if($('.adf-request-quote-popup.template_two').length){
		updateFormState();
		
		jQuery(document).on('click', '.afrfq-right-button', async function(){ 
			let currentStep = parseInt($('.afrfq-popup-form-steps').attr('data-current-page')) || 1;
			if (currentStep === 1 ) {

				var form = jQuery('.af_quote_fields_template_two').closest('form')[0];
				var screenTwoFields = $('.af_quote_fields').find(':input'); // Target only screen 2 fields

				// Disable screen 2 fields before validation
				screenTwoFields.prop('disabled', true);

				if (!form.checkValidity()) {
					// updateFormState();
					form.reportValidity();
				}

				// } else {
				// }

				$(this).addClass('loading disabled'); 
				$('.afrfq-left-button').addClass('disabled');

				var formData = new FormData(form);

				const isSuccess = await popup_checkout_place_quote(formData,'template_two','1');
				
				if (isSuccess) {
					$('.afrfq-popup-form-steps').attr('data-current-page', currentStep + 1);
					$(this).removeClass('loading disabled'); 
					$('.afrfq-left-button').removeClass('disabled');            
					updateFormState();
				}
				else{
					$(this).removeClass('loading disabled'); 
					$('.afrfq-left-button').removeClass('disabled');  
					$('.afrfq-product-selection-section').animate({ scrollTop: 0 }, 'slow');
				}

				
				// Re-enable screen 2 fields after validation
				screenTwoFields.prop('disabled', false);

			}
			else if (currentStep === 2) {
				
				var form = jQuery('.af_quote_fields_template_two').closest('form')[0];

				jQuery('#popup-notice-container').hide();


				if (form.checkValidity()) {
					var formData = new FormData(form);
					$(this).addClass('loading disabled'); 
					$('.afrfq-left-button').addClass('disabled');  
					
					const isDataSaved = await popup_data_saved();
					const isSuccess = await popup_checkout_place_quote(formData,'template_two','2');
				
					if (isSuccess) {
						$('.afrfq-popup-form-steps').attr('data-current-page', currentStep + 1);
						$(this).removeClass('loading disabled'); 
						$('.afrfq-left-button').removeClass('disabled');            
						updateFormState();
					}
					else{
						$(this).removeClass('loading disabled'); 
						$('.afrfq-left-button').removeClass('disabled');  
						$('.afrfq-product-selection-section').animate({ scrollTop: 0 }, 'slow');
					}

				} else {
					form.reportValidity();
				}
				
			} 
			else if (currentStep === 3) {					
				var form = jQuery('.af_quote_fields_template_two').closest('form')[0];
				var formData = new FormData(form);
				$(this).addClass('loading disabled'); 
				popup_checkout_place_quote(formData,'template_two');
				if(''!= afrfq_phpvars.redirect_to_url){
					window.location.href = afrfq_phpvars.redirect_to_url;
				}
				
			}
		});
		
		jQuery(document).on('click', '.afrfq-left-button', function(){
			let currentStep = parseInt($('.afrfq-popup-form-steps').attr('data-current-page')) || 1;
			if (currentStep > 1) {
				$('.afrfq-popup-form-steps').attr('data-current-page', currentStep - 1);
				updateFormState();
			} else {
				$(".afrfq-quote-popup-modal").hide();
			}
		});
		
		function updateFormState() {

			if (!$('.adf-multi-select').hasClass('select2-hidden-accessible')) {
				$('.adf-multi-select').select2();
			}

			let currentStep = parseInt($('.afrfq-popup-form-steps').attr('data-current-page')) || 1;
			
			currentStep = Math.max(1, Math.min(3, currentStep));
			
			$('.afrfq-popup-form-step').removeAttr('data-active');
			$(`.afrfq-popup-form-step:nth-child(${currentStep})`).attr('data-active', 'true');
			
			$('.afrfq-left-button').text(currentStep === 1 ? 'Continue Shopping' : 'Previous step');
			
			$('.afrfq-right-button').text(currentStep === 3 ? 'Submit Request' : 'Next step');
			
			$('.adf-quote-detail-wrap').toggle(currentStep === 1);
			$('.af_quote_fields_template_two').toggle(currentStep == 2 );

			if(currentStep == 1 || currentStep == 2){
				$('.afrfq-popup-review-info-page').remove();
			}
		}
	}

	// Remove remove links in blocks cart in case of quote to cart conversion
	if ( window.wc && window.wc.blocksCheckout ) {
		const { registerCheckoutFilters } = window.wc.blocksCheckout;

		const modifyShowRemoveItemLink = ( defaultValue, extensions, args ) => {
			const isCartContext = args?.context === 'cart';

			if ( ! isCartContext ) {
				return defaultValue;
			}
			if ( args.cart?.extensions?.addify_rfq?.quote_conversion ) {
				return false;
			}
			return defaultValue;
		};

		registerCheckoutFilters( 'afrfq-remove-item-link-extension', {
			showRemoveItemLink: modifyShowRemoveItemLink,
		} );
	}

	//for checkout blocks -- Comapring email at checkout for guest users
	setTimeout(function() {
		const placeOrderBtn = document.querySelector('.wc-block-components-checkout-place-order-button');
		
		if (placeOrderBtn) {
			placeOrderBtn.addEventListener('click', function(e) {
				// Get the email from the form
				const emailInput = document.getElementById('email');
				const enteredEmail = emailInput.value;
				
				// Check if the entered email matches the quote email
				if (afrfq_phpvars.quote_to_cart_data && 
					afrfq_phpvars.quote_to_cart_data.quote_email &&
					enteredEmail !== afrfq_phpvars.quote_to_cart_data.quote_email) {
					
					// Show error message
					addErrorNotice(afrfq_phpvars.quote_to_cart_data.quote_message);
					window.scrollTo({ top: 0, behavior: 'smooth' });
					
					e.preventDefault();
					e.stopPropagation();
					return false;
				}
				
				// If emails match, allow form submission
				return true;
			});
		}
	}, 1000);

	const ajaxUrl  = afrfq_phpvars.admin_url;
	const nonce    = afrfq_phpvars.nonce;

	jQuery(document).on('click', 'button[name="afrfq_clear_cart"]', function() {
		$(this).addClass('loading');
		jQuery.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action     : 'afrfq_clear_cart',
				nonce      : nonce,
			},
			success: function (response) {
				if(response.success){
					location.reload();
				}
			},
			error: function (response) {
	
				// console.log( response );
				
			}
		});
	});

	

	function addErrorNotice(message){
		jQuery('#guest-user-email-notice').remove();
		jQuery('#main').before(
			'<div class="wc-block-components-notice-banner is-error" id="guest-user-email-notice" role="alert">' +
				'<div class="wc-block-components-notice-banner__content">' +
				message +
				'</div></div>'
		);
	}
	

	
	

});