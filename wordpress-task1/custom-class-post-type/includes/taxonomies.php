<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function classes_register_taxonomies() {
    $labels = array(
        'name'              => __( 'Subjects', 'classes-cpt' ),
        'singular_name'     => __( 'Subject', 'classes-cpt' ),
        'search_items'      => __( 'Search Subjects', 'classes-cpt' ),
        'all_items'         => __( 'All Subjects', 'classes-cpt' ),
        'parent_item'       => __( 'Parent Subject', 'classes-cpt' ),
        'parent_item_colon' => __( 'Parent Subject:', 'classes-cpt' ),
        'edit_item'         => __( 'Edit Subject', 'classes-cpt' ),
        'update_item'       => __( 'Update Subject', 'classes-cpt' ),
        'add_new_item'      => __( 'Add New Subject', 'classes-cpt' ),
        'new_item_name'     => __( 'New Subject Name', 'classes-cpt' ),
        'menu_name'         => __( 'Subjects', 'classes-cpt' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'subject' ),
        'show_in_rest'      => true,
        'rest_base'         => 'subjects',
    );

    register_taxonomy( 'subject', array( 'class' ), $args );

    $labels = array(
        'name'                       => __( 'Instructors', 'classes-cpt' ),
        'singular_name'              => __( 'Instructor', 'classes-cpt' ),
        'search_items'               => __( 'Search Instructors', 'classes-cpt' ),
        'popular_items'              => __( 'Popular Instructors', 'classes-cpt' ),
        'all_items'                  => __( 'All Instructors', 'classes-cpt' ),
        'edit_item'                  => __( 'Edit Instructor', 'classes-cpt' ),
        'update_item'                => __( 'Update Instructor', 'classes-cpt' ),
        'add_new_item'               => __( 'Add New Instructor', 'classes-cpt' ),
        'new_item_name'              => __( 'New Instructor Name', 'classes-cpt' ),
        'separate_items_with_commas' => __( 'Separate instructors with commas', 'classes-cpt' ),
        'add_or_remove_items'        => __( 'Add or remove instructors', 'classes-cpt' ),
        'choose_from_most_used'      => __( 'Choose from the most used instructors', 'classes-cpt' ),
        'not_found'                  => __( 'No instructors found.', 'classes-cpt' ),
        'menu_name'                  => __( 'Instructors', 'classes-cpt' ),
    );

    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'instructor' ),
        'show_in_rest'          => true,
        'rest_base'             => 'instructors',
    );

    register_taxonomy( 'instructor', 'class', $args );

    $labels = array(
        'name'              => __( 'Levels', 'classes-cpt' ),
        'singular_name'     => __( 'Level', 'classes-cpt' ),
        'search_items'      => __( 'Search Levels', 'classes-cpt' ),
        'all_items'         => __( 'All Levels', 'classes-cpt' ),
        'parent_item'       => __( 'Parent Level', 'classes-cpt' ),
        'parent_item_colon' => __( 'Parent Level:', 'classes-cpt' ),
        'edit_item'         => __( 'Edit Level', 'classes-cpt' ),
        'update_item'       => __( 'Update Level', 'classes-cpt' ),
        'add_new_item'      => __( 'Add New Level', 'classes-cpt' ),
        'new_item_name'     => __( 'New Level Name', 'classes-cpt' ),
        'menu_name'         => __( 'Levels', 'classes-cpt' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'class-level' ),
        'show_in_rest'      => true,
        'rest_base'         => 'levels',
    );

    register_taxonomy( 'class_level', array( 'class' ), $args );
}
