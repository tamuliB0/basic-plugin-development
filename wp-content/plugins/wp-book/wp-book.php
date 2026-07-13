<?php
/**
 * Plugin Name: WP Book
 * Description: A WordPress plugin for managing books using custom post types and taxonomies.
 * Version: 1.0.0
 * Author: tamuliB0
 * 
 * @package wp-book
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$dir = plugin_dir_path( __FILE__ );
require_once $dir . 'includes/post-type.php';
require_once $dir . 'includes/taxonomies.php';
require_once $dir . 'includes/db.php';
require_once $dir . 'includes/meta-box.php';
require_once $dir . 'includes/save-meta.php';
require_once $dir . 'includes/admin-page.php';
require_once $dir . 'includes/shortcode.php';
require_once $dir . 'includes/widgets.php';

register_activation_hook( __FILE__, 'wp_book_create_meta_table' );
