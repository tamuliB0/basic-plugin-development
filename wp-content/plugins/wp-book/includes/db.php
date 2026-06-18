<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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