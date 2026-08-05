<?php
namespace CustomBookingManager\API;

use CustomBookingManager\Database;

class RestAPI {
	private $db;

	public function __construct() {
		$this->db = new Database();
		add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );
	}

	public function register_endpoints() {
		register_rest_route( 'cbm/v1', '/bookings', [
			'methods'  => 'GET',
			'callback' => [ $this, 'get_bookings' ],
			'permission_callback' => [ $this, 'check_permission' ]
		] );
	}

	public function check_permission() {
		return current_user_can( 'manage_options' );
	}

	public function get_bookings( $request ) {
		$status = $request->get_param( 'status' );
		$bookings = $this->db->get_bookings( $status );
		return rest_ensure_response( $bookings );
	}
}

// Initialize immediately so the endpoints register
new RestAPI();
