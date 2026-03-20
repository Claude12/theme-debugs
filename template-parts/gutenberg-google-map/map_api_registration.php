<?php

// Include in function.php if it is not already there
//if(file_exists(get_stylesheet_directory() . '/template-parts/gutenberg-google-map/map_api_registration.php')) require_once( get_stylesheet_directory() . '/template-parts/gutenberg-google-map/map_api_registration.php');

function my_acf_google_map_api( $api ){
  $api['key'] = get_field('google_api_key','option');
  return $api;
}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');