<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Add a widget to the dashboard.
 */
function wp_book_register_dashboard_widget() {
	wp_add_dashboard_widget(
		'wp_book_top_categories_widget',
		__( 'Top Book Categories', 'wp-book' ),
		'wp_book_dashboard_widget_html'
	);
}
add_action( 'wp_dashboard_setup', 'wp_book_register_dashboard_widget' );

/**
 * Display the content of the Dashboard Widget.
 */
function wp_book_dashboard_widget_html() {
    $terms = get_terms(
		array(
			'taxonomy'   => 'book-category',
			'hide_empty' => false,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 5,
		)
	);
    if ( empty( $terms ) ) {
        echo '<p>' . esc_html_e( 'No book categories found.', 'wp-book' ) . '</p>';
    }
    echo '<ul>';
    foreach ( $terms as $term ) {
        echo '<li>' . esc_html( $term->name ) . ' (' . absint( $term->count ) . ')</li>';
    }
    echo '</ul>';
}