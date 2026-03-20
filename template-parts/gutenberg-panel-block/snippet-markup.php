<?php

  $intro = get_field('intro_text') ?: null;
  $title = get_field('title');
  $content = get_field('content');
  $panels = get_field('panels') ?: [];
  $buttons = get_field('button')['links'];

  // Classes
  $blockClasses = array_merge(['km-panel-block'],$args['acf-classes']);
  $mainContentLocation = get_field('main_content_location');
  $introClasses = createClasses(['km-pb-intro','km-pb-intro-' . $mainContentLocation], 'intro_colour');
  $titleClasses = ['km-pb-title','km-pb-title-' . $mainContentLocation];
  $contentClasses = ['km-pb-content', 'km-wysiwyg','km-pb-content-' . $mainContentLocation];

  // Colours
  $backgroundColour = get_field('bg_colour')['picker']['slug'];
  $titleColour = get_field('title_colour')['picker']['slug'];
  $contentColour = get_field('content_colour')['picker']['slug'];
  $linkColour = get_field('link_colour')['picker']['slug'];
  $bulletColour = get_field('bullet_colour')['picker']['slug'];

  // Panel colours
  $panelBackgroundColour = get_field('panel_bg_colour')['picker']['slug'];
  $panelTitleColour = get_field('panel_title_colour')['picker']['slug'];
  $panelContentColour = get_field('panel_content_colour')['picker']['slug'];
  $panelLinkColour = get_field('panel_link_colour')['picker']['slug'];
  $panelBulletColour = get_field('panel_bullet_colour')['picker']['slug'];

  if($backgroundColour) array_push($blockClasses, 'has-' . $backgroundColour . '-background-colour');
  if($titleColour) array_push($titleClasses, 'has-' . $titleColour . '-colour');
  if($contentColour) array_push($contentClasses, 'has-' . $contentColour . '-colour');
  if($linkColour) array_push($contentClasses, 'has-' . $linkColour . '-link-colour');
  if($bulletColour) array_push($contentClasses, 'has-' . $bulletColour . '-bullet-colour');

  // Config
  $panelBlockClasses = ['km-pb-panels'];
  $panelLayout = get_field('panel_layout');
  $panelContentLocation = get_field('panel_content_location');
  if($panelLayout) array_push($panelBlockClasses, 'km-pb-' . $panelLayout);

  if($panelContentLocation) array_push($panelBlockClasses, 'km-pbp-content-' . $panelContentLocation);
  
  $activateAccordion = get_field('activate_accordion');
  if($activateAccordion) array_push($blockClasses, 'activateAccordion');



?>

<section class="<?php echo implode(' ', $blockClasses); ?>"  data-activate-accordion="<?php echo $activateAccordion; ?>">

  <?php if($title || $content || $intro || !empty($panels)) : ?>
   <div class="km-pb-wrap">

      <?php // Intro ?>
      <?php if($intro) : ?>
        <div class="<?php echo $introClasses; ?>">
          <p><?php echo $intro; ?></p>
      </div>
      <?php endif; ?>

      <?php // TITLE ?>
      <?php if($title) : ?>
        <h2 class="<?php echo implode(' ', $titleClasses); ?>"><?php echo $title; ?></h2>
      <?php endif; ?>

      <?php // CONTENT ?>
      <?php if($content) : ?>
        <div class="<?php echo implode(' ', $contentClasses); ?>"><?php echo $content; ?></div>
      <?php endif; ?>

      <?php // Panels ?>
      <?php if(!empty($panels)) : ?>
        <div class="<?php echo implode(' ', $panelBlockClasses); ?>">
          <?php foreach ($panels as &$panel) : ?>
            <?php 
              $panelTitle = $panel['panel_title'];
              $panelContent = $panel['panel_content']; 
              
              // Classes
              $panelClasses = ['km-pb-panel'];
              $panelTitleClasses = ['km-pb-panel-title'];
              $panelContentClasses = ['km-pb-panel-content', 'km-wysiwyg'];

              if($panelTitleColour) array_push($panelTitleClasses, 'has-' . $panelTitleColour . '-colour');
              if($panelTitleColour) array_push($panelTitleClasses, 'has-' . $panelTitleColour . '-fill');
              if($panelContentColour) array_push($panelContentClasses, 'has-' . $panelContentColour . '-colour');
              if($panelLinkColour) array_push($panelContentClasses, 'has-' . $panelLinkColour . '-link-colour');
              if($panelBulletColour) array_push($panelContentClasses, 'has-' . $panelBulletColour . '-bullet-colour');
              if($panelBackgroundColour) array_push($panelClasses, 'has-' . $panelBackgroundColour . '-background-colour');

            ?>
            <article class="<?php echo implode(' ', $panelClasses); ?>">

              <?php // PANEL TITLE ?>
              <?php if($panelTitle) : ?>
                <button class="km-pb-trigger button-reset <?php echo implode(' ', $panelTitleClasses); ?>">
                  <span class="km-pb-t-wrap">
                    <span class="pb-title secondary-font"><?php echo $panelTitle; ?></span>
                    <span class="icon"><svg><use href="#arrow-down" /></svg></span>
                  </span>
                </button>
              <?php endif; ?>

              <?php // PANEL CONTENT ?>
              <?php if($panelContent) : ?>
                <div class="<?php echo implode(' ', $panelContentClasses); ?>">
                  <div class="km-pb-panel-content-wrap"><?php echo $panelContent; ?></div>
                </div>
              <?php endif; ?>


            </article>
          <?php endforeach; unset($panel); ?>
        </div>
      <?php endif; ?>

      <?php // Buttons ?>
      <?php if(!empty($buttons)) : ?>
        <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
          'links' => $buttons,
          'classPrefix' => $mainContentLocation, // optional
          'extraButtonClass' => 'cta-hover-x' // optional
        ] );
        ?>   
      <?php endif; ?>

    </div>
  <?php endif; ?>

</section>