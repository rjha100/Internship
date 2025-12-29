<?php

if (! defined('ABSPATH')) {
    exit;
}

function classes_edit_columns($columns)
{
    $new_columns = [];

    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = __('Title', 'classes-cpt');
    $new_columns['subject'] = __('Subject', 'classes-cpt');
    $new_columns['instructor'] = __('Instructor', 'classes-cpt');
    $new_columns['date'] = $columns['date'];

    return $new_columns;
}
add_filter('manage_edit-class_columns', 'classes_edit_columns');

function classes_custom_columns($column, $post_id)
{
    switch ($column) {
        case 'subject':
            $terms = get_the_term_list($post_id, 'subject', '', ', ', '');
            if (is_string($terms)) {
                echo $terms;
            } else {
                echo '-';
            }
            break;
        case 'instructor':
            $terms = get_the_term_list($post_id, 'instructor', '', ', ', '');
            if (is_string($terms)) {
                echo $terms;
            } else {
                echo '-';
            }
            break;
    }
}
add_action('manage_class_posts_custom_column', 'classes_custom_columns', 10, 2);

function classes_sortable_columns($columns)
{
    $columns['subject'] = 'subject';
    $columns['instructor'] = 'instructor';
    return $columns;
}
add_filter('manage_edit-class_sortable_columns', 'classes_sortable_columns');
