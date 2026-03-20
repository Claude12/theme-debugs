<?php

  $slides = $args['slides'] ?: [];
  if(empty($slides)) return;

  $wrapClasses = ['km-content-carousel'];
  $contentBg = $args['content_background']['picker']['slug'];
  if($contentBg) array_push($wrapClasses, 'has-' . $contentBg . '-background-colour');

  $contentClasses = ['km-carousel-sc-content','km-has-bullet-slug'];
  $contentColour = $args['content_colour']['picker']['slug'];
  if($contentColour) array_push($contentClasses, 'has-' . $contentColour . '-colour');

  $linkColour = $args['link_colour']['picker']['slug'];
  if($linkColour) array_push($contentClasses, 'has-' . $linkColour . '-link-colour');

  $bulletColour = $args['bullets_colour']['picker']['slug'] ?: null;


  $id = km_get_page_id();
  $pageObject = get_post( $id );

?>

<div class="<?php echo implode(' ', $wrapClasses) ?>">
  <?php foreach ($slides as $key => &$slide) : ?>
    <?php
        $content = $slide['content'];
        $links = array_key_exists('ctas_links', $slide) ? $slide['ctas_links'] : [];

        if(!$content && empty($links)) {
          continue;
        }
    ?>

      <div class="km-carousel-slide-content<?php if($content || !empty($links)) echo ' km-carousel-has-content'; ?>" data-slide="<?php echo $key; ?>">
        <div class="km-wysiwyg">
          
          <?php // Content ?>
          <?php if($content) : ?>
            <div class="<?php echo implode(' ', $contentClasses) ?>" <?php if($bulletColour) echo 'km-bullet-slug="' . $bulletColour . '"'; ?>><?php echo $content; ?></div>
          <?php endif; ?>

          <?php // CTAS ?>
          <?php if(!empty($links)) : ?>
            <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
                'links' => $links,
                'classPrefix' => 'km-carousel', // optional
                'extraButtonClass' => 'cta-hover-x' // optional
              ] );
            ?>   
          <?php endif; ?>
        </div>
      </div>

  <?php endforeach; unset($slide); ?>
</div>

