<?php

/**
 * Plugin Name:       Subscribe Me Block
 * Description:       Example block scaffolded with Create Block tool.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       subscribe-me-block
 *
 * @package Smb
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$rootFiles         = glob(plugin_dir_path(__FILE__) . 'includes/*.php');
$subdirectoryFiles = glob(plugin_dir_path(__FILE__) . 'includes/**/*.php');
$allFiles          = array_merge($rootFiles, $subdirectoryFiles);

foreach ($allFiles as $filename) {
    include_once($filename);
}

function smb_subscribe_me_block_block_init()
{
    if (function_exists('wp_register_block_types_from_metadata_collection')) {
        wp_register_block_types_from_metadata_collection(__DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php');
        return;
    }

    if (function_exists('wp_register_block_metadata_collection')) {
        wp_register_block_metadata_collection(__DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php');
    }
    $manifest_data = require __DIR__ . '/build/blocks-manifest.php';
    foreach (array_keys($manifest_data) as $block_type) {
        register_block_type(__DIR__ . "/build/{$block_type}");
    }
}

add_action('init', 'smb_subscribe_me_block_block_init');
