
    <?php

    $config = isset($args) ? $args : [];
    $isGlobal = array_key_exists('global', $config) ? $config['global'] : false;
    $globalData = get_field('site_info_section','option');
    $columns = $isGlobal ? $globalData['columns'] : get_field('columns');
  
    // Classes
    $acfClasses =  array_key_exists('acf-classes',$config) ? $config['acf-classes'] : [];
    $blockClasses = array_merge(['km-site-info'],$acfClasses);
    $titleClasses = ['km-si-title'];
    $contentClasses = ['km-si-content', 'km-wysiwyg'];
  
    // Colours
    $backgroundColour = $isGlobal ? $globalData['bg_colour']['picker']['slug'] : get_field('bg_colour')['picker']['slug'];
    $contentColour = $isGlobal ? $globalData['content_colour']['picker']['slug'] : get_field('content_colour')['picker']['slug'];
    $linkColour = $isGlobal ? $globalData['link_colour']['picker']['slug'] : get_field('link_colour')['picker']['slug'];
    $bulletColour = $isGlobal ? $globalData['bullet_colour']['picker']['slug'] : get_field('bullet_colour')['picker']['slug'];
  

    if($backgroundColour) array_push($blockClasses, 'has-' . $backgroundColour . '-background-colour');

    $colours = [
      'content' => '',
      'links' => '',
      'bullets' => ''
    ];

    if($contentColour) $colours['content'] =  'has-' . $contentColour . '-colour';
    if($linkColour) $colours['links'] = 'has-' . $linkColour . '-link-colour';
    if($bulletColour) $colours['bullets'] = 'has-' . $bulletColour . '-bullet-colour';

    ?>
  
  
  <div class="<?php echo implode(' ', $blockClasses); ?>">
    <?php

        // Module background Image
        get_template_part('template-parts/common/module-background-image/module-background-image', null, [
          'image' => $isGlobal ? $globalData['image'] : get_field('image'),
          'image_props' => $isGlobal ? $globalData['image_props'] : get_field('image_props'),
        ]);


        // Module Overlay
        get_template_part('template-parts/common/module-overlay/module-overlay', null, [
          'overlay' => $isGlobal ? $globalData['overlay'] : get_field('overlay') ?: null
        ]);

    ?>

    <div class="km-si-wrap">

        <?php foreach ($columns as &$column) : ?>
          <?php
            $buttons = array_key_exists('buttons',$column) ? $column['buttons'] : [];
            $buttonSet = array_key_exists('links',$buttons) ? $buttons['links'] : [];
          ?>

          <article class="km-si-column km-si-type-<?php echo $column['type']; ?>">
            <?php
            get_template_part('template-parts/gutenberg-site-info/'. $column['type'] .'/snippet-markup', null, [
              'data' => $column,
              'colours' => $colours
            ]);?>

            <?php if(!empty($buttonSet)) : ?>
              <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
                  'links' => $buttonSet,
                  'classPrefix' => 'km-si', // optional
                  'extraButtonClass' => 'cta-hover-x' // optional
                ] );
              ?>   
            <?php endif; ?>

          </article>

        <?php endforeach; ?>
    </div>
   
  </div> 

    