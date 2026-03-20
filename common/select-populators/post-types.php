<?php


/**
 * 
 *  ACF select field to select from available post types.
 *  Note: This list populates only publicly queriable post types. Amend args if that is not what you want.
 *  Ref: https://developer.wordpress.org/reference/functions/get_post_types/
 *  "post" added manually because it is the only built in we want.
 *  
 */

add_filter('acf/load_field/name=km_post_type_select', 'km_load_available_post_types');

function km_load_available_post_types($field){
  $field['choices'] = [];
  $args = [
    'public'   => true,
    '_builtin' => false,
    'publicly_queryable' => true
  ];

  $output = 'objects'; // 'names' or 'objects' (default: 'names')
  $operator = 'and'; // 'and' or 'or' (default: 'and')
  $postTypes = get_post_types( $args, $output, $operator );
  $manualPts = [
    [ 'name' => 'post', 'label' => 'Post' ]
  ];

  // handle those added manually
  foreach($manualPts as $index => $manualPt) {
    $field['choices'][$manualPt['name']] = $manualPt['label'];
  } 

  // Handle those registered within theme
  foreach($postTypes as $index => $postType) {
      $field['choices'][$postType->name] =   $postType->label;
  }

  return $field;
}

