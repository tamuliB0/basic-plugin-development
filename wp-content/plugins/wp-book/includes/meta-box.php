<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a custom meta box to Book post type edit screen.
 */
function wp_book_add_custom_box() {
	add_meta_box(
        'wp_book_details_box',
        __( 'Book Information', 'wp-book' ),
        'wp_book_custom_box_html',
        'book',
		'normal'
    );
}
add_action( 'add_meta_boxes', 'wp_book_add_custom_box' );

/**
 * Get book meta data for a book post.
 *
 * @param int $post_id Book post ID.
 */
function wp_book_get_book_meta( $post_id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'book_meta';

	return $wpdb->get_row(
		$wpdb->prepare( 
			"SELECT * FROM $table_name WHERE book_id = %d",
			$post_id
		),
		ARRAY_A
	);
}

/**
 * Display meta box fields.
 *
 * @param WP_Post $post Current post object.
 */
function wp_book_custom_box_html( $post ) {

	$book_meta = wp_book_get_book_meta( $post->ID );

    wp_nonce_field( 'wp_book_save_meta_box', 'wp_book_meta_box_nonce' ); 
    
	$author_name = $book_meta[ 'author_name' ] ?? '';
	$price = $book_meta[ 'price' ] ?? '';
	$publisher = $book_meta[ 'publisher' ] ?? '';
	$year = $book_meta[ 'year' ] ?? '';
	$edition = $book_meta[ 'edition' ] ?? '';
	$book_url = $book_meta[ 'book_url' ] ?? '';
	?>
    <label><?php esc_html_e('Author name:', 'wp-book'); ?></label>
    <input type="text" value="<?php echo esc_attr( $author_name ) ?>" name="wp_book_author_name"> 

	<label><?php esc_html_e('Price:', 'wp-book'); ?></label>
    <input type="number" value="<?php echo esc_attr( $price ) ?>" name="wp_book_price"> 

	<label><?php esc_html_e('Publisher:', 'wp-book'); ?></label>
    <input type="text" value="<?php echo esc_attr( $publisher ) ?>" name="wp_book_publisher"> 

	<label><?php esc_html_e('Year:', 'wp-book'); ?></label>
    <input type="number" value="<?php echo esc_attr( $year ) ?>" name="wp_book_year"> 

	<label><?php esc_html_e('Edition:', 'wp-book'); ?></label>
    <input type="text" value="<?php echo esc_attr( $edition ) ?>" name="wp_book_edition"> 
	
	<label><?php esc_html_e('URL:', 'wp-book'); ?></label>
    <input type="text" value="<?php echo esc_attr( $book_url ) ?>" name="wp_book_url"> 
	<?php
}