jQuery(function($) {

	"use strict";

	 $('.af-rfq-live-search').select2();
	 $('.afrfq-hide-categories').select2();
	 $('.afrfq-hide-brands').select2();
	 $('.afrfq_enable_for_specific_user_role').select2();


	$('.afrfq_upload_button').click(function (e) {
		e.preventDefault();
		var image = wp.media({
			title: 'Upload Image',
			multiple: false
		}).open().on('select', function (e) {
			var uploadedImage = image.state().get('selection').first();
			var imageURL = uploadedImage.toJSON().url;
			$('#afrfq_company_logo').val(imageURL);
			checkImageSrc();
			$('#afrfq_company_logo_preview img').attr('src', imageURL);
			$('.afrfq_remove_button').show();
		});
	});
	checkImageSrc();
 function checkImageSrc() {
      var imageURL = $('#afrfq_company_logo').val();
      if (imageURL) {
          $('#afrfq_company_logo_preview img').attr('src', imageURL).show();
          $('.afrfq_remove_button').show();
      } else {
          $('#afrfq_company_logo_preview img').hide();
          $('.afrfq_remove_button').hide();
      }
  }

 $('.afrfq_remove_button').click(function (e) {
      e.preventDefault();
      $('#afrfq_company_logo').val('');
      $('#afrfq_company_logo_preview img').attr('src', '').hide();
      $(this).hide();
  });

	if ( jQuery('input#afrfq_redirect_after_submission').is(':checked') ) {
		jQuery('input#afrfq_redirect_url').closest('tr').show();
	} else {
		jQuery('input#afrfq_redirect_url').closest('tr').hide();
	}

	jQuery('input#afrfq_redirect_after_submission').change(function(){

		if ( jQuery(this).is(':checked') ) {
			jQuery('input#afrfq_redirect_url').closest('tr').show();
		} else {
			jQuery('input#afrfq_redirect_url').closest('tr').hide();
		}

	});
	
	var ajaxurl = afrfq_php_vars.admin_url;
	var nonce   = afrfq_php_vars.nonce;

	$('.multi-select').select2({
	});

	$(document).on('click', 'a.delete-quote-item', function(event){
		event.preventDefault();
		var current_button = $(this);
		$(this).closest('tr').css('opacity', '0.4');
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action     : 'afrfq_delete_quote_item',
				nonce      : nonce,
				quote_key  : current_button.data( 'quote_item_id' ),
				post_id    : $('input#post_ID').val(),
			},
			success: function (response) {
				// response = JSON.parse( response );
				$('#addify_quote_items_container').replaceWith( response['quote-details-table'] );
				
			},
			error: function (response) {
				jQuery(this).removeClass('loading');
				
			}
		});
	});

	$(document).on('click', '.add_option_button', function(event){
		event.preventDefault();

		var html = '<div class="option_row"><input type="text" name="afrfq_field_options[]" value=""><span type="button" title="Add Option" id="afrfq_field_add_option" class="dashicons dashicons-plus-alt2 add_option_button"></span><span type="button" title="Remove Option" class="dashicons dashicons-no-alt remove_option_button"></span></div>';
		$( html ).insertAfter( $(this).closest('div.option_row') );
	});

	$(document).on('click', '.remove_option_button', function(event){
		event.preventDefault();

		if ( $(document).find( 'div.option_row' ).length > 1 ) {
			$(this).closest( 'div.option_row').remove();
		}
		
	});
	
	$(document).ready( function(event) {
		var value = $('select[name="afrfq_field_type"]').val();
		$('select[name="afrfq_field_value"]').closest('tr').show();
		$('input[name="afrfq_field_placeholder"]').closest('tr').show();

		if ( 'select' == value || 'multiselect' == value || 'radio' == value || 'checkbox' == value ) {
			$('tr.options-field').show();
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();		
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
		} else if ( 'file' == value ) {
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
			$('input[name="afrfq_file_types"]').closest('tr').show();
			$('input[name="afrfq_file_size"]').closest('tr').show();
			$('tr.options-field').hide();

		} else if ( 'terms_cond' == value ) {
			$('textarea[name="afrfq_field_terms"]').closest('tr').show();
			$('tr.options-field').hide();
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();
			$('select[name="afrfq_field_value"]').closest('tr').hide();
			$('input[name="afrfq_field_placeholder"]').closest('tr').hide();
		} else {
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
			$('tr.options-field').hide();
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();
		}
	});

	$(document).on('change', 'select[name="afrfq_field_type"]', function(event){
		var value = $(this).val();

		$('select[name="afrfq_field_value"]').closest('tr').show();
		$('input[name="afrfq_field_placeholder"]').closest('tr').show();

		if ( 'select' == value || 'multiselect' == value || 'radio' == value || 'checkbox' == value  ) {
			$('tr.options-field').show();
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();		
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
		} else if ( 'file' == value ) {
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
			$('input[name="afrfq_file_types"]').closest('tr').show();
			$('input[name="afrfq_file_size"]').closest('tr').show();
			$('tr.options-field').hide();

		} else if ( 'terms_cond' == value ) {
			$('textarea[name="afrfq_field_terms"]').closest('tr').show();
			$('tr.options-field').hide();
			
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();
			$('select[name="afrfq_field_value"]').closest('tr').hide();
			$('input[name="afrfq_field_placeholder"]').closest('tr').hide();
		} else {
			$('textarea[name="afrfq_field_terms"]').closest('tr').hide();
			$('tr.options-field').hide();
			$('input[name="afrfq_file_types"]').closest('tr').hide();
			$('input[name="afrfq_file_size"]').closest('tr').hide();
		}
		
	});

	$('#addify_add_item').click( function(){
		$('div#af-backbone-add-product-modal').show();
		$('.af-single_select-product').select2({

			ajax: {
				url: ajaxurl, // AJAX URL is predefined in WordPress admin
				dataType: 'json',
				type: 'POST',
				delay: 250, // delay in ms while typing when to perform a AJAX search
				data: function (params) {
					return {
						q: params.term, // search query
						action: 'afrfqsearch_product_and_variation', // AJAX action for admin-ajax.php
						nonce: nonce // AJAX nonce for admin-ajax.php
					};
				},
				processResults: function( data ) {

					var options = [];
					if ( data ) {
	   
						// data is the array of arrays, and each of them contains ID and the Label of the option
						$.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
							options.push( { id: text[0], text: text[1]  } );
						});
	   
					}
					return {
						results: options
					};
				},
				success: function( $data ){
					$('p.af-backbone-message').remove();
					$('div#af-backbone-add-product-modal button#btn-ok').removeClass('loading');
					$('div#af-backbone-add-product-modal button#btn-ok').css('opacity', '1');
				},
				cache: true
			},
			multiple: false,
			placeholder: 'Choose Product',
			minimumInputLength: 3 // the minimum of symbols to input before perform a search
			
		});		
	});

	$('.af-single_select-product').on('select2:select', function (e) {
		$('div#af-backbone-add-product-modal button#btn-ok').prop('disabled', false);
	});

	$('div#af-backbone-add-product-modal button#btn-ok').click( function(event){

		event.preventDefault();
		if ( $(this).css('opacity') == 0.2 ) {
			return;
		}
		var current_button = $(this);
		$(this).css('opacity' ,'0.2' );
		$('p.af-backbone-message').remove();

		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action     : 'afrfq_insert_product_row',
				nonce      : nonce,
				post_id    : current_button.val(),
				product_id : $('div#af-backbone-add-product-modal select.af-single_select-product').val(),
				quantity   : $('div#af-backbone-add-product-modal input[name="afacr_product_quantity"]').val(),
			},
			success: function (response) {

				if ( response['success'] ) {

					$('div#af-backbone-add-product-modal').hide();
					current_button.removeClass('loading');
					current_button.css('opacity', '1');
					$('#addify_quote_items_container').replaceWith( response['quote-details-table'] );
					$('.af-single_select-product').val(null).trigger('change');
					$('div#af-backbone-add-product-modal button#btn-ok').prop('disabled', true);
				} else {
					
					$('div#af-backbone-add-product-modal table.widefat').after("<p class='af-backbone-message'>" + response['message'] + "</p>");
				}
			},
			error: function (response) {
				jQuery(this).removeClass('loading');
			}
		});
	});

	$('span.af-backbone-close').click( function(){
		$('div#af-backbone-add-product-modal').hide();
	});

	$(".accordion").accordion({
		active: 'none',
		collapsible: true
	});
	
	$('.ajax_customer_search').select2({
		ajax: {
			url: ajaxurl, // AJAX URL is predefined in WordPress admin.
			dataType: 'json',
			type: 'POST',
			delay: 250, // Delay in ms while typing when to perform a AJAX search.
			data: function (params) {
				return {
					q: params.term, // Search query.
					action: 'afrfq_search_users', // AJAX action for admin-ajax.php.
					nonce: nonce // AJAX nonce for admin-ajax.php.
				};
			},
			processResults: function ( data ) {
				var options = [];
				if (data ) {

					// Data is the array of arrays, and each of them contains ID and the Label of the option.
					$.each(
						data, function ( index, text ) {
							// Do not forget that "index" is just auto incremented value.
							options.push({ id: text[0], text: text[1]  });
						}
					);

				}
				return {
					results: options
				};
			},
			cache: true
		},
		multiple: false,
		placeholder: 'Choose User',
		minimumInputLength: 3 // The minimum of symbols to input before perform a search.

	});

	$('.afrfq_hide_products').select2({

		ajax: {
			url: ajaxurl, // AJAX URL is predefined in WordPress admin
			dataType: 'json',
			type: 'POST',
			delay: 250, // delay in ms while typing when to perform a AJAX search
			data: function (params) {
				return {
					q: params.term, // search query
					action: 'af_r_f_q_search_products', // AJAX action for admin-ajax.php
					nonce: nonce // AJAX nonce for admin-ajax.php
				};
			},
			processResults: function( data ) {
				var options = [];
				if ( data ) {
   
					// data is the array of arrays, and each of them contains ID and the Label of the option
					$.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
						options.push( { id: text[0], text: text[1]  } );
					});
   
				}
				return {
					results: options
				};
			},
			cache: true
		},
		multiple: true,
		placeholder: 'Choose Products',
		minimumInputLength: 3 // the minimum of symbols to input before perform a search
		
	});

	$(".namediv").click(function(){
		$(".fieldsdiv").toggle();
	});

	$(".emaildiv").click(function(){
		$(".emailfieldsdiv").toggle();
	});

	$(".companydiv").click(function(){
		$(".companyfieldsdiv").toggle();
	});

	$(".phonediv").click(function(){
		$(".phonefieldsdiv").toggle();
	});

	$(".filediv").click(function(){
		$(".filefieldsdiv").toggle();
	});

	$(".messagediv").click(function(){
		$(".messagefieldsdiv").toggle();
	});

	$(".field1div").click(function(){
		$(".field1fieldsdiv").toggle();
	});

	$(".field2div").click(function(){
		$(".field2fieldsdiv").toggle();
	});

	$(".field3div").click(function(){
		$(".field3fieldsdiv").toggle();
	});

	$('.afrfq_hide_urole').select2();

	$('#afrfq_apply_on_all_products').change(function () {
		if (this.checked) { 
			//  ^
			$('.hide_all_pro').fadeOut('fast');
		} else {
			$('.hide_all_pro').fadeIn('fast');
		}
	});

	if ($("#afrfq_apply_on_all_products").is(':checked')) {
		$(".hide_all_pro").hide();  // checked
	} else {
		$(".hide_all_pro").show();
	}

	$('#afrfq_apply_on_all_user_role').change(function () {
		if (this.checked) { 
			//  ^
			$('.adf-all-user-roles').fadeOut('fast');
		} else {
			$('.adf-all-user-roles').fadeIn('fast');
		}
	});

	if ($("#afrfq_apply_on_all_user_role").is(':checked')) {
		$(".adf-all-user-roles").hide();  // checked
	} else {
		$(".adf-all-user-roles").show();
	}

	$(".child").on("click",function() {
		$parent = $(this).prevAll(".parent");
		if ($(this).is(":checked")) {
			$parent.prop("checked",true);
		} else {
			var len = $(this).parent().find(".child:checked").length;
			$parent.prop("checked",len>0);
		}
	});
	$(".parent").on("click",function() {
		$(this).parent().find(".child").prop("checked",this.checked);
	});

	var value = $("#afrfq_rule_type option:selected").val();
	if (value == 'afrfq_for_registered_users') {
		$('#quteurr').show();
	} else {
		$('#quteurr').hide();
	}

	var value1 = $("#afrfq_is_hide_price option:selected").val();
	if (value1 == 'yes') {
		$('#hpircetext').show();
	} else {
		$('#hpircetext').hide();
	}

	var value2 = $("#afrfq_is_hide_addtocart option:selected").val();
	if (value2 == 'replace_custom' || value2 == 'addnewbutton_custom') {
		jQuery('#afcustom_link').show();
	} else {
		jQuery('#afcustom_link').hide();
	}



	afrfq_get_templete()

		function afrfq_get_templete(){
	
			var afrfq_pdf_select_layout = $( '#afrfq_pdf_select_layout' ).val();			
			
			if (afrfq_pdf_select_layout == "afrfq_template1") {
							
				$( '#afrfq_template1' ).show();
				$( '#afrfq_template2' ).hide();
				$( '#afrfq_template3' ).hide();

			}

			if (afrfq_pdf_select_layout == "afrfq_template2") {

				$( '#afrfq_template1' ).hide();
				$( '#afrfq_template2' ).show();
				$( '#afrfq_template3' ).hide();

			}

			if (afrfq_pdf_select_layout == "afrfq_template3") {

				$( '#afrfq_template1' ).hide();
				$( '#afrfq_template2' ).hide();
				$( '#afrfq_template3' ).show();

			}
		}

		$( "#afrfq_pdf_select_layout" ).on(
			"change",
			function() {
			
				afrfq_get_templete()
			}
		);
		

});

jQuery(document).ready(function($){
	
	if ( $("#afrfq_redirect_after_submission").is(':checked') ) {
		$(".URL_Quote_Submitted").show();  // checked
	} else {
		$(".URL_Quote_Submitted").hide();
	}

	$("#afrfq_redirect_after_submission").on('click' , function(){
		if ( $(this).is(':checked') ) {
			$(".URL_Quote_Submitted").show();  // checked
		} else {
			$(".URL_Quote_Submitted").hide();
		}
	});

});

function afrfq_getUserRole(value) {

	"use strict";
	if (value == 'afrfq_for_registered_users') {
		jQuery('#quteurr').show();
	} else {
		jQuery('#quteurr').hide();
	}
}

function afrfq_HidePrice(value) {

	"use strict";
	if (value == 'yes') {
		jQuery('#hpircetext').show();
	} else {
		jQuery('#hpircetext').hide();
	}
}

function getCustomURL(value) {

	"use strict";
	if (value == 'replace_custom' || value == 'addnewbutton_custom') {
		jQuery('#afcustom_link').show();
	} else {
		jQuery('#afcustom_link').hide();
	}

}

jQuery( function() {
	"use strict";
	jQuery( "#addify_settings_tabs" ).tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
});

jQuery(document).ready(function($){

	afrfq_select_page_template();
	$(document).on('change', '#afrfq_select_page_template', function(){
		afrfq_select_page_template();
	});
	afrfq_pdf_select_layout();
	$(document).on('change', '#afrfq_pdf_select_layout', function(){
		afrfq_pdf_select_layout();
	});
	$(document).on('change', '#afrfq_enable_border', function(){
		afrfq_enable_border();
	});
	// afrfq_enable_table_header();
	$(document).on('change', '#afrfq_enable_table_header_color', function(){
		afrfq_enable_table_header();
	});
	function afrfq_select_page_template(){
		var pageTemplate = $('#afrfq_select_page_template').val();
		if (pageTemplate == 'template_two'){
			afrfq_enable_table_header();
			$('#afrfq_select_page_template').closest('tr').nextAll('tr').hide();
		}else{
			$('#afrfq_select_page_template').closest('tr').nextAll('tr').show();
			afrfq_enable_table_header();
		}
		
	}
	function afrfq_enable_table_header(){
		if ($('#afrfq_enable_table_header_color').prop('checked')){
			$('#afrfq_enable_table_header_color').closest('tr').nextAll('tr').show();
		}else{
			$('#afrfq_enable_table_header_color').closest('tr').nextAll('tr').hide();
		}	
	}
	function afrfq_pdf_select_layout(){
		var pdfTemplate = $('#afrfq_pdf_select_layout').val();
		if (pdfTemplate == 'afrfq_template1'){
			$('#afrfq_pdf_select_layout').closest('tr').next('tr').show();
			$('#afrfq_pdf_select_layout').closest('tr').next('tr').next('tr').show();
			$('#afrfq_pdf_select_layout').closest('tr').next('tr').next('tr').next('tr').hide();
			$('#afrfq_pdf_select_layout').closest('tr').next('tr').next('tr').next('tr').next('tr').hide();
				afrfq_enable_border();
		}else{
			afrfq_enable_border();
			$('#afrfq_pdf_select_layout').closest('tr').next('tr').hide();
			$('#afrfq_pdf_select_layout').closest('tr').next('tr').next('tr').hide();
			$('#afrfq_pdf_select_layout').closest('tr').next('tr').next('tr').next('tr').show();
			$('#afrfq_pdf_select_layout').closest('tr').next('tr').next('tr').next('tr').next('tr').show();

		}	
	}
	function afrfq_enable_border(){
		if($('#afrfq_enable_border').prop('checked')){
			$('#afrfq_enable_border').closest('tr').next().show();
		}else{
			$('#afrfq_enable_border').closest('tr').next().hide();
		}
	}

  afrfq_enable_qoute_button();
  $(document).on('change', '#afrfq_enable_qoute_button_color', function(){
		afrfq_enable_qoute_button();
	})
	function afrfq_enable_qoute_button(){
		if ($('#afrfq_enable_qoute_button_color').prop('checked')){
			$('#afrfq_enable_qoute_button_color').closest('tr').nextAll('tr').show();
		}else{
			$('#afrfq_enable_qoute_button_color').closest('tr').nextAll('tr').hide();
		}	
	}

	afrfq_enable_term_and_condition();
	
  $(document).on('change', '#afrfq_enable_term_and_condition', function(){
		afrfq_enable_term_and_condition();
	})
	function afrfq_enable_term_and_condition(){
		if ($('#afrfq_enable_term_and_condition').prop('checked')){
			$('#afrfq_enable_term_and_condition').closest('tr').next('tr').show();
		}else{
			$('#afrfq_enable_term_and_condition').closest('tr').next('tr').hide();
		}	
	}

	$(document).on('change', '.quote-qty-input',function(){

		let subtotal_price = 0 ;
		let offered_price = 0 ;

		$('.quote-qty-input').each(function(){

			let qty = $(this).val();
			let product_price = $(this).closest('tr').find('.offered-price-input').val() || $(this).closest('tr').find('.offered-price-input').data('actual_price');

			subtotal_price += parseFloat($(this).closest('tr').find('.offered-price-input').data('actual_price')) * parseFloat(qty);
			offered_price += parseFloat(product_price) * parseFloat(qty);

			let currency_type = afrfq_php_vars.currency_type;

			prodcut_actual_price = $(this).closest('tr').find('.offered-price-input').data('actual_price');

			prodcutSubtotal_price = parseFloat(prodcut_actual_price) * parseFloat(qty);

			product_level_subtotal = currency_type.replace('0', prodcutSubtotal_price);

			$(this).closest('tr').find('.sub-total-price').html(product_level_subtotal);

			var offered_sub_total = $(this).closest('tr').find('.offered-price-input').val();
			prodcut_offer_price = parseFloat(offered_sub_total) * parseFloat(qty);
			product_offer_subtotal = currency_type.replace('0', prodcut_offer_price);
			$(this).closest('tr').find('.offered-sub-price').html(product_offer_subtotal);

			currency_type = currency_type.replace('0', subtotal_price);

			$('.Subtotal-Standard').html(currency_type);

			$('.Total-Standard').html(currency_type);

			currency_type = afrfq_php_vars.currency_type;

			currency_type = currency_type.replace('0', offered_price);

			$('.Offered-Subtotal').html(currency_type);
		});
	});
	$(document).on('change', '.offered-price-input', function(){
		
		let totalOfferedPrice = 0;

		let currency_type = afrfq_php_vars.currency_type;
    
    $('.offered-price-input').each(function () {
 
        let price = parseFloat($(this).val()) || 0;
        let quantity = parseInt($(this).closest('tr').find('.quote-qty-input').val()) || 0;
        let subtotal = price * quantity;
        var subtotalOfferedPrice = currency_type.replace('0', subtotal);
        $(this).closest('tr').find('.offered-sub-price').html(subtotalOfferedPrice);

        totalOfferedPrice += subtotal;
    });

    totalOfferedPrice = currency_type.replace('0', totalOfferedPrice);
    $('.Offered-Subtotal').html(totalOfferedPrice);
	});


	// quote popup customization tab
	if($('#afrfq_select_popup_template').length){
		handleLayoutToggle();
		handleCheckboxToggles();

		$('#afrfq_select_popup_template').on('change', function() {
			handleLayoutToggle();
		});

		$(document).on('change', 'input[type="checkbox"]', function() {
			handleCheckboxToggles();
		});
		
		function handleLayoutToggle() {
			const layout = $('#afrfq_select_popup_template').val();

			toggleSectionByHeading('Place Quote Button Settings', layout === 'template_one');
			toggleSectionByHeading('Navigation Button Settings', layout === 'template_two');
		}

		function toggleSectionByHeading(headingText, shouldShow) {
			const heading = $(`h2:contains("${headingText}")`);
			const sectionRows = heading.nextUntil('h2', 'table, .form-table, .form-table tbody, .form-table tr').filter('table, tr');

			if (shouldShow) {
				heading.show();
				sectionRows.show();
			} else {
				heading.hide();
				sectionRows.hide();
			}
			
		}

		function handleCheckboxToggles() {
			$('input[type="checkbox"]').each(function() {
				const checkbox = $(this);
				const isChecked = checkbox.prop('checked');

				const relatedRows = [];
				checkbox.closest('tr').nextAll('tr').each(function () {
					if ($(this).find('input[type="checkbox"]').length > 0 || $(this).prev('h2').length > 0) return false; // stop at next checkbox or section
					relatedRows.push(this);
				});

				$( relatedRows ).each(function() {
					isChecked ? $(this).show() : $(this).hide();
				});
			});
		}

	}

	
	

	//active step button
	afrfq_enable_active_step_button_color();
	$(document).on('change', '#afrfq_popup_enable_active_step_button_color', function(){
		afrfq_enable_active_step_button_color();
	})
	function afrfq_enable_active_step_button_color(){
		if ($('#afrfq_popup_enable_active_step_button_color').prop('checked')){
			$('#afrfq_popup_enable_active_step_button_color').closest('tr').nextAll('tr').slice(0, 2).show();
		}else{
			$('#afrfq_popup_enable_active_step_button_color').closest('tr').nextAll('tr').slice(0, 2).hide();
		}	
	}

	//previous step button
	afrfq_enable_previous_step_button_color();
	$(document).on('change', '#afrfq_popup_enable_previous_step_button_color', function(){
		afrfq_enable_previous_step_button_color();
	})
	function afrfq_enable_previous_step_button_color(){
		if ($('#afrfq_popup_enable_previous_step_button_color').prop('checked')){
			$('#afrfq_popup_enable_previous_step_button_color').closest('tr').nextAll('tr').slice(0, 2).show();
		}else{
			$('#afrfq_popup_enable_previous_step_button_color').closest('tr').nextAll('tr').slice(0, 2).hide();
		}	
	}

	//previous step button
	afrfq_enable_next_step_button_color();
	$(document).on('change', '#afrfq_popup_enable_next_step_button_color', function(){
		afrfq_enable_next_step_button_color();
	})
	function afrfq_enable_next_step_button_color(){
		if ($('#afrfq_popup_enable_next_step_button_color').prop('checked')){
			$('#afrfq_popup_enable_next_step_button_color').closest('tr').nextAll('tr').slice(0, 2).show();
		}else{
			$('#afrfq_popup_enable_next_step_button_color').closest('tr').nextAll('tr').slice(0, 2).hide();
		}	
	}

	//copy shortcode button -- quote shortcode button tab
	$('.afrfq-copy-shortcode').on('click', function (event) {
		event.preventDefault();
		
		const button = $(this);
		const text = button.closest('div').find('.afrfq-shortcode-text').text().trim();
		
		navigator.clipboard.writeText(text).then(() => {
			button.text('Copied!');
			setTimeout(() => button.text('Copy'), 2000);
		});
	});

	// add quote note -- in quote post

	if(jQuery('.afrfq_quote_messages').length){
		jQuery('.afrfq_quote_messages').scrollTop(jQuery('.afrfq_quote_messages')[0].scrollHeight);
	}


	$(document).on('click', '.afrfq_add_note', function(){

		var quote_id = $(this).data('quote_id');
		var quote_note = $('#afrfq_quote_note').val();
		var note_type = $('#afrfq_quote_note_type').val();

		var ajaxurl = afrfq_php_vars.admin_url;
		var nonce   = afrfq_php_vars.nonce;

		if('' != quote_note){
			jQuery('.afrfq-spinner').show();

			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action     : 'afrfq_add_quote_note',
					nonce      : nonce,
					quote_id   : quote_id,
					quote_note   : quote_note,
					note_type   : note_type,
				},
				success: function (response) {

					jQuery('.afrfq-spinner').hide();

					if(response.success){
						jQuery('.afrfq_quote_messages').html(response.data.output_html);
						jQuery('.afrfq_quote_messages').scrollTop(jQuery('.afrfq_quote_messages')[0].scrollHeight);

						jQuery('#afrfq_quote_note').val('');
					}
					
				
				},
				error: function (response) {
					
				}
			});
		}
		
		
	});
	
	$(document).on('click', '.afrfq_delete_note', function(event){
		event.preventDefault();

		var quote_id = $(this).data('quote_id');
		var note_id = $(this).data('note_id');

		var ajaxurl = afrfq_php_vars.admin_url;
		var nonce   = afrfq_php_vars.nonce;		

		
		if (confirm('Are you sure you want to delete this note?')) {
			jQuery('.afrfq-spinner').show();

			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action     : 'afrfq_delete_quote_note',
					nonce      : nonce,
					quote_id   : quote_id,
					note_id   : note_id,
				},
				success: function (response) {

					jQuery('.afrfq-spinner').hide();

					if(response.success){
						jQuery('.afrfq_quote_messages').html(response.data.output_html);
					}
				},
				error: function (response) {
					jQuery('.afrfq-spinner').hide();			
				}
			});
		} 
		
		
		
		
	});
	
	//copy cart url
	$('.afrfq-copy-cart-url').on('click', function (event) {
		event.preventDefault();
		
		const button = $(this);
		const text = button.closest('div').find('#afrfq_convert_to_cart_url').val().trim();
		
		navigator.clipboard.writeText(text).then(() => {
			button.text('Copied!');
			setTimeout(() => button.text('Copy'), 2000);
		});
	});

	// cart link restriction sub tab
	$('#afrfq_cart_link_restriction_type').on('change', function() {
		if ($(this).is(':checked')) {
			$('#afrfq_cart_link_expiry_time').closest('tr').show();
		} else {
			$('#afrfq_cart_link_expiry_time').closest('tr').hide();
		}
	});

	// Trigger change after handler is set up
	$('#afrfq_cart_link_restriction_type').trigger('change');

	$('#afrfq_disabled_payment_methods').select2({
		placeholder: 'Select Payment Methods',
	});

});



