<?php
namespace CustomBookingManager\Admin;

use CustomBookingManager\Database;

class Export {
	private $db;

	public function __construct( Database $db ) {
		$this->db = $db;
	}

	public function init() {
		add_action( 'admin_post_cbm_export_csv', [ $this, 'export_csv' ] );
	}

	public function export_csv() {
		if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
		
		$bookings = $this->db->get_bookings();

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=bookings_export_' . date('Y-m-d') . '.csv' );
		
		$output = fopen( 'php://output', 'w' );
		fputcsv( $output, [ 'ID', 'Type', 'Full Name', 'Email', 'Phone', 'Date', 'Time', 'Details', 'Status', 'Created At' ] );

		foreach ( $bookings as $booking ) {
			fputcsv( $output, [
				$booking->id,
				$booking->booking_type,
				$booking->full_name,
				$booking->email,
				$booking->phone,
				$booking->booking_date,
				$booking->booking_time,
				$booking->booking_details,
				$booking->status,
				$booking->created_at
			] );
		}
		
		fclose( $output );
		exit;
	}
}
