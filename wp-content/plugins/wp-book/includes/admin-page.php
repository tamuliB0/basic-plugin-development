<?php
/**
 * Register admin submenu page under Books.
 */
function wp_book_register_admin_page() {
	add_submenu_page(
		'edit.php?post_type=book',  // slug (parent-slug) for Custom Post Types: ‘edit.php?post_type=your_post_type’ 
		__( 'Book Settings', 'wp-book' ), 
		__( 'Settings', 'wp-book' ), 
		'manage_options', 
		'wp_book_settings', 
		'wp_book_settings_page_html' 
	);
}
add_action( 'admin_menu', 'wp_book_register_admin_page' );

/**
 * Display callback for the submenu page.
 */
function wp_book_settings_page_html() { 
    ?>
    <h1><?php _e( 'WP-Book', 'textdomain' ); ?></h1>
    <p><?php _e( 'Configure display settings for book', 'textdomain' ); ?></p>
    <?php
}