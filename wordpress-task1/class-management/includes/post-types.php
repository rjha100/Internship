<?php

/**
 * Register Custom Post Types
 *
 * @package ClassManagement
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Custom Post Type: Classes
 */
function cm_register_class_post_type()
{
    $labels = array(
    'name'                  => __('Classes', 'class-management'),
    'singular_name'         => __('Class', 'class-management'),
    'menu_name'             => __('Classes', 'class-management'),
    'name_admin_bar'        => __('Class', 'class-management'),
    'add_new'               => __('Add New', 'class-management'),
    'add_new_item'          => __('Add New Class', 'class-management'),
    'new_item'              => __('New Class', 'class-management'),
    'edit_item'             => __('Edit Class', 'class-management'),
    'view_item'             => __('View Class', 'class-management'),
    'all_items'             => __('All Classes', 'class-management'),
    'search_items'          => __('Search Classes', 'class-management'),
    'parent_item_colon'     => __('Parent Classes:', 'class-management'),
    'not_found'             => __('No classes found.', 'class-management'),
    'not_found_in_trash'    => __('No classes found in Trash.', 'class-management')
    );

    $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array('slug' => 'class'),
    'capability_type'    => array('class', 'classes'),
    'map_meta_cap'       => true,
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => 5,
    'menu_icon'          => 'dashicons-welcome-learn-more',
    'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
    'show_in_rest'       => true,
    );

    register_post_type('class', $args);
}
add_action('init', 'cm_register_class_post_type');
