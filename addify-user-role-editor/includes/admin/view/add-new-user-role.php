<?php
if (!defined('ABSPATH')) {
	exit();
}

$get_default_wp_roles = wp_roles();

if (!isset($get_default_wp_roles)) {
	$get_default_wp_roles = new WP_Roles();
}

$all_capabilities = array();

foreach ($get_default_wp_roles->role_objects as $role_slug => $current_role_object) {

	foreach ($current_role_object->capabilities as $capability => $value) {

		$all_capabilities[ $capability ] = true;

	}

}

?>
<div class="af-ure-create-new-user-role-container af-ure-create-user-role-capabilities" style="display:none;">
	<i class="fa fa-close af-ure-close-send-bulk-email-popup-main-container" style="top:-18px;right:-20px;"></i>
	<div>
		<form method="post" class="af-ure-create-new-user-role">
			<?php wp_nonce_field('addify_user_role_editor', 'addify_user_role_editor'); ?>
			<table class="wp-list-table widefat fixed striped table-view-list customers dataTable">
				<tbody>
					<tr>
						<th>
							<?php echo esc_html__('Select User Role Name', 'addify_b2b'); ?>
						</th>
						<td>
							<input style="width:100%;max-width:100% !important;" type="text" required
								name="user_role_name">
						</td>
					</tr>
					<tr>
						<th>
							<?php echo esc_html__('Select User Role Key', 'addify_b2b'); ?>
						</th>
						<td>
							<input style="width:100%;max-width:100% !important;" type="text" required
								name="user_role_key" pattern=".*[a-zA-Z]+.*"
								title="Please include at least one alphabet character.">
						</td>
					</tr>
					<tr>
						<th>
							<?php echo esc_html__('Capabilities Type', 'addify_b2b'); ?>
						</th>
						<td>
							<select style="width:100%;max-width:100% !important;" name="capabilitites_type"
								class="capabilitites_type">
								<option value="user_role">
									<?php echo esc_html__('Copy User Role Capabilities', 'addify_b2b'); ?>
								</option>
								<option value="select_custom_capabilities">
									<?php echo esc_html__('Add Custom Capabilities', 'addify_b2b'); ?>
								</option>
							</select>
						</td>
					</tr>
					<tr class="af-select-user-role-for-capabilities">
						<th>
							<?php echo esc_html__('Select User Role', 'addify_b2b'); ?>
						</th>
						<td>
							<select style="width: 50%;" name="selected_user_role">
								<?php foreach ($get_default_wp_roles->get_names() as $key => $value) { ?>

									<option value="<?php echo esc_attr($key); ?>">
										<?php echo esc_attr($value); ?>
									</option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr class="af-select-custom-capabilities">
						<th>
							<?php echo esc_html__('Select Custom Capabilities', 'addify_b2b'); ?>
						</th>
						<td>
							<select class="af_ure_live_search selected_capabilites" style="width: 50%;"
								name="selected_capabilites[]">
								<?php foreach ($all_capabilities as $key => $value) { ?>

									<option value="<?php echo esc_attr($key); ?>">
										<?php echo esc_attr(ucfirst(str_replace('_', ' ', $key))); ?>
									</option>
								<?php } ?>
							</select>
						</td>
					</tr>

				</tbody>
			</table>
			<input style="margin: 6px 0px 6px 0px;" type="submit" name="submit" value="<?php echo esc_html__('Create', 'addify_b2b'); ?>"
				class="af-cm-create-new-user-role button button-primary">
		</form>
	</div>
</div>

<div class="tablenav top">
	<div class="alignleft actions bulkactions">

		<input type="submit" class="af-ure-show-popup button button-primary"
			data-show_class="af-ure-create-new-user-role-container" value="<?php echo esc_html__('Create User Role', 'addify_b2b'); ?>">
	</div>
</div>
<div class="af-show-content"></div>

<div class="af-listed-user-roles" style="margin: 0px 20px 0px 2px;">
	<table
		class="af-listed-user-roles af-listed-user-roles-table wp-list-table widefat fixed striped table-view-list customers">
		<thead>
			<thead>
				<th><?php echo esc_html__('User Role', 'addify_b2b'); ?></th>
				<th><?php echo esc_html__('Role Key', 'addify_b2b'); ?></th>
				<th><?php echo esc_html__('Assign Customer ', 'addify_b2b'); ?></th>
				<th><?php echo esc_html__('Action', 'addify_b2b'); ?></th>
			</thead>
		</thead>
		<tbody>
			<?php

			$roles_name = $get_default_wp_roles->get_names();

			foreach ($get_default_wp_roles->roles as $current_user_role_key => $value) {

				?>
				<tr class="af-ure-user-role-<?php echo esc_attr($current_user_role_key); ?>">
					<th>
						<?php echo esc_attr($value['name']); ?>
					</th>
					<td>
						<?php echo esc_attr($current_user_role_key); ?>
					</td>
					<td>
						<?php

						$args = array(
							'role'   => $current_user_role_key,
							'number' => -1,
							'fields' => 'ids',
						);
						echo esc_attr(count(get_users($args)));

						?>
					</td>

					<td>

						<div class="af-ure-user-and-customer-detail af-ure-create-view-user-role-capabilities af-ure-create-user-role-capabilities"
							style="display:none;">
							<i class="fa fa-close af-ure-close-send-bulk-email-popup-main-container"
								style="top:-18px;right:-20px;"></i>

							<div style="height: 356px;width: 1000px;overflow: scroll;padding: 18px;background: #fff;">

								<form>

									<ul>
										<?php foreach ($all_capabilities as $key => $true_or_false) { ?>
											<li>
												<input type="checkbox"
													name="current_user_role_capabilities[<?php echo esc_attr($current_user_role_key); ?>][<?php echo esc_attr($key); ?>]"
													<?php
													if (isset($value['capabilities']) && in_array($key, array_keys((array) $value['capabilities']))) {
														?>
														checked <?php } ?>>

												<label>
													<?php echo esc_attr(ucfirst(str_replace('_', ' ', $key))); ?>
												</label>

											</li>
										<?php } ?>
									</ul>
								</form>
							</div>
						</div>
						<div class="af-ure-user-and-customer-detail af-ure-create-edit-user-role-capabilities"
							style="display:none;">
							<i class="fa fa-close af-ure-close-send-bulk-email-popup-main-container"
								style="top:-18px;right:-20px;"></i>

							<div style="height: 356px;width: 1000px;overflow: scroll;padding: 18px;background: #fff;">

								<form class="af-ure-create-edit-user-role-capabilities-form" method="post">
									<ul>
										<?php foreach ($all_capabilities as $key => $true_or_false) { ?>
											<li>
												<input type="checkbox" class="current_user_role_capabilities"
													name="current_user_role_capabilities[]"
													value="<?php echo esc_attr($key); ?>" 
																		<?php
																		if (isset($value['capabilities']) && in_array($key, array_keys((array) $value['capabilities']))) {
																			?>
															checked <?php } ?>>

												<label>
													<?php echo esc_attr(ucfirst(str_replace('_', ' ', $key))); ?>
												</label>

											</li>
										<?php } ?>
									</ul>
									<input type="submit" name="submit" value="<?php echo esc_html__('Update', 'addify_b2b'); ?>" class="button button-primary">
									<input type="hidden" name="user_role"
										value="<?php echo esc_attr($current_user_role_key); ?>">

								</form>
							</div>

						</div>

						<a data-popup_class="af-ure-create-view-user-role-capabilities"
							class="af-ure-edit-or-view-capabilities af-tips fa fa-eye" target="_blank" href="#"><span>
								<?php echo esc_html__('View Capabilities', 'addify_b2b'); ?>
							</span></a>

						<a data-popup_class="af-ure-create-edit-user-role-capabilities" href="#"
							class="af-ure-edit-or-view-capabilities af-tips fa fa-pencil"><span>
								<?php echo esc_html__('Edit Capabilities', 'addify_b2b'); ?>
							</span></a>

						<a data-popup_class="af-ure-create-edit-user-role-capabilities" href="#"
							class="af-ure-delete-user-role af-tips fa fa-trash"
							data-action_type="show_popup_with_new_user_role"
							data-role_key="<?php echo esc_attr($current_user_role_key); ?>">
							<span>
								<?php echo esc_html__('Delete User Role', 'addify_b2b'); ?>
							</span>
						</a>

					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php
