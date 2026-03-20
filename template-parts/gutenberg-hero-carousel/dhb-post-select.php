<?php

/**
 *  Add to functions.php if it is not there
 * require get_template_directory().'/template-parts/gutenberg-hero-carousel/dhb-post-select.php'; 
 */
add_filter('acf/load_field/name=km_default_banners_post_type', 'km_load_banner_post_types');

function km_load_banner_post_types($field){
  // Post types that are not required
  $excludeTypes = [];

  $field['choices']['generic'] = 'Generic'; // fall back to all post types
  $field['choices']['404'] = '404'; // fall back to all post types
  $field['choices']['search'] = 'Search'; // fall back to all post types

  foreach ( get_post_types( ['show_in_nav_menus' => true], 'objects' ) as $postType ) {
    $name = $postType->name;
    $label = $postType->label;
    if(!in_array($name,$excludeTypes)) $field['choices'][$name] = $label;
  }
  return $field;
}