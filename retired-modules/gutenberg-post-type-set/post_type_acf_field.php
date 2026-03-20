<?php
/**
 *  Add to functions.php if it is not there. Failing to add will result in ERROR 400
 * if(file_exists(get_stylesheet_directory() . '/template-parts/gutenberg-post-type-set/post_type_acf_field.php')) {
 * require get_stylesheet_directory() . '/template-parts/gutenberg-post-type-set/post_type_acf_field.php';
 * }

 */
add_filter('acf/load_field/name=km_post_type_select', 'km_load_post_type_selection');

function km_load_post_type_selection($field){
  // Post types that are not required
  $excludeTypes = ['page'];

  foreach ( get_post_types( ['show_in_nav_menus' => true], 'objects' ) as $postType ) {
    $name = $postType->name;
    $label = $postType->label;
    if(!in_array($name,$excludeTypes)) $field['choices'][$name] = $label;
  }
  return $field;
}

/**
 *  Fetch relevant posts
 */
function km_load_more_posts() {

    if ( isset($_REQUEST) ) {
      $currentpage = $_REQUEST['currentpage'];
      $cardMarkup = $_REQUEST['single_markup'] ?: 'post';
      $page = $_REQUEST['page'];
      $postType = $_REQUEST['post_type'];
      $step = $_REQUEST['step'];
      $orderBy = $_REQUEST['order_by'];
      $orderDirection = $_REQUEST['order_direction'];
      $orderProp = [$orderBy => $orderDirection];
      $args = [
        'post_type' => $postType,
        'posts_per_page' => intVal($step) * intVal($page),
        'paged' => 1,
        'ignore_sticky_posts' => true,
        'post_status' => 'publish',
        'orderby'   => $orderProp,
        'post__not_in' => array($currentpage) // exclude current page
        // 'tax_query' => array(
        //   array(
        //       'taxonomy' => 'case-studies-categories', //double check your taxonomy name in you dd 
        //       'field'    => 'id',
        //       'terms'    => 143,
        //   ),
        //  ),
        ];

      $query = new WP_Query($args);

      if($query->have_posts()) {
        while($query->have_posts()) {
          $query->the_post(); 
          //get_template_part('template-parts/gutenberg-post-type-set/templates/' .  $cardMarkup);
          get_template_part('template-parts/post-type-templates/' .  $cardMarkup . '/single-markup');
        }
      }
      
      wp_reset_postdata();
      die(); 

  }

}

add_action( 'wp_ajax_km_load_more_posts', 'km_load_more_posts' );
add_action( 'wp_ajax_nopriv_km_load_more_posts', 'km_load_more_posts' );

