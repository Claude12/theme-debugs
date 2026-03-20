<?php

/**
 * Register a custom post type called "Testimonials".
 *
 * @see get_post_type_labels() for label keys.
 */
function km_init_testimonials_post_type() {
  $labels = array(
      'name'                  => _x( 'Testimonials', 'Post type general name', 'textdomain' ),
      'singular_name'         => _x( 'Testimonial', 'Post type singular name', 'textdomain' ),
      'menu_name'             => _x( 'Testimonials', 'Admin Menu text', 'textdomain' ),
      'name_admin_bar'        => _x( 'Testimonial', 'Add New on Toolbar', 'textdomain' ),
      'add_new'               => __( 'Add New', 'textdomain' ),
      'add_new_item'          => __( 'Add New Testimonial', 'textdomain' ),
      'new_item'              => __( 'New Testimonial', 'textdomain' ),
      'edit_item'             => __( 'Edit Testimonial', 'textdomain' ),
      'view_item'             => __( 'View Testimonial', 'textdomain' ),
      'all_items'             => __( 'All Testimonials', 'textdomain' ),
      'search_items'          => __( 'Search Testimonials', 'textdomain' ),
      'parent_item_colon'     => __( 'Parent Testimonials:', 'textdomain' ),
      'not_found'             => __( 'No Testimonials found.', 'textdomain' ),
      'not_found_in_trash'    => __( 'No Testimonials found in Trash.', 'textdomain' ),
      'featured_image'        => _x( 'Testimonial Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
      'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
      'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
      'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
      'archives'              => _x( 'Testimonial\'s archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
      'insert_into_item'      => _x( 'Insert into Testimonial', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
      'uploaded_to_this_item' => _x( 'Uploaded to this Testimonial', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
      'filter_items_list'     => _x( 'Filter Testimonial\'s list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
      'items_list_navigation' => _x( 'Testimonials list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
      'items_list'            => _x( 'Testimonials list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
  );

  $args = array(
      'labels'             => $labels,
      'public'             => true,
      'publicly_queryable' => false,
      'menu_icon'          => 'dashicons-testimonial',
      'show_ui'            => true,
      'show_in_menu'       => true,
      'query_var'          => true,
      'rewrite'            => array( 'slug' => 'testimonial' ),
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => 5,
      'taxonomies'         => array(),
      'supports'           => array('title','editor'), //'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'
  );

  register_post_type( 'testimonial', $args );
}


function testimonial_info_markup($object){
  wp_nonce_field(basename(__FILE__), "meta-box-nonce");


   $textFields = [
     [
      'label' => 'Intro title',
      'field' => 'testimonial-intro-title'
     ],
     [
     'label' => 'Author',
     'field' => 'testimonial-author'
     ],
     [
      'label' => 'Author Position',
      'field' => 'testimonial-author-title'
      ]
     ];

  ?>
    <style type="text/css">

    #wp-admin-km-testimonial-info > div{
      margin:5px 0;
    }
    #wp-admin-km-testimonial-info label {
      display:block;
      padding:10px 0;
    }
    #wp-admin-km-testimonial-info label,
    #wp-admin-km-testimonial-info input {
      width:100%;
    }

    </style>
  <?php foreach ($textFields as &$textField) : ?>
    <div>
      <label for="<?php echo $textField['field']; ?>"><strong><?php echo $textField['label']; ?>:</strong></label>
      <input name="<?php echo $textField['field']; ?>" type="text" value="<?php echo get_post_meta($object->ID, $textField['field'], true); ?>">
    </div>
  <?php endforeach; unset($textField); ?>

  <?php  
    
}

function add_testimonial_metas(){
    add_meta_box("wp-admin-km-testimonial-info", "Testimonial Info", "testimonial_info_markup", "testimonial", "side", "high", null);
}

function save_testimonial_metas($post_id, $post, $update){

    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__))) return $post_id;
    if(!current_user_can("edit_post", $post_id))  return $post_id;
    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) return $post_id;

    $slug = "testimonial";
    if($slug != $post->post_type)return $post_id;

    $textFields = ['testimonial-intro-title','testimonial-author','testimonial-author-title'];

    foreach ($textFields as &$textField) {
      if(isset($_POST[$textField])) $testimonial_author = $_POST[$textField];
      update_post_meta($post_id, $textField, $testimonial_author);
    }
  
    unset($textField); 
}


function testimonial_taxonomies() {
  // Tags
  // Remember to flush permalinks!! 
   $tagLabels = array(
    'name' => _x( 'Testimonial Tags', 'taxonomy general name' ),
    'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Tags' ),
    'popular_items' => __( 'Popular Tags' ),
    'all_items' => __( 'All Tags' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Tag' ), 
    'update_item' => __( 'Update Tag' ),
    'add_new_item' => __( 'Add New Tag' ),
    'new_item_name' => __( 'New Tag Name' ),
    'separate_items_with_commas' => __( 'Separate tags with commas' ),
    'add_or_remove_items' => __( 'Add or remove tags' ),
    'choose_from_most_used' => __( 'Choose from the most used tags' ),
    'menu_name' => __( 'Tags' ),
  ); 

  register_taxonomy('testimonial-tag', array('testimonial'),array(
    'hierarchical' => false,
    'labels' => $tagLabels,
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'testimonial/tag', 'with_front' => false ),
  ));
}



add_action( 'init', 'km_init_testimonials_post_type' );
add_action("add_meta_boxes", "add_testimonial_metas");
add_action("save_post", "save_testimonial_metas", 10, 3);
add_action( 'init', 'testimonial_taxonomies', 0 );