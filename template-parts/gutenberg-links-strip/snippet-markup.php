<?php
    $title = get_field('title');
    $links = get_field('links') ?: [];
    $sectionMinHeight = get_field('section_minimum_height');

    // Classes
    $blockClasses = array_merge(['km-links-strip'],$args['acf-classes']);
    $titleClasses = ['km-ls-title'];
    $contentClasses = ['km-ls-content', 'km-wysiwyg'];
  
    // Colours
    $backgroundColour = get_field('bg_colour')['picker']['slug'];
    $titleColour = get_field('title_colour')['picker']['slug'];
   
    if($backgroundColour) array_push($blockClasses, 'has-' . $backgroundColour . '-background-colour');
    if($titleColour) array_push($titleClasses, 'has-' . $titleColour . '-colour');

    // Options
    $spacing = get_field('spacing') ?: null;
    if($spacing) array_push($blockClasses, 'km-ls-space-' . $spacing);

    if(empty($links)) return;
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

    <div class="km-ls-wrap" <?php if($sectionMinHeight) echo 'style="min-height:' . $sectionMinHeight . '"'; ?> >
  
      <?php // TITLE ?>
      <?php if($title) : ?>
        <h2 class="<?php echo implode(' ', $titleClasses); ?>"><?php echo $title; ?></h2>
      <?php endif; ?>

      <?php // ITEMS ?>
      <div class="km-ls-links">
        <?php foreach($links as $item) : ?>
          <?php
            $icon = $item['icon']['picker']['value'];
            $colour = $item['colour']['picker']['slug'] ? 'has-' . $item['colour']['picker']['slug'] . '-fill' : null;
            $link = $item['link'] ?: [];

            $linkLbl = array_key_exists('title', $link) ? $link['title'] : null;
            $linkUrl = array_key_exists('url', $link) ? $link['url'] : '#0';
            $linkTarget = array_key_exists('target', $link) ? $link['target'] === '_blank' ? 'target="_blank" rel="noopener"' : null : null;
          ?>
          <a class="km-ls-link<?php if($colour) echo ' ' . $colour; ?>" href="<?php echo $linkUrl; ?>" <?php if($linkTarget) echo $linkTarget; ?> <?php if($linkLbl) echo 'title="' . $linkLbl . '"'; ?>>
            <svg >
              <use href="#<?php echo $icon; ?>"/>
            </svg>
          </a>

        <?php endforeach; ?>
      </div>
  
    </div>
  </section> 

    