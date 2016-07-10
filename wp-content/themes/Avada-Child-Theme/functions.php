<?php

// Hide WP admin bar - at least until we launch the site
// add_filter('show_admin_bar', '__return_false');

//==================================================

function avada_child_scripts() {
	if ( ! is_admin() && ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
		$theme_info = wp_get_theme();
		wp_enqueue_style( 'avada-child-stylesheet', get_template_directory_uri() . '/style.css', array(), $theme_info->get( 'Version' ) );
	}
  wp_enqueue_style ('custom-css', get_stylesheet_directory_uri() . '/css/custom.css' );

  wp_enqueue_script( 'jquery-placeholder', get_stylesheet_directory_uri() . '/js/jquery.placeholder.min.js', '', '1.0', true );
  wp_enqueue_script( 'ouibounce', get_stylesheet_directory_uri() . '/js/ouibounce.min.js', '', '1.0', true );
  wp_enqueue_script( 'jquery-accordion', get_stylesheet_directory_uri() . '/js/accordion.js', '', '1.0', true );
  wp_enqueue_script( 'replace-number', get_stylesheet_directory_uri() . '/js/replace-phone-number.js', '', '1.0', true );  
  wp_enqueue_script( 'main-child', get_stylesheet_directory_uri() . '/js/main-child.js', '', '1.0', true );
  wp_enqueue_script( 'rslides', get_stylesheet_directory_uri() . '/js/responsiveslides.min.js', '', '1.0', true );    
  wp_enqueue_script( 'isotope', get_stylesheet_directory_uri() . '/js/isotope.min.js', '', '1.0', true );
}
add_action('wp_enqueue_scripts', 'avada_child_scripts');

//==================================================

// Defer parsing of JavaScript
//function defer_parsing_of_js ( $url ) {
//if ( FALSE === strpos( $url, '.js' ) ) return $url;
//if ( strpos( $url, 'jquery.js' ) ) return $url;
//return "$url' defer ";
//}
//add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );

//==================================================

// Add new skin to Royal Slider options
add_filter('new_royalslider_skins', 'new_royalslider_add_custom_skin', 10, 2);
function new_royalslider_add_custom_skin($skins) {
      $skins['myCustomSkin'] = array(
           'label' => 'Custom Skin',
           'path' => get_stylesheet_directory_uri() . '/rs-custom-skin.css'  // get_stylesheet_directory_uri returns path to your theme folder
      );
      $skins['myCustomSkinAlt'] = array(
           'label' => 'Custom Skin Alt',
           'path' => get_stylesheet_directory_uri() . '/rs-custom-skin-alt.css'  // get_stylesheet_directory_uri returns path to your theme folder
      );
      return $skins;
}

//==================================================

// Allow SVG files to be uploaded in Wordpress media uploader
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

//==================================================

// Add Categories and Tags metaboxes to pages
function add_categories_to_pages() {  
// Add tag metabox to page
register_taxonomy_for_object_type('post_tag', 'page'); 
// Add category metabox to page
register_taxonomy_for_object_type('category', 'page');  
}
 // Add to the init hook of your theme functions.php file 
add_action( 'init', 'add_categories_to_pages' );

//==================================================

// Move Page Attributes metabox to highest priority on pages
function custom_move_meta_box(){
    remove_meta_box( 'postimagediv', 'page', 'side' );
    add_meta_box('postimagediv', __('Featured Image'), 'post_thumbnail_meta_box', 'page', 'side', 'low');

    remove_meta_box( 'categorydiv', 'page', 'side' );
    add_meta_box('categorydiv', __('Categories'), 'post_categories_meta_box', 'page', 'side', 'default');

    remove_meta_box( 'tagsdiv-post_tag', 'page', 'side' );
    add_meta_box('tagsdiv-post_tag', __('Tags'), 'post_tags_meta_box', 'page', 'side', 'default');
}
add_action('do_meta_boxes', 'custom_move_meta_box');

//==================================================

// Create custom meta descriptions and meta titles using custom fields
/* Basic WP SEO
  Usage: 
    1. add this code to functions.php
    2. replace the $default_keywords with your own
    3. add <?php echo basic_wp_seo(); ?> to header.php
    4. test well and fine tune as needed

  Optional: add custom description, keywords, and/or title
  to any post or page using these custom field keys:

    seo_desc
    seo_title

  To migrate from any SEO plugin, replace its custom field 
  keys with those listed above. More information:

    @ http://digwp.com/2013/08/basic-wp-seo/
*/
function basic_wp_seo() {
  global $page, $paged, $post;
  $output = '';

  // Title
  $title_custom = get_post_meta($post->ID, 'seo_title', true);
  $url = ltrim(esc_url($_SERVER['REQUEST_URI']), '/');
  $name = get_bloginfo('name', 'display');
  $title = trim(wp_title('', false));
  $cat = single_cat_title('', false);
  $tag = single_tag_title('', false);
  $search = get_search_query();

  if (!empty($title_custom)) $title = htmlentities($title_custom);
  if ($paged >= 2 || $page >= 2) $page_number = ' | ' . sprintf('Page %s', max($paged, $page));
  else $page_number = '';

  if (is_home() || is_front_page()) $seo_title = $title;
  elseif (is_singular())            $seo_title = $title;
  elseif (is_tag())                 $seo_title = 'Tag Archive: ' . $tag;
  elseif (is_category())            $seo_title = 'Category Archive: ' . $cat;
  elseif (is_archive())             $seo_title = 'Archive: ' . $title;
  elseif (is_search())              $seo_title = 'Search: ' . $search;
  elseif (is_404())                 $seo_title = '404 - Not Found: ' . $url;
  else                              $seo_title = $name . ' | ' . $description;

  $output .= "\t\t" . '<title>' . esc_attr($seo_title . $page_number) . '</title>' . "\n";  

  // Description
  $seo_desc = get_post_meta($post->ID, 'seo_desc', true);
  $pagedata = get_post($post->ID);
  if (is_singular()) {
    if (!empty($seo_desc)) {    
      $output .= '<meta name="description" content="' . htmlentities($seo_desc) . '">' . "\n";
    }
  }
  else {
    $output = '';
  }

  return $output;
}

//==================================================

// Add more options to the WYSIWYG editor
// Enable font size & font family selects in the editor
if ( ! function_exists( 'wpex_mce_buttons' ) ) {
  function wpex_mce_buttons( $buttons ) {
    array_unshift( $buttons, 'fontsizeselect' ); // Add Font Size Select
    return $buttons;
  }
}
add_filter( 'mce_buttons_3', 'wpex_mce_buttons' );

//==================================================

// Customize mce editor font sizes
if ( ! function_exists( 'wpex_mce_text_sizes' ) ) {
  function wpex_mce_text_sizes( $initArray ){
    $initArray['fontsize_formats'] = "8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px 23px 24px 28px 32px 36px 40px 44px 48px 52px 56px 60px 64px 68px 72px";
    return $initArray;
  }
}
add_filter( 'tiny_mce_before_init', 'wpex_mce_text_sizes' );

//==================================================

// Add Formats Dropdown Menu To MCE
if ( ! function_exists( 'wpex_style_select' ) ) {
  function wpex_style_select( $buttons ) {
    array_push( $buttons, 'styleselect' );
    return $buttons;
  }
}
add_filter( 'mce_buttons_3', 'wpex_style_select' );

//==================================================

// Add source code button to WYSIWYG editor

function enable_more_buttons($buttons) {
 $buttons[] = 'code';
 return $buttons;
}

add_filter("mce_buttons_2", "enable_more_buttons");

//==================================================/*

// Add new styles to the TinyMCE options
function my_mce_before_init_insert_formats( $init_array ) {  
  // Define the style_formats array
  $style_formats = array(  
    array(
      'title' => __( 'Text Styles', 'wpex' ),
      'items' => array(
        array(  
          'title'     => 'Thin Text',  
          'inline'    => 'span',  
          'classes'   => 'txt-thin',      
        ),  
        array(  
          'title'     => 'Semibold Text',  
          'inline'    => 'span',  
          'classes'   => 'txt-semibold',
        ),
        array(  
          'title'     => 'Lowercase',  
          'inline'    => 'span',  
          'classes'   => 'lowercase',
        ),
        array(  
          'title'     => 'Uppercase',  
          'inline'    => 'span',  
          'classes'   => 'uppercase',
        ),
        array(  
          'title'     => 'Capitalize First',  
          'inline'    => 'span',  
          'classes'   => 'capitalize',
        ),
      ),
    ),
    array(
      'title' => __( 'Colors', 'wpex' ),
      'items' => array(
        array(  
          'title'     => 'Orange',  
          'inline'    => 'span',  
          'classes'   => 'orange',
        ),
        array(  
          'title'     => 'Blue',  
          'inline'    => 'span',  
          'classes'   => 'blue',
        ),
        array(  
          'title'     => 'Main Gray',  
          'inline'    => 'span',  
          'classes'   => 'main-gray',
        ),
        array(  
          'title'     => 'Dark Gray',  
          'inline'    => 'span',  
          'classes'   => 'dark-gray',
        ),
        array(  
          'title'     => 'Light Gray',  
          'inline'    => 'span',  
          'classes'   => 'light-gray',
        ),
      ),
    ),
    array(
      'title' => __( 'Line Heights', 'wpex' ),
      'items' => array(
        array(  
          'title'     => 'Line Height 1.0',  
          'block'    => 'div',  
          'classes'   => 'lh-1-0',
        ),
        array(  
          'title'     => 'Line Height 1.1',  
          'block'    => 'div',  
          'classes'   => 'lh-1-1',
        ),
        array(  
          'title'     => 'Line Height 1.2',  
          'block'    => 'div',  
          'classes'   => 'lh-1-2',
        ),
        array(  
          'title'     => 'Line Height 1.3',  
          'block'    => 'div',  
          'classes'   => 'lh-1-3',
        ),
        array(  
          'title'     => 'Line Height 1.4',  
          'block'    => 'div',  
          'classes'   => 'lh-1-4',
        ),
        array(  
          'title'     => 'Line Height 1.5',  
          'block'    => 'div',  
          'classes'   => 'lh-1-5',
        ),
        array(  
          'title'     => 'Line Height 1.6',  
          'block'    => 'div',  
          'classes'   => 'lh-1-6',
        ),
      ),
    ),
    array(
      'title' => __( 'Custom Styles', 'wpex' ),
      'items' => array(
        array(  
          'title'     => 'Header Bar Gray',  
          'block'    => 'div',  
          'classes'   => 'header-bar-gray',
        ),
        array(  
          'title'     => 'Blue Bar',  
          'block'    => 'div',  
          'classes'   => 'blue-bar',
        ),
      ),
    ),
    array(
      'title' => __( 'Spacing', 'wpex' ),
      'items' => array(
        array(  
          'title'     => 'Margin Bottom 0',  
          'block'     => 'div',  
          'classes'   => 'bot-mar-0',
        ),
        array(  
          'title'     => 'Margin Bottom 5',  
          'block'     => 'div',  
          'classes'   => 'bot-mar-5',
        ),
        array(  
          'title'     => 'Margin Bottom 10',  
          'block'     => 'div',  
          'classes'   => 'bot-mar-10',
        ),
        array(  
          'title'     => 'Margin Bottom 15',  
          'block'     => 'div',  
          'classes'   => 'bot-mar-15',
        ),
        array(  
          'title'     => 'Margin Bottom 20',  
          'block'     => 'div',  
          'classes'   => 'bot-mar-20',
        ),
        array(  
          'title'     => 'Margin Bottom 25',  
          'block'     => 'div',  
          'classes'   => 'bot-mar-25',
        ),
        array(  
          'title'     => 'Margin Bottom 30',  
          'block'     => 'div',  
          'classes'   => 'bot-mar-30',
        ),
        array(  
          'title'     => 'Margin Bottom 35',  
          'block'     => 'div',  
          'classes'   => 'bot-mar-35',
        ),
        array(  
          'title'     => 'Margin Bottom 40',  
          'block'     => 'div',  
          'classes'   => 'bot-mar-40',
        ),
      ),
    ),
    array(
      'title' => __( 'Width', 'wpex' ),
      'items' => array(
        array(  
          'title'     => 'Width 90%',  
          'block'     => 'div',  
          'classes'   => 'w90pct',
        ),
        array(  
          'title'     => 'Width 80%',  
          'block'     => 'div',  
          'classes'   => 'w80pct',
        ),
        array(  
          'title'     => 'Width 70%',  
          'block'     => 'div',  
          'classes'   => 'w70pct',
        ),
        array(  
          'title'     => 'Width 60%',  
          'block'     => 'div',  
          'classes'   => 'w60pct',
        ),
        array(  
          'title'     => 'Width 50%',  
          'block'     => 'div',  
          'classes'   => 'w50pct',
        ),
      ),
    ),
  );  
  // Insert the array, JSON ENCODED, into 'style_formats'
  $init_array['style_formats'] = json_encode( $style_formats );  
  
  return $init_array;  
  
} 
// Attach callback to 'tiny_mce_before_init' 
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' );

//==================================================

// Shortcode for USA map on city pages
function usa_map_embed() {
  return '<div class="usa-map">
  <img src="http://localhost:8888/meetingtomorrow/wp-content/uploads/2015/01/usa-orange.svg" alt="" />
  <div class="star"><img src="http://localhost:8888/meetingtomorrow/wp-content/uploads/2015/01/icon-star-white.svg" alt="" /></div>
  <div class="dot dot-1"></div><div class="dot dot-2"></div><div class="dot dot-3"></div><div class="dot dot-4"></div><div class="dot dot-5"></div><div class="dot dot-6"></div><div class="dot dot-7"></div><div class="dot dot-8"></div><div class="dot dot-9"></div><div class="dot dot-10"></div><div class="dot dot-11"></div><div class="dot dot-12"></div><div class="dot dot-13"></div><div class="dot dot-14"></div><div class="dot dot-15"></div><div class="dot dot-16"></div><div class="dot dot-17"></div><div class="dot dot-18"></div><div class="dot dot-19"></div><div class="dot dot-20"></div><div class="dot dot-21"></div><div class="dot dot-22"></div><div class="dot dot-23"></div><div class="dot dot-24"></div><div class="dot dot-25"></div><div class="dot dot-26"></div><div class="dot dot-27"></div><div class="dot dot-28"></div><div class="dot dot-29"></div><div class="dot dot-30"></div><div class="dot dot-31"></div><div class="dot dot-32"></div><div class="dot dot-33"></div><div class="dot dot-34"></div><div class="dot dot-35"></div><div class="dot dot-36"></div><div class="dot dot-37"></div><div class="dot dot-38"></div><div class="dot dot-39"></div><div class="dot dot-40"></div><div class="dot dot-41"></div><div class="dot dot-42"></div><div class="dot dot-43"></div><div class="dot dot-44"></div><div class="dot dot-45"></div><div class="dot dot-46"></div><div class="dot dot-47"></div><div class="dot dot-48"></div><div class="dot dot-49"></div><div class="dot dot-50"></div><div class="dot dot-50"></div><div class="dot dot-51"></div><div class="dot dot-52"></div><div class="dot dot-53"></div><div class="dot dot-54"></div><div class="dot dot-55"></div><div class="dot dot-56"></div><div class="dot dot-57"></div><div class="dot dot-58"></div><div class="dot dot-59"></div><div class="dot dot-60"></div><div class="dot dot-60"></div><div class="dot dot-61"></div><div class="dot dot-62"></div><div class="dot dot-63"></div><div class="dot dot-64"></div><div class="dot dot-65"></div><div class="dot dot-66"></div><div class="dot dot-67"></div><div class="dot dot-68"></div><div class="dot dot-69"></div><div class="dot dot-70"></div><div class="dot dot-71"></div><div class="dot dot-72"></div><div class="dot dot-73"></div><div class="dot dot-74"></div><div class="dot dot-75"></div><div class="dot dot-76"></div><div class="dot dot-77"></div><div class="dot dot-78"></div><div class="dot dot-79"></div><div class="dot dot-80"></div><div class="dot dot-81"></div><div class="dot dot-82"></div><div class="dot dot-83"></div><div class="dot dot-84"></div><div class="dot dot-85"></div><div class="dot dot-86"></div><div class="dot dot-87"></div><div class="dot dot-88"></div><div class="dot dot-89"></div><div class="dot dot-90"></div><div class="dot dot-91"></div><div class="dot dot-92"></div><div class="dot dot-93"></div><div class="dot dot-94"></div><div class="dot dot-95"></div><div class="dot dot-96"></div><div class="dot dot-97"></div><div class="dot dot-98"></div><div class="dot dot-99"></div><div class="dot dot-100"></div><div class="dot dot-101"></div><div class="dot dot-102"></div><div class="dot dot-103"></div><div class="dot dot-104"></div><div class="dot dot-105"></div><div class="dot dot-106"></div><div class="dot dot-107"></div><div class="dot dot-108"></div><div class="dot dot-109"></div><div class="dot dot-110"></div><div class="dot dot-111"></div><div class="dot dot-112"></div><div class="dot dot-113"></div><div class="dot dot-114"></div><div class="dot dot-115"></div><div class="dot dot-116"></div><div class="dot dot-117"></div><div class="dot dot-118"></div><div class="dot dot-119"></div><div class="dot dot-120"></div></div>';
}
add_shortcode('usa_map', 'usa_map_embed');



function change_password_protected_text($output)
{
    $adminEmail = get_option( 'admin_email' );
    $newPasswordText = 'This is a password protected page to request the password please email <a href="mailto:'.antispambot( $adminEmail ).'">'.antispambot( $adminEmail ).'</a>';
    $output = str_replace('This content is password protected YOOOO ss. To view it please enter your password below:', $newPasswordText, $output);

    return $output;
}
add_filter( 'the_password_form', 'change_password_protected_text', 999);

//==================================================

// Create custom default avatar

add_filter( 'avatar_defaults', 'new_default_avatar' );
function new_default_avatar ( $avatar_defaults ) {
    //Set the URL where the image file for your avatar is located
    $new_avatar_url = get_bloginfo( 'stylesheet_directory' ) . '/img/mt-avatar-default.png';
    //Set the text that will appear to the right of your avatar in Settings>>Discussion
    $avatar_defaults[$new_avatar_url] = 'Default MT Avatar';
    return $avatar_defaults;
}

//==================================================

// Testimonial custom posts
function custom_post_testimonial() { 
  register_post_type( 'testimonial', 
    array('labels' => array(
        'name' => __('Testimonials', 'emc'), 
        'singular_name' => __('Testimonial', 'emc'), 
        'all_items' => __('All Testimonials', 'emc'), 
        'add_new' => __('Add New', 'emc'), 
        'add_new_item' => __('Add New Testimonial', 'emc'), 
        'edit' => __( 'Edit', 'emc' ), 
        'edit_item' => __('Edit Testimonial', 'emc'), 
        'new_item' => __('New Testimonial', 'emc'), 
        'view_item' => __('View Testimonial', 'emc'), 
        'search_items' => __('Search Testimonial', 'emc'), 
        'not_found' =>  __('Nothing found in the Database.', 'emc'),
        'not_found_in_trash' => __('Nothing found in Trash', 'emc'), 
        'parent_item_colon' => ''
      ), 
        'description' => __( 'This is the example Testimonial', 'emc' ), 
        'public' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'show_ui' => true,
        'query_var' => true,
        'menu_position' => 20, 
        'rewrite' => array( 'slug' => 'testimonials', 'with_front' => false ),
        'has_archive' => 'testimonials', 
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array( 'title', 'category', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky', 'page-attributes'
      ),
        'taxonomies' => array('category', 'post_tag') // this is IMPORTANT
  ));   
} 
  add_action( 'init', 'custom_post_testimonial');

//==================================================

/* Add new image sizes
add_theme_support( 'post-thumbnails' );

if ( function_exists( 'add_image_size' ) ) {
      add_image_size( 'testimonial-slider', 250, 280, array('center','center'), true ); //(cropped)
    }
    add_filter('image_size_names_choose', 'my_image_sizes');
    function my_image_sizes($sizes) {
      $addsizes = array(
        "testimonial-slider" => __( "Testimonial Slider")
      );
      $newsizes = array_merge($sizes, $addsizes);
      return $newsizes;
    }
*/

//==================================================

// Remove search engine date stamp from Pages, except News & Insights (Blog) page
function remove_page_snippet_date(){
  if( is_page() && !is_page(6719) ){
    add_filter('the_time', '__return_false');
    add_filter('get_the_time', '__return_false');
    add_filter('the_modified_time', '__return_false');
    add_filter('get_the_modified_time', '__return_false');
    add_filter('the_date', '__return_false');
    add_filter('get_the_date', '__return_false');
    add_filter('the_modified_date', '__return_false');
    add_filter('get_the_modified_date', '__return_false');
    add_filter('get_comment_date', '__return_false');
    add_filter('get_comment_time', '__return_false');
  }
}
add_action( 'loop_start', 'remove_page_snippet_date' );

//==================================================

// No-index Archive, Category, and Tag pages
function add_noindex_tags(){  
  # More info here: http://orbitingweb.com/blog/wp-archive-noindex/
  # Add noindex tag to all archive, category, and tag pages.
  if( is_archive() || is_category() || is_tag() )
  echo '<meta name="robots" content="noindex,follow">';
}
add_action('wp_head','add_noindex_tags', 4 );

//==================================================

// Add category name as a class to the <body> element
add_filter('body_class','add_category_to_page');
function add_category_to_page($classes) {
  if (!is_admin() && is_page() ) {
    global $post;
    foreach((get_the_category($post->ID)) as $category) {
      // add category slug to the $classes array
      $classes[] = $category->category_nicename;
    }
  }
  // return the $classes array
  return $classes;
}

//======================================================

// Add Page Slug to Body Class
function add_slug_body_class( $classes ) {
  global $post;
  if ( isset( $post ) ) {
  $classes[] = $post->post_type . '-' . $post->post_name;
  }
  return $classes;
  }
add_filter( 'body_class', 'add_slug_body_class' );

//======================================================

// Add category nicenames in body and post class
add_filter('body_class','body_class_section');

function body_class_section($classes) {
    global $wpdb, $post;
    if (is_page()) {
        if ($post->post_parent) {
            $parent  = end(get_post_ancestors($current_page_id));
        } else {
            $parent = $post->ID;
        }
        $post_data = get_post($parent, ARRAY_A);
        $classes[] = 'parent-' . $post_data['post_name'];
    }
    return $classes;
}

//==================================================

// Remove Emoji Scripts
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );


//==================================================

// Remove auto-formatting of quotes and other special characters
/*remove_filter('the_content', 'wptexturize');
remove_filter('the_excerpt', 'wptexturize');
remove_filter('comment_text', 'wptexturize');
remove_filter('the_title', 'wptexturize');*/

//==================================================

// Register another Sidebar
add_action( 'widgets_init', 'theme_slug_widgets_init' );
function theme_slug_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Alternate Sidebar', 'theme-slug' ),
        'id' => 'sidebar-alt',
        'before_title'  => '<h3 class="widgettitle">',
        'after_title'   => '</h3>',
    ) );
}