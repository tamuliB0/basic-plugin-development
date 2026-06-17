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

/**
 * Register two taxonomies 
 * 'Book Category' and 'Book Tag', 
 * for the custom post type "book".
 */
function wp_book_register_taxonomies() {
	$labels = array(
		'name'              => _x( 'Book Category', 'taxonomy general name', 'wp-book' ),
		'singular_name'     => _x( 'Book Category', 'taxonomy singular name', 'wp-book' ),
		'search_items'      => __( 'Search Book Category', 'wp-book' ),
		'all_items'         => __( 'All Book Categories', 'wp-book' ),
		'parent_item'       => __( 'Parent Book Category', 'wp-book' ),
		'parent_item_colon' => __( 'Parent Book Category:', 'wp-book' ),
		'edit_item'         => __( 'Edit Book Category', 'wp-book' ),
		'update_item'       => __( 'Update Book Category', 'wp-book' ),
		'add_new_item'      => __( 'Add Book Category', 'wp-book' ),
		'new_item_name'     => __( 'New Book Category', 'wp-book' ),
		'menu_name'         => __( 'Book Category', 'wp-book' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'book-category' ),
	);

	register_taxonomy( 'book-category', array( 'book' ), $args );

	unset( $args );
	unset( $labels );

	$labels = array(
		'name'                       => _x( 'Book Tag', 'taxonomy general name', 'wp-book' ),
		'singular_name'              => _x( 'Book Tag', 'taxonomy singular name', 'wp-book' ),
		'search_items'               => __( 'Search Book Tag', 'wp-book' ),
		'popular_items'              => __( 'Popular Book Tag', 'wp-book' ),
		'all_items'                  => __( 'All Book Tags', 'wp-book' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Book Tag', 'wp-book' ),
		'update_item'                => __( 'Update Book Tag', 'wp-book' ),
		'add_new_item'               => __( 'Add Book Tag', 'wp-book' ),
		'new_item_name'              => __( 'New Book Tag', 'wp-book' ),
		'separate_items_with_commas' => __( 'Separate Book Tags with commas', 'wp-book' ),
		'add_or_remove_items'        => __( 'Add or remove Book Tag', 'wp-book' ),
		'choose_from_most_used'      => __( 'Choose from the most used Book Tags', 'wp-book' ),
		'not_found'                  => __( 'No Book Tag found.', 'wp-book' ),
		'menu_name'                  => __( 'Book Tag', 'wp-book' ),
	);

	$args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'writer' ),
	);

	register_taxonomy( 'book-tag', 'book', $args );
}
add_action( 'init', 'wp_book_register_taxonomies' );