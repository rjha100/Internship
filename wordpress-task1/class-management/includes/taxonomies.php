<?php

/**
 * Register Custom Taxonomies
 *
 * @package ClassManagement
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Custom Taxonomy: Class Subject
 */
function cm_register_class_subject_taxonomy()
{
    $labels = array(
    'name'                       => __('Class Subjects', 'class-management'),
    'singular_name'              => __('Class Subject', 'class-management'),
    'menu_name'                  => __('Subjects', 'class-management'),
    'all_items'                  => __('All Subjects', 'class-management'),
    'parent_item'                => __('Parent Subject', 'class-management'),
    'parent_item_colon'          => __('Parent Subject:', 'class-management'),
    'new_item_name'              => __('New Subject Name', 'class-management'),
    'add_new_item'               => __('Add New Subject', 'class-management'),
    'edit_item'                  => __('Edit Subject', 'class-management'),
    'update_item'                => __('Update Subject', 'class-management'),
    'view_item'                  => __('View Subject', 'class-management'),
    'separate_items_with_commas' => __('Separate subjects with commas', 'class-management'),
    'add_or_remove_items'        => __('Add or remove subjects', 'class-management'),
    'choose_from_most_used'      => __('Choose from the most used', 'class-management'),
    'popular_items'              => __('Popular Subjects', 'class-management'),
    'search_items'               => __('Search Subjects', 'class-management'),
    'not_found'                  => __('Not Found', 'class-management'),
    'no_terms'                   => __('No subjects', 'class-management'),
    'items_list'                 => __('Subjects list', 'class-management'),
    'items_list_navigation'      => __('Subjects list navigation', 'class-management'),
    );

    $args = array(
    'labels'                     => $labels,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
    'show_in_rest'               => true,
    'rewrite'                    => array('slug' => 'class-subject'),
    'capabilities'               => array(
      'manage_terms'             => 'edit_classes',
      'edit_terms'               => 'edit_classes',
      'delete_terms'             => 'manage_categories',
      'assign_terms'             => 'edit_classes',
    ),
    );

    register_taxonomy('class_subject', array('class'), $args);
}
add_action('init', 'cm_register_class_subject_taxonomy');

/**
 * Register Custom Taxonomy: Class Level
 */
function cm_register_class_level_taxonomy()
{
    $labels = array(
    'name'                       => __('Class Levels', 'class-management'),
    'singular_name'              => __('Class Level', 'class-management'),
    'menu_name'                  => __('Levels', 'class-management'),
    'all_items'                  => __('All Levels', 'class-management'),
    'parent_item'                => __('Parent Level', 'class-management'),
    'parent_item_colon'          => __('Parent Level:', 'class-management'),
    'new_item_name'              => __('New Level Name', 'class-management'),
    'add_new_item'               => __('Add New Level', 'class-management'),
    'edit_item'                  => __('Edit Level', 'class-management'),
    'update_item'                => __('Update Level', 'class-management'),
    'view_item'                  => __('View Level', 'class-management'),
    'separate_items_with_commas' => __('Separate levels with commas', 'class-management'),
    'add_or_remove_items'        => __('Add or remove levels', 'class-management'),
    'choose_from_most_used'      => __('Choose from the most used', 'class-management'),
    'popular_items'              => __('Popular Levels', 'class-management'),
    'search_items'               => __('Search Levels', 'class-management'),
    'not_found'                  => __('Not Found', 'class-management'),
    'no_terms'                   => __('No levels', 'class-management'),
    'items_list'                 => __('Levels list', 'class-management'),
    'items_list_navigation'      => __('Levels list navigation', 'class-management'),
    );

    $args = array(
    'labels'                     => $labels,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
    'show_in_rest'               => true,
    'rewrite'                    => array('slug' => 'class-level'),
    'capabilities'               => array(
      'manage_terms'             => 'manage_categories',
      'edit_terms'               => 'manage_categories',
      'delete_terms'             => 'manage_categories',
      'assign_terms'             => 'edit_classes',
    ),
    );

    register_taxonomy('class_level', array('class'), $args);
}
add_action('init', 'cm_register_class_level_taxonomy');
