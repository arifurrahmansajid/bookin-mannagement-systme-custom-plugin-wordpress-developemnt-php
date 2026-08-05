<?php
/**
 * Fired when the plugin is uninstalled.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Optionally drop custom tables or delete options.
// We might want to keep the data if the user reinstalls, but usually uninstall cleans up.
global $wpdb;
$table_name = $wpdb->prefix . 'custom_bookings';
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

// Delete plugin options
delete_option( 'cbm_google_client_id' );
delete_option( 'cbm_google_client_secret' );
delete_option( 'cbm_google_access_token' );
delete_option( 'cbm_google_refresh_token' );
delete_option( 'cbm_google_recording_calendar' );
delete_option( 'cbm_google_performance_calendar' );
delete_option( 'cbm_recaptcha_site_key' );
delete_option( 'cbm_recaptcha_secret_key' );
