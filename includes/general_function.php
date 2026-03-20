<?php 

/**
 * Add ketchup Modules block to gutenberg. 
 * Reorder categories so that ketchup module would appear first
 * 
 */

function gutenberg_ketchup_modules( $categories ) {

  $useAllCategories = true; // set tu true if you want all categories
  $category_slugs = wp_list_pluck( $categories, 'slug' );

  // return categories as is if ketchup-modules exist
  if(in_array( 'ketchup-modules', $category_slugs, true )) return $categories;

  $reusableBlockCategory = [
    'slug' =>  'reusable',
    'title' =>  'Reusable Blocks',
    'icon' => null
  ];

  $newCategories = [];

  $ketchupBlock = array(
               'slug'  => 'ketchup-modules',
               'title' => __( 'Ketchup Modules' ),
               'icon'  => function_exists('gutenbergBlockIcon') ? gutenbergBlockIcon() : null,
  );

  array_push($newCategories, $ketchupBlock);

  if($useAllCategories) {
    foreach ($categories as $category) {
      array_push($newCategories, $category);
    } 
  } else {
    array_push($newCategories, $reusableBlockCategory);
  }

  return $newCategories;
}

/* Render gutenberg block icon
==============================*/
function gutenbergBlockIcon(){

  return '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 62.495 62.317">
  <path id="Path_24937" data-name="Path 24937" d="M-270.958-153.265a.463.463,0,0,0,.463-.463v-1.666a.462.462,0,0,0-.463-.462h-8.474a.462.462,0,0,0-.462.462V-94a.462.462,0,0,0,.462.463h8.474a.463.463,0,0,0,.463-.463v-1.666a.463.463,0,0,0-.463-.463H-277.3v-57.134Z" transform="translate(279.894 155.856)" fill="#000" />
  <path id="Path_24938" data-name="Path 24938" d="M-226.335-96.13a.463.463,0,0,0-.463.463V-94a.463.463,0,0,0,.463.463h8.474A.462.462,0,0,0-217.4-94v-61.392a.462.462,0,0,0-.462-.462h-8.474a.462.462,0,0,0-.463.462v1.667a.463.463,0,0,0,.463.463h6.345V-96.13Z" transform="translate(279.894 155.856)" fill="#000" />
  <path id="Path_24939" data-name="Path 24939" d="M-250.537-125.423h.178l6.912-13.958a.706.706,0,0,1,.714-.49h1.739a.523.523,0,0,1,.4.8l-7.625,15.34,7.625,15.34a.523.523,0,0,1-.4.8h-1.739a.707.707,0,0,1-.714-.491l-6.912-13.958h-.178v13.78a.591.591,0,0,1-.669.669h-1.427a.591.591,0,0,1-.669-.669V-139.2a.591.591,0,0,1,.669-.669h1.427a.591.591,0,0,1,.669.669Z" transform="translate(279.894 155.856)" fill="#000" />
</svg>';
};

// Grabs the custom image sizes from the theme options page, pass through the field name of the custom image size as the argument. Call the function in includes/custom_image_size.php
function km_custom_image_size($image_name) {
  $array = get_field($image_name, 'option') ? get_field($image_name, 'option') : []; // array always defaults to empty array options page not available.
  $w = array_key_exists('width', $array) ? intval($array['width']) : null;
  $h = array_key_exists('height', $array) ? intval($array['height']) : null;
  $crop = array_key_exists('crop', $array) ? $array['crop'] : false;
  $crop_x = array_key_exists('x_crop', $array) ? $array['x_crop'] : 'center';
  $crop_y = array_key_exists('y_crop', $array) ? $array['y_crop'] : 'center';
  $img_crop  = $crop ? [$crop_x, $crop_y] : false;
  
  return add_image_size($image_name, $w, $h, $img_crop); 
}

function wpt_remove_version() {  
  return '';  
}  

// Disable support for comments and trackbacks in post types
function df_disable_comments_post_types_support() {
  $post_types = get_post_types();
  foreach ($post_types as $post_type) {
    if(post_type_supports($post_type, 'comments')) {
      remove_post_type_support($post_type, 'comments');
      remove_post_type_support($post_type, 'trackbacks');
    }
  }
}

// Close comments on the front-end
function df_disable_comments_status() {
  return false;
}

// Hide existing comments
function df_disable_comments_hide_existing_comments($comments) {
  $comments = array();
  return $comments;
}

// Remove comments page in menu
function df_disable_comments_admin_menu() {
  remove_menu_page('edit-comments.php');
}

// Redirect any user trying to access comments page
function df_disable_comments_admin_menu_redirect() {
  global $pagenow;
  if ($pagenow === 'edit-comments.php') {
    wp_redirect(admin_url()); exit;
  }
}

// Remove comments metabox from dashboard
function df_disable_comments_dashboard() {
  remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}

// Remove comments links from admin bar
function df_disable_comments_admin_bar() {
  if (is_admin_bar_showing()) {
    remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
  }
}

//Remove Draft Code
function remove_draft_archive_link($options, $field, $the_post) {
  $options['post_status'] = array('publish');
  return $options;
}

//Blog Content Limit

function km_unicorn_limitText($string,$limit)
{
  if(!empty($string))
  {
    $string = strip_tags($string);
    if (strlen($string) > $limit)
    {
      $stringCut = substr($string, 0, $limit);
      $string = substr($stringCut, 0, strrpos($stringCut, ' ')) ; 
    }
    return $string;
  }
  else
  {
    return false; 
  }
}
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}

/**
 * Disable the emoji's
 */
function disable_emojis() {
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
  add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
 }
 add_action( 'init', 'disable_emojis' );
 
 /**
  * Filter function used to remove the tinymce emoji plugin.
  * 
  * @param array $plugins 
  * @return array Difference betwen the two arrays
  */
 function disable_emojis_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
  return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
  return array();
  }
 }
 
 /**
  * Remove emoji CDN hostname from DNS prefetching hints.
  *
  * @param array $urls URLs to print for resource hints.
  * @param string $relation_type The relation type the URLs are printed for.
  * @return array Difference betwen the two arrays.
  */
 function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
  if ( 'dns-prefetch' == $relation_type ) {
  /** This filter is documented in wp-includes/formatting.php */
  $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
 
 $urls = array_diff( $urls, array( $emoji_svg_url ) );
  }
 
 return $urls;
 }
 
// ===================================
// Get Template Part
// ===================================
function km_unicorn_get_template_part($slug = null, $name = null, array $params = array()) {
  global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
  do_action("get_template_part_{$slug}", $slug, $name);
  $templates = array();
  if (isset($name))
    $templates[] = "{$slug}-{$name}.php";
    $templates[] = "{$slug}.php";
    $_template_file = locate_template($templates, false, false);
  if (is_array($wp_query->query_vars)) {
    extract($wp_query->query_vars, EXTR_SKIP);
  }
  extract($params, EXTR_SKIP);
  require($_template_file);
}

/**
 * Add Manage Cookies link if cookie is saved.
 * 
 */
// add_filter( 'wp_nav_menu_items', 'add_manage_cookies', 10, 2 );
function add_manage_cookies ( $items, $args ) {
  if(isset($_COOKIE['privacy-preferences'])){
    if ( $args->theme_location == 'menu-2') $items .= '<li class="toggle-cookie-pref footer-tcp underline-hover">Manage Cookies</li>';
  }
    return $items;
}

/**
* Add a custom link to the end of a specific menu that uses the wp_nav_menu() function
*/
add_filter( 'wp_nav_menu_items', 'add_kecthup_link', 10, 2 );
function add_kecthup_link ( $items, $args ) {
     
    if ( $args->theme_location == 'menu-2'){

      if(isset($_COOKIE['privacy-preferences'])){
        if ( $args->theme_location == 'menu-2') $items .= '<li class="toggle-cookie-pref footer-tcp underline-hover">Manage Cookies</li>';
      }
      // $items .= '<li class="ketchup-link"><a target="_blank" rel="noopener" href="//www.ketchup-marketing.co.uk">Made better with Ketchup</a></li>';
      // $items .= '<li class="km-case-study"><a href="'. get_site_url(null, '/case-study/') . '">Case Study</a></li>';
      //$items = '<li>Copyright &#169;<span id="km-copyright"></span><script>document.getElementById("km-copyright").innerHTML = new Date().getUTCFullYear();</script></li>' . $items;
    } 

    return $items;
}

/**
* 
* @param Array - Associative array. key is CSS property and value is CSS property value 
* @return String - returns full style attribute with set params
* @return NULL - returns null if array is empty
* @example -
* $attributes = ['background-color' => 'teal','color' => 'white'];
* var_dump(populateStyleAttribute($attributes));
* 
*/
function populateStyleAttribute($attributes = []){
  if(empty($attributes)) return null;

  $btnAttr = [];
  foreach ($attributes as $attribute => &$val) {
    if(!empty($val)) array_push($btnAttr, $attribute . ': ' . $val . ';');
  }
  
  unset($val);
  return 'style="' . implode(' ',$btnAttr) . '"';
}

/**
 * Renders relevant gutenberg module
 */
function ketchup_gutenberg_block( $block ) {
  $slug = str_replace('acf/', '', $block['name']);
  $args = [
    'acf-classes' => []
  ];

  if(array_key_exists('className',$block)) {
    $args['acf-classes'] = explode(' ', $block['className']);
  }

  get_template_part('/template-parts/gutenberg-' . $slug . '/snippet','markup',$args);
}

/**
 * 
 * Clone administrator role with a different name.
 * Our usage : Reveals otherwise hidden config section in WP admin
 */
function createRole() {
  $adm = get_role('administrator');
  $adm_cap= array_keys( $adm->capabilities ); //get administator capabilities
  add_role('developer', 'Developer'); //create new role
  $new_role = get_role('developer');
   foreach ( $adm_cap as $cap ) {
    $new_role->add_cap( $cap ); //clone administrator capabilities to new role
   }
 }

/**
 * Prevents Ketchup page from being deleted by the client
 */
function ketchupLinkProtection($post_id) {
  if( $post_id == 9999999 ) {
    exit('
      <div style="max-width: 800px; text-align: center; font-family: Helvetica; font-size: 14px; margin:0 auto;">
      <svg viewBox="0 0 261.3 99.1" id="ketchup-logo-strap" style="width:200px">
        <path style="fill:#BD1218;" d="M30.2,23.2c0.2,0,0.4-0.2,0.4-0.4v-1.5c0-0.2-0.2-0.4-0.4-0.4h-7.8c-0.2,0-0.4,0.2-0.4,0.4v56.5    c0,0.2,0.2,0.4,0.4,0.4h7.8c0.2,0,0.4-0.2,0.4-0.4v-1.5c0-0.2-0.2-0.4-0.4-0.4h-5.8V23.2H30.2z" />
        <path style="fill:#BD1218;" d="M231.1,75.7c-0.2,0-0.4,0.2-0.4,0.4v1.5c0,0.2,0.2,0.4,0.4,0.4h7.8c0.2,0,0.4-0.2,0.4-0.4V21.2    c0-0.2-0.2-0.4-0.4-0.4h-7.8c-0.2,0-0.4,0.2-0.4,0.4v1.5c0,0.2,0.2,0.4,0.4,0.4h5.8v52.6H231.1z" />
        <path style="fill:#353637;" d="M49,48.8h0.2L55.5,36c0.2-0.3,0.3-0.5,0.7-0.5h1.6c0.4,0,0.5,0.4,0.4,0.7l-7,14.1l7,14.1    c0.2,0.3,0,0.7-0.4,0.7h-1.6c-0.3,0-0.5-0.1-0.7-0.5l-6.4-12.8H49v12.7c0,0.4-0.2,0.6-0.6,0.6h-1.3c-0.4,0-0.6-0.2-0.6-0.6V36.1    c0-0.4,0.2-0.6,0.6-0.6h1.3c0.4,0,0.6,0.2,0.6,0.6V48.8z" />
        <path style="fill:#353637;" d="M81.9,35.5c0.4,0,0.6,0.2,0.6,0.6v1.1c0,0.4-0.2,0.6-0.6,0.6h-7v11.4h5.5c0.4,0,0.6,0.2,0.6,0.6v1.1    c0,0.4-0.2,0.6-0.6,0.6h-5.5v11.4h7c0.4,0,0.6,0.2,0.6,0.6v1.1c0,0.4-0.2,0.6-0.6,0.6H73c-0.4,0-0.6-0.2-0.6-0.6V36.1    c0-0.4,0.2-0.6,0.6-0.6H81.9z" />
        <path style="fill:#353637;" d="M103.4,37.8v26.8c0,0.4-0.2,0.6-0.6,0.6h-1.3c-0.4,0-0.6-0.2-0.6-0.6V37.8h-5c-0.4,0-0.6-0.2-0.6-0.6v-1.1    c0-0.4,0.2-0.6,0.6-0.6h12.5c0.4,0,0.6,0.2,0.6,0.6v1.1c0,0.4-0.2,0.6-0.6,0.6H103.4z" />
        <path style="fill:#353637;" d="M134.3,46.2c0,0.4-0.2,0.6-0.6,0.6h-1.3c-0.4,0-0.6-0.2-0.6-0.6v-5c0-2.3-0.5-3.6-3.2-3.6    c-2.7,0-3.2,1.3-3.2,3.6v18.4c0,2.3,0.5,3.6,3.2,3.6c2.7,0,3.2-1.3,3.2-3.6v-5c0-0.4,0.2-0.6,0.6-0.6h1.3c0.4,0,0.6,0.2,0.6,0.6v5    c0,3.7-0.9,5.9-5.7,5.9c-4.8,0-5.7-2.1-5.7-5.9V41.2c0-3.7,0.9-5.9,5.7-5.9c4.8,0,5.7,2.2,5.7,5.9V46.2z" />
        <path style="fill:#353637;" d="M152.4,49.2h6.3V36.1c0-0.4,0.2-0.6,0.6-0.6h1.3c0.4,0,0.6,0.2,0.6,0.6v28.5c0,0.4-0.2,0.6-0.6,0.6h-1.3    c-0.4,0-0.6-0.2-0.6-0.6V51.5h-6.3v13.1c0,0.4-0.2,0.6-0.6,0.6h-1.3c-0.4,0-0.6-0.2-0.6-0.6V36.1c0-0.4,0.2-0.6,0.6-0.6h1.3    c0.4,0,0.6,0.2,0.6,0.6V49.2z" />
        <path style="fill:#353637;" d="M176.9,36.1c0-0.4,0.2-0.6,0.6-0.6h1.3c0.4,0,0.6,0.2,0.6,0.6v23.4c0,2.3,0.5,3.6,3.2,3.6s3.2-1.3,3.2-3.6    V36.1c0-0.4,0.2-0.6,0.6-0.6h1.3c0.4,0,0.6,0.2,0.6,0.6v23.4c0,3.7-0.9,5.9-5.7,5.9c-4.8,0-5.7-2.1-5.7-5.9V36.1z" />
        <path style="fill:#353637;" d="M206.4,52.6v11.9c0,0.4-0.2,0.6-0.6,0.6h-1.3c-0.4,0-0.6-0.2-0.6-0.6V36.1c0-0.4,0.2-0.6,0.6-0.6h4    c5.4,0,6.4,2.5,6.4,6.6V46c0,4.2-1,6.6-6.4,6.6H206.4z M208.5,50.4c3.3,0,3.9-1.6,3.9-4.3v-3.9c0-2.7-0.6-4.3-3.9-4.3h-2.1v12.6    H208.5z" />
        <path style="fill:#353637;" d="M62.9,79.8l0-4.6L60.6,79h-0.4L58,75.2v4.6h-0.9v-6.3h0.7l2.6,4.4l2.6-4.4h0.7l0,6.3H62.9z" />
        <path style="fill:#353637;" d="M72.9,78.2h-3.4l-0.7,1.6h-0.9l2.9-6.3h0.9l2.9,6.3h-0.9L72.9,78.2z M72.6,77.5l-1.4-3.1l-1.4,3.1H72.6z" />
        <path style="fill:#353637;" d="M82.8,79.8l-1.4-1.9c-0.2,0-0.3,0-0.4,0h-1.6v1.9h-0.9v-6.3h2.5c0.8,0,1.5,0.2,1.9,0.6    c0.5,0.4,0.7,0.9,0.7,1.6c0,0.5-0.1,0.9-0.4,1.2c-0.2,0.3-0.6,0.6-1,0.7l1.5,2.1H82.8z M82.4,76.7c0.3-0.2,0.5-0.6,0.5-1.1    c0-0.5-0.2-0.8-0.5-1.1c-0.3-0.2-0.7-0.4-1.3-0.4h-1.5v2.8H81C81.6,77.1,82.1,77,82.4,76.7z" />
        <path style="fill:#353637;" d="M90.4,77l-1.2,1.2v1.6h-0.9v-6.3h0.9V77l3.4-3.5h1L91,76.3l2.9,3.5h-1.1L90.4,77z" />
        <path style="fill:#353637;" d="M102.5,79v0.8H98v-6.3h4.5v0.8h-3.6v1.9h3.2V77h-3.2v2H102.5z" />
        <path style="fill:#353637;" d="M108.4,74.3h-2.2v-0.8h5.2v0.8h-2.2v5.5h-0.9V74.3z" />
        <path style="fill:#353637;" d="M115.6,73.5h0.9v6.3h-0.9V73.5z" />
        <path style="fill:#353637;" d="M127,73.5v6.3h-0.7l-3.8-4.7v4.7h-0.9v-6.3h0.7l3.8,4.7v-4.7H127z" />
        <path style="fill:#353637;" d="M136.4,76.6h0.9v2.5c-0.3,0.3-0.7,0.5-1.1,0.6c-0.4,0.1-0.8,0.2-1.3,0.2c-0.6,0-1.2-0.1-1.7-0.4    c-0.5-0.3-0.9-0.7-1.2-1.2c-0.3-0.5-0.4-1-0.4-1.7c0-0.6,0.1-1.2,0.4-1.7c0.3-0.5,0.7-0.9,1.2-1.2s1.1-0.4,1.7-0.4    c0.5,0,1,0.1,1.4,0.2c0.4,0.2,0.8,0.4,1.1,0.7l-0.6,0.6c-0.5-0.5-1.1-0.7-1.8-0.7c-0.5,0-0.9,0.1-1.3,0.3    c-0.4,0.2-0.7,0.5-0.9,0.9c-0.2,0.4-0.3,0.8-0.3,1.2c0,0.5,0.1,0.9,0.3,1.2c0.2,0.4,0.5,0.7,0.9,0.9c0.4,0.2,0.8,0.3,1.3,0.3    c0.6,0,1.1-0.1,1.5-0.4V76.6z" />
        <path style="fill:#353637;" d="M151.8,78.2h-3.4l-0.7,1.6h-0.9l2.9-6.3h0.9l2.9,6.3h-0.9L151.8,78.2z M151.5,77.5l-1.4-3.1l-1.4,3.1H151.5z" />
        <path style="fill:#353637;" d="M161.8,76.6h0.9v2.5c-0.3,0.3-0.7,0.5-1.1,0.6c-0.4,0.1-0.8,0.2-1.3,0.2c-0.6,0-1.2-0.1-1.7-0.4    c-0.5-0.3-0.9-0.7-1.2-1.2c-0.3-0.5-0.4-1-0.4-1.7c0-0.6,0.1-1.2,0.4-1.7c0.3-0.5,0.7-0.9,1.2-1.2c0.5-0.3,1.1-0.4,1.7-0.4    c0.5,0,1,0.1,1.4,0.2c0.4,0.2,0.8,0.4,1.1,0.7l-0.6,0.6c-0.5-0.5-1.1-0.7-1.8-0.7c-0.5,0-0.9,0.1-1.3,0.3    c-0.4,0.2-0.7,0.5-0.9,0.9c-0.2,0.4-0.3,0.8-0.3,1.2c0,0.5,0.1,0.9,0.3,1.2c0.2,0.4,0.5,0.7,0.9,0.9c0.4,0.2,0.8,0.3,1.3,0.3    c0.6,0,1.1-0.1,1.5-0.4V76.6z" />
        <path style="fill:#353637;" d="M172.1,79v0.8h-4.6v-6.3h4.5v0.8h-3.6v1.9h3.2V77h-3.2v2H172.1z" />
        <path style="fill:#353637;" d="M182.2,73.5v6.3h-0.7l-3.8-4.7v4.7h-0.9v-6.3h0.7l3.8,4.7v-4.7H182.2z" />
        <path style="fill:#353637;" d="M188.3,79.4c-0.5-0.3-0.9-0.7-1.2-1.2c-0.3-0.5-0.4-1-0.4-1.7c0-0.6,0.1-1.2,0.4-1.7c0.3-0.5,0.7-0.9,1.2-1.2    c0.5-0.3,1.1-0.4,1.7-0.4c0.5,0,0.9,0.1,1.4,0.2c0.4,0.2,0.8,0.4,1,0.7l-0.6,0.6c-0.5-0.5-1.1-0.7-1.8-0.7c-0.5,0-0.9,0.1-1.3,0.3    s-0.7,0.5-0.9,0.9c-0.2,0.4-0.3,0.8-0.3,1.2c0,0.5,0.1,0.9,0.3,1.2c0.2,0.4,0.5,0.7,0.9,0.9c0.4,0.2,0.8,0.3,1.3,0.3    c0.7,0,1.3-0.3,1.8-0.8l0.6,0.6c-0.3,0.3-0.6,0.6-1.1,0.7c-0.4,0.2-0.9,0.3-1.4,0.3C189.4,79.9,188.8,79.7,188.3,79.4z" />
        <path style="fill:#353637;" d="M199.2,77.6v2.2h-0.9v-2.2l-2.5-4.1h1l2,3.3l2-3.3h0.9L199.2,77.6z" />
      </svg>
      <p>The page you are trying to delete is protected and cannot be deleted.</p>
      <p>If you need to remove this page please email <a href="mailto:info@ketchup.marketing" style="color:#BD1218; font-weight:bold;">info@ketchup.marketing</a> or call <strong style="color:#BD1218;">01476 852990</strong>.</p>
      <a href="javascript:history.go(-1)" title="Return to the previous page" style="color:#BD1218; font-weight:bold;">&laquo; Return to admin</a>
      </div>
    ');
  }
}
add_action('wp_trash_post', 'ketchupLinkProtection', 10, 1);
add_action('before_delete_post', 'ketchupLinkProtection', 10, 1);


/**
 * Disables Full Screen Mode by default in the admin area 
 */ 
if (is_admin()) { 
  function jba_disable_editor_fullscreen_by_default() {
  $script = "jQuery( window ).load(function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } });";
  wp_add_inline_script( 'wp-blocks', $script );
}
add_action( 'enqueue_block_editor_assets', 'jba_disable_editor_fullscreen_by_default' );
}


/**
 *  Function to aid cookie setting retrieval in PHP
 *  Intended to be used along with PrivacyManager JS plugin
 *  var_dump(getPrivacyPref('functional'));
 *  Two named arguments.
 *  $name - name of the cookie or cookiee group in question. Matches input field name in Privacy Manager HTML
 *  $cookie - cookie name in browser. Defaults : 'privacy-preferences'. This should match with the one set in Privacy Manger.
 * 
 *  Returns '' (empty string) if no key/preference is found within cookie or boolean. true - (cookie) approved, false - (cookie) denied.
 * 
 *  Calling function without arguments will return list of all preferences.
 * 
 *  getPrivacyPref(); // returns all available preferences set within cookie
 *  getPrivacyPref('functional'); // returns boolean if checked or not or empty string if cookie does not contain such value.
 * 
 * Example: 
 * $acceptAnalytics = getPrivacyPref('analytical') ?: false;
 * if($acceptAnalytics) {
 *  // Execute whatever you need.
 * }
 */

function getPrivacyPref($name = '', $cookie = 'privacy-preferences'){

  $noName = trim($name) === '';

  if(!isset($_COOKIE[$cookie])) return null;

  $jsonString = urldecode($_COOKIE[$cookie]);
  $jsonString = str_replace("\\", "", $jsonString);
  $data = JSON_decode($jsonString,true);

  $result = '';
  $all = [];

  foreach ($data as &$item) {
    if(trim($item['name']) === trim($name)) $result = $item['checked'];
    $curr = [
      'name' => $item['name'],
      'checked' => $item['checked']
    ];

    array_push($all,$curr);
  }

  unset($item);

  return $noName ? $all : $result;

}

/**
 * 
 *  Delete cookie from the same domain (.domain-name);
 *  clearCookies(['cookie-one','cookie-two']);
 * 
 */
function clearCookies($src = []) {
  $domain = substr($_SERVER['HTTP_HOST'], 0, 4) === 'www.' ? substr($_SERVER['HTTP_HOST'], 4) :  $_SERVER['HTTP_HOST'];
	foreach ($src as $value) {
		if(isset($_COOKIE[$value])) {
			setcookie($value, '', time() - 3600, '/', '.' . $domain); 
		}
	}
}

/**
 * 
 * Build class from slug and property.
 * $slug (string) red,blue,green (any)
 * $property (string) colour, background, fill etc.
 * $gap = add space before class name or not (useful if multiple classes on element)
 * if slug not present returns empty string;
 * 
 */
function buildClass($slug = '', $property='colour', $gap = true) {
  if(!isset($slug)) return;
  $prefix = $gap ? ' has-' : 'has';
  echo  $prefix . $slug . '-' . $property;
}

/**
 * 
 * Render block depending on its name. Useful if you need to render particular block outside the_content();
 * 
 *  renderBlocks('acf/hero-banner');
 * 
 * 
 */
function renderBlocks($blockName){
  $blocks = parse_blocks( get_the_content() );
  $content_markup = '';

  foreach ( $blocks as $block ) {
    if ( $blockName === $block['blockName'] ) {
      $content_markup .= render_block( $block );
    } 
  }

  return apply_filters( 'the_content', $content_markup);
}

/**
 * Like get_template_part() put lets you pass args to the template file
 * Args are available in the tempalte as $template_args array
 * @param string filepart
 * @param mixed wp_args style argument list
 * in target : $template_args['option'];
 * usage : hm_get_template_part( 'template_path', [ 'option' => 'value' ] );
 */
function hm_get_template_part( $file, $template_args = array(), $cache_args = array() ) {
  $template_args = wp_parse_args( $template_args );
  $cache_args = wp_parse_args( $cache_args );
  if ( $cache_args ) {
      foreach ( $template_args as $key => $value ) {
          if ( is_scalar( $value ) || is_array( $value ) ) {
              $cache_args[$key] = $value;
          } else if ( is_object( $value ) && method_exists( $value, 'get_id' ) ) {
              $cache_args[$key] = call_user_method( 'get_id', $value );
          }
      }
      if ( ( $cache = wp_cache_get( $file, serialize( $cache_args ) ) ) !== false ) {
          if ( ! empty( $template_args['return'] ) )
              return $cache;
          echo $cache;
          return;
      }
  }
  $file_handle = $file;
  do_action( 'start_operation', 'hm_template_part::' . $file_handle );
  if ( file_exists( get_stylesheet_directory() . '/' . $file . '.php' ) )
      $file = get_stylesheet_directory() . '/' . $file . '.php';
  elseif ( file_exists( get_template_directory() . '/' . $file . '.php' ) )
      $file = get_template_directory() . '/' . $file . '.php';
  ob_start();
  $return = require( $file );
  $data = ob_get_clean();
  do_action( 'end_operation', 'hm_template_part::' . $file_handle );
  if ( $cache_args ) {
      wp_cache_set( $file, $data, serialize( $cache_args ), 3600 );
  }
  if ( ! empty( $template_args['return'] ) )
      if ( $return === false )
          return false;
      else
          return $data;
  echo $data;
}




/**
 *  Get colour poperty from field
 *  createClasses(['initial'], 'acf_field_name', 'property')
 */
function createClasses($initialClasses = [], $field = null, $prop = 'colour'){
  $result = $initialClasses;
  if(!$field) return [];
  $src = get_field($field)['picker']['slug'];
  if($src){
      array_push($result, "has-" . $src . "-" . $prop);
  } 
  return implode(' ', $result);
}

/* Retrieve Child pages by ID
*
* getChildPages($post->ID, 1) // optional limit of items
*
*/

function getChildPages($pageId, $limit = -1) {
  global $post;
  $pages = [];
  $the_query = new WP_Query([
    'wpse_include_parent' => true,
    'post_type'           => 'page',
    'post_parent'         => $pageId,
    'posts_per_page'      => $limit,
    'orderby'             => 'menu_order',
    'order'               => 'ASC',
  ]);
  while ( $the_query->have_posts() ) {
    $the_query->the_post();
    $pages[] = $post;
  }
  wp_reset_postdata();
  return $pages;
}

/**
 *  Determine Current theme environment
 *  returns array of data
 *  [
 *    'enforced' => Boolean (returns true or false. depends on enforce_environmnet field)
 *    'render' => Boolean ( if user role exists for current user ? true : false)
 *    'env' => String 'dev or live'
 *  ]
 *  This function also sets GLOBAL CONSTANT with current environment to enable conditional actions.
 */

function km_define_env(){
  $config = get_field('environment','option') ?: null;
  $user = wp_get_current_user();
  $userRoles = $user->roles;
  $rolesToNotify = ['developer', 'administrator']; // add any roles here. all matching users will be notified
  $render = false;
 
  if(!$config) return null;

  foreach ($userRoles as &$role) {
      if(in_array($role,$rolesToNotify)){
        $render = true;
      }
  }
  unset($role);

  $currentUrl = site_url();
  $stage = $config['staging_url'];
  $live = $config['live_url'];
  $enforceEnv = $config['enforce_environment'];
  $envToEnforce = $config['environment_to_enforce'] ?: null;
  $currentENV = null;

  switch ($currentUrl) {
    case $stage:
        $currentENV = 'dev';
        break;
    case $live:
        $currentENV = 'live';
        break;
    default:
        $currentENV = null;
        break;
  }

  if($enforceEnv && $envToEnforce) $currentENV = $envToEnforce;

  // Define constant only if it is not already set
  if(!defined('KM_ENV')) {
    define('KM_ENV', $currentENV);
  }

  return [
    'enforced' => $enforceEnv,
    'render' => $render,
    'env' => $currentENV
  ];
}

/**
 * Check if is blog landing page
 */
function is_blog () {
  return ( is_archive() || is_author() || is_category() || is_home() || is_tag()) && 'post' == get_post_type();
}

/**
 *  Check if search query has any results
 * 
 */
function search_has_results() {
  if ( is_search()) {
    global $wp_query;
    $result = ( 0 != $wp_query->found_posts ) ? true : false;
    return $result;
  }
}

function yoastVariableToTitle($post_id) {
  $yoast_title = get_post_meta($post_id, '_yoast_wpseo_title', true);
  $title = strstr($yoast_title, '%%', true);
  if (empty($title)) {
      $title = get_the_title($post_id);
  }
  return $title;
}

/**
 *  Excerpt
 *  // text, cutOff length , symbols after cutoff
 *  km_excerpt('Hello World is here',2, '*');
 */
function km_excerpt($content, $cutOffLength, $excerptSymbol = '...') {

  $charAtPosition = "";
  $contentLength = strlen($content);

  $excSymbol = $contentLength > $cutOffLength ? $excerptSymbol : '';

  do {
      $cutOffLength++;
      $charAtPosition = substr($content, $cutOffLength, 1);
  } while ($cutOffLength < $contentLength && $charAtPosition != " ");

  return substr($content, 0, $cutOffLength) . $excSymbol;

}


/* 
Workaround to allow us to use pages for 
CPT landing pages rather than using as archive page. 
This allows us to add additional Gutenberg blocks 
to that page whilst maintaining the breadcrumb structure
*/
//add_filter( 'wpseo_breadcrumb_links', 'unbox_yoast_seo_breadcrumb_append_link' );
 function unbox_yoast_seo_breadcrumb_append_link( $links ) {
     global $post;
     if( is_singular('post')){
        $breadcrumb = array(
          'url' => site_url( '/news/' ), 
          'text' => 'News', 
        ); 
        array_splice($links, 1, 0, [$breadcrumb]); 
     } else if( is_singular('case-studies')){
      $breadcrumb = array(
        'url' => site_url( '/case-studies/' ), 
        'text' => 'Case Studies', 
      );
      array_splice($links, 1, 0, [$breadcrumb]);  
    }
     return $links;
 }


 /**
  * Automates Post type "require" in functions.php; 
  *
  */
 function km_load_post_types(){
   $postTypes = glob(get_template_directory(). '/template-parts/post-type-templates/*' , GLOB_ONLYDIR);

    foreach($postTypes as $postType) {
      $postTypePath = $postType . '/post_type.php';
      if(file_exists($postTypePath )) {
        require $postTypePath;
      }
    }
 }

 /*
 * Automates ACF select population files "require
 */

 function km_load_acf_selects(){
    $path = get_template_directory() . '/template-parts/common/select-populators';
    $selectPopulators = array_diff(scandir($path), array('.', '..'));

    foreach($selectPopulators as $populator) {
      require get_template_directory().'/template-parts/common/select-populators/' . $populator; 
    }
 }

/**
 * 
 * Generates svg file with bullet icons using colours registered in the site builder.
 * Note: Call function on condition. For example: only when logged in, is dev etc.
 * This way you will not rewrite file on each refresh. Only when needed.
 * 
 */
 function siteBuilderSvgIcons(){

   $path = WP_CONTENT_DIR . '/themes/km_unicorn/assets/';
   $themeColours = json_decode(file_get_contents($path . 'styles/colours.json'), true) ?: [];
   $iconData = json_decode(file_get_contents($path . 'svg-icons/bullet-icon.json'), true) ?: [];
   $svgViewBox = $iconData['viewBox'];
   $template = file_get_contents($path . 'svg-icons/bullet-icon.svg');
   $siteBuilderColours = get_field('php_colour_set','option') ?: [];
   $colours = array_merge($themeColours['colours'], $siteBuilderColours);

   $result = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="' . $svgViewBox . '" xml:space="preserve"><defs><style>g {display: none;}g:target {display: inline;}</style></defs>';

  foreach ($colours as &$colour){
    $label = $colour['label'];
    $slug = sanitize_title($label);
    $value = $colour['value'];
    $result .= str_replace('<g id="bullet-template">', '<g id="' . $slug . '-bullet-icon"' . ' style="fill:' . $value . '">"', $template);
  } 

  $result .= '</svg>';
  unset($colour); 

  file_put_contents($path . 'svg-icons/generated-bullet-icons.svg', $result);

 }

/**
 * 
 * Generates css file with class names for colour picker using colours registered in the site builder.
 * Note: Call function on condition. For example: only when logged in, is dev etc.
 * This way you will not rewrite file on each refresh. Only when needed.
 * 
 */
 function siteBuilderColours(){
  $path = WP_CONTENT_DIR . '/themes/km_unicorn/assets/';
  $colours = get_field('php_colour_set','option') ?: [];
  $fullPath = $path . 'styles/km-site-builder-colours.css';

  if(file_exists($fullPath)){
    file_put_contents($fullPath, '');
  }

  if(empty($colours)) return;

  $result = '/* These class names are auto generated. */';

  foreach ($colours as &$colour){

    $label = $colour['label'];
    $slug = sanitize_title($label);
    $value = $colour['value'];
  
    $result .= '.has-'. $slug . '-background-colour{background-color: '. $value . '!important; }';
    $result .= '.has-'. $slug . '-colour{color: '. $value . '!important; }';
    $result .= '.has-'. $slug . '-border-colour{border-color: '. $value . '!important; }';
    $result .= '.has-'. $slug . '-fill{fill: '. $value . '!important; }';
    $result .= '.has-'. $slug . '-link-colour a{color: '. $value . '!important; }';
    $result .= '.has-'. $slug . '-link-colour a:hover{color: inherit!important; }';
    $result .= '.has-'. $slug . '-bullet-colour li::before{content: url(../svg-icons/generated-bullet-icons.svg#'. $slug . '-bullet-icon)!important; color: ' . $value . ';!important }';
  } 
  
  unset($colour); 

  file_put_contents($fullPath, $result);

 }


 /**
  * Populate both - bullet icons and colours from the site builder. 
  * Note that in order this to work you need to be:
  * 1. Logged in
  * 2. Have a role of 'developer'
  * 3. have to be in admin area
  * This is because function run create 2 files on the server. We dont want to be doing that on each refresh in production.
  */
 function km_populate_site_builder_colours(){
  $user = wp_get_current_user();
  $isDeveloper =  in_array( 'developer', $user->roles );
  if(is_user_logged_in() && $isDeveloper && is_admin()){
    siteBuilderColours();
    siteBuilderSvgIcons();
  }
 }

 /**
 *  Retrieve available repeater-templates from ajax load more
 *  getAjaxLoadMoreRepeaters()
 *  returns array of repeaters with aliases and corresponding template.
 */

function getAjaxLoadMoreRepeaters(){
  global $wpdb;
  $table_name = $wpdb->prefix . "alm_unlimited";
  $rows = $wpdb->get_results("SELECT * FROM $table_name"); // Get all Repeaters.
  // Default is considered for "posts" default post type.
  $result = [
    [
      'alias' => 'Default',
      'name' => 'default',
      'slug' => 'post'
    ]
  ];
  
  foreach( $rows as $repeater ) {
   $item = [
    'alias' => $repeater->alias,
    'name' => $repeater->name,
    'slug' => sanitize_title($repeater->alias)
   ];
   array_push($result, $item);
  }    

  return $result;
}    

/**
 *  Return list of manual repeater templates
 *  This is used as an addition to automated repeater templates set in Ajax load more,
 *  sometimes CPT can be rendered in multiple ways.
 *  Function because it is used in multiple places (index.php, post type feed etc)
 *  Leave blank array if no overrides are required
 */

 function manualRepeaterTemplates(){
  
  $result = [
    [
    'alias' => 'Remove me if not in use (general_functions.php)',
    'name' => 'template_4',
    ],
  ];

  return $result;

 }

/**
 *  Retrieves current page id in front end and admin view.
 *  Dependency: ACF!
 * 
 */
 function km_get_page_id() {
	if ( is_admin() && function_exists( 'acf_maybe_get_POST' ) ) :
		return intval( acf_maybe_get_POST( 'post_id' ) );
	else :
		global $post;
    if(!is_object($post)) return null;
		return $post->ID;
	endif;
}

/**
 * This function modifies the main WordPress query to look in particular post type
 *
 * @param object $query The main WordPress query.
 */
function km_search_rules( $query ) {
  if ( $query->is_main_query() && $query->is_search() && ! is_admin() ) {
      $query->set( 'post_type', array( 'page' ) );
  }    
}

/**
 *  Name your scss as critical.scss within template-parts to inline css within head. CSS that appears above the fold.
 * add_action('wp_head', 'km_hook_critical_assets');
 */
function km_hook_critical_assets() {
  $path = get_template_directory() . '/assets/prod/';
  $criticalCss = file_get_contents($path . 'critical-css.php');
  echo $criticalCss;
}

/**
 *  Retrieve list of items under category
 *  km_get_related_category_posts()
 *  [categoryposts count=5]
 * 
 */
function km_get_related_category_posts($atts = []){

  $data = is_array($atts) ? $atts : [];
  $categories = get_the_category();
  $catList = [];
  $postType = get_post_type() ?: 'post';
  $count = array_key_exists('count', $data) ? $data['count'] : 5;

  foreach($categories as $cat) {
    array_push($catList, $cat->term_id);
  }

  // the query
  $the_query = new WP_Query( array('post_status' => 'publish','post_type' => $postType, 'cat' => implode(',',$catList), 'posts_per_page' => $count ) ); 
  $string = '';

  // The Loop
  if ( $the_query->have_posts() ) {
    $string .= '<ul class="km-list-by-cat">';
      while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $title = get_the_title() ?: 'Title not provided';
        $string .= '<li><a href="' . get_the_permalink() .'" rel="bookmark">' . $title .'</a></li>';
      }
    $string .= '</ul>';
   } else {
    $string .= '<p>No related posts found.</p>';
  }

  return $string;

  /* Restore original Post Data */
  wp_reset_postdata();
}

/**
 * Simulate non-empty content to enable Gutenberg editor
 *
 * @param bool    $replace Whether to replace the editor.
 * @param WP_Post $post    Post object.
 * @return bool
 */
function enable_gutenberg_editor_for_blog_page( $replace, $post ) {

  if ( ! $replace && absint( get_option( 'page_for_posts' ) ) === $post->ID && empty( $post->post_content ) ) {
      // This comment will be removed by Gutenberg since it won't parse into block.
      $post->post_content = '<!--non-empty-content-->';
  }

  return $replace;

}

function get_all_checked_labels($entry, $field_id) {
    $items = array();
    $field_keys = array_keys($entry);
    
    // Get the form object
    $form = GFAPI::get_form($entry['form_id']);
    
    // Get the field object
    $field = GFAPI::get_field($form, $field_id);
    
    // Loop through every field of the entry in search of each checkbox belonging to this $field_id
    foreach ($field_keys as $input_id) {
        
        // Individual checkbox fields such as "14.1" belongs to field int(14)
        if (is_numeric($input_id) && absint($input_id) == $field_id) {
            $value = rgar($entry, $input_id);
            
            // If checked, $value will be the value from the checkbox (not the label, though sometimes they are the same)
            // If unchecked, $value will be an empty string
            if ("" !== $value) {
                // Find the label for the value
                foreach ($field->choices as $choice) {
                    if ($choice['value'] == $value) {
                        $items[$input_id] = $choice['text'];
                    }
                }
            }
        }
    }
    
    return $items;
}