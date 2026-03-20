
    <?php
    $items = get_field('items') ?: [];
    $layout = get_field('layout') ?: 3;

    $blockClasses = array_merge(['km-counter-block'],$args['acf-classes']);
    $minHeight = get_field('block_minimum_height');
    $backgroundColour = get_field('bg_colour')['picker']['slug'];
    $colour = get_field('content_colour')['picker']['slug'];
    $numberColour = get_field('number_colour')['picker']['slug'];
    $animation = get_field('animation') ?: 'on';

    array_push($blockClasses, 'animation-' . $animation);

    array_push($blockClasses, 'item-layout-' . $layout);
    if($backgroundColour) array_push($blockClasses, 'has-' . $backgroundColour . '-background-colour');

    if(empty($items)) return;

    ?>
  
  <section class="<?php echo implode(' ', $blockClasses); ?>" <?php if($minHeight) echo 'style="min-height:' . $minHeight . '"';?>>
  
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

    <div class="km-cb-wrap">

        
      <?php foreach ($items as $item) : ?>
        <?php 
            $above = $item['above_number'] ?: null;
            $number = $item['number'] ?: 0;
            $below = $item['below_number'] ?: 0;
            $link = $item['link'] ?: null;
        ?>

        <?php if($link) : ?>
          <a class="km-cb-item<?php if($colour) echo ' has-' . $colour . '-colour';?>" href='<?php echo $link ?>'>
        <?php else : ?>
          <div class="km-cb-item<?php if($colour) echo ' has-' . $colour . '-colour';?>">
        <?php endif; ?>

          <?php if($above) : ?>
            <span class="km-cbi-text km-cb-upper<?php if($colour) echo ' has-' . $colour . '-colour'; ?>"><?php echo $above; ?></span>
          <?php endif; ?>

          <?php if($number) : ?>
            <span class="km-cbi-number secondary-font<?php if($numberColour) echo ' has-' . $numberColour . '-colour'; ?>" data-number="<?php echo $number; ?>"><?php echo $animation === 'on' ? 0 : $number; ?></span>
          <?php endif; ?>

          <?php if($below) : ?>
            <span class="km-cbi-text<?php if($colour) echo ' has-' . $colour . '-colour'; ?>"><?php echo $below; ?></span>
          <?php endif; ?>
          
        <?php if($link) : ?>
          </a>
        <?php else : ?>
          </div>
        <?php endif; ?>


      <?php endforeach; ?>


    </div>
  </section> 

    