(function( $ ) {

	'use strict';

	$(function() {

		var ajaxurl = afpvu_php_vars.admin_url;
		var nonce   = afpvu_php_vars.nonce;

		$('.afpvu_applied_products').select2({

			ajax: {
				url: ajaxurl, // AJAX URL is predefined in WordPress admin
				dataType: 'json',
				type: 'POST',
				delay: 250, // delay in ms while typing when to perform a AJAX search
				data: function (params) {
					return {
						q: params.term, // search query
						action: 'afpvusearchProducts', // AJAX action for admin-ajax.php
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


		var value1 = $("#afpvu_global_redirection_mode option:selected").val();
		if ('custom_url' == value1) {

			jQuery('.showcustomurl').show();
			jQuery('.showcustommessage').hide();
		} else if ('custom_message' == value1) {

			jQuery('.showcustomurl').hide();
			jQuery('.showcustommessage').show();
		}


		

	});



})( jQuery );


function setGlobalRedirect(value) {

	if ('custom_url' == value) {

		jQuery('.showcustomurl').show();
		jQuery('.showcustommessage').hide();
	} else if ('custom_message' == value) {

		jQuery('.showcustomurl').hide();
		jQuery('.showcustommessage').show();
	}

}


