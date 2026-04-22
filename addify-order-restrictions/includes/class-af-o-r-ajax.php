<?php

defined('ABSPATH') || exit;

class AF_O_R_Ajax {

	public function __construct() {

		// Users search.
		add_action( 'wp_ajax_af_o_r_search_users', array( $this, 'search_users' ) );
	}

	/**
	 * Search users by Ajax.
	 */
	public function search_users() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $nonce, 'af-o-r-ajax-nonce' ) ) {
			die( 'Failed Ajax security check!' );
		}

		$s = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';
		
		$users = new WP_User_Query(
			array(
				'search'         => '*' . esc_html( $s ) . '*',
				'search_columns' => array(
					'user_login',
					'user_nicename',
					'user_email',
					'user_url',
				),
			)
		);

		$users_found = $users->get_results();
		$data_array  = array();

		if ( ! empty( $users_found ) ) {
			foreach ( $users_found as $user ) {
				$title        = $user->display_name . '(' . $user->user_email . ')';
				$data_array[] = array( $user->ID, $title ); // array( User ID, User name and email ).
			}
		}

		wp_send_json( $data_array );
		die();
	}
}

new AF_O_R_Ajax();
