<?php

  $sideLinks = get_field('widget_side_links','option');
  $enabledFlag = $sideLinks['visible'];
  $links = $sideLinks['links'];


  if(!$enabledFlag || empty($links)) return; 
?>

<!-- Widget : Side Links -->
<article class="widget-side-links desktop-only wsl-<?php echo $sideLinks['side']; ?>">
 <h2 class="sr-only">Side Links</h2>
 <div class="wsl-wrap">

  <ul class="wsl-set">
    
    <?php foreach ($links as &$item) : ?>

      <?php

        $defaultLink = [
          'title' => '',
          'url' => '',
          'target' => ''
        ];

        $link = $item['link'] ?: $defaultLink;
        $title = $link['title'] ?: null;
        $url =  $link['url'] ?: null ;
        $external = !empty($link['target']) ? 'target="_blank" rel="noopener"' : null;
        $icon = $item['icon']['picker']['value'];


        $iconColour = $item['icon_colour']['picker']['slug'] ?: null;
        $itemBg = $item['background_colour']['picker']['slug'] ?: null;

        $linkClasses = ['wsl-item'];
        if($icon === 'wsl-title') array_push($linkClasses, 'connect-icon');

        if($iconColour) array_push($linkClasses, 'has-' . $iconColour . '-fill');
        if($itemBg) array_push($linkClasses, 'has-' . $itemBg . '-background-colour');

      ?>

      <?php if($url && $icon) :  ?>
        <li class="<?php echo implode(' ', $linkClasses); ?>">
        <a href="<?php echo $url; ?>" <?php if($title) echo 'title="' . $title . '"'; ?> <?php if($external) echo $external; ?>>
          <span class="wsl-icon">
            <svg>
              <use xlink:href="#<?php echo $icon; ?>" />
            </svg>
          </span>
        </a>
        </li>
      <?php endif; ?>
    <?php unset($item); endforeach; ?>
    </ul>
  </div>
</article>


