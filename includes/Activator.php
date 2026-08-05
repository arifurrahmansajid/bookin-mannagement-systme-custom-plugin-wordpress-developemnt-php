<?php
namespace CustomBookingManager;

class Activator {
	public static function activate() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'custom_bookings';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			booking_type varchar(50) NOT NULL,
			full_name varchar(100) NOT NULL,
			email varchar(100) NOT NULL,
			phone varchar(20) NOT NULL,
			booking_date date NOT NULL,
			booking_time time NOT NULL,
			booking_details longtext NOT NULL,
			status varchar(20) DEFAULT 'pending' NOT NULL,
			google_event_id varchar(255) DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}
