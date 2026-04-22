<?php
/*
 * Products Visibility by User Roles
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Addify_Products_Visibility' ) ) {

	class Addify_Products_Visibility {

		public function __construct() {

			$this->afpvu_global_constents_vars();
			register_activation_hook(__FILE__, array( $this, 'afpvu_add_quote_page' ));


			if (is_admin() ) {
				include_once AFPVU_PLUGIN_DIR . 'class_afpvu_admin.php';
			} else {
				include_once AFPVU_PLUGIN_DIR . 'class_afpvu_front.php';
			}
		}

		public function afpvu_global_constents_vars() {

			if (!defined('AFPVU_URL') ) {
				define('AFPVU_URL', plugin_dir_url(__FILE__));
			}

			if (!defined('AFPVU_BASENAME') ) {
				define('AFPVU_BASENAME', plugin_basename(__FILE__));
			}

			if (! defined('AFPVU_PLUGIN_DIR') ) {
				define('AFPVU_PLUGIN_DIR', plugin_dir_path(__FILE__));
			}
		}


		public function afpvu_add_quote_page() {

			if (null == get_page_by_path('af-product-visibility')) {

				$new_page = array(
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'post_author'    => 1,
					'post_name'      => esc_html__('af-product-visibility', 'addify_b2b'),
					'post_title'     => esc_html__('Products Visibility', 'addify_b2b'),
					'post_content'   => '[addify-product-visibility-page]',
					'post_parent'    => 0,
					'comment_status' => 'closed',
				);

				$page_id = wp_insert_post($new_page);

				update_option('addify_pvu_page_id', $page_id);
			} else {
				$page_id = get_page_by_path('af-product-visibility');
				update_option('addify_pvu_page_id', $page_id->ID);
			}
		}
	}

	new Addify_Products_Visibility();

}
