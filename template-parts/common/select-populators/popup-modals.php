<?php
/**
 *  Add to functions.php if it is not there
 * require get_template_directory().'/template-parts/widget-modal-popout/acf_select_populator.php'; 
 */

 
 add_filter('acf/load_field/name=km_popup_widget_select', 'km_load_available_popups');

function km_load_available_popups($field){
  $field['choices'] = [];
  $modals = get_field('popout_modal','option') ?: [];
 
  foreach($modals as $index => $modal) {

     if($modal['enabled']) {

      $all_pages = $modal['appears_on_all_pages'];
      $modalCount = ($index + 1);
      $selected_pages = is_bool($modal['selected_pages']) ? [] : array_values($modal['selected_pages']);
      $time_delay = !isset($modal['time_delay']) ? 0 : intval($modal['time_delay']);
      $modalIdentifier = $all_pages ? 'all' : implode("-", $selected_pages);
      $pages = $all_pages ? 'all' : implode("-", $selected_pages);
      $title = $modal['title'] ?: null;
      $value = 'km-modal-' . $modalIdentifier. '-number-' . $modalCount . '-at-' . $time_delay;
      $field['choices'][$value] =  $title ?: 'Modal number: ' . ($index + 1);
     }

  }

  return $field;
}