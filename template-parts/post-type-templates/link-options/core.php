<?php

  // Links to categories or tags
  $isTag = true;
   
  // Extra link 
  $extraLink = array_key_exists('extra_link', $args) ? $args['extra_link'] : null;


  //$customTax = get_post_type() . '-' . $linkType;
  $items = [];

  $isSwiperLinks = $args['is_swiper_links'];

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

  if(array_key_exists('override_items', $args)) $items = $args['override_items']; // ability to pass in items manually

  if(empty($items)) return;
?>

<?php 

  $linkArgs = [
    'items' => $items,
    'is_tag' => $isTag,
    'extra_link' => $extraLink
  ];

 if($extraLink) $linkArgs['extra_link'] = $extraLink;

  $linkTemplate = $isSwiperLinks ? 'swiper-links' : 'block-links';

  get_template_part('template-parts/post-type-templates/link-options/'. $linkTemplate .'/links', null ,$linkArgs);

?>

