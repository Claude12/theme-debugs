<?php

/*
 Ajax functions
*/
add_action( 'wp_ajax_nopriv_load_more','load_more'); // logged out users
add_action( 'wp_ajax_load_more','load_more'); // logged in users

function load_more(){
   $paged = $_POST['page'] + 1;

   $query = new WP_Query(array(
    'post_type' => 'post',
    'paged' => $paged
   ));
   
   if($query->have_posts()) : 
     while($query->have_posts()) : $query->the_post(); 
       get_template_part('template-parts/blog/single');  
     endwhile; 
   endif;  

   wp_reset_postdata();
   die(); // important for Ajax apparently
}

