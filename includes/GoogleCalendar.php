<?php
namespace CustomBookingManager;

class GoogleCalendar {
	private $client_id;
	private $client_secret;
	private $redirect_uri;
	
	public function __construct() {
		$this->client_id = get_option( 'cbm_google_client_id' );
		$this->client_secret = get_option( 'cbm_google_client_secret' );
		$this->redirect_uri = admin_url( 'admin.php?page=cbm-settings' );
	}

	public function get_auth_url() {
		if ( empty( $this->client_id ) || empty( $this->client_secret ) ) {
			return false;
		}

		$params = [
			'client_id'     => $this->client_id,
			'redirect_uri'  => $this->redirect_uri,
			'response_type' => 'code',
			'scope'         => 'https://www.googleapis.com/auth/calendar.events',
			'access_type'   => 'offline',
			'prompt'        => 'consent'
		];

		return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query( $params );
	}

	public function authenticate( $code ) {
		$response = wp_remote_post( 'https://oauth2.googleapis.com/token', [
			'body' => [
				'code'          => $code,
				'client_id'     => $this->client_id,
				'client_secret' => $this->client_secret,
				'redirect_uri'  => $this->redirect_uri,
				'grant_type'    => 'authorization_code'
			]
		] );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['access_token'] ) ) {
			update_option( 'cbm_google_access_token', $body['access_token'] );
			if ( isset( $body['refresh_token'] ) ) {
				update_option( 'cbm_google_refresh_token', $body['refresh_token'] );
			}
			update_option( 'cbm_google_token_expires', time() + $body['expires_in'] );
			return true;
		}

		return false;
	}

	public function get_access_token() {
		$token = get_option( 'cbm_google_access_token' );
		$expires = get_option( 'cbm_google_token_expires' );

		if ( ! $token ) {
			return false;
		}

		// Refresh token if expired or expiring within 5 minutes
		if ( time() > ( $expires - 300 ) ) {
			$refresh_token = get_option( 'cbm_google_refresh_token' );
			if ( $refresh_token ) {
				$response = wp_remote_post( 'https://oauth2.googleapis.com/token', [
					'body' => [
						'client_id'     => $this->client_id,
						'client_secret' => $this->client_secret,
						'refresh_token' => $refresh_token,
						'grant_type'    => 'refresh_token'
					]
				] );

				if ( ! is_wp_error( $response ) ) {
					$body = json_decode( wp_remote_retrieve_body( $response ), true );
					if ( isset( $body['access_token'] ) ) {
						update_option( 'cbm_google_access_token', $body['access_token'] );
						update_option( 'cbm_google_token_expires', time() + $body['expires_in'] );
						return $body['access_token'];
					}
				}
			}
			return false;
		}

		return $token;
	}

	public function create_event( $booking ) {
		$token = $this->get_access_token();
		if ( ! $token ) return false;

		$calendar_id = ( $booking->booking_type === 'recording' ) 
			? get_option( 'cbm_google_recording_calendar', 'primary' ) 
			: get_option( 'cbm_google_performance_calendar', 'primary' );

		// Format start and end time (assuming 1 hour duration for simplicity if not specified)
		$start_datetime = $booking->booking_date . 'T' . $booking->booking_time . ':00';
		$end_timestamp = strtotime( $start_datetime ) + 3600; // +1 hour
		$end_datetime = date( 'Y-m-d\TH:i:s', $end_timestamp );

		$event_data = [
			'summary'     => ucfirst( $booking->booking_type ) . ' Session: ' . $booking->full_name,
			'description' => "Name: {$booking->full_name}\nEmail: {$booking->email}\nPhone: {$booking->phone}\nDetails: {$booking->booking_details}",
			'start'       => [
				'dateTime' => $start_datetime,
				'timeZone' => wp_timezone_string()
			],
			'end'         => [
				'dateTime' => $end_datetime,
				'timeZone' => wp_timezone_string()
			]
		];

		$response = wp_remote_post( "https://www.googleapis.com/calendar/v3/calendars/" . urlencode($calendar_id) . "/events", [
			'headers' => [
				'Authorization' => 'Bearer ' . $token,
				'Content-Type'  => 'application/json',
			],
			'body' => wp_json_encode( $event_data )
		] );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		
		if ( isset( $body['id'] ) ) {
			return $body['id']; // Return Google Event ID
		}

		return false;
	}
}
