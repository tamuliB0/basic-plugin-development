<?php
/**
 * Register admin submenu page under Books.
 */
function wp_book_register_admin_page() {
	add_submenu_page(
		'edit.php?post_type=book', 
		__( 'Book Settings', 'wp-book' ),
		__( 'Settings', 'wp-book' ),
		'manage_options',
		'wp_book_settings',
		'wp_book_settings_page_html'
	);
}
add_action( 'admin_menu', 'wp_book_register_admin_page' );

/**
 * Register WP Book settings.
 */
function wp_book_register_settings() {
    add_settings_section(
        'wp_book_main_section',
        __( 'Book Display Settings', 'wp-book' ),
        'wp_book_main_section_html',
        'wp_book_settings'
    );
    add_settings_field(
		'wp_book_curreny',
		__( 'Choose currency', 'wp-book' ),
		'wp_book_currency_field_html',
		'wp_book_settings',
		'wp_book_main_section'
	);
    add_settings_field(
		'wp_book_per_page_books',
		__( 'Books per page', 'wp-book' ),
		'wp_book_per_page_books_field_html',
		'wp_book_settings',
		'wp_book_main_section'
	);
    register_setting(
		'wp_book_settings_group',
		'wp_book_currency',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'wp_book_validate_currency_input',
			'default'           => 'INR',
		)
	);
	register_setting(
		'wp_book_settings_group',
		'wp_book_per_page_books',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'wp_book_validate_per_page_books_input',
			'default'           => 10,
		)
	);
    
}
add_action( 'admin_init', 'wp_book_register_settings' );

/**
 * Validates currency input.
 * 
 * @param string $input User provided value.
 */
function wp_book_validate_currency_input( $input ) {
    $input = strtoupper( $input );
    $allowed_currencies = [ 'INR', 'USD', 'EUR', 'RUB' ];

    if ( ! in_array( $input, $allowed_currencies, true ) ) {
        add_settings_error(
            'wp_book_currency',
            'wp_book_currency_error',
            __( 'Please select a valid currency', 'wp-book' ),
            'error'
        );
        return get_option( 'wp_book_currency', 'INR' );
    }
    return $input;
}

/**
 * Validates per page books input.
 * 
 * @param int $input User provided value.
 */
function wp_book_validate_per_page_books_input( $input ) {
    $input = absint( $input );
    if ( $input <= 0 ) {
        add_settings_error(
            'wp_book_per_page_books',
            'wp_book_per_page_books_error',
            __( 'Please select a positive integer', 'wp-book' ),
            'error'
        );
        return get_option( 'wp_book_per_page_books', 5 );
    }
    return $input;
}

/**
 * Currency field callback.
 */
function wp_book_currency_field_html() {
    $value = get_option( 'wp_book_currency', 'INR' );
	?>
	<select name="wp_book_currency">
		<option value="INR" <?php selected( $value, 'INR' ); ?>><?php esc_html_e( 'INR', 'wp-book' ); ?></option>
		<option value="USD" <?php selected( $value, 'USD' ); ?>><?php esc_html_e( 'USD', 'wp-book' ); ?></option>
		<option value="EUR" <?php selected( $value, 'EUR' ); ?>><?php esc_html_e( 'EUR', 'wp-book' ); ?></option>
		<option value="EUR" <?php selected( $value, 'RUB' ); ?>><?php esc_html_e( 'RUB', 'wp-book' ); ?></option>
	</select>
	<?php 
}

/**
 * Currency field callback.
 */
function wp_book_per_page_books_field_html() {
    $value = get_option( 'wp_book_books_per_page', 5 );
	?>
	<input type="number" name="wp_book_books_per_page" value="<?php echo esc_attr( $value ); ?>" />
	<?php 
}

/**
 * Display callback for Settings section. 
 */
function wp_book_main_section_html() {
    ?>
    <p><?php esc_html_e( 'Configure how books are displayed on the site', 'wp-book' )?></p>
    <?php
}

/**
 * Display callback for the submenu page.
 */
function wp_book_settings_page_html() { 
    ?>
    <h1><?php echo esc_html_e( 'Settings', 'wp-book' ); ?></h1>
    <?php settings_errors(); ?>
    <form method="post" action="options.php">
        <?php
        settings_fields( 'wp_book_settings_group' );
        do_settings_sections( 'wp_book_settings' );
        submit_button();
        ?>
    </form>
    <?php
}