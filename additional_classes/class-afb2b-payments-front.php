<?php

if (! defined('ABSPATH') ) {
	exit; // restict for direct access
}


if (! class_exists('Addify_Role_Based_Payments') ) {

	class Addify_Role_Based_Payments extends Addify_B2B_Plugin {
	


		public function __construct() {

			add_filter('woocommerce_available_payment_gateways', array( $this, 'adb2b_user_role_payments_methods' ), 10, 1);
		}

		public function adb2b_user_role_payments_methods( $payment_methods ) {

			if (count($payment_methods) < 2 ) {
				return $payment_methods;
			}
			$role_base_methods = get_option('afb2b_payments');

			if (is_user_logged_in() ) {

				$user = wp_get_current_user();

				$user_roles = $user->roles;

				if (isset($role_base_methods[ current( $user_roles ) ] ) ) {
					$user_role_base_methods = $role_base_methods[ current( $user_roles ) ];
				}

			} else {

				$user_role = 'guest';
				if (isset($role_base_methods[ $user_role ]) ) {
					$user_role_base_methods = $role_base_methods[ $user_role ];
				}
			}

			if (isset($user_role_base_methods) && ! empty($user_role_base_methods) ) {

				foreach ( $payment_methods as $key => $method ) {
					if (! in_array($key, $user_role_base_methods) ) {
						unset($payment_methods[ $key ]);
					}
				}
			}
			
			return $payment_methods;
		}
	}

	new Addify_Role_Based_Payments();
}
