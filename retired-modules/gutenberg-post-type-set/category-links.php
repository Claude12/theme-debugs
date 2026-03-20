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
  <?php foreach ($categories as &$cat)  : ?>
    <a href="<?php echo get_category_link($cat); ?>"><?php echo $cat->name; ?></a>
  <?php unset($cat); endforeach; ?>
<?php endif; ?>