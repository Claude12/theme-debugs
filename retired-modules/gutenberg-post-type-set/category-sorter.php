<?php 
  $customTax = $template_args['post_type'] . '-categories';
  $taxonomy = taxonomy_exists($customTax) ? $template_args['post_type'] . '-categories' : null;
  $args = [
      "hide_empty" => 1,
      'taxonomy' => $taxonomy ?: 'category',
      "type"      => "post",      
      "orderby"   => "name",
      "order"     => "ASC" 
    ];

    $categories = get_categories($args);
?>


<?php if(!empty($categories)) : ?>
  <article class="km-ptc-categories">
  <div class="km-ptc-cat-wrap">
      <div class="km-ptc-cat-list">
          <button class="km-ptc-btn km-ptc-cat" data-category-id="km-all-cats">
            <span>All</span>
          </button>
        <?php foreach ($categories as &$cat)  : ?>
          <button class="km-ptc-btn km-ptc-cat" data-category-id="<?php echo $cat->cat_ID; ?>">
            <span><?php echo $cat->name; ?></span>
          </button>
        <?php unset($cat); endforeach; ?>
      </div>
  </div>
  </article>
<?php endif; ?>