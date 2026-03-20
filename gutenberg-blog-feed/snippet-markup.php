<?php


  $title = get_field('title');
  $content = get_field('content');
  $posts = get_field('blog_items') ?: [];
  $buttons = get_field('button')['links'] ?: [];

  // Classes
  $blockClasses = array_merge(['blog-feed'],$args['acf-classes']);
  $titleClasses = ['bf-title'];
  $contentClasses = ['bf-content', 'km-wysiwyg'];

  // Colours
  $backgroundColour = get_field('bg_colour')['picker']['slug'];
  $titleColour = get_field('title_colour')['picker']['slug'];
  $contentColour = get_field('content_colour')['picker']['slug'];
  $linkColour = get_field('link_colour')['picker']['slug'];
  $bulletColour = get_field('bullet_colour')['picker']['slug'];

  if($backgroundColour) array_push($blockClasses, 'has-' . $backgroundColour . '-background-colour');
  if($titleColour) array_push($titleClasses, 'has-' . $titleColour . '-colour');
  if($contentColour) array_push($contentClasses, 'has-' . $contentColour . '-colour');
  if($linkColour) array_push($contentClasses, 'has-' . $linkColour . '-link-colour');
  if($bulletColour) array_push($contentClasses, 'has-' . $bulletColour . '-bullet-colour');

?>


 <section class="<?php echo implode(' ', $blockClasses); ?>">

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

  <?php if($title || $content || !empty($buttons) || !empty($posts)) : ?>
   <div class="bf-wrap bf-top">

    <?php // TITLE ?>
    <?php if($title) : ?>
      <h2 class="<?php echo implode(' ', $titleClasses); ?>"><?php echo $title; ?></h2>
    <?php endif; ?>

    <?php // CONTENT ?>
    <?php if($content) : ?>
      <div class="<?php echo implode(' ', $contentClasses); ?>"><?php echo $content; ?></div>
    <?php endif; ?>
   </div>

    <?php // BLOG FEED ?>

    <div class="km-post-type-feed <?php echo 'km-post-feed'; ?>">
      <div class="km-ptf-wrap">
        <?php
          foreach ($posts as $post)  {
            get_template_part('template-parts/post-type-templates/post/single-markup', null, ['post' => $post]);
          }
        ?>
      </div>
    </div>

    <?php // Buttons ?>
      <?php if(!empty($buttons)) : ?>
      <div class="bf-wrap bf-bottom">

        <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
          'links' => $buttons,
          'classPrefix' => 'km-csh', // optional
          'extraButtonClass' => 'cta-hover-x' // optional
        ] );
        ?>   
      </div>
    <?php endif; ?>

  <?php endif; ?>


</section>