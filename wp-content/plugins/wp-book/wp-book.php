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

/**
 * Register Book post type.
 */
function wp_book_register_post_type() {
    $labels = array(
        'name'                  => __( 'Books', 'wp-book' ),
        'singular_name'         => __( 'Book', 'wp-book' ),
        'menu_name'             => __( 'Books', 'wp-book' ),
        'name_admin_bar'        => __( 'Books', 'wp-book' ),
        'add_new'               => __( 'Add Book', 'wp-book' ),
        'add_new_item'          => __( 'Add Book', 'wp-book' ),
        'new_item'              => __( 'New Book', 'wp-book' ),
        'edit_item'             => __( 'Edit Book', 'wp-book' ),
        'view_item'             => __( 'View Book', 'wp-book' ),
        'search_items'          => __( 'Search Books', 'wp-book' ),
        'not_found'             => __( 'No books found.', 'wp-book' ),
        'not_found_in_trash'    => __( 'No books found in Trash.', 'wp-book' ),
    );
    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'show_in_rest'  => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-book',
        'supports'      => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'author',
            'revisions',
        ),
    );
    register_post_type( 'book', $args );
}
add_action( 'init', 'wp_book_register_post_type' ); 
