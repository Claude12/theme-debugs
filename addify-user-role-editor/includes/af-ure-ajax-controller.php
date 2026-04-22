<?php
if (!defined('ABSPATH')) {
	die;
}

class Af_Ure_Ajax_Controller {


	public function __construct() {

		// Add User Role.
		add_action('wp_ajax_af_ure_create_new_user_role', array( $this, 'af_ure_create_new_user_role' ));

		//Update Capabilities.
		add_action('wp_ajax_af_ure_delete_customer', array( $this, 'af_ure_delete_customer' ));

		add_action('wp_ajax_af_ure_update_capabilities', array( $this, 'af_ure_update_capabilities' ));

		//Delete and assign user role .

		add_action('wp_ajax_af_ure_delete_user_role', array( $this, 'af_ure_delete_user_role' ));
	}

	public function af_ure_create_new_user_role() {

		$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
		if (!wp_verify_nonce($nonce, 'addify_user_role_editor')) {
			wp_die(esc_html__('Security Violated!', 'addify_b2b'));
		}

		$get_default_wp_roles = wp_roles();


		if (!isset($get_default_wp_roles)) {
			$get_default_wp_roles = new WP_Roles();
		}


		global $wp_roles;
		if (isset($_POST['form_data'])) {

			parse_str(sanitize_text_field($_POST['form_data']), $form_data);

			$user_role_name     = isset($form_data['user_role_name']) ? $form_data['user_role_name'] : '';
			$user_role_key      = isset($form_data['user_role_key']) ? $form_data['user_role_key'] : '';
			$capabilitites_type = isset($form_data['capabilitites_type']) ? $form_data['capabilitites_type'] : 'user_role';
			$selected_user_role = isset($form_data['selected_user_role']) ? $form_data['selected_user_role'] : 'administrator';


			$all_capabilities = array();

			if ('select_custom_capabilities' == $capabilitites_type && isset($_POST['selected_capabilites']) && !empty($_POST['selected_capabilites'])) {

				$originalArray    = sanitize_meta('', $_POST['selected_capabilites'], '');
				$all_capabilities = array_combine($originalArray, array_fill(1, count($originalArray), null));


			} else {



				foreach ($get_default_wp_roles->role_objects as $role_slug => $current_role) {

					if (isset($current_role->name) && $selected_user_role == $current_role->name) {

						$all_capabilities = (array) $current_role->capabilities;

					}
				}
			}

			foreach ($all_capabilities as $cap_type => $value) {
				$all_capabilities[ $cap_type ] = true;
			}

			$wp_all_created_roles = $wp_roles->get_names();
			if (isset($wp_all_created_roles[ $user_role_key ])) {
				ob_start();
				?>
				<div id="message" class="error">
					<p>
						<strong>
							<?php echo esc_html__('Role Already Created', 'addify_b2b'); ?>
						</strong>
					</p>
				</div>
				<?php
				$result = ob_get_clean();

				wp_send_json_success(array( 'error' => $result ));

			} elseif (!empty($user_role_name) && !empty($user_role_key)) {

				$role = add_role($user_role_key, $user_role_name, $all_capabilities);

				$old_roles = (array) get_option('af_ure_created_user_role_from_our_plugin');

				if (is_wp_error($role)) {
					ob_start();

					$errors = $role->get_error_messages();

					foreach ($errors as $error) {

						?>
						<br>
						<div id="message" class="error">
							<p>
								<strong>
									<?php echo esc_attr($error); ?>
								</strong>
							</p>
						</div>
						<?php
					}

					$result = ob_get_clean();

					wp_send_json_success(array( 'error' => $result ));

				} else {

					$old_roles[] = $user_role_key;
					$old_roles   = array_unique($old_roles);
					update_option('af_ure_created_user_role_from_our_plugin', $old_roles);

					$users = get_users(array( 'role' => $user_role_key ));

					foreach ($users as $user) {
						$user->remove_role($user_role_key);
					}

					ob_start();
					?>
					<div id="message" class="success">
						<p><strong>
								<?php esc_html_e('User Role created Successfully.', 'addify_b2b'); ?>
							</strong></p>
					</div>
					<?php

					$result = ob_get_clean();

					wp_send_json_success(array( 'success_message' => $result ));
				}


			}


		}
	}

	public function af_ure_update_capabilities() {

		$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
		if (!wp_verify_nonce($nonce, 'addify_user_role_editor')) {
			wp_die(esc_html__('Security Violated!', 'addify_b2b'));
		}

		$get_default_wp_roles = wp_roles();

		if (!isset($get_default_wp_roles)) {
			$get_default_wp_roles = new WP_Roles();
		}


		if (isset($_POST['form_data'])) {

			parse_str(sanitize_text_field($_POST['form_data']), $form_data);
			$form_data['new_capabilities'] = isset($_POST['new_capabilities']) ? sanitize_meta('', $_POST['new_capabilities'], '') : array();

			if ($form_data['user_role']) {

				$user_role = $form_data['user_role'];

				$current_user_role = get_role($user_role);

				if ($current_user_role) {
					$old_capabilities = $current_user_role->capabilities;

					foreach ($old_capabilities as $cap_type => $true_or_false) {

						$current_user_role->remove_cap($cap_type);

					}

				}

				if ($form_data['new_capabilities']) {
					foreach ((array) $form_data['new_capabilities'] as $key => $value) {

						if ($value) {
							$current_user_role->add_cap($value);
						}
					}

					ob_start();
					?>
					<br>
					<div id="message" class="success">
						<p>
							<strong>
								<?php echo esc_html__('Capabilities Update Successfully.', 'addify_customer_manager'); ?>
							</strong>
						</p>
					</div>
					<?php
					$result = ob_get_clean();
					wp_send_json_success(array( 'success_message' => $result ));
				}
			}


		}
	}
	public function af_ure_delete_customer() {

		$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
		if (!wp_verify_nonce($nonce, 'addify_user_role_editor')) {
			wp_die(esc_html__('Security Violated!', 'addify_b2b'));
		}

		if (isset($_POST['user_id']) && get_userdata(sanitize_text_field($_POST['user_id']))) {

			$user_id = sanitize_text_field($_POST['user_id']);
			// wp_delete_user($user_id); // nosemgrep: audit.php.wp.security.xss.query-arg

			global $wpdb;

			$wpdb->delete($wpdb->usermeta, array( 'user_id' => $user_id ), array( '%d' ));

			$wpdb->delete($wpdb->users, array( 'ID' => $user_id ), array( '%d' ));

			wp_send_json(array( 'success' => true ));


		}
	}
	public function af_ure_delete_user_role() {

		$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
		if (!wp_verify_nonce($nonce, 'addify_user_role_editor')) {
			wp_die(esc_html__('Security Violated!', 'addify_b2b'));
		}

		$action_type  = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
		$old_role_key = isset($_POST['role_key']) ? sanitize_text_field($_POST['role_key']) : '';

		if (empty($old_role_key)) {
			return;
		}

		global $wp_roles;

		if ('show_popup_with_new_user_role' == $action_type) {

			$users = get_users( // nosemgrep: audit.php.wp.security.xss.query-arg
				array(
					'role' => $old_role_key,
				)
			);

			// $users = $user_query->get_results();

			if (empty($users)) {

				ob_start();
				$wp_roles->remove_role($old_role_key);

				if (!$wp_roles->is_role($old_role_key)) {
					?>
					<div class="af-ure-delete-and-assign-new-user-role-to-user-main-div">
						<i class="fa fa-close af-ure-close-send-bulk-email-popup-main-container" style="top:-18px;right:-20px;"></i>

						<div id="message" class="success">
							<p>
								<strong>
									<?php echo esc_html__('User role deleted successfully.', 'addify_customer_manager'); ?>
								</strong>
							</p>
						</div>
					</div>
					<?php
				} else {

					?>
					<div class="af-ure-delete-and-assign-new-user-role-to-user-main-div">
						<i class="fa fa-close af-ure-close-send-bulk-email-popup-main-container" style="top:-18px;right:-20px;"></i>

						<div id="message" class="success">
							<p>
								<strong>
									<?php echo esc_html__('Role deletion failed.', 'addify_customer_manager'); ?>
								</strong>
							</p>
						</div>
					</div>
					<?php
				}
				$result = ob_get_clean();
				wp_send_json(
					array(
						'html'    => $result,
						'success' => true,
						'action'  => 'delete',
					)
				);
				wp_die();


			} else {

				ob_start();
				?>
				<div class="af-ure-delete-and-assign-new-user-role-to-user-main-div af-ure-user-role-popup">
					<i class="fa fa-close af-ure-close-send-bulk-email-popup-main-container" style="top:-18px;right:-20px;"></i>
					<form data-action_type="delete" action="post" data-role_key="<?php echo esc_attr($old_role_key); ?>"
						class="af-ure-delete-and-assign-new-user-role-to-user">
						<?php echo esc_html__('Assign New User Role ', 'addify_customer_manager'); ?>
						<select name="new_select_user_role" id="">
							<option value="<?php echo esc_attr(get_option('default_role')); ?>">
								<?php echo esc_attr($wp_roles->get_names()[ get_option('default_role') ]); ?>
							</option>

							<?php
							foreach ($wp_roles->get_names() as $key => $role_name) {
								if (get_option('default_role') == $key) {
									continue;
								}
								?>
								<option value="<?php echo esc_attr($key); ?>">
									<?php echo esc_attr($role_name); ?>
								</option>
							<?php } ?>
						</select>
						<input type="hidden" name="role_key" value="<?php echo esc_attr($old_role_key); ?>">
						<input type="submit" value="Apply" name="apply" class="button button-primary">
					</form>
					<br>
					<p>
						<b>
							<?php echo esc_html__('Note: ', 'addify_customer_manager'); ?>
						</b>
						<i>
							<?php echo esc_html__('Assign the selected user role to customers who already had this user role', 'addify_customer_manager'); ?>
						</i>
					</p>
				</div>
				<?php
				$result = ob_get_clean();
				wp_send_json(array( 'html' => $result ));
				wp_die();
			}
		}



		if ('delete' == $action_type && isset($_POST['form'])) {
			parse_str(sanitize_text_field(( $_POST['form'] )), $form_data);
			if (empty($form_data['new_select_user_role']) || !isset($form_data['new_select_user_role'])) {
				return;
			}

			$new_select_user_role = $form_data['new_select_user_role'];
			$users                = get_users( // nosemgrep: audit.php.wp.security.xss.query-arg
				array(
					'role' => $old_role_key,
				)
			);

			// $users = $user_query->get_results();

			if (!empty($users)) {
				foreach ($users as $user) {
					$user->add_role($new_select_user_role);
				}
			}

			ob_start();
			$wp_roles->remove_role($old_role_key);

			if (!$wp_roles->is_role($old_role_key)) {
				?>
				<div class="af-ure-delete-and-assign-new-user-role-to-user-main-div">
					<i class="fa fa-close af-ure-close-send-bulk-email-popup-main-container" style="top:-18px;right:-20px;"></i>

					<div id="message" class="success">
						<p>
							<strong>
								<?php echo esc_html__('User role deleted successfully.', 'addify_customer_manager'); ?>
							</strong>
						</p>
					</div>
				</div>
				<?php
			} else {

				?>
				<div class="af-ure-delete-and-assign-new-user-role-to-user-main-div">
					<i class="fa fa-close af-ure-close-send-bulk-email-popup-main-container" style="top:-18px;right:-20px;"></i>

					<div id="message" class="success">
						<p>
							<strong>
								<?php echo esc_html__('Role deletion failed.', 'addify_customer_manager'); ?>
							</strong>
						</p>
					</div>
				</div>
				<?php
			}
			$result = ob_get_clean();
			wp_send_json(
				array(
					'html'    => $result,
					'success' => true,
					'action'  => 'delete',
				)
			);
			wp_die();

		}
	}
}
new Af_Ure_Ajax_Controller();
