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

function theme_enqueue_scripts()
{
    wp_enqueue_script('theme-ajax', get_template_directory_uri() . '/js/ajax-newsletter.js', array('jquery'), '1.0', true);

    wp_localize_script('theme-ajax', 'themeAjax', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('newsletter_nonce')
    ));

    wp_enqueue_script('auth-modals', get_template_directory_uri() . '/js/auth-modals.js', array('jquery'), '1.0', true);

    wp_enqueue_style('class-details', get_template_directory_uri() . '/css/class-details.css', array(), '1.0');

    if (is_page_template('templates/template-classes-grid.php')) {
        wp_enqueue_style('classes-grid', get_template_directory_uri() . '/css/classes-grid.css', array(), '1.0');
        wp_enqueue_script('class-filters', get_template_directory_uri() . '/js/class-filters.js', array('jquery'), '1.0', true);
    }

    if (is_singular('class')) {
        wp_enqueue_script('reduced-rate-modal', get_template_directory_uri() . '/js/reduced-rate-modal.js', array('jquery'), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');

/**
 * Ensure instructor role has proper capabilities for class post type
 * Run this after plugins are loaded
 */
function ensure_instructor_capabilities()
{
    $instructor_role = get_role('instructor');

    if ($instructor_role) {
        $capabilities = array(
            'read',
            'edit_classes',
            'edit_published_classes',
            'publish_classes',
            'read_class',
            'upload_files',
            'edit_posts',
            'publish_posts'
        );

        foreach ($capabilities as $cap) {
            if (!$instructor_role->has_cap($cap)) {
                $instructor_role->add_cap($cap);
            }
        }
    }
}
add_action('plugins_loaded', 'ensure_instructor_capabilities');

function handle_newsletter_subscription()
{
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

function handle_reduced_rate_application()
{
    check_ajax_referer('newsletter_nonce', 'nonce');

    $class_id = intval($_POST['class_id']);
    $class_title = sanitize_text_field($_POST['class_title']);
    $applicant_name = sanitize_text_field($_POST['applicant_name']);
    $applicant_email = sanitize_email($_POST['applicant_email']);
    $reason = sanitize_textarea_field($_POST['reason']);

    if (!is_email($applicant_email)) {
        wp_send_json_error(array('message' => 'Please enter a valid email address.'));
    }

    if (empty($applicant_name) || empty($reason)) {
        wp_send_json_error(array('message' => 'Please fill in all required fields.'));
    }

    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    $subject = 'New Reduced-Rate Application for ' . $class_title;

    $message = '
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <style>
      body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; }
      .container { max-width: 600px; margin: 0 auto; padding: 20px; }
      .header { background: linear-gradient(135deg, #00ACB4 0%, #0088A8 100%); color: white; padding: 30px 20px; border-radius: 8px 8px 0 0; text-align: center; }
      .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
      .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; }
      .info-row { margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; }
      .info-row:last-child { border-bottom: none; }
      .label { font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
      .value { font-size: 16px; color: #333; }
      .reason-box { background: #f9f9f9; border-left: 4px solid #00ACB4; padding: 15px; margin-top: 10px; border-radius: 4px; }
      .footer { text-align: center; padding: 20px; color: #999; font-size: 13px; }
      a { color: #00ACB4; text-decoration: none; }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="header">
        <h1>📋 New Reduced-Rate Application</h1>
      </div>
      <div class="content">
        <div class="info-row">
          <div class="label">Class</div>
          <div class="value">' . esc_html($class_title) . '</div>
        </div>
        <div class="info-row">
          <div class="label">Applicant Name</div>
          <div class="value">' . esc_html($applicant_name) . '</div>
        </div>
        <div class="info-row">
          <div class="label">Email Address</div>
          <div class="value"><a href="mailto:' . esc_attr($applicant_email) . '">' . esc_html($applicant_email) . '</a></div>
        </div>
        <div class="info-row">
          <div class="label">Reason for Application</div>
          <div class="reason-box">' . nl2br(esc_html($reason)) . '</div>
        </div>
      </div>
      <div class="footer">
        <p>This application was submitted from ' . esc_html($site_name) . '</p>
        <p>Submitted on ' . date('F j, Y \a\t g:i A') . '</p>
      </div>
    </div>
  </body>
  </html>';

    $headers = array('Content-Type: text/html; charset=UTF-8');
    $mail_sent = wp_mail($admin_email, $subject, $message, $headers);

    $applicant_subject = 'Application Received: ' . $class_title;

    $applicant_message = '
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <style>
      body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; }
      .container { max-width: 600px; margin: 0 auto; padding: 20px; }
      .header { background: linear-gradient(135deg, #00ACB4 0%, #0088A8 100%); color: white; padding: 30px 20px; border-radius: 8px 8px 0 0; text-align: center; }
      .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
      .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; }
      .message { font-size: 16px; line-height: 1.8; color: #333; margin-bottom: 20px; }
      .highlight-box { background: #f0f9fa; border: 2px solid #00ACB4; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; }
      .class-name { font-size: 18px; font-weight: 600; color: #00ACB4; margin: 10px 0; }
      .footer { text-align: center; padding: 20px; color: #999; font-size: 13px; border-top: 1px solid #e0e0e0; margin-top: 30px; }
      .checkmark { font-size: 48px; color: #00ACB4; }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="header">
        <h1>✓ Application Received</h1>
      </div>
      <div class="content">
        <p class="message">Dear ' . esc_html($applicant_name) . ',</p>
        <p class="message">Thank you for submitting your application for a reduced-rate spot!</p>

        <div class="highlight-box">
          <div class="checkmark">✓</div>
          <div class="class-name">' . esc_html($class_title) . '</div>
        </div>

        <p class="message">We have received your application and our team will review it carefully. We understand that financial circumstances vary, and we appreciate you taking the time to share your situation with us.</p>

        <p class="message">We will get back to you within 3-5 business days with a decision. If you have any questions in the meantime, please don\'t hesitate to reach out.</p>

        <div class="footer">
          <p><strong>Best regards,</strong><br>' . esc_html($site_name) . ' Team</p>
        </div>
      </div>
    </div>
  </body>
  </html>';

    wp_mail($applicant_email, $applicant_subject, $applicant_message, $headers);

    if ($mail_sent) {
        wp_send_json_success(array('message' => 'Application submitted successfully!'));
    } else {
        wp_send_json_error(array('message' => 'There was an error submitting your application. Please try again.'));
    }
}
add_action('wp_ajax_submit_reduced_rate_application', 'handle_reduced_rate_application');
add_action('wp_ajax_nopriv_submit_reduced_rate_application', 'handle_reduced_rate_application');

/**
 * Extend search to include custom fields for class post type
 */
function extend_class_search($search, $wp_query)
{
    global $wpdb;

    $post_type = $wp_query->get('post_type');
    $search_term = $wp_query->get('s');

    if (is_admin() || empty($search) || empty($search_term) || $post_type !== 'class') {
        return $search;
    }

    $like_term = '%' . $wpdb->esc_like($search_term) . '%';

    $meta_search = " OR EXISTS (
    SELECT 1 FROM {$wpdb->postmeta}
    WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
    AND (
      ({$wpdb->postmeta}.meta_key = 'location_address' AND {$wpdb->postmeta}.meta_value LIKE '{$like_term}')
      OR ({$wpdb->postmeta}.meta_key = 'location_city' AND {$wpdb->postmeta}.meta_value LIKE '{$like_term}')
      OR ({$wpdb->postmeta}.meta_key = 'location_state' AND {$wpdb->postmeta}.meta_value LIKE '{$like_term}')
      OR ({$wpdb->postmeta}.meta_key = 'location_zip' AND {$wpdb->postmeta}.meta_value LIKE '{$like_term}')
      OR ({$wpdb->postmeta}.meta_key = 'category_tag' AND {$wpdb->postmeta}.meta_value LIKE '{$like_term}')
      OR ({$wpdb->postmeta}.meta_key = 'level_tag' AND {$wpdb->postmeta}.meta_value LIKE '{$like_term}')
      OR ({$wpdb->postmeta}.meta_key = 'class_time' AND {$wpdb->postmeta}.meta_value LIKE '{$like_term}')
    )
  )
  OR EXISTS (
    SELECT 1 FROM {$wpdb->postmeta} pm
    INNER JOIN {$wpdb->posts} instructor ON pm.meta_value = instructor.ID
    WHERE pm.post_id = {$wpdb->posts}.ID
    AND pm.meta_key = 'instructor'
    AND instructor.post_title LIKE '{$like_term}'
  )";

    $search = preg_replace('/AND \(\(/i', 'AND ((' . $meta_search . ' OR (', $search);

    return $search;
}
add_filter('posts_search', 'extend_class_search', 10, 2);

/**
 * Handle user registration via AJAX
 * Assigns instructor role to new users
 */
function handle_user_registration()
{
    check_ajax_referer('newsletter_nonce', 'nonce');

    $name = sanitize_text_field($_POST['name']);
    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];

    if (empty($username)) {
        wp_send_json_error(array('message' => 'Please enter a username.'));
    }

    if (!validate_username($username)) {
        wp_send_json_error(array('message' => 'Username contains invalid characters.'));
    }

    if (username_exists($username)) {
        wp_send_json_error(array('message' => 'This username is already taken. Please choose another.'));
    }

    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Please enter a valid email address.'));
    }

    if (email_exists($email)) {
        wp_send_json_error(array('message' => 'This email is already registered.'));
    }

    if (strlen($password) < 8) {
        wp_send_json_error(array('message' => 'Password must be at least 8 characters long.'));
    }

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(array('message' => $user_id->get_error_message()));
    }

    wp_update_user(array(
    'ID' => $user_id,
    'display_name' => $name,
    'first_name' => $name
    ));

    $user = new WP_User($user_id);
    $user->set_role('instructor');

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    wp_send_json_success(array(
    'message' => 'Account created successfully!',
    'user_id' => $user_id
    ));
}
add_action('wp_ajax_nopriv_user_register', 'handle_user_registration');
add_action('wp_ajax_user_register', 'handle_user_registration');

/**
 * Check if username or email is available (AJAX)
 */
function check_user_availability()
{
    check_ajax_referer('newsletter_nonce', 'nonce');

    $type = sanitize_text_field($_POST['type']); // 'username' or 'email'
    $value = sanitize_text_field($_POST['value']);

    if ($type === 'username') {
        if (empty($value)) {
            wp_send_json_error(array('message' => 'Username is required.'));
        }

        if (username_exists($value)) {
            wp_send_json_error(array('message' => 'This username is already taken.'));
        } else {
            wp_send_json_success(array('message' => 'Username is available.'));
        }
    } elseif ($type === 'email') {
        if (empty($value)) {
            wp_send_json_error(array('message' => 'Email is required.'));
        }

        if (!is_email($value)) {
            wp_send_json_error(array('message' => 'Please enter a valid email address.'));
        }

        if (email_exists($value)) {
            wp_send_json_error(array('message' => 'This email is already registered.'));
        } else {
            wp_send_json_success(array('message' => 'Email is available.'));
        }
    } else {
        wp_send_json_error(array('message' => 'Invalid request.'));
    }
}
add_action('wp_ajax_nopriv_check_user_availability', 'check_user_availability');
add_action('wp_ajax_check_user_availability', 'check_user_availability');

/**
 * Handle user login via AJAX
 */
function handle_user_login()
{
    check_ajax_referer('newsletter_nonce', 'nonce');

    $email_or_username = sanitize_text_field($_POST['email_or_username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';

    if (is_email($email_or_username)) {
        $user = get_user_by('email', $email_or_username);

        if (!$user) {
            wp_send_json_error(array('message' => 'Invalid email or password.'));
        }

        $username = $user->user_login;
    } else {
        $username = $email_or_username;
        $user = get_user_by('login', $username);

        if (!$user) {
            wp_send_json_error(array('message' => 'Invalid username or password.'));
        }
    }

    $user_auth = wp_authenticate($username, $password);

    if (is_wp_error($user_auth)) {
        wp_send_json_error(array('message' => 'Invalid username/email or password.'));
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, $remember);

    wp_send_json_success(array(
    'message' => 'Login successful!',
    'user_id' => $user->ID
    ));
}
add_action('wp_ajax_nopriv_user_login', 'handle_user_login');
add_action('wp_ajax_user_login', 'handle_user_login');

/**
 * Handle user logout via AJAX
 */
function handle_user_logout()
{
    check_ajax_referer('newsletter_nonce', 'nonce');

    wp_logout();

    wp_send_json_success(array(
    'message' => 'Logged out successfully!'
    ));
}
add_action('wp_ajax_user_logout', 'handle_user_logout');
