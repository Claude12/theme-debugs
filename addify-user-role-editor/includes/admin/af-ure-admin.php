<?php
if (!defined('ABSPATH')) {
	die;
}

class Af_Ure_Admin {

	public function __construct() {
		
		add_action('admin_enqueue_scripts', array( $this, 'af_ure_enqueue_scripts' ));

		add_action('show_user_profile', array( $this, 'af_ure_add_custom_user_profile_field' ));
		add_action('edit_user_profile', array( $this, 'af_ure_add_custom_user_profile_field' ));

		add_action('profile_update', array( $this, 'af_ure_save_custom_user_profile_field' ), 10);
	}
	

	// Add a custom field to user edit profile
	public function af_ure_add_custom_user_profile_field( $user ) {


		$old_rules_array = $user->roles;
		wp_nonce_field('addify_ure_nonce', 'addify_ure_nonce');

		?>
		<table class="form-table">
			<tr>
				<th><label for="af_ure_secondary_role">
						<?php echo esc_html__('Secondary Role', 'addify_b2b'); ?>
					</label></th>
				<td>
					<select style="width:25%;" name="af_ure_secondary_role[]" id="af_ure_secondary_role" multiple
						class="af_ure_live_search">
						<?php
						$roles = wp_roles()->get_names();
						foreach ($roles as $role_slug => $role_name) {

							if (current($old_rules_array) == $role_slug) {
								continue;
							}

							?>
							<option value="<?php echo esc_attr($role_slug); ?>" <?php echo in_array($role_slug, $user->roles) ? 'selected' : ''; ?>>
								<?php echo esc_attr($role_name); ?>
							</option>
						<?php } ?>
					</select>
					<p class="description">
						<?php echo esc_html__('Select the secondary role for the user.', 'addify_b2b'); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}
	public function af_ure_save_custom_user_profile_field( $user_id ) {
		// Check current user's permissions
		if (!current_user_can('edit_user', $user_id) || is_ajax()) {
			return false;
		}

		$nonce = isset($_POST['addify_ure_nonce']) ? sanitize_text_field($_POST['addify_ure_nonce']) : '';
		if (isset($_POST['af_ure_secondary_role']) && !wp_verify_nonce($nonce, 'addify_ure_nonce')) {
			wp_die(esc_html__('Security Violated!', 'addify_b2b'));
		}

		$secondary_user_role = isset($_POST['af_ure_secondary_role']) ? sanitize_meta('', $_POST['af_ure_secondary_role'], '') : array( '' );


		// Remove the primary role from the roles array (if it exists)
		$user      = new WP_User($user_id);
		$main_role = current($user->roles);

		foreach ($user->roles as $new_role) {
			if ($new_role) {
				$user->remove_role($new_role);
			}
		}

		$user->set_role($main_role);

		foreach ($secondary_user_role as $secondary_user_role_key) {
			if ($secondary_user_role_key && current($user->roles) != $secondary_user_role_key) {
				$user->add_role($secondary_user_role_key);
			}
		}
	}

	public function af_ure_tab_callback() {

		global $active_tab;
		$nonce = isset($_GET['addify_user_role_editor']) ? sanitize_text_field($_GET['addify_user_role_editor']) : '';
		if (isset($_GET['form_data']) && !wp_verify_nonce($nonce, 'addify_user_role_editor')) {
			wp_die(esc_html__('Security Violated!', 'addify_b2b'));
		}

		if (isset($_GET['tab'])) {
			$active_tab = sanitize_text_field(wp_unslash($_GET['tab']));
		} else {
			$active_tab = 'add_user_role';
		}

		?>


		<div class="wrap" id="loco-admin">
			<h2 class="nav-tab-wrapper" style="display: none;">
				<?php settings_errors(); ?>

				<!-- Add User Role -->
				<a href="?page=af_ure&tab=add_user_role"
					class="nav-tab  <?php echo esc_attr($active_tab) === 'add_user_role' ? ' nav-tab-active' : ''; ?>">
					<?php esc_html_e('Add User Role', 'addify_b2b'); ?>
				</a>

			</h2>
			<span class="clear"></span>
		</div>

		<span class="clear"></span>
		<div class="loco-content">
			<?php

			if ('add_user_role' === $active_tab) {
				include AF_URE_PLUGIN_DIR . 'includes/admin/view/add-new-user-role.php';
			}
			?>
		</div>
		<?php
	}

	public function af_ure_enqueue_scripts() {

		$screen = get_current_screen();

		if ('b2b_page_af-ure' == $screen->id) {
		wp_enqueue_style('af_ure_af_a_nd_s_m_f_link_ty', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0', false);
		wp_enqueue_style('af_ure_admin_side_stylee_select2', AF_URE_URL . 'assets/css/select2.css', array(), '1.0', false);

		wp_enqueue_style('af_ure_admin_side_stylee', AF_URE_URL . 'assets/css/admin.css', array(), '1.0', false);
		wp_enqueue_style('af-ure-dataTables-style', 'https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css', array(), '1.12.1');

		wp_enqueue_script('af-ure-select2-scripts', AF_URE_URL . 'assets/js/select2.js', array( 'jquery' ), '1.0.2', false);
		wp_enqueue_script('af-ure-admin-scripts', AF_URE_URL . 'assets/js/admin.js', array( 'jquery' ), '1.0.2', false);
		wp_enqueue_script('af-ure-datatable-js', AF_URE_URL . 'assets/js/af-datatable.js', array( 'jquery' ), '1.0.1', true);
		wp_enqueue_script('af-ure-dataTables', 'https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js', array( 'jquery' ), '1.12.1', false);

			?>
		<div class="af-cmfw-loading-icon-div" style="display:none">
			<div class="af-cmfw-loading-icon-main-div">
				<img src="<?php echo esc_url(AF_URE_URL . 'assets/loading-icons/loading-icon-2nd.gif'); ?>"
					class="af-cmfw-loading-icon">
			</div>
		</div>
		<?php
		ob_start();
		global $wp_roles;
			?>
		<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-top" class="screen-reader-text">
				<?php echo esc_html__('Assign Role', 'addify_b2b'); ?>
			</label>
			<select name="af_ure_user_role_filter" id="bulk-action-selector-top">
				<option value="switch_to">
					<?php echo esc_html__('Switch to', 'addify_b2b'); ?>
				</option>
				<option value="primary">
					<?php echo esc_html__('Primary', 'addify_b2b'); ?>
				</option>
				<option value="secondary">
					<?php echo esc_html__('Secondary', 'addify_b2b'); ?>
				</option>
			</select>
		</div>
		<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-top" class="screen-reader-text">
				<?php echo esc_html__('Assign Role', 'addify_b2b'); ?>
			</label>
			<select name="af_ure_selected_user_role" id="bulk-action-selector-top">
				<?php foreach ($wp_roles->get_names() as $key => $value) : ?>
					<option value="<?php echo esc_attr($key); ?>">
						<?php echo esc_attr($value); ?>
					</option>
				<?php endforeach ?>
			</select>
			<input type="submit" name="af_ure_assign_user_role_to_selected_customer" id="doaction"
				class="button action af_ure_assign_user_role_to_selected_customer" value="<?php echo esc_html__('Apply', 'addify_b2b'); ?>">
		</div>

		<?php
		$user_role_filter = ob_get_clean();

		$aurgs = array(
			'ajaxurl'          => admin_url('admin-ajax.php'),
			'nonce'            => wp_create_nonce('addify_user_role_editor'),
			'user_role_filter' => $user_role_filter,
		);

		wp_localize_script('af-ure-admin-scripts', 'php_var', $aurgs);
		}
	}

	// Add custom fields to user profile in admin
}
new Af_Ure_Admin();