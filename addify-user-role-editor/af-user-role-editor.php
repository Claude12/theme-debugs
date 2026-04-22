<?php
/**
 * Addify User Role Editor
 */

if (!defined('WPINC')) {
	die;
}

if (!class_exists('Addify_User_Role_Editor')) {

	class Addify_User_Role_Editor {
	

		public function __construct() {

			$this->af_ure_global_constents_vars();

			

			include AF_URE_PLUGIN_DIR . 'includes/af-ure-ajax-controller.php';

			if (is_admin()) {
				include AF_URE_PLUGIN_DIR . 'includes/admin/af-ure-admin.php';
			}
		}//end __construct()


		public function af_ure_global_constents_vars() {

			if (!defined('AF_URE_URL')) {
				define('AF_URE_URL', plugin_dir_url(__FILE__));
			}

			if (!defined('AF_URE_PLUGIN_DIR')) {
				define('AF_URE_PLUGIN_DIR', plugin_dir_path(__FILE__));
			}
		}
	}

	new Addify_User_Role_Editor();
}
