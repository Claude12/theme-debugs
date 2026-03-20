<?php

// Typical usage.
/*
  <?php if(count($item['links'] ?: []) > 0) : ?>
    <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
        'links' => $item['links'],
        'classPrefix' => 'hb', // optional
        'extraButtonClass' => 'cta-hover-y' // optional
      ] );
    ?>   
  <?php endif; ?>
*/

?>

<?php
  $set = array_key_exists('links', $template_args) ? $template_args['links'] : null;
  $prefix = array_key_exists('classPrefix', $template_args) ? $template_args['classPrefix'] : null; // optional
  $extraClass = array_key_exists('extraButtonClass', $template_args) ? $template_args['extraButtonClass'] : null; // optional
  $wrapperClass = $prefix ? $prefix . '-ctas' : 'ctas';


  // Do not render set if there are not good links
  $renderSet = false;

  foreach ($set as &$linkItem) if(!empty($linkItem['link']) || !empty($linkItem['km_popup_widget_select'])) $renderSet = true;
  unset($linkItem); 
?>

<?php if(count($set) > 0 && $renderSet) : ?>
<div class="cta-set <?php echo $wrapperClass; ?>">
  <?php foreach ($set as $item) : ?>
    <?php 
      $data = is_array($item) ? $item : [];
      $linkClass = [$prefix ? $prefix . '-cta' : 'cta'];
      $titleClass = $prefix ? $prefix . '-cta-title' : 'cta-title';
      $iconClass = $prefix ? $prefix . '-cta-icon' : 'cta-icon';
      $show = array_key_exists('visible', $data) ? $data['visible'] : true;
      $link = array_key_exists('link', $data) ? is_array($data['link']) ? $data['link'] : [] : [];
      $label = array_key_exists('label', $data) ? $data['label'] : 'No Title Provided';
      $linkTitle = array_key_exists('title', $link) ? $link['title'] : 'No title provided';
      $linkUrl = array_key_exists('url', $link) ? $link['url'] : '#0';
      $external = array_key_exists('target', $link) ? $link['target'] : false;
      $icon = array_key_exists('icon', $data) ? $data['icon']['picker']['slug'] : false;
      $buttonStyle = array_key_exists('button_style', $data) ? $data['button_style']['button']['value'] : false;
      $buttonType = array_key_exists('button_type', $item) ? $item['button_type'] : 'link';
      $popupWidget = array_key_exists('km_popup_widget_select', $item) ? $item["km_popup_widget_select"] : null; 

      if($buttonStyle) array_push($linkClass, $buttonStyle);

    ?>
    <?php if($show && !empty($link) || !empty($popupWidget)) : ?>

      <?php if($buttonType === 'popup') : ?>
        <button class="<?php echo implode(' ', $linkClass); ?> reset-btn cta-button <?php if($extraClass) echo $extraClass; ?> km-cta-popup-trigger" data-modal="<?php echo $popupWidget; ?>">
      <?php else : ?>
        <a class="<?php echo implode(' ', $linkClass); ?> cta-button <?php if($extraClass) echo $extraClass; ?>" href="<?php echo $linkUrl; ?>" <?php if($external) echo 'target="_blank" rel="noopener noreferrer"';  ?>>
      <?php endif; ?>

        <span class="<?php echo $titleClass; ?>"><?php echo $buttonType === 'popup' ? $label : $linkTitle; ?></span>

        <?php if($icon): ?>
          <span class="svg-icon <?php echo $iconClass; ?>">
            <svg>
              <use href="#<?php echo $icon; ?>"></use>
            </svg>
          </span>
        <?php endif; ?>
        <?php // LINK ?>

      <?php if($buttonType === 'popup') : ?>
        </button>
      <?php else : ?>
       </a>
      <?php endif; ?>

    <?php endif; ?>

  <?php endforeach; ?>
</div>
<?php endif; ?>




