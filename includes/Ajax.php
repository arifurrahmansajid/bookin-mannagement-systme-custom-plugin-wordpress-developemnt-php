<?php
namespace CustomBookingManager;

class Ajax {
	private $db;
	private $mailer;
	private $google_calendar;

	public function __construct( Database $db, Mailer $mailer, GoogleCalendar $google_calendar ) {
		$this->db = $db;
		$this->mailer = $mailer;
		$this->google_calendar = $google_calendar;
	}

	public function init() {
		add_action( 'wp_ajax_cbm_submit_booking', [ $this, 'handle_submission' ] );
		add_action( 'wp_ajax_nopriv_cbm_submit_booking', [ $this, 'handle_submission' ] );
	}

	public function handle_submission() {
		check_ajax_referer( 'cbm_booking_nonce', 'nonce' );

		$booking_type = isset( $_POST['booking_type'] ) ? sanitize_text_field( $_POST['booking_type'] ) : 'custom';
		
		$dynamic_data = [];
		$customer_email = '';
		$customer_name = 'Customer';
		$customer_phone = '';
		$booking_date = date('Y-m-d');
		$booking_time = '12:00';

		// Extract dynamic fields starting with "dynamic_"
		foreach ( $_POST as $key => $value ) {
			if ( strpos( $key, 'dynamic_' ) === 0 ) {
				$clean_key = str_replace( 'dynamic_', '', $key );
				$clean_val = sanitize_text_field( $value );
				$dynamic_data[ $clean_key ] = $clean_val;
				
				// Try to guess core fields for the mailer/table if not explicitly provided
				if ( strpos( strtolower($clean_key), 'email' ) !== false && empty($customer_email) ) {
					$customer_email = sanitize_email( $value );
				}
				if ( strpos( strtolower($clean_key), 'name' ) !== false && $customer_name === 'Customer' ) {
					$customer_name = $clean_val;
				}
				if ( strpos( strtolower($clean_key), 'phone' ) !== false && empty($customer_phone) ) {
					$customer_phone = $clean_val;
				}
				if ( strpos( strtolower($clean_key), 'date' ) !== false ) {
					$booking_date = $clean_val;
				}
				if ( strpos( strtolower($clean_key), 'time' ) !== false ) {
					$booking_time = $clean_val;
				}
			}
		}

		if ( empty( $dynamic_data ) ) {
			wp_send_json_error( [ 'message' => 'No form data received.' ] );
		}

		// Store the entire dynamic array as a JSON string in booking_details
		$details_json = wp_json_encode( $dynamic_data );

		$data = [
			'booking_type'    => $booking_type,
			'full_name'       => $customer_name,
			'email'           => $customer_email,
			'phone'           => $customer_phone,
			'booking_date'    => $booking_date,
			'booking_time'    => $booking_time,
			'booking_details' => $details_json,
		];

		$booking_id = $this->db->insert_booking( $data );

		if ( $booking_id ) {
			$this->mailer->send_new_booking_admin( $booking_id, $data );
			if ( ! empty( $customer_email ) ) {
				$this->mailer->send_booking_received_customer( $data );
			}
			wp_send_json_success( [ 'message' => 'Your booking request has been submitted successfully.' ] );
		} else {
			wp_send_json_error( [ 'message' => 'There was an error processing your request. Please try again.' ] );
		}
	}
}
