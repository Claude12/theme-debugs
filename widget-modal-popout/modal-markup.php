<?php

 $modal = $args['modal'];
 $extraClasses = $modal['extra_classes'];
 $modalCount = $modal['count'];
 $popupType = $modal['popup_type'];
 $all_pages = $modal['all_pages'];
 $selected_pages = is_bool($modal['selected_pages']) ? [] : array_values($modal['selected_pages']);
 $title = empty($modal['title']) ? null : $modal['title'] ;
 $content = empty($modal['content']) ? null : $modal['content'];
 $time_delay = !isset($modal['time_delay']) ? 0 : intval($modal['time_delay']);
 $ninjaForm = !empty($modal['ninjaForm']) ? $modal['ninjaForm'] : null;
 $current_page_id = get_queried_object_id();
 $renderModal = in_array( $current_page_id , $selected_pages) || $all_pages == true; // If page id array contains current ID or all pages is set to true

 $rangeEnabled = $modal['date_time_range'];
 $range = $modal['range'];

  // Check date and time range
  if($rangeEnabled && $popupType === 'timed'){
    $now = date('YmdHis');
    $from = $range['from'];
    $to = $range['to'];
    if(!($from <= $now && $to >= $now)) return;
  }

 if(!$renderModal && $popupType === 'timed') return;

 $modalIdentifier = $all_pages ? 'all' : implode("-", $selected_pages);

  // Classes
  $modalWrap = ['ktm-wrap'];
  $closeBtn = ['ktm-close'];
  $closeX = ['ktmc-line'];
  $titleClasses = ['ktmc-title'];
  $contentClasses = ['ktm-content','km-wysiwyg'];
  $formClasses = ['ktm-body'];

  if($modal['centralise_content']) array_push($modalWrap, 'ktm-centralised-content');

  // Colours
  $backgroundColour = $modal['colours']['modal_bg'] ?: get_field('global_modal_bg','option')['picker']['slug'];
  $titleColour = $modal['colours']['modal_title'] ?: get_field('global_modal_title','option')['picker']['slug'];
  $contentColour = $modal['colours']['modal_content'] ?: get_field('global_modal_content','option')['picker']['slug'];
  $linkColour = $modal['colours']['modal_links'] ?: get_field('global_modal_links','option')['picker']['slug'];
  $bulletColour = $modal['colours']['modal_bullets'] ?: get_field('global_modal_bullets','option')['picker']['slug'];
  $formBackground = $modal['colours']['modal_form_bg'] ?: get_field('global_modal_form_bg','option')['picker']['slug'];
  $closeBtnBg = $modal['colours']['modal_close_bg'] ?: get_field('global_modal_close_bg','option')['picker']['slug'];
  $closeBtnColour = $modal['colours']['modal_close_colour'] ?: get_field('global_modal_close_colour','option')['picker']['slug'];

  if($backgroundColour) array_push($modalWrap, 'has-' . $backgroundColour . '-background-colour');
  if($closeBtnBg) array_push($closeBtn, 'has-' . $closeBtnBg . '-background-colour');
  if($closeBtnColour) array_push($closeBtn, 'has-' . $closeBtnColour . '-border-colour');
  if($closeBtnColour) array_push($closeX, 'has-' . $closeBtnColour . '-background-colour');
  if($titleColour) array_push($titleClasses, 'has-' . $titleColour . '-colour');
  if($contentColour) array_push($contentClasses, 'has-' . $contentColour . '-colour');
  if($linkColour) array_push($contentClasses, 'has-' . $linkColour . '-link-colour');
  if($bulletColour) array_push($contentClasses, 'has-' . $bulletColour  . '-bullet-colour');
  if($formBackground) array_push($formClasses, 'has-' . $formBackground . '-background-colour');

  // Background Image
  $bgImage = array_key_exists('background_image',$modal) ? $modal['background_image'] : null;

  $imageOptions = array_key_exists('image_options',$modal) ? $modal['image_options'] : get_field('global_image_options','option');
  $imageProps = [];

  if($bgImage) {
    $imageProps['background-image'] = 'url(' . $bgImage . ')';
    $imageProps['background-position'] = $imageOptions['bg_position'];
    $imageProps['background-repeat'] = $imageOptions['bg_repeat'];
    $imageProps['background-size'] = $imageOptions['bg_size'];
  }
?>

<article class="km-timed-modal km-modal-<?php echo $modalIdentifier; ?><?php if(!empty($extraClasses)) echo ' ' . $extraClasses; ?>" data-modal-type="<?php echo $popupType; ?>" data-km-modal-time="<?php echo $time_delay; ?>" data-km-modal-sskey="<?php echo 'km-modal-' . $modalIdentifier. '-number-' . $modalCount . '-at-' . $time_delay;  ?>">

  <div class="ktmc-cross-wrap">

    <button class="<?php echo implode(' ', $closeBtn); ?>">
      <span class="ktmc-cross">
        <span class="<?php echo implode(' ', $closeX); ?> ktmc-line-1">&nbsp;</span>
        <span class="<?php echo implode(' ', $closeX); ?> ktmc-line-2">&nbsp;</span>
      </span>
    </button>

    <div class="<?php echo implode(' ', $modalWrap); ?>">

      <div class="kmtc-inner-wrap">

        <?php // Image ?>
        <?php if($bgImage) :?>
          <div class="ktmc-bg-image" <?php echo populateStyleAttribute($imageProps); ?> >&nbsp;</div>
        <?php endif; ?>

        <div class="ktm-head">
          <?php if($title) : ?>
            <h2 class="<?php echo implode(' ', $titleClasses); ?>"><?php echo $title; ?></h2>
          <?php endif; ?>

          <?php if($content) : ?>
            <div class="<?php echo implode(' ', $contentClasses); ?>"><?php echo $content; ?></div>
          <?php endif; ?>
        </div>

          <?php if($ninjaForm && function_exists('Ninja_Forms')) : ?>
            <div class="<?php echo implode(' ', $formClasses); ?>"><?php Ninja_Forms()->display($ninjaForm); ?></div>
          <?php endif; ?>

      </div>
    </div>
  </div>
</article>


