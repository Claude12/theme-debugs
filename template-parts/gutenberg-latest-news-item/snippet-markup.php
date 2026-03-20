
<?php


 $title = get_field('title') ?: get_the_title();
 $content = get_field('content');
 $image = get_the_post_thumbnail_url(get_the_ID());
 $showSidebar = get_field('show_sidebar') === NULL ? true : get_field('show_sidebar');
 $initialBlockClasses = ['km-news-item','km-has-bullet-slug'];

 if($showSidebar) array_push($initialBlockClasses, 'km-sidebar-on');

 $blockClasses = createClasses($initialBlockClasses, 'section_background', 'background-colour');
 $wrapClasses = createClasses(['km-ni-wrap'], 'article_background', 'background-colour');

 $titleClasses = createClasses(['km-ni-title'], 'title_colour');
 $initialContentClasses = ['km-ni-content','km-wysiwyg'];
 $linkColour = get_field('link_colour')['picker']['slug'];
 $bulletColour = get_field('bullet_colour')['picker']['slug'];

 if($linkColour) array_push($initialContentClasses, 'has-' . $linkColour . '-link-colour');
 if($bulletColour) array_push($initialContentClasses, 'has-' . $bulletColour . '-bullet-colour');
 $contentClasses = createClasses($initialContentClasses, 'content_colour');


  
?>

<div class="<?php echo $blockClasses; ?>" >

  <article class="<?php echo $wrapClasses; ?>">

    <?php // Image ?>
    <?php if($image) : ?>
      <div class="km-ni-image">
        <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" />  
      </div>
    <?php endif; ?>

    <div class="km-ni-wrapper">
      <div class="km-ni-content-wrap">

          <?php // Title ?>
          <?php if($title) : ?>
            <h2 class="<?php echo $titleClasses; ?>"><?php echo $title; ?></h2>
          <?php endif; ?>

          <?php // Content ?>
          <?php if($content) : ?>
            <div class="<?php echo $contentClasses; ?>"><?php echo $content; ?></div>
          <?php endif; ?>

      </div>
      <?php if($showSidebar) get_template_part('sidebar'); ?>
    </div>
  </article>

  <?php if(get_field('show_navigation')) : ?>
    <div class="km-ni-navigation">
      <?php previous_post_link('%link', '<span class="km-ni-nav-item km-ni-prev" title="%title"><svg><use href="#single-post-prev" /></svg></span>'); ?>
      <a class="km-ni-all" href="<?php echo get_post_type_archive_link('post'); ?>"><span>All News</span></a>
      <?php next_post_link('%link', '<span class="km-ni-nav-item km-ni-next" title="%title"><svg><use href="#single-post-next" /></svg></span>'); ?>
    </div>
  <?php endif; ?>

</div>