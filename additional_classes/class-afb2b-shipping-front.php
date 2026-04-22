<?php

if (! defined('ABSPATH') ) {
	exit; // restict for direct access
}


if (! class_exists('Addify_Role_Based_Shipping') ) {

	class Addify_Role_Based_Shipping extends Addify_B2B_Plugin {
	


		public function __construct() {

			add_filter('woocommerce_package_rates', array( $this, 'afb2b_user_role_shipping_methods' ), 100, 2);
		}

		public function afb2b_user_role_shipping_methods( $shipping_methods, $package ) {

			if (count($shipping_methods) < 2 ) {
				return $shipping_methods;
			}
			$role_base_methods = get_option('afb2b_shipping');

			if (is_user_logged_in() ) {
				$user       = wp_get_current_user();
				$user_roles = $user->roles;
				if (isset($role_base_methods[ current( $user_roles ) ]) ) {
					$user_role_base_methods = $role_base_methods[ current( $user_roles ) ];
				}
			} else {

				$user_role = 'guest';
				if (isset($role_base_methods[ $user_role ]) ) {
					$user_role_base_methods = $role_base_methods[ $user_role ];
				}
			}

			if (isset($user_role_base_methods) && ! empty($user_role_base_methods) ) {

				foreach ( $shipping_methods as $key => $method ) {
					if (! in_array($method->method_id, $user_role_base_methods) ) {
						unset($shipping_methods[ $key ]);
					}
				}
			}
			return $shipping_methods;
		}
	}

	new Addify_Role_Based_Shipping();
}
