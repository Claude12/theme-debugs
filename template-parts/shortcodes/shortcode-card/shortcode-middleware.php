<?php
//https://kinsta.com/blog/wordpress-shortcodes/#:~:text=shortcode_atts()%20is%20a%20WordPress,from%20user%2Ddefined%20shortcode%20attributes.
//var_dump($content, $atts);

  $params = is_array($atts) ? $atts : [];
  $isPost = array_key_exists('post_type', $params) ? $params['post_type'] : null ;
  $img = array_key_exists('card_img', $params) ? $params['card_img'] : null ;
  $postCount = array_key_exists('post_count', $params) ? intval($params['post_count']) : 1 ;

if($isPost){

  $my_posts= wp_get_recent_posts(array(
    'numberposts' => $postCount,
    'post_status' => 'publish', // Show only the published posts
    'post_type' => $isPost
  ));


  // Invalid post type will return 0 results
  if(sizeof($my_posts) == 0) return;

  foreach($my_posts as $my_post) {

    $postId = $my_post['ID'];

    $data = [
      'id' => $postId,
      'img' => get_the_post_thumbnail_url($postId) ? get_the_post_thumbnail_url($postId) : $img,
      'link' => get_permalink($postId),
      'title' => $my_post['post_title'],
      'content' => empty($my_post['post_excerpt']) ? wp_trim_words($my_post['post_content'], 55, '...') : $my_post['post_excerpt'],
    ];

    hm_get_template_part(get_template_directory() . '/template-parts/shortcodes/shortcode-card/shortcode-card-markup.php', [ 'card' => $data, 'isPost' => $isPost ] );

  }

  wp_reset_query();
} else {

  $data = [
     'id' => null,
     'img' => $img,
     'link' => null,
     'title' => null,
     'content' =>  $content
  ];

  hm_get_template_part( get_template_directory() . '/template-parts/shortcodes/shortcode-card/shortcode-card-markup.php', [ 'card' => $data, 'isPost' => $isPost ] );
}




?>




