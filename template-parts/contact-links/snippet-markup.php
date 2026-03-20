<?php
  // Use:
  /*

  <?php get_template_part('template-parts/contact-links/snippet', 'markup', [
    'location' => 'bottom', // top or bottom
    'classes' => 'footer-links' //any additional classes for container
  ]); ?>
  */

  $thisLocation = array_key_exists('location', $args) ? $args['location'] : 'top';
  $extraClasses = array_key_exists('classes', $args) ? $args['classes'] : '';
  $links = get_field('link_set','option') ?: [];
  $defaultLink = [
    'title' => null,
    'url' => null,
    'target' => ''
  ];
  
  if(empty($links)) return;
  
?>

<section class="contact-links <?php echo $extraClasses; ?>">
  <ul class="cl-items">

    <?php foreach ($links as &$link) : ?> 

      <?php
        $enabled = $link['enabled'];
        $linkType = ' cl-' . $link['link_type'];
        $icon = $link['icon']['picker']['slug'] ?: null;
        $linkData = empty($link['link']) ? $defaultLink : $link['link'];
        $title = $linkData['title'];
        $url = $linkData['url'];
        $external = $linkData['target'] === '_blank' ? 'target="_blank" rel="noopener noreferrer"' : null;
        $loc = $link['link_location'];
        $iconIdentifier = $title ? sanitize_title($title) : $icon;
        $renderLink = false;


       if($loc === $thisLocation || $loc === 'both') {
         $renderLink = true;
       }

        if(!$enabled || !$renderLink || !$url) continue;
      ?>


      <li class="cl-item underline-hover<?php echo $linkType; ?> cl-<?php echo $iconIdentifier; ?>">
        <a href="<?php echo $url; ?>" <?php if($external) echo $external; ?>>

        <?php // ICON ?>
          <?php if($icon) : ?>
            <span class="cl-icon">
              <svg>
                <use href="#<?php echo $icon; ?>" />
              </svg>
            </span>
          <?php endif; ?>

          <?php // TITLE ?>
          <?php if($title) : ?>
            <span class="cl-title"><?php echo $title; ?></span>
          <?php endif;?>

        </a>
      </li>
    <?php endforeach; unset($link); ?>

  </ul>
</section>