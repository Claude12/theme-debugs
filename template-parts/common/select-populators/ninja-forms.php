<?php
/**
 *  Add to functions.php if it is not there
 * require get_template_directory().'/template-parts/widget-modal-popout/acf_select_populator.php'; 
 */

 
 add_filter('acf/load_field/name=km_ninja_form_select', 'km_load_ninja_forms');

function km_load_ninja_forms($field){
  $field['choices'] = [];
  if(function_exists('Ninja_Forms')) {

    $forms = Ninja_Forms()->form()->get_forms();

    foreach($forms as $index => $form) {
      $formId = $form->get_id();
      $formName = $form->get_setting( 'title' );
      $field['choices'][$formId] = $formName .' ( ID: ' . $formId . ' )';
    }
  }

  return $field;
}