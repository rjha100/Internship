<?php
register_nav_menus(
  array('primary_menu' => 'Top Menu'),
);


add_theme_support('post-thumbnails');
add_theme_support('custom-header');

register_sidebar(
  array(
    'name' => 'Sidebar Location',
    'id'   => 'Sidebar',
  )
);

add_post_type_support('page', 'excerpt');

function theme_enqueue_scripts() {
  wp_enqueue_script('theme-ajax', get_template_directory_uri() . '/js/ajax-newsletter.js', array('jquery'), '1.0', true);
  
  wp_localize_script('theme-ajax', 'themeAjax', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('newsletter_nonce')
  ));
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');

function handle_newsletter_subscription() {
  check_ajax_referer('newsletter_nonce', 'nonce');
  
  $email = sanitize_email($_POST['email']);
  
  if (!is_email($email)) {
    wp_send_json_error(array('message' => 'Please enter a valid email address.'));
  }
  
  $to = $email;
  $subject = 'Thank you for subscribing!';
  $message = 'Thank you for subscribing to the news letter';
  $headers = array('Content-Type: text/html; charset=UTF-8');
  
  $mail_sent = wp_mail($to, $subject, $message, $headers);
  
  if ($mail_sent) {
    wp_send_json_success(array('message' => 'Thank you for subscribing! Check your email.'));
  } else {
    wp_send_json_error(array('message' => 'There was an error. Please try again.'));
  }
}
add_action('wp_ajax_subscribe_newsletter', 'handle_newsletter_subscription');
add_action('wp_ajax_nopriv_subscribe_newsletter', 'handle_newsletter_subscription');

// Add active class to Classes menu item when viewing class archive or taxonomies
function add_active_class_to_classes_menu($classes, $item, $args) {
  if (is_post_type_archive('class') || 
      is_tax('subject') || 
      is_tax('instructor') || 
      is_tax('class_level') ||
      is_singular('class')) {
    
    if (strpos($item->url, '/classes') !== false || 
        strpos($item->title, 'Classes') !== false ||
        $item->object == 'class') {
      $classes[] = 'current-menu-item';
      $classes[] = 'current_page_item';
    }
  }
  
  return $classes;
}
add_filter('nav_menu_css_class', 'add_active_class_to_classes_menu', 10, 3);
