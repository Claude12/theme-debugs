jQuery(document).ready(function ($) {

	jQuery(document).on('click', '.af-ure-list-show-table-list', function () {
		jQuery('.af-ure-order-detail-table').hide();
		$(this).closest('div.af-ure-order-detail-main-div').find('.af-ure-order-detail-table').show();
	});

	capabilitites_type();
	jQuery(document).on('change', '.capabilitites_type', function () {
		capabilitites_type();
	});

	function capabilitites_type() {

		if ('select_custom_capabilities' == $('.capabilitites_type').val()) {

			$('.af-select-custom-capabilities').show();
			$('.af-select-user-role-for-capabilities').hide();

		} else {

			$('.af-select-custom-capabilities').hide();
			$('.af-select-user-role-for-capabilities').show();

		}

	}

	var export_data_to_csv = jQuery('.af-ure-export-csv-data').html();

	jQuery('.af-ure-register-user-list-table .tablenav .alignleft').first().after(export_data_to_csv);
	jQuery('.af-ure-guest-page-listing-user .tablenav .alignleft').first().after(export_data_to_csv);

	jQuery('.af-ure-register-user-list-table .tablenav .alignleft').first().after(php_var.user_role_filter);
	jQuery('.af-ure-guest-page-listing-user .tablenav .alignleft').first().after(php_var.user_role_filter);


	jQuery(document).on('click', '.af-ure-show-popup', function (e) {
		e.preventDefault();
		let show_class = $(this).data('show_class');
		$('.' + show_class).hide();

		$('.' + show_class).show();


	});

	jQuery(document).on('click', '.af-ure-edit-or-view-capabilities', function (e) {
		e.preventDefault();
		let popup_class = $(this).data('popup_class');

		$('.af-ure-user-and-customer-detail').each(function () {
			$(this).hide();
		});

		$(this).closest('tr').find('.' + popup_class).show();


	});

	jQuery(document).on('click', '.af-ure-close-send-bulk-email-popup-main-container', function () {
		$(this).closest('div').hide();
	});


	jQuery(document).on('click', '.af-delete-current-customer', function (e) {
		e.preventDefault();
		if ($(this).closest('tr').find('.af-ure-selected-user').val()) {

			af_ure_delete_customer($(this), $(this).closest('tr').find('.af-ure-selected-user').val());
		}
	});
	jQuery(document).on('click', '.af-ure-register-user-list-table .bulkactions #doaction', function (event) {
		if ($(this).hasClass('af_ure_assign_user_role_to_selected_customer')) {
			return;
		}
		event.preventDefault();

		var selected_value = jQuery('.af-ure-register-user-list-table .bulkactions select#bulk-action-selector-top').val();

		if ('delete' == selected_value) {
			jQuery('.af-ure-selected-user').each(function () {

				if ($(this).is(':checked') && $(this).val()) {


					af_ure_delete_customer($(this), $(this).val());
				}

			});
		}
		// window.location.reload(true);

	});

	jQuery('.af_customer_user_role').select2({
		multiple: true,
	});
	jQuery('.af_ure_live_search').select2({
		multiple: true,
	});

	function af_ure_delete_customer(current_btn, user_id) {
		if (!user_id) {
			return;
		}

		hide_show_loading_icon();
		jQuery.ajax({
			url: php_var.ajaxurl,
			type: 'POST',
			data: {
				action: 'af_ure_delete_customer',
				nonce: php_var.nonce,
				user_id: user_id

			},
			success: function (response) {
				hide_show_loading_icon();
				if (response['success']) {

					current_btn.closest('tr').remove();

				}

			},
		});
	}


	jQuery(document).on('click ', '.af-ure-delete-user-role', function (e) {
		e.preventDefault();

		current_elemet = $(this);
		delete_or_shift_user_role(current_elemet)

	});

	jQuery(document).on('submit', 'form.af-ure-delete-and-assign-new-user-role-to-user', function (e) {
		e.preventDefault();

		current_elemet = $(this);
		delete_or_shift_user_role(current_elemet)

	});

	function delete_or_shift_user_role(current_elemet) {
		if ('show_popup_with_new_user_role' == current_elemet.data('action_type')) {
			$('.af-ure-delete-and-assign-new-user-role-to-user-main-div').hide();
		}

		hide_show_loading_icon();
		jQuery.ajax({
			url: php_var.ajaxurl,
			type: 'POST',
			data: {
				action: 'af_ure_delete_user_role',
				role_key: current_elemet.data('role_key'),
				form: current_elemet.serialize(),
				action_type: current_elemet.data('action_type'),
				nonce: php_var.nonce
			},
			success: function (response) {
				hide_show_loading_icon();
				if (response) {

					if (response['html'] && 'show_popup_with_new_user_role' == current_elemet.data('action_type')) {

						current_elemet.closest('td').append(response['html']);

					}

					if (response['html'] && response['success'] && 'delete' == current_elemet.data('action_type')) {

						$('.af-ure-delete-and-assign-new-user-role-to-user-main-div').remove();
						$('.af-show-content').html(response['html']);

					}

					if ('delete' == response['action']) {

						setTimeout(function () {

							$('.af-ure-delete-and-assign-new-user-role-to-user-main-div').remove();
							$('.af-ure-user-role' + current_elemet.data('role_key')).remove();
							window.location.reload();

						}, 3000);

					}
				}

			},
		});
	}

	jQuery(document).on('submit', 'form.af-ure-create-new-user-role', function (e) {

		e.preventDefault();
		hide_show_loading_icon();
		var current_btn = $(this);
		jQuery.ajax({
			url: php_var.ajaxurl,
			type: 'POST',
			data: {
				action: 'af_ure_create_new_user_role',
				form_data: $(this).serialize(),
				nonce: php_var.nonce,
				selected_capabilites: $('.selected_capabilites').val(),
			},
			success: function (response) {
				hide_show_loading_icon();
				if (response && response.data && response.data['success_message']) {
					$('.form.af-ure-create-new-user-role').find('.message').remove();
					$('.af-cm-create-new-user-role').after(response.data['success_message']);

					setTimeout(function () {

						window.location.reload();

					}, 2000);

				}
				if (response && response.data && response.data['error']) {
					$('.form.af-ure-create-new-user-role').find('.message').remove();
					$('.af-cm-create-new-user-role').after(response.data['error']);

				}


			}
		});

	});

	jQuery(document).on('submit', 'form.af-ure-create-edit-user-role-capabilities-form', function (e) {

		e.preventDefault();

		var current_btn = $(this);
		let new_capabilities = [];

		$(this).find('.current_user_role_capabilities:checked').each(function () {
			new_capabilities.push($(this).val());
		});
		hide_show_loading_icon();
		jQuery.ajax({
			url: php_var.ajaxurl,
			type: 'POST',
			data: {
				action: 'af_ure_update_capabilities',
				form_data: $(this).serialize(),
				nonce: php_var.nonce,
				new_capabilities: new_capabilities,
			},
			success: function (response) {
				hide_show_loading_icon();
				if (response.data && response.data['success_message']) {

					current_btn.before(response.data['success_message']);
					current_btn.find('input[type=submit]').after(response.data['success_message']);

					setTimeout(function () {

						window.location.reload();

					}, 2000);

				}

			}
		});

	});


});

jQuery(document).ready(function ($) {

	if (jQuery('.af-ure-register-user-list-table table').length) {
		jQuery('.af-ure-register-user-list-table table').DataTable();
	}

});

function hide_show_loading_icon() {

	if (jQuery(".af-cmfw-loading-icon-div").is(":visible")) {
		jQuery('.af-cmfw-loading-icon-div').hide();
	} else {
		jQuery('.af-cmfw-loading-icon-div').show();
	}
}