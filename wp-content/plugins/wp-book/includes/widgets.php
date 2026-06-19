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

/**
 * Register WP Book sidebar.
 */
function wp_book_register_sidebar() {
    register_sidebar(
        array(
            'name' => __( 'WP Book Sidebar', 'wp-book' ),
            'id'   => 'wp-book-sidebar',
        )
    );
}
add_action( 'widgets_init', 'wp_book_register_sidebar' );

/**
 * Widget to display books from a selected book category.
 */
class WP_Book_Category_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'wp_book_category_widget',
			__( 'WP Book Category Widget', 'wp-book' ),
			array(
				'description' => __( 'Displays books from a selected book category.', 'wp-book' ),
            )
        );
	}
	public $args = array(
		'before_title'  => '<h4 class="widgettitle">',
		'after_title'   => '</h4>',
		'before_widget' => '<div class="widget-wrap">',
		'after_widget'  => '</div></div>',
	);

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
        $category_id = ! empty( $instance['category_id'] ) ? absint( $instance['category_id'] ) : 0;
        if ( $category_id ) {
			$book_query = new WP_Query(
				array(
					'post_type'      => 'book',
					'posts_per_page' => 5,
					'tax_query'      => array(
						array(
							'taxonomy' => 'book-category',
							'field'    => 'term_id',
							'terms'    => $category_id,
						),
					),
				)
			);
            if ( $book_query->have_posts() ) {
                echo '<ul>';
                while ( $book_query->have_posts() ) {
                    $book_query->have_posts();
                    echo '<li><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></li>';
                }
                echo '</ul>';
                wp_reset_postdata();
            } else {
                echo '<p>' . esc_html( 'No books found in this category.', 'wp-book' ) . '</p>';
            }
        } else {
                echo '<p>' . esc_html( 'Please select a category.', 'wp-book' ) . '</p>';
        }
		echo $args['after_widget'];
	}
}

/**
 * Register WP Book category widget.
 */
function wp_book_register_widgets() {
	register_widget( 'WP_Book_Category_Widget' );
}
add_action( 'widgets_init', 'wp_book_register_widgets' );
