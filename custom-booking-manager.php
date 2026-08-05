<?php
/**
 * Plugin Name: Custom Booking Manager
 * Plugin URI: https://example.com
 * Description: A custom booking system with admin approval workflow, Google Calendar integration, and frontend forms.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * Text Domain: custom-booking-manager
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'CBM_VERSION', '1.0.0' );
define( 'CBM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CBM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Custom PSR-4 Autoloader
spl_autoload_register( function ( $class ) {
	$prefix = 'CustomBookingManager\\';
	$base_dir = CBM_PLUGIN_DIR . 'includes/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

// Activation and Deactivation hooks
register_activation_hook( __FILE__, [ 'CustomBookingManager\Activator', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'CustomBookingManager\Deactivator', 'deactivate' ] );

// Initialize Plugin
function run_custom_booking_manager() {
	$plugin = new CustomBookingManager\Core();
	$plugin->run();
}
run_custom_booking_manager();
