<?php
/**
 * Plugin Name: Widget Company Block
 * Plugin URI: https://github.com/yourusername/widget-company-directory
 * Description: A WordPress block for displaying recommended companies.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WIDGET_COMPANY_DIRECTORY_VERSION', '1.0.0' );
define( 'WIDGET_COMPANY_DIRECTORY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WIDGET_COMPANY_DIRECTORY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Register the block using block.json
 */
function widget_directory_register_blocks() {
    register_block_type_from_metadata( __DIR__ . '/src/blocks/company-list' );
}
add_action( 'init', 'widget_directory_register_blocks' );
