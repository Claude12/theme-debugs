<?php

$modals = get_field('popout_modal','option') ?: [];
if(!$modals || !is_array($modals) && !is_object($modals)) return;

foreach($modals as $index => $modal) {

  $modalData = [
    'count' => $index + 1,
    'extra_classes' => $modal['extra_classes'],
    'popup_type' => $modal['popup_type'],
    'enabled' => $modal['enabled'],
    'all_pages' => $modal['appears_on_all_pages'],
    'selected_pages' => $modal['selected_pages'],
    'title' => $modal['title'],
    'content' => $modal['content'],
    'time_delay' => $modal['time_delay'],
    'ninjaForm' => $modal['km_ninja_form_select'],
    'date_time_range' => $modal['date_time_range'],
    'range' => $modal['range'],
    'colours' => [
      'modal_bg' => $modal['modal_bg']['picker']['slug'],
      'modal_title' => $modal['modal_title']['picker']['slug'],
      'modal_content' => $modal['modal_content']['picker']['slug'],
      'modal_links' => $modal['modal_links']['picker']['slug'],
      'modal_bullets' => $modal['modal_bullets']['picker']['slug'],
      'modal_form_bg' => $modal['modal_form_bg']['picker']['slug'],
      'modal_close_bg' => $modal['modal_close_bg']['picker']['slug'],
      'modal_close_colour' => $modal['modal_close_colour']['picker']['slug']
    ],
    'centralise_content' => $modal['centralise_content'],
  ];

  if($modal['background_image']) {
    $modalData['background_image'] = $modal['background_image'];
  }

  if($modal['image_options']) {
    $modalData['image_options'] = $modal['image_options'];
  }


  if($modal['enabled']) get_template_part( 'template-parts/widget-modal-popout/modal-markup', null, [ 'modal' => $modalData ] );
}






