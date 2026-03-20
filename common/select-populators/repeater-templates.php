<?php
/**
 * 
 *  ACF select field to select from available repeater templates ( Ajax load more plugin)
 *   This automates data from repeater templates , but here you can also add overrides.
 */

add_filter('acf/load_field/name=km_repeater_templates_select', 'km_load_available_repeater_templates');

function km_load_available_repeater_templates($field){
  $field['choices'] = [];
  $automatedRepeaters = getAjaxLoadMoreRepeaters();
  $manualRepeaters = manualRepeaterTemplates();
  $repeaters =  array_merge($automatedRepeaters, $manualRepeaters);
  
  foreach($repeaters as $index => $repeater) {
      $field['choices'][$repeater['name']] = $repeater['alias'];
  }

  return $field;
}

