(function ( $ ) {

	'use strict';

	$(
		function () {

			$("#accordion").accordion(
				{
					active: 'none',
					collapsible: true
				}
			);
			$(".accordion").accordion(
				{
					active: 'none',
					collapsible: true
				}
			);

			var ajaxurl = afb2b_php_vars.admin_url;
			var nonce   = afb2b_php_vars.nonce;

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

})(jQuery);

jQuery(document).ready(
	function () {
		var value1 = $("#afpvu_global_redirection_mode option:selected").val();
		if ('custom_url' == value1) {

			jQuery('.showcustomurl').show();
			jQuery('.showcustommessage').hide();
		} else if ('custom_message' == value1) {

			jQuery('.showcustomurl').hide();
			jQuery('.showcustommessage').show();
		}
	}
);

function setGlobalRedirect(value)
{

	if ('custom_url' == value) {

		jQuery('.showcustomurl').show();
		jQuery('.showcustommessage').hide();
	} else if ('custom_message' == value) {

		jQuery('.showcustomurl').hide();
		jQuery('.showcustommessage').show();
	}
}



