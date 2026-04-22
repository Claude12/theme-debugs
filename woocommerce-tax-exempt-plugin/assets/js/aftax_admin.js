(function ($) {

	'use strict';

	if ($('#aftax_enable_tax_exm_msg').is(':checked')) {
		$('.aftax_role_message_text_div').show();
	} else {
		$('.aftax_role_message_text_div').hide();
	}

	$('#aftax_enable_tax_exm_msg').change(function () {

		if ($(this).is(':checked')) {
			$('.aftax_role_message_text_div').show();
		} else {
			$('.aftax_role_message_text_div').hide();
		}

	});


	$(
		function () {

			var ajaxurl = aftax_php_vars.admin_url;
			var nonce = aftax_php_vars.nonce;


			$('.aftax_exempted_user_roles').select2();
			$('#aftax_exempted_customers').select2(
				{

					ajax: {
						url: ajaxurl, // AJAX URL is predefined in WordPress admin
						dataType: 'json',
						type: 'POST',
						delay: 250, // delay in ms while typing when to perform a AJAX search
						data: function (params) {
							return {
								q: params.term, // search query
								action: 'aftaxsearchUsers', // AJAX action for admin-ajax.php
								nonce: nonce // AJAX nonce for admin-ajax.php
							};
						},
						processResults: function (data) {
							var options = [];
							if (data) {

								// data is the array of arrays, and each of them contains ID and the Label of the option
								$.each(
									data,
									function (index, text) {
										// do not forget that "index" is just auto incremented value
										options.push({ id: text[0], text: text[1] });
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
					placeholder: 'Choose Users',
					minimumInputLength: 3 // the minimum of symbols to input before perform a search

				}
			);

		}
	);

})(jQuery);


jQuery(document).ready(
	function () {

		var form_enc = jQuery('form').attr("enctype");
		if (form_enc != 'multipart/form-data') {
			jQuery('form').attr("enctype", "multipart/form-data");
		}


	}
);
