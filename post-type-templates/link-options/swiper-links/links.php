
<?php 

  $items = $args['items'] ?: [];
  $isTag = $args['is_tag'] ?: false;
  $extraLink = array_key_exists('extra_link', $args) ? $args['extra_link']['link'] : get_post_type_archive_link(get_post_type());
  $extraLinkLbl = array_key_exists('extra_link', $args) ? $args['extra_link']['label'] : 'All';

  if(empty($items)) return;


?>

<div class="km-cpt-links">
  <div class="km-cptl-wrap">
  <div class="swiper-button-prev cptl-nav"></div>

    <?php // Show all items ?>
    <div class="km-cptl-all">
      <a href="<?php echo $extraLink; ?>">
        <span><?php echo $extraLinkLbl; ?></span>
      </a>
    </div>

    <?php // Tag or category items ?>
    <div class="swiper-container">
      <div class="swiper-wrapper">
        <?php foreach ($items as &$item)  : ?>
          <?php
            $link = $isTag ? get_tag_link($item) : get_category_link($item);
          ?>
          <div class="swiper-slide">
            <a class="km-cptl-link" href="<?php echo $link; ?>"><span><?php echo $item->name; ?></span></a>
          </div>
        <?php unset($item); endforeach; ?>
      </div>
      <div class="swiper-scrollbar"></div>
    </div>

    <div class="swiper-button-next cptl-nav"></div>

  </div>
</div>

