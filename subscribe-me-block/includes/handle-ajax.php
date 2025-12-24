<?php

function smb_enqueue_scripts()
{
    wp_localize_script('smb-subscribe-me-block-view-script', 'smbAjax', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('smb_subscribe_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'smb_enqueue_scripts');

function smb_handle_subscription()
{
    check_ajax_referer('smb_subscribe_nonce', 'nonce');
    $email = sanitize_email($_POST['email']);
    if (! is_email($email)) {
        wp_send_json_error([ 'message' => 'Please enter a valid email address.' ]);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'smb_subscribers';

    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT email FROM $table_name WHERE email = %s",
        $email
    ));

    if ($existing) {
        wp_send_json_error([ 'message' => 'This email is already subscribed.' ]);
    }

    $result = $wpdb->insert(
        $table_name,
        [ 'email' => $email ],
        [ '%s' ]
    );

    if ($result === false) {
        wp_send_json_error([ 'message' => 'Failed to subscribe. Please try again.' ]);
    }

    $to = $email;
    $subject = 'Subscription Confirmation';
    $message = "Thank you for subscribing!\n\nYou have successfully subscribed to our newsletter.\n\nBest regards,\n" . get_bloginfo('name');
    $headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

    wp_mail($to, $subject, $message, $headers);
    wp_send_json_success([ 'message' => 'You have been subscribed successfully' ]);
}
add_action('wp_ajax_smb_subscribe', 'smb_handle_subscription');
add_action('wp_ajax_nopriv_smb_subscribe', 'smb_handle_subscription');
