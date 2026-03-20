
  <?php

    $acfClasses = isset($args['acf-classes']) ? $args['acf-classes'] : [];
    $randomTestimonial = get_posts(array(
      'orderby'     => 'rand',
      'post_type'   => 'testimonial'
    ));
    $specificTestimonial = get_field('testimonials') ?: []; // ACF group name
    $slides = [];
    $sectionBg = get_field('background')['picker']['slug'];

    $defaultSwiperConfig = [
      'autoplay' => true,
      'slide_interval' => '',
      'speed' => '2000',
      'auto_height' => true,
      'slides_per_view' => '',
      'loop' => true,
      'navigation' => true,
      'side_navigation_arrows' => false,
      'navigation_type' => false
    ];

    $swiperConfig = get_field('swiper_config') ?: $defaultSwiperConfig;
    $filePath = 'template-parts/gutenberg-testimonial-block/';
    $items = empty($specificTestimonial) ? $randomTestimonial : $specificTestimonial;


  // Populate slides
  foreach ($items as &$item) {
    $slide = [
      'id' => $item->ID,
      'identifier' => $item->post_title, // post main title
      'content' => $item->post_content,
      'status' => $item->post_status, // we are after publish
      'author' => get_post_meta( $item->ID, 'testimonial-author', true ),
      'intro_title' => get_post_meta( $item->ID, 'testimonial-intro-title', true ),
      'author_position' => get_post_meta( $item->ID, 'testimonial-author-title', true )
    ];

    if($slide['status'] === 'publish') array_push($slides, $slide);

  }
  unset($item); // break the reference with the last element
  
  if(empty($slides)) return; 

  $setup = [
    'hook-above' => $filePath . '_hook-above', //optional - inject html within wrapper 
    'section-extra-classes' => [], // optional
    'section-title' => 'testimonial-block', // optional. will be replaced with km-block- uniqueid() if not presented ( will create class name on main block )
    'data' => $slides, // defaults to empty array. content of repeater block;
    'slide-markup-path' => $filePath . 'single-slide', // required
    //'hook-below' => $filePath . '_hook-below' // //optional - inject html within wrapper 
  ];

  if($sectionBg) {
    array_push($setup['section-extra-classes'], 'has-' . $sectionBg . '-background-colour');
  }

  if(!empty($acfClasses)) {
    $setup['section-extra-classes'] = array_merge($setup['section-extra-classes'], $acfClasses);
  }

?>

<?php //Create swiper instance with setup  ?>
<?php hm_get_template_part( get_template_directory() . '/template-parts/common/swiper/snippet-markup.php', [
    'setup' => $setup,
    'swiper-config' => $swiperConfig 
  ] );
?>   
    