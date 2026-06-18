<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Save book meta box data.
 *
 * @param int $post_id Book post ID.
 */
function wp_book_save_meta_box( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['wp_book_meta_box_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp_book_meta_box_nonce'] ) ), 'wp_book_save_meta_box' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	global $wpdb;
	$table_name = $wpdb->prefix . 'book_meta';

	$author_name = isset( $_POST['wp_book_author_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_book_author_name'] ) ) : '';
	$price       = isset( $_POST['wp_book_price'] ) ? floatval( wp_unslash( $_POST['wp_book_price'] ) ) : 0;
	$publisher   = isset( $_POST['wp_book_publisher'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_book_publisher'] ) ) : '';
	$year        = isset( $_POST['wp_book_year'] ) ? intval( wp_unslash( $_POST['wp_book_year'] ) ) : 0;
	$edition     = isset( $_POST['wp_book_edition'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_book_edition'] ) ) : '';
	$book_url    = isset( $_POST['wp_book_url'] ) ? esc_url_raw( wp_unslash( $_POST['wp_book_url'] ) ) : '';

	$data = array(
		'book_id'      => $post_id,
		'author_name'  => $author_name,
		'price'        => $price,
		'publisher'    => $publisher,
		'year'         => $year,
		'edition'      => $edition,
		'book_url'     => $book_url,
	);

	$formats = array( '%d', '%s', '%f', '%s', '%d', '%s', '%s' );
	$existing = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT meta_id FROM $table_name WHERE book_id = %d",
			$post_id
		)
	);
	if ( $existing ) {
		$result = $wpdb->update(
			$table_name,
			$data,
			array( 'book_id' => $post_id ),
			$formats,
			array( '%d' )
		);
	} else {
		$result = $wpdb->insert(
			$table_name,
			$data,
			$formats
		);
	}
}
add_action( 'save_post_book', 'wp_book_save_meta_box' );
