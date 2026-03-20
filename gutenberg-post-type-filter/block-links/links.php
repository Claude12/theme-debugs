<?php 
  $acfClasses = $args['acf-classes'];
  $items = $args['items'] ?: [];
  $isTag = $args['is_tag'] ?: false;

  $extraLink = array_key_exists('extra_link', $args) ? $args['extra_link']['link'] : get_post_type_archive_link(get_post_type());
  $extraLinkLbl = array_key_exists('extra_link', $args) ? $args['extra_link']['label'] : 'All';

  if(empty($items)) return;
 
  $category = get_queried_object();
  $catId = is_object($category) ? $category->term_id : null;

?>

<article class="km-post-type-links<?php if(!empty($acfClasses)) echo ' ' . implode(' ',$acfClasses); ?>">
  <div class="km-ptl-wrap">
    <ul class="km-ptl-list">
    <li>
      <a class="km-ptl-item<?php if(!$catId) echo ' km-ptl-active'; ?>" href="<?php echo $extraLink; ?>"><span><?php echo $extraLinkLbl; ?></span></a>
    </li>
    <?php foreach ($items as &$item)  : ?>
      <?php
        $link = $isTag ? get_tag_link($item) : get_category_link($item);
      ?>
      <li>
          <a class="km-ptl-item <?php if($item->term_id === $catId) echo "km-ptl-active"; ?>" href="<?php echo $link; ?>"><span><?php echo $item->name; ?></span></a>
      </li>
      <?php unset($item); endforeach; ?>
    </ul>
  </div>
</article>
