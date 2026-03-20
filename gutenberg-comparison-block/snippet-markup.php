
  <?php
    $sections = get_field('sections') ?: [];
    $divider = get_field('divider') ?: null;
    $dividerColour = get_field('divider_colour')['picker']['slug'];

    $mainBlockBg = '';
    if(is_array($sections[0]) && array_key_exists('bg_colour',$sections[0])) {
      $mainBlockBg = 'has-' . $sections[0]['bg_colour']['picker']['slug'] . '-background-colour';
    }
    
  ?>
  
  <section class="km-comparison-block<?php if($mainBlockBg) echo ' ' .$mainBlockBg; ?><?php if(!empty($args['acf-classes'])) echo ' ' . implode(' ',$args['acf-classes']); ?>">

    <div class="km-comparison-wrap">

      <?php foreach($sections as $index => $section) : ?>
      <?php
        $content = $section['content'];
        $ctas = $section['buttons']['links'];
        $hasDivider = ($divider !== null) && ($index % 2 === 0);

        $backgroundColour = $section['bg_colour']['picker']['slug'];
        $contentColour = $section['content_colour']['picker']['slug'];
        $linkColour = $section['link_colour']['picker']['slug'];
        $bulletColour = $section['bullet_colour']['picker']['slug'];

        $blockClasses = ['km-comparison-item'];
        $bgClasses = ['divider-bg'];
        $contentClasses = ['km-comparison-content', 'km-wysiwyg'];

        if(!$divider) {
          array_push($blockClasses, 'km-no-divider');
          array_push($bgClasses, 'km-no-divider');
        }
        if(!($index % 2 === 0)) array_push($bgClasses, 'divider-bg-right');
        if($index % 2 === 0) array_push($bgClasses, 'divider-bg-left');
 
        if($backgroundColour) array_push($bgClasses, 'has-' . $backgroundColour . '-background-colour');
        if($contentColour) array_push($contentClasses, 'has-' . $contentColour . '-colour');
        if($linkColour) array_push($contentClasses, 'has-' . $linkColour . '-link-colour');
        if($bulletColour) array_push($contentClasses, 'has-' . $bulletColour . '-bullet-colour');

      ?>

      <div class="<?php echo implode(' ', $blockClasses); ?>">

        <span class="<?php echo implode(' ', $bgClasses); ?>">&nbsp;</span>
 
        <?php if($section['image']) : ?>
          <div class="km-comparison-image">

            <?php // Module background Image ?>
              <?php
                get_template_part('template-parts/common/module-background-image/module-background-image', null, [
                  'image' => $section['image'],
                  'image_props' => $section['image_props'],
                ]);
              ?>

         
            <?php
              get_template_part('template-parts/common/module-overlay/module-overlay', null, [
                'overlay' => array_key_exists('overlay',$section) ? $section['overlay'] : null
              ]);
            ?>
          </div>
        <?php endif; ?>

        <?php if($content || !empty($ctas)) : ?>
          <div class="km-comparison-content-wrap">
            <div class="<?php echo implode(' ', $contentClasses); ?>"><?php echo $content; ?></div>
            <?php if(!empty($ctas)) : ?>
              <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
                'links' => $ctas,
                'classPrefix' => 'km-comparison', // optional
                'extraButtonClass' => 'cta-hover-x' // optional
              ] );
              ?>   
              <?php endif; ?>
          </div>
        <?php endif; ?>

      </div>

      <?php if($hasDivider) : ?>
        <span class="km-comparison-divider <?php if($dividerColour) echo 'has-' . $dividerColour . '-colour'; ?>">
          <span class="km-c-divider-text"><span><?php echo $divider; ?></span></span>
        </span>
      <?php endif; ?>

      <?php endforeach; ?>

    </div>
  </section> 

    

