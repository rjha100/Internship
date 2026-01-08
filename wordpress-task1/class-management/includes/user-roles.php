<?php

/**
 * User Roles and Capabilities
 *
 * @package ClassManagement
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Instructor User Role
 * This should only be called during plugin activation
 */
function cm_register_instructor_role()
{
    if (!get_role('instructor')) {
        add_role(
            'instructor',
            __('Instructor', 'class-management'),
            array(
            'read' => true,
            'edit_classes' => true,
            'edit_published_classes' => true,
            'publish_classes' => true,
            'read_class' => true,
            'delete_classes' => false,
            'delete_published_classes' => false,
            'read_private_classes' => false,
            'edit_private_classes' => false,
            'upload_files' => true,
            'assign_terms' => true,
            'manage_categories' => true,
            )
        );
    }
}

/**
 * Filter map_meta_cap to allow instructors to edit their own classes
 * Instructors can edit classes if they are either:
 * 1. The assigned instructor (instructor meta field)
 * 2. The post author
 */
function cm_instructor_edit_class_cap($caps, $cap, $user_id, $args)
{
    if (in_array($cap, array('edit_class', 'delete_class', 'edit_published_class', 'delete_published_class'))) {
        $user = get_userdata($user_id);

        if ($user && in_array('instructor', (array) $user->roles)) {
            if (!empty($args[0])) {
                $post = get_post($args[0]);

                if ($post && $post->post_type === 'class') {
                    $assigned_instructor = get_post_meta($post->ID, 'instructor', true);

                    $assigned_instructor = intval($assigned_instructor);
                    $user_id = intval($user_id);

                    if ($assigned_instructor === $user_id || $post->post_author == $user_id) {
                        $caps = array('edit_classes');
                    } else {
                        $caps = array('do_not_allow');
                    }
                }
            }
        }
    }

    return $caps;
}
add_filter('map_meta_cap', 'cm_instructor_edit_class_cap', 10, 4);

/**
 * Clear user capability caches when instructor field is updated
 */
function cm_clear_instructor_caps_cache($meta_id, $post_id, $meta_key, $meta_value)
{
    if ($meta_key === 'instructor') {
        $old_instructor = get_post_meta($post_id, 'instructor', true);
        if ($old_instructor && $old_instructor != $meta_value) {
            $old_user = get_userdata($old_instructor);
            if ($old_user) {
                $old_user->get_role_caps(); // Force refresh
            }
        }

        if ($meta_value) {
            $new_user = get_userdata($meta_value);
            if ($new_user) {
                $new_user->get_role_caps(); // Force refresh
            }
        }
    }
}
add_action('updated_post_meta', 'cm_clear_instructor_caps_cache', 10, 4);

/**
 * Prevent admins from changing the post author of class posts
 */
function cm_preserve_class_post_author($data, $postarr)
{
    if (isset($data['post_type']) && $data['post_type'] === 'class') {
        $current_user_id = get_current_user_id();
        $is_admin = current_user_can('manage_options');

        if ($is_admin && !empty($postarr['ID'])) {
            $original_author = get_post_field('post_author', $postarr['ID']);

            if ($original_author && $original_author != $current_user_id) {
                $data['post_author'] = $original_author;
            }
        }
    }

    return $data;
}
add_filter('wp_insert_post_data', 'cm_preserve_class_post_author', 10, 2);

/**
 * Add class capabilities to administrator on plugin activation
 */
function cm_add_admin_capabilities()
{
    $admin_role = get_role('administrator');
    if ($admin_role) {
        $admin_role->add_cap('edit_classes');
        $admin_role->add_cap('edit_others_classes');
        $admin_role->add_cap('publish_classes');
        $admin_role->add_cap('read_private_classes');
        $admin_role->add_cap('delete_classes');
        $admin_role->add_cap('delete_private_classes');
        $admin_role->add_cap('delete_published_classes');
        $admin_role->add_cap('delete_others_classes');
        $admin_role->add_cap('edit_private_classes');
        $admin_role->add_cap('edit_published_classes');
    }
}
