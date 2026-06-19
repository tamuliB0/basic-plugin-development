<?php
/**
 * Display book shortcode output.
 *
 * @param array $atts Shortcode attributes. Default empty.
 */
function wp_book_shortcode( $atts = array() ) {
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );
	$atts = shortcode_atts(
		array(
			'id'          => '',
			'author_name' => '',
			'year'        => '',
			'category'    => '',
			'tag'         => '',
			'publisher'   => '',
		),
		$atts,
		'book'
	);

	$book_id        = absint( $atts['id'] );
	$author_name    = sanitize_text_field( $atts['author_name'] );
	$year           = absint( $atts['year'] );
	$category       = sanitize_text_field( $atts['category'] );
	$tag    	    = sanitize_text_field( $atts['tag'] );
	$publisher      = sanitize_text_field( $atts['publisher'] );
	$posts_per_page = absint( get_option( 'wp_book_per_page_books', 5 ) );

	global $wpdb;
	$table_name = $wpdb->prefix . 'book_meta';

	$query_args = [
		'post_type'      => 'book',
		'post_status'    => 'publish',
		'posts_per_page' => $posts_per_page,
	];

	if ( $book_id ) {
		$query_args['p']              = $book_id;
		$query_args['posts_per_page'] = 1;
	}
	$tax_query = [];
	if ( ! empty( $category ) ) {
		$tax_query[] = [
			'taxonomy' => 'book-category',
			'field'    => 'slug',
			'terms'    => $category
		];
	}
	if ( ! empty( $tag ) ) {
		$tax_query[] = [
			'taxonomy' => 'book-tag',
			'field'    => 'slug',
			'terms'    => $tag
		];
	}
	if ( count( $tax_query ) > 1  ) {
		$tax_query[ 'relation' ] = 'AND';
	}
	if ( ! empty( $tax_query ) ) {
		$query_args[ 'tax_query' ] = $tax_query;
	}

	$books = new WP_Query( $query_args );
	if ( ! $books->have_posts() ) {
		return '<p>' . esc_html__( 'No books found.', 'wp-book' ) . '</p>';
	}
	ob_start();
	?>
	<?php
    while ( $books->have_posts() ) :
        $books->the_post();
        $current_book_id = get_the_ID();
        
        $book_meta = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT author_name, price, publisher, year, edition, book_url
				FROM $table_name
				WHERE book_id = %d",
				$current_book_id
				)
			);
            if ( ! $book_meta ) {
				continue;
			}
			if ( ! empty( $author_name ) && $author_name !== $book_meta->author_name ) {
				continue;
			}
			if ( ! empty( $publisher ) && $publisher !== $book_meta->publisher ) {
				continue;
			}
			if ( ! empty( $year ) &&  $year !== ( int ) $book_meta->year ) {
				continue;
			}
			?>
			<h3><?php echo esc_html( get_the_title() ); ?></h3>

			<p>
                <strong><?php esc_html_e( 'Author:', 'wp-book' ); ?></strong>
                <?php echo esc_html( $book_meta->author_name ); ?>
			</p>

			<p>
				<strong><?php esc_html_e( 'Publisher:', 'wp-book' ); ?></strong>
				<?php echo esc_html( $book_meta->publisher ); ?>
			</p>

			<p>
				<strong><?php esc_html_e( 'Year:', 'wp-book' ); ?></strong>
				<?php echo esc_html( $book_meta->year ); ?>
			</p>

			<p>
				<strong><?php esc_html_e( 'Edition:', 'wp-book' ); ?></strong>
				<?php echo esc_html( $book_meta->edition ); ?>
			</p>

			<p>
				<strong><?php esc_html_e( 'Price:', 'wp-book' ); ?></strong>
				<?php echo esc_html( $book_meta->price ); ?>
			</p>
			<?php if ( ! empty( $book_meta->book_url ) ) : ?>
				<p>
					<strong><?php esc_html_e( 'URL:', 'wp-book' ); ?></strong>
					<a href="<?php echo esc_url( $book_meta->book_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html( $book_meta->book_url ); ?>
					</a>
				</p>
			<?php endif; ?>
	<?php endwhile; ?>
	<?php wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'book', 'wp_book_shortcode' );