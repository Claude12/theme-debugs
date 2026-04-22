<?php
if (! defined('WPINC') ) {
	die;
}

if (! class_exists('AF_Tax_Front') ) {

	class AF_Tax_Front {
	


		public function __construct() {

			add_filter('option_woocommerce_tax_display_shop', array( $this, 'afrfq_change_tax_display' ), 100, 1);
			add_filter('option_woocommerce_tax_display_cart', array( $this, 'afrfq_change_tax_display' ), 100, 1);
		}

		public function afrfq_change_tax_display( $value ) {

			$user = wp_get_current_user();

			$current_role = current($user->roles);

			$afb2b_tax_display = get_option('afb2b_tax_display');

			if (is_user_logged_in() ) {


				if (isset($afb2b_tax_display[ $current_role ]) && !empty($afb2b_tax_display[ $current_role ]) ) {
					return $afb2b_tax_display[ $current_role ];
				}

			} elseif (!is_user_logged_in() ) {

				if (isset($afb2b_tax_display['guest']) && !empty($afb2b_tax_display['guest']) ) {
					return $afb2b_tax_display['guest'];
				}
			}

			

			return $value;
		}
	}

	new AF_Tax_Front();
}
