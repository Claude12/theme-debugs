<?php
/**
 * km_unicorn functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package km_unicorn
 */

if ( ! function_exists( 'km_unicorn_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function km_unicorn_setup() {
	
		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'km_unicorn' ),
			'menu-2' => esc_html__( 'Footer', 'footer-menu' ),
			'menu-3' => esc_html__( 'Right side', 'footer-menu-right' ),
		));

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
	}
endif;

if( function_exists('acf_add_options_page') ) {
 $option_page = acf_add_options_page(array(
  'page_title'  => 'Client Settings',
  'menu_title'  => 'Client Settings',
  'menu_slug'  => 'client-settings',
  'capability'  => 'edit_posts',
  'redirect'  => false
 ));
}

/**
 * 
 * Add options page only if logged in user has a role of developer
 * 
 */

 $user = wp_get_current_user();
 if(in_array('developer', (array) $user->roles) && function_exists('acf_add_options_page')){
	acf_add_options_page(array(
		'page_title' 	=> 'Site Builder',
		'menu_title'	=> 'Site Builder',
		'menu_slug' 	=> 'site-builder',
		'capability'	=> 'edit_posts',
		'redirect'		=> true,
		'icon_url' => 'dashicons-hammer',
	));
 } 

 /**
	*  Add options page for Popup modals.
	*/
	if( function_exists('acf_add_options_page') ) {
		$option_page = acf_add_options_page(array(
		 'page_title'  => 'Popup Modals',
		 'menu_title'  => 'Popup Modals',
		 'menu_slug'  => 'km-popup-modals',
		 'capability'  => 'edit_posts',
		 'redirect'  => false,
		 'icon_url' => 'dashicons-welcome-comments'
		));
	 }


	/**
	* Add Widget Area 
	* 	add_action( 'widgets_init', 'km_register_sidebars' );
	*/
	function km_register_sidebars() {

		register_sidebar( array(
			'name'          => 'Blog Sidebar',
			'id'            => 'post',
			'before_widget' => '<div id="%1$s" class="km-widget-container %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5 class="km-widget-title">',
			'after_title'   => '</h5>',
		) );
	}



	// Booking System
	require( get_template_directory() . '/custom/register-events.php' );
	require( get_template_directory() . '/custom/planner-email.php' );

	// options pages

	if(function_exists('acf_add_options_page')) { 

		acf_add_options_page('Planner');
		acf_add_options_sub_page( array( 'title' => 'Dates', 'parent' => 'acf-options-planner' ) );
		acf_add_options_sub_page( array( 'title' => 'Ceremony Plan', 'parent' => 'acf-options-planner' ) );
		acf_add_options_sub_page( array( 'title' => 'Number of guests', 'parent' => 'acf-options-planner' ) );
		acf_add_options_sub_page( array( 'title' => 'Dressing Rooms', 'parent' => 'acf-options-planner' ) );
		acf_add_options_sub_page( array( 'title' => 'Canapés', 'parent' => 'acf-options-planner' ) );
		acf_add_options_sub_page( array( 'title' => 'Breakfast', 'parent' => 'acf-options-planner' ) );
		acf_add_options_sub_page( array( 'title' => 'Drinks', 'parent' => 'acf-options-planner' ) );
		acf_add_options_sub_page( array( 'title' => 'Extended Bar', 'parent' => 'acf-options-planner' ) );
		acf_add_options_sub_page( array( 'title' => 'Evening Menu', 'parent' => 'acf-options-planner' ) );
		acf_add_options_sub_page( array( 'title' => 'Bedrooms', 'parent' => 'acf-options-planner' ) );
		acf_add_options_sub_page( array( 'title' => 'Personal Details', 'parent' => 'acf-options-planner' ) );
		acf_add_options_sub_page( array( 'title' => 'Terms', 'parent' => 'acf-options-planner' ) );
		acf_add_options_page('Global');

	}