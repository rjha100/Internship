<?php

if (! defined('ABSPATH')) {
    exit;
}

function classes_register_post_type()
{
    $labels = [
        'name'               => __('Classes', 'classes-cpt'),
        'singular_name'      => __('Class', 'classes-cpt'),
        'menu_name'          => __('Classes', 'classes-cpt'),
        'name_admin_bar'     => __('Class', 'classes-cpt'),
        'add_new'            => __('Add New', 'classes-cpt'),
        'add_new_item'       => __('Add New Class', 'classes-cpt'),
        'new_item'           => __('New Class', 'classes-cpt'),
        'edit_item'          => __('Edit Class', 'classes-cpt'),
        'view_item'          => __('View Class', 'classes-cpt'),
        'all_items'          => __('All Classes', 'classes-cpt'),
        'search_items'       => __('Search Classes', 'classes-cpt'),
        'parent_item_colon'  => __('Parent Classes:', 'classes-cpt'),
        'not_found'          => __('No classes found.', 'classes-cpt'),
        'not_found_in_trash' => __('No classes found in Trash.', 'classes-cpt'),
    ];

    $supports = [ 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions', 'custom-fields' ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => [ 'slug' => 'classes' ],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-welcome-learn-more',
        'supports'           => $supports,
        'show_in_rest'       => true,
        'rest_base'          => 'classes',
    ];

    register_post_type('class', $args);
}
