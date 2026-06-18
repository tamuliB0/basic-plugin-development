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
		'rewrite'           => array( 'slug' => 'book' ),
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
		'rewrite'               => array( 'slug' => 'book' ),
	);
	register_taxonomy( 'book-tag', 'book', $args );
}
add_action( 'init', 'wp_book_register_taxonomies' );

/**
 * Runs on plugin activation.
 */
function wp_book_create_meta_table() {
	global $wpdb;

	$table_name      = $wpdb->prefix . 'book_meta';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		book_id bigint(20) unsigned NOT NULL,
		author_name varchar(255) DEFAULT '' NOT NULL,
		price decimal(10,2) DEFAULT 0.00 NOT NULL,
		publisher varchar(255) DEFAULT '' NOT NULL,
		year int(4) DEFAULT 0 NOT NULL,
		edition varchar(100) DEFAULT '' NOT NULL,
		book_url varchar(255) DEFAULT '' NOT NULL,
		PRIMARY KEY  (meta_id),
		UNIQUE KEY book_id (book_id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}
register_activation_hook( __FILE__, 'wp_book_create_meta_table' );

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
