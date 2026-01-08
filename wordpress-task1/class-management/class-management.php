<?php

/**
 * Plugin Name: Class Management
 * Plugin URI: https://example.com/class-management
 * Description: Manages classes and instructors with custom post types and native WordPress meta boxes
 * Version: 1.0.0
 * Author: Rishabh
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: class-management
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CLASS_MANAGEMENT_VERSION', value: '1.0.0');
define('CLASS_MANAGEMENT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CLASS_MANAGEMENT_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once CLASS_MANAGEMENT_PLUGIN_DIR . 'includes/post-types.php';
require_once CLASS_MANAGEMENT_PLUGIN_DIR . 'includes/taxonomies.php';
require_once CLASS_MANAGEMENT_PLUGIN_DIR . 'includes/user-roles.php';
require_once CLASS_MANAGEMENT_PLUGIN_DIR . 'includes/meta-boxes.php';
require_once CLASS_MANAGEMENT_PLUGIN_DIR . 'includes/instructor-profile.php';

/**
 * Flush rewrite rules on plugin activation
 */
function cm_activate()
{
    cm_register_class_post_type();
    cm_register_class_subject_taxonomy();
    cm_register_class_level_taxonomy();
    cm_register_instructor_role();
    cm_add_admin_capabilities();
    cm_create_default_taxonomy_terms();

    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'cm_activate');

/**
 * Create default taxonomy terms
 */
function cm_create_default_taxonomy_terms()
{
  // Create default class levels if they don't exist
    $levels = array('Beginner', 'Intermediate', 'Advanced');

    foreach ($levels as $level) {
        if (!term_exists($level, 'class_level')) {
            wp_insert_term($level, 'class_level');
        }
    }
}

/**
 * Flush rewrite rules on plugin deactivation
 */
function cm_deactivate()
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'cm_deactivate');
