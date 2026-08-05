<?php
namespace CustomBookingManager;

class Database {
	private $table_name;
	private $wpdb;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->table_name = $wpdb->prefix . 'custom_bookings';
	}

	public function insert_booking( $data ) {
		$result = $this->wpdb->insert(
			$this->table_name,
			[
				'booking_type'    => sanitize_text_field( $data['booking_type'] ),
				'full_name'       => sanitize_text_field( $data['full_name'] ),
				'email'           => sanitize_email( $data['email'] ),
				'phone'           => sanitize_text_field( $data['phone'] ),
				'booking_date'    => sanitize_text_field( $data['booking_date'] ),
				'booking_time'    => sanitize_text_field( $data['booking_time'] ),
				'booking_details' => wp_kses_post( $data['booking_details'] ),
				'status'          => 'pending',
				'created_at'      => current_time( 'mysql' ),
			],
			[ '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
		);

		if ( $result ) {
			return $this->wpdb->insert_id;
		}

		return false;
	}

	public function get_booking( $id ) {
		return $this->wpdb->get_row(
			$this->wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $id )
		);
	}

	public function get_bookings( $status = null ) {
		if ( $status ) {
			return $this->wpdb->get_results(
				$this->wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE status = %s ORDER BY created_at DESC", $status )
			);
		}
		
		return $this->wpdb->get_results( "SELECT * FROM {$this->table_name} ORDER BY created_at DESC" );
	}

	public function update_status( $id, $status ) {
		return $this->wpdb->update(
			$this->table_name,
			[ 'status' => sanitize_text_field( $status ) ],
			[ 'id' => intval( $id ) ],
			[ '%s' ],
			[ '%d' ]
		);
	}
    
    public function update_google_event_id( $id, $event_id ) {
        return $this->wpdb->update(
            $this->table_name,
            [ 'google_event_id' => sanitize_text_field( $event_id ) ],
            [ 'id' => intval( $id ) ],
            [ '%s' ],
            [ '%d' ]
        );
    }
    
    public function delete_booking( $id ) {
        return $this->wpdb->delete(
            $this->table_name,
            [ 'id' => intval( $id ) ],
            [ '%d' ]
        );
    }
}
