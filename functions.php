<?php

require get_template_directory().'/constants.php'; 
require get_template_directory().'/includes/general_function.php'; 

if( function_exists('acf_register_block')) require get_template_directory().'/assets/gutenberg-acf/gacf-bundle.php'; // Register gutenberg blocks
add_action('acf/init', 'my_acf_init');
add_filter( 'block_categories', 'gutenberg_ketchup_modules' , 10, 2);

require get_template_directory().'/includes/enqueue_scripts.php'; //Enqueue Scripts and style
require get_template_directory().'/includes/themesetup.php'; //Theme setup and widgets , custom option pages 

require get_template_directory().'/includes/custom_image_size.php';//custom image sizes
require get_template_directory().'/includes/ajax.php'; // All ajax functions

// Load Post types
km_load_post_types();

// Load ACF selects
km_load_acf_selects();

if(file_exists(get_stylesheet_directory() . '/theme-shortcodes.php')) {
  require_once( get_stylesheet_directory() . '/theme-shortcodes.php');
}

if(file_exists(get_stylesheet_directory() . '/template-parts/gutenberg-google-map/map_api_registration.php')) {
  require_once( get_stylesheet_directory() . '/template-parts/gutenberg-google-map/map_api_registration.php');
}


// Post type set block
if(file_exists(get_stylesheet_directory() . '/template-parts/gutenberg-post-type-set/post_type_acf_field.php')) {
  require get_stylesheet_directory() . '/template-parts/gutenberg-post-type-set/post_type_acf_field.php';
}


remove_filter( 'the_content', 'wpautop');

add_action('init', 'createRole');


if(function_exists('acf_register_block')) {

  // Set environment constant ( dev or live )
  add_action('init', 'km_define_env');

  // Populate bullets and colours from site builder setup.
  add_action('init', 'km_populate_site_builder_colours');
}

add_action( 'admin_footer', 'admin_footer_data' );
add_action( 'after_setup_theme', 'km_unicorn_setup' );
add_action( 'wp_enqueue_scripts', 'km_unicorn_scripts' );
add_action( 'wp_enqueue_scripts', 'load_custom_fonts' ); 
add_action( 'enqueue_block_assets', 'km_common_scripts');
add_action('wp_head', 'km_hook_critical_assets');
add_action('admin_head', 'km_hook_critical_assets');

remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

add_action('admin_init', 'df_disable_comments_dashboard');
add_action('admin_init', 'df_disable_comments_post_types_support');
add_action('init', 'df_disable_comments_admin_bar');
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');
add_action('admin_menu', 'df_disable_comments_admin_menu'); 
add_filter('login_errors', 'inn_wrong_login');
add_filter('the_generator', 'wpt_remove_version');
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);
add_filter('acf/fields/page_link/query', 'remove_draft_archive_link', 10, 3);
add_filter( 'admin_post_thumbnail_html', 'add_featured_image_instruction');
add_filter('upload_mimes', 'cc_mime_types');

add_action( 'pre_get_posts', 'km_search_rules', 10, 2 );
add_post_type_support( 'page', 'excerpt' );

// Sidebar
add_action( 'widgets_init', 'km_register_sidebars' );
add_shortcode('categoryposts', 'km_get_related_category_posts');
add_filter('widget_text', 'do_shortcode');

add_filter( 'replace_editor', 'enable_gutenberg_editor_for_blog_page', 10, 2 );