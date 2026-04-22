<?php
if (! defined('WPINC') ) {
	die;
}

if (!class_exists('Addify_B2B_Plugin_Front') ) {

	class Addify_B2B_Plugin_Front extends Addify_B2B_Plugin {
	

		public function __construct() {

			add_action('wp_enqueue_scripts', array( $this, 'afb2b_front_script' ));
		}



		

		public function afb2b_front_script() {

			wp_enqueue_style('afb2b-front', plugins_url('/assets/css/afb2b_front.css', __FILE__), false, '1.0');
		}
	}

	new Addify_B2B_Plugin_Front();
}
