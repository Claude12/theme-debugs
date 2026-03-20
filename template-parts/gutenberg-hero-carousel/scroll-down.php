
<?php
  
  $defaults = [
   'enabled' => false,
   'title' => null,
   'controls_colour' => [
    'picker' => [
      'slug' => 'white'
      ]
    ],
   'icon' => [
     'picker' => [
       'slug' => 'chevron-down'
     ]
   ]
  ];

  $data = isset($args) ? $args : $defaults;
  $enabled = $data['enabled'];
  $icon = $data['icon']['picker']['slug'];
  $title = $data['title'];

  $id = 'km-kcb-' . uniqid();
  $colour = $data['controls_colour'];
  $itemClasses = ['km-carousel-scroll-down','desktop-only'];

  if($colour['picker']['slug']) {
    array_push($itemClasses, 'has-' . $colour['picker']['slug'] . '-colour');
    array_push($itemClasses, 'has-' . $colour['picker']['slug'] . '-fill');
  } 

  if(!$enabled) return;
?>

<a href="#<?php echo $id; ?>" class="<?php echo implode(' ', $itemClasses); ?>">

  <?php if($icon) : ?>
    <svg>
      <use href="#<?php echo $icon; ?>" />
    </svg>
  <?php endif; ?>

  <?php if($title) : ?>
    <span><?php echo $title; ?></span>
  <?php endif; ?>
</a>

<div class="km-carousel-bottom" id="<?php echo $id; ?>">&nbsp;</div>