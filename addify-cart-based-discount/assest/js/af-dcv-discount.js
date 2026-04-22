/**
 * Discount cart js.
 *
 * @package Discount Cart By Total Value.
 */

var ajaxurl = php_vars.admin_url;
var nonce   = php_vars.nonce;
var html    = php_vars.tr_html;

jQuery( document ).ready(
	function($){

		// for repeater fields.
		jQuery( document ).on(
			'click',
			'.af-add-row',
			function() {

				$( '.af_dc_discount_add_row' ).append( html );
			}
		);

		jQuery( document ).on(
			'click',
			'.af-close-row',
			function() {

				$( this ).closest( 'tr' ).remove();
			}
		);

		// Message field hide.
		af_message_check();

		$( document ).on(
			'click',
			'.af_dcv_check',
			function(){
				af_message_check();
			}
		);
		function af_message_check() {

			if ( $( '.af_dcv_check' ).is( ':checked' )) {
				jQuery( '.af_dcv_discount_message' ).show();
			} else {
				jQuery( '.af_dcv_discount_message' ).hide();
			}
		}

		// general settiing message field hide.
		af_dcv_discount_gen_sett_message();

		$( document ).on(
			'click',
			'.af_dcv_coupons_enable_class',
			function(){
				af_dcv_discount_gen_sett_message();
			}
		);

		function af_dcv_discount_gen_sett_message() {

			if ( $( '.af_dcv_coupons_enable_class' ).is( ':checked' )) {
				jQuery( '.af_dcv_couple_message_disable_class' ).show();
			} else {
				jQuery( '.af_dcv_couple_message_disable_class' ).hide();
			}
		}

		// Hide discount type.
		af_discout_select_hide();

		$( document ).on(
			'click',
			'.af_dcv_discount_product_cart',
			function(){
				af_discout_select_hide();
			}
		);

		function af_discout_select_hide() {

			var selectedValue = $( '.af_dcv_discount_product_cart' ).val();

			if ( selectedValue == 'product_discount') {
				jQuery( '.af_dcv_products_method_field' ).show();

			} else {
				jQuery( '.af_dcv_products_method_field' ).hide();
			}
		}

		// Hide product method type.
		af_discout_select_hide_show_products();

		$( document ).on(
			'click',
			'.af_dcv_discount_product_methods',
			function(){
				af_discout_select_hide_show_products();
			}
		);

		function af_discout_select_hide_show_products() {

			var selectedValue = $( '.af_dcv_discount_product_methods' ).val();

			if ( selectedValue == 'specific_product') {
				jQuery( '.af_dcv_products_hide' ).show();
				jQuery( '.af_dcv_discount_by_total_price' ).show();

			} else {
				jQuery( '.af_dcv_products_hide' ).hide();
				jQuery( '.af_dcv_discount_by_total_price' ).hide();

			}
		}

		// Hide product total/price.

		af_discout_select_hide_total_price();

		$( document ).on(
			'click',
			'.af_dcv_discount_product_cart',
			function(){
				af_discout_select_hide_total_price();
			}
		);

		function af_discout_select_hide_total_price() {

			var selectedValue = $( '.af_dcv_discount_product_cart' ).val();

			if ( selectedValue == 'cart_discount') {
				jQuery( '.af_dcv_discount_by_total_price' ).hide();

			}
		}

		jQuery( '.af_discount_user_select_live_search' ).select2();
		jQuery( '.af_dcv_discount_tag_select_live_search' ).select2(
			{
				placeholder: 'Choose Tag',
			}
		);

		jQuery( '.af_dcv_discount_product_list' ).select2(
			{
				ajax: {
					url: ajaxurl, // AJAX URL is predefined in WordPress admin.
					dataType: 'json',
					type: 'POST',
					delay: 20, // Delay in ms while typing when to perform a AJAX search.
					data: function (params) {
						return {
							q: params.term, // search query!
							action: 'af_dcv_discount_get_products', // AJAX action for admin-ajax.php.//aftaxsearchUsers(is function name which isused in adminn file).
							nonce: nonce // AJAX nonce for admin-ajax.php.
						};
					},
					processResults: function ( data ) {
						var options = [];
						if (data ) {
							// data is the array of arrays, and each of them contains ID and the Label of the option!
							$.each(
								data,
								function ( index, text ) {
									// do not forget that "index" is just auto incremented value.
									options.push( { id: text[0], text: text[1]  } );
								}
							);
						}
						return {
							results: options
						};
					},
					cache: true
				},
				multiple: true,
				placeholder: 'Choose Products',
				minimumInputLength: 3 // the minimum of symbols to input before perform a search.
				}
		);
		jQuery( '.af_dcv_discount_category_list' ).select2(
			{
				ajax: {
					url: ajaxurl, // AJAX URL is predefined in WordPress admin.
					dataType: 'json',
					type: 'POST',
					delay: 20, // Delay in ms while typing when to perform a AJAX search.
					data: function (params) {
						return {
							q: params.term, // search query!
							action: 'af_dcv_discount_get_categories', // AJAX action for admin-ajax.php.//aftaxsearchUsers(is function name which isused in adminn file).
							nonce: nonce // AJAX nonce for admin-ajax.php.
						};
					},
					processResults: function ( data ) {
						var options = [];
						if (data ) {
							// data is the array of arrays, and each of them contains ID and the Label of the option!
							$.each(
								data,
								function ( index, text ) {
									// do not forget that "index" is just auto incremented value.
									options.push( { id: text[0], text: text[1]  } );
								}
							);
						}
						return {
							results: options
						};
					},
					cache: true
				},
				multiple: true,
				placeholder: 'Choose Category',
				minimumInputLength: 3 // the minimum of symbols to input before perform a search.
				}
		);

	}
);
