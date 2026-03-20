<?php

  // Links to categories or tags
  $isTag = true;
  $labels = get_post_type_object(get_post_type())->labels;
  $items = [];
 
  // is custom post type tag page
  if(is_tax( get_post_type() . '-tag' )) {

    $items = get_terms([
      'taxonomy'  => get_post_type() . '-tag',
      'hide_empty'  => false
    ]);
  }

  // is custom post type category page
  if(is_tax( get_post_type() . '-categories' ) || (!is_tax( get_post_type() . '-tag' ) && !is_tax( get_post_type() . '-categories' ))) {
    $items = get_categories([
      "hide_empty" => 1,
      'taxonomy' => get_post_type() . '-categories',
      "type"      => "post",      
      "orderby"   => "name",
      "order"     => "ASC" 
    ]);
    $isTag = false;
  }

  // handle default landing page 
  if(is_home() || is_category()){
    $items = get_categories();
    $isTag = false;
  }elseif(is_tag()){
    $items = get_tags();
  }


  if(is_admin()) {
    echo '<h2 style="width=100%; text-align:center; padding:20px 0;">Post type filter</h2>';
  }

  if(empty($items)) return;
?>

<?php 

  $linkArgs = [
    'acf-classes' => $args['acf-classes'],
    'items' => $items,
    'is_tag' => $isTag,
    'extra_link' => [
        'link' => get_post_type_archive_link(get_post_type()),
        'label' => 'All ' . $labels->name
    ]
  ];

  $linkTemplate = get_field('filter_type') ?: 'block-links';

  get_template_part('template-parts/gutenberg-post-type-filter/'. $linkTemplate . '/links', null, $linkArgs);

?>

