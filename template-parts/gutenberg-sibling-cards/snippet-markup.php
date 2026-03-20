 
<?php

  // Ensure page has parent page if nothing is displayed.
  $id = km_get_page_id();
  $pageObject = get_post( $id );

  $currentPage = $pageObject;
  $page = get_field('target_page') ?: $currentPage; // make sure page is assigned to parent ( page edit )
  $parentId = $page->post_parent; // 0 if has no parents
  $parentTitle = get_the_title($page) ?: 'Related pages';
  $additionalPages = get_field('additional_pages') ?: [];
  $layout = get_field('card_layout') ?: 'layout-3';

  $initBlock = array_merge(['km-sibling-cards'],$args['acf-classes']);
  if($layout) array_push($initBlock, 'cards-' . $layout);
  
  $content = get_field('content') ?: null;
  $ctas = get_field('buttons') ? get_field('buttons')['links'] ?: [] : [];

  // Colours
  $initialContentClasses = ['km-s-cards-content','km-wysiwyg'];
  $linkColour = get_field('link_colour')['picker']['slug'];
  $bulletColour = get_field('bullet_colour')['picker']['slug'];
  if($linkColour) array_push($initialContentClasses, 'has-' . $linkColour . '-link-colour');
  if($bulletColour) array_push($initialContentClasses, 'has-' . $bulletColour . '-bullet-colour');
  $contentClasses = createClasses($initialContentClasses, 'content_colour');

  $blockClasses = createClasses($initBlock, 'main_background','background-colour');
  $itemBg = get_field('item_background')['picker']['slug'] ? 'has-' . get_field('item_background')['picker']['slug'] . '-background-colour' : null;
  $itemColour = get_field('item_colour')['picker']['slug'] ? 'has-' . get_field('item_colour')['picker']['slug'] . '-colour' : null;
  $defaultImg = get_field('default_image') ?: '';

  $pages = get_pages( [
    'child_of'    => $parentId,
    'parent'      => $parentId, // used to ensure only first tier
    //'exclude'   => [ $page->ID ],// exclude current page
    'sort_column' => 'menu_order',
    'order'       => 'ASC'
  ]);

  $pages = array_merge($pages, $additionalPages);

  if(!$parentId) return null;

  // Exclude pages from result
  $excludePages = get_field('exclude_pages') ?: [];
  $excludeItems = [];
  foreach( $excludePages as $pg ) $excludeItems[$pg->ID] = $pg;

 ?>


<section class="<?php echo $blockClasses; ?>">

  <?php // Module background Image ?>
  <?php
    get_template_part('template-parts/common/module-background-image/module-background-image', null, [
      'image' => get_field('image'),
      'image_props' => get_field('image_props'),
    ]);
  ?>

 <?php // Module Overlay ?>
  <?php
    get_template_part('template-parts/common/module-overlay/module-overlay', null, [
      'overlay' => get_field('overlay')
    ]);
  ?>

  <?php // Content ?>
  <?php if($content) : ?>
    <div class="<?php echo $contentClasses; ?>"><?php echo $content; ?></div>
  <?php endif; ?>

  <div class="km-s-cards-wrap">
    <?php foreach( $pages as $page ): ?>
    <?php if(array_key_exists ( $page->ID , $excludeItems )) continue; ?>

    <?php
      $itemClasses = ['km-rm-item'];
      if($page->ID === $currentPage->ID) array_push($itemClasses, 'km-rm-active');
      if($itemBg) array_push($itemClasses, $itemBg);
      if($itemColour) array_push($itemClasses, $itemColour);
      $image = get_the_post_thumbnail_url($page) ?: $defaultImg;
      $altTitle = get_field('alt_title', $page->ID ) ?: null;
      $title = empty($altTitle) ? $page->post_title :  $altTitle;
      $cardContent = get_the_excerpt($page);
      $link = get_permalink($page);
    ?>

      <a class="km-s-cards-card" href="<?php echo $link; ?>" data-heading="<?php echo $title; ?>">

        <?php // IMAGE ?>
        <div class="km-s-cards-image">
          <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" />
        </div>

        <div class="km-s-cards-content-wrap">

          <?php // Title ?>
          <?php if($title): ?>
            <h3 class="km-s-cards-heading"><?php echo $title; ?></h3>
          <?php endif; ?>

          <?php // Content ?>
          <?php if($cardContent): ?>
            <div class="km-s-cards-card-content"><?php echo $cardContent; ?></div>
          <?php endif; ?>
     
        </div>

      </a>
    <?php endforeach; ?>
  </div>

  <?php // CTAS ?>
  <?php if(count($ctas) > 0) : ?>
    <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
        'links' => $ctas,
        'classPrefix' => 'km-s-cards',
        'extraButtonClass' => 'cta-hover-x'
      ] );
    ?>   
  <?php endif; ?>

</section>
