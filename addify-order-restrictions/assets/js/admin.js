jQuery(function($){

	var ajaxurl = php_vars.admin_url;
	var nonce   = php_vars.nonce;
	
	$(document).ready( function(){

		if ( ! $.isFunction($.fn.select2) ) {
			return;
		}
		
		$('.afor_user_roles').select2();

		$('.afor_customers').select2({
	        ajax: {
	            url: ajaxurl, // AJAX URL is predefined in WordPress admin.
	            dataType: 'json',
	            type: 'POST',
	            delay: 250, // Delay in ms while typing when to perform a AJAX search.
	            data: function (params) {
	                return {
	                    q: params.term, // Search query.
	                    action: 'af_o_r_search_users', // AJAX action for admin-ajax.php.
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
	        multiple: true,
	        placeholder: 'Choose User',
	        minimumInputLength: 3 // The minimum of symbols to input before perform a search.

	    });
	});
		
});