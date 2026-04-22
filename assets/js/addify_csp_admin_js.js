  jQuery(
	function ($) {
	
		jQuery('.sel2').select2();
		var ajaxurl = csp_php_vars.admin_url;
		var nonce   = csp_php_vars.nonce;

		jQuery('.rbp_chose_select_brand').select2({});

		jQuery('.sel_pros').select2(
			{

				ajax: {
					url: ajaxurl, // AJAX URL is predefined in WordPress admin
					dataType: 'json',
					type: 'POST',
					delay: 250, // delay in ms while typing when to perform a AJAX search
					data: function (params) {
						return {
							q: params.term, // search query
							action: 'cspsearchProducts', // AJAX action for admin-ajax.php
							nonce: nonce // AJAX nonce for admin-ajax.php
						};
					},
					processResults: function ( data ) {
						var options = [];
						if (data ) {
   
								 // data is the array of arrays, and each of them contains ID and the Label of the option
								$.each(
									data, function ( index, text ) {
										// do not forget that "index" is just auto incremented value
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
				multiple: true,
				placeholder: 'Choose Products',
				minimumInputLength: 3 // the minimum of symbols to input before perform a search
		
			}
		);


		jQuery('.sel22').select2(
			{

				ajax: {
					url: ajaxurl, // AJAX URL is predefined in WordPress admin
					dataType: 'json',
					type: 'POST',
					delay: 250, // delay in ms while typing when to perform a AJAX search
					data: function (params) {
							 return {
									q: params.term, // search query
									action: 'cspsearchUsers', // AJAX action for admin-ajax.php
									nonce: nonce // AJAX nonce for admin-ajax.php
						};
					},
					processResults: function ( data ) {
						var options = [];
						if (data ) {
   
							// data is the array of arrays, and each of them contains ID and the Label of the option
							$.each(
								data, function ( index, text ) {
									// do not forget that "index" is just auto incremented value
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
				placeholder: 'Choose Users',
				minimumInputLength: 3 // the minimum of symbols to input before perform a search
		
			}
		);


		$('.save-variation-changes').prop('disabled', false);

		$('#csp_enable_hide_pirce').change(
			function () {
				if (this.checked) { 
					//  ^
					$('#hide_div').fadeIn('fast');
				} else {
					$('#hide_div').fadeOut('fast');
				}
			}
		);

		$('#csp_enable_hide_pirce_registered').change(
			function () {
				if (this.checked) { 
					//  ^
					$('#userroles').fadeIn('fast');
				} else {
					$('#userroles').fadeOut('fast');
				}
			}
		);

		$('#csp_hide_price').change(
			function () {
				if (this.checked) { 
					//  ^
					$('#hp_price').fadeIn('fast');
				} else {
					$('#hp_price').fadeOut('fast');
				}
			}
		);

		$('#csp_hide_cart_button').change(
			function () {
				if (this.checked) { 
					//  ^
					$('.hp_cart').fadeIn('fast');
				} else {
					$('.hp_cart').fadeOut('fast');
				}
			}
		);

		$('#csp_apply_on_all_products').change(
			function () {
				if (this.checked) { 
					//  ^
					$('.hide_all_pro').fadeOut('fast');
				} else {
					$('.hide_all_pro').fadeIn('fast');
				}
			}
		);


		//On Load

		if ($("#csp_enable_hide_pirce").is(':checked')) {
			  $("#hide_div").show();  // checked
		} else {
			$("#hide_div").hide();
		}

		if ($("#csp_enable_hide_pirce_registered").is(':checked')) {
			$("#userroles").show();  // checked
		} else {
			$("#userroles").hide();
		}

		if ($("#csp_hide_price").is(':checked')) {
			$("#hp_price").show();  // checked
		} else {
			$("#hp_price").hide();
		}

		if ($("#csp_hide_cart_button").is(':checked')) {
			$(".hp_cart").show();  // checked
		} else {
			$(".hp_cart").hide();
		}


		if ($("#csp_apply_on_all_products").is(':checked')) {
			$(".hide_all_pro").hide();  // checked
		} else {
			$(".hide_all_pro").show();
		}

	} 
);

jQuery(
	function ($) {
		$(".child").on(
			"click",function () {
				$parent = $(this).prevAll(".parent");
				if ($(this).is(":checked")) {
					$parent.prop("checked",true);
				} else {
					var len = $(this).parent().find(".child:checked").length;
					$parent.prop("checked",len>0);
				}    
			}
		);
		$(".parent").on(
			"click",function () {
				$(this).parent().find(".child").prop("checked",this.checked);
			}
		);
	}
);

//design template for role based pricing module

jQuery( function($) {
	jQuery(document).on('click', '#remove_image_upload' , function() {
		jQuery('#afb2b_role_template_icon').val('');
		jQuery('#afb2b_role_selected_image_display').attr('src', "");
	});
	
	$(document).on('click','#upload-image-btn',function(){ 
		"use strict";
		var image = wp.media({ 
			title: 'Upload Image',
			multiple: false
		}).open()
		.on('select', function(){
			var uploaded_image = image.state().get('selection').first();
			var image_url = uploaded_image.toJSON().url;
			jQuery('#afb2b_role_template_icon').val(image_url);
			jQuery('#afb2b_role_selected_image_display').attr("src", image_url);
		});
	})

	//reset template settings to default

	jQuery(document).on('click', '#afb2b_role_reset_settings_to_default' , function(event) {
		event.preventDefault();

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'afb2b_role_reset_pricing_template_settings',
				nonce: csp_php_vars.nonce
			},
			success:function(){
				location.reload();
			}
		})
	})


	
	
	//enable /disable template heading
	$('#afb2b_role_enable_template_heading').change(enable_template_heading_change);
	
	function enable_template_heading_change() {
		if ($('#afb2b_role_enable_template_heading').is(":checked")) { 
			$('#afb2b_role_template_heading_text').closest('tr').show();
			$('#afb2b_role_template_heading_text_font_size').closest('tr').show();
	
		} else {
			$('#afb2b_role_template_heading_text').closest('tr').hide();
			$('#afb2b_role_template_heading_text_font_size').closest('tr').hide();
	
		}
	}
	
	//enable /disable template icon
	$('#afb2b_role_enable_template_icon').change(enable_template_icon_change);
	function enable_template_icon_change() {
		if ($('#afb2b_role_enable_template_icon').is(":checked")) { 
			$('#afb2b_role_template_icon').closest('tr').show();
		} else {
			$('#afb2b_role_template_icon').closest('tr').hide();
		}
	}
	
	//enable / disable table border
	$('#afb2b_role_enable_table_border').change(enable_table_border_change);
	function enable_table_border_change() {
		if ($('#afb2b_role_enable_table_border').is(":checked") && $('#afb2b_role_pricing_design_type').val() == 'table' ) { 
			$('#afb2b_role_table_border_color').closest('tr').show();
		} else {
			$('#afb2b_role_table_border_color').closest('tr').hide();
		}
	}
	
	//enable / disable sale tag for card
	$('#afb2b_role_enable_card_sale_tag').change(enable_card_sale_tag_change);
	function enable_card_sale_tag_change() {
		if ($('#afb2b_role_enable_card_sale_tag').is(":checked") && $('#afb2b_role_pricing_design_type').val() == 'card') { 
			$('#afb2b_role_sale_tag_background_color').closest('tr').show();
			$('#afb2b_role_sale_tag_text_color').closest('tr').show();
	
		} else {
			$('#afb2b_role_sale_tag_background_color').closest('tr').hide();
			$('#afb2b_role_sale_tag_text_color').closest('tr').hide();
		}
	}
	
	
	pricing_design_select_change();
	
	enable_template_heading_change();
	enable_template_icon_change();
	enable_table_border_change();
	enable_card_sale_tag_change();
	
	
	//pricing type design select change
	$(document).on('change','#afb2b_role_pricing_design_type',pricing_design_select_change);
	function pricing_design_select_change(){
		 if($('#afb2b_role_pricing_design_type').val() == 'table'){
			$('.afb2b_role_table_img').show();
			$('.afb2b_role_card_img').hide();
			$('.afb2b_role_list_img').hide();
			$('.afb2b_role_default_template_img').hide();
			$('.afb2b_role_default_template_row').closest('tr').hide();
			$('.afb2b_role_table_row').closest('tr').show();
			$('.afb2b_role_list_row').closest('tr').hide();
			$('.afb2b_role_card_row').closest('tr').hide();
	
	
	
		}
		else if($('#afb2b_role_pricing_design_type').val() == 'list'){
			$('.afb2b_role_table_img').hide();
			$('.afb2b_role_card_img').hide();
			$('.afb2b_role_list_img').show();
			$('.afb2b_role_default_template_img').hide();
			$('.afb2b_role_default_template_row').closest('tr').hide();
			$('.afb2b_role_table_row').closest('tr').hide();
			$('.afb2b_role_list_row').closest('tr').show();
			$('.afb2b_role_card_row').closest('tr').hide();
	
		}
		else if($('#afb2b_role_pricing_design_type').val() == 'card'){
			$('.afb2b_role_table_img').hide();
			$('.afb2b_role_card_img').show();
			$('.afb2b_role_list_img').hide();
			$('.afb2b_role_default_template_img').hide();
			$('.afb2b_role_default_template_row').closest('tr').hide();
			$('.afb2b_role_table_row').closest('tr').hide();
			$('.afb2b_role_list_row').closest('tr').hide();
			$('.afb2b_role_card_row').closest('tr').show();
		}
		else{
			$('.afb2b_role_table_img').hide();
			$('.afb2b_role_card_img').hide();
			$('.afb2b_role_list_img').hide();
			$('.afb2b_role_default_template_img').show();
			$('.afb2b_role_default_template_row').closest('tr').show();
			$('.afb2b_role_table_row').closest('tr').hide();
			$('.afb2b_role_list_row').closest('tr').hide();
			$('.afb2b_role_card_row').closest('tr').hide();
			
		}
		enable_template_heading_change();
		enable_template_icon_change();
		enable_table_border_change();
		enable_card_sale_tag_change();
	}
	
})
	
