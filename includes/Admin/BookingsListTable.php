<?php
namespace CustomBookingManager\Admin;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

use WP_List_Table;
use CustomBookingManager\Database;

class BookingsListTable extends WP_List_Table {
	private $db;
	private $status_filter;

	public function __construct( Database $db, $status_filter = null ) {
		parent::__construct( [
			'singular' => 'booking',
			'plural'   => 'bookings',
			'ajax'     => false
		] );
		$this->db = $db;
		$this->status_filter = $status_filter;
	}

	public function get_columns() {
		return [
			'id'           => 'ID',
			'customer'     => 'Customer',
			'type'         => 'Type',
			'date'         => 'Date',
			'time'         => 'Time',
			'status'       => 'Status',
			'actions'      => 'Actions'
		];
	}

	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = [];
		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$this->items = $this->db->get_bookings( $this->status_filter );
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				return $item->id;
			case 'customer':
				return '<strong>' . esc_html( $item->full_name ) . '</strong><br>' . esc_html( $item->email ) . '<br>' . esc_html( $item->phone );
			case 'type':
				return ucfirst( esc_html( $item->booking_type ) );
			case 'date':
				return esc_html( $item->booking_date );
			case 'time':
				return esc_html( $item->booking_time );
			case 'status':
				$color = $item->status === 'approved' ? 'green' : ( $item->status === 'rejected' ? 'red' : 'orange' );
				return "<span style='color: {$color}; font-weight: bold;'>" . ucfirst( esc_html( $item->status ) ) . "</span>";
			case 'actions':
				$actions = [];
				if ( $item->status === 'pending' ) {
					$approve_url = wp_nonce_url( admin_url( 'admin-post.php?action=cbm_update_booking_status&id=' . $item->id . '&status=approved' ), 'cbm_update_status' );
					$reject_url = wp_nonce_url( admin_url( 'admin-post.php?action=cbm_update_booking_status&id=' . $item->id . '&status=rejected' ), 'cbm_update_status' );
					$actions[] = "<a href='{$approve_url}' style='color: green;'>Approve</a>";
					$actions[] = "<a href='{$reject_url}' style='color: red;'>Reject</a>";
				}
				
				// Format dynamic details for JS alert
				$details_arr = json_decode( $item->booking_details, true );
				$formatted_details = '';
				if ( is_array( $details_arr ) ) {
					foreach ( $details_arr as $k => $v ) {
						$formatted_details .= ucfirst( str_replace( '_', ' ', $k ) ) . ": " . esc_js( $v ) . "\\n";
					}
				} else {
					$formatted_details = esc_js( $item->booking_details );
				}

				$actions[] = "<a href='#' onclick='alert(\"Dynamic Form Details:\\n\\n\" + \"" . $formatted_details . "\"); return false;'>View Details</a>";
				return implode( ' | ', $actions );
			default:
				return print_r( $item, true );
		}
	}
}
