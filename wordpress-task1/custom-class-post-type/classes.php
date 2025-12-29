<?php

/**
 * Plugin Name: Classes CPT
 * Plugin URI:  https://example.com/
 * Description: Registers a `class` custom post type and related taxonomies (subject, instructor, level).
 * Version:     0.1.0
 * Author:      Rishabh
 * Text Domain: classes-cpt
 * Domain Path: /languages
 */

if (! defined('ABSPATH')) {
    exit;
}

define('CLASSES_CPT_VERSION', '0.1.0');
define('CLASSES_CPT_PATH', plugin_dir_path(__FILE__));
define('CLASSES_CPT_URL', plugin_dir_url(__FILE__));

require_once CLASSES_CPT_PATH . 'includes/class-post-type.php';
require_once CLASSES_CPT_PATH . 'includes/taxonomies.php';
require_once CLASSES_CPT_PATH . 'includes/admin.php';

function classes_cpt_activate()
{
    classes_register_post_type();
    classes_register_taxonomies();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'classes_cpt_activate');

function classes_cpt_deactivate()
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'classes_cpt_deactivate');


function classes_load_textdomain()
{
    load_plugin_textdomain('classes-cpt', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_action('init', 'classes_register_post_type');
add_action('init', 'classes_register_taxonomies');
add_action('init', 'classes_load_textdomain');
