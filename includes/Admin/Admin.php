<?php
namespace CustomBookingManager\Admin;

use CustomBookingManager\Database;
use CustomBookingManager\Mailer;
use CustomBookingManager\GoogleCalendar;

class Admin {
	private $db;
	private $mailer;
	private $google_calendar;

	public function __construct( Database $db, Mailer $mailer, GoogleCalendar $google_calendar ) {
		$this->db = $db;
		$this->mailer = $mailer;
		$this->google_calendar = $google_calendar;
	}

	public function init() {
		add_action( 'admin_menu', [ $this, 'register_menus' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_post_cbm_update_booking_status', [ $this, 'handle_status_update' ] );
		
		// Settings initialization
		$settings = new Settings();
		$settings->init();

		// Export initialization
		$export = new Export( $this->db );
		$export->init();
	}

	public function register_menus() {
		add_menu_page(
			'Bookings',
			'Bookings',
			'manage_options',
			'cbm-bookings',
			[ $this, 'render_dashboard' ],
			'dashicons-calendar-alt',
			25
		);

		add_submenu_page(
			'cbm-bookings',
			'All Bookings',
			'All Bookings',
			'manage_options',
			'cbm-bookings',
			[ $this, 'render_dashboard' ]
		);

		add_submenu_page(
			'cbm-bookings',
			'Pending',
			'Pending',
			'manage_options',
			'cbm-bookings-pending',
			[ $this, 'render_dashboard' ]
		);

		add_submenu_page(
			'cbm-bookings',
			'Approved',
			'Approved',
			'manage_options',
			'cbm-bookings-approved',
			[ $this, 'render_dashboard' ]
		);

		add_submenu_page(
			'cbm-bookings',
			'Rejected',
			'Rejected',
			'manage_options',
			'cbm-bookings-rejected',
			[ $this, 'render_dashboard' ]
		);
		
		add_submenu_page(
			'cbm-bookings',
			'Calendar View',
			'Calendar View',
			'manage_options',
			'cbm-calendar',
			[ $this, 'render_calendar' ]
		);

		add_submenu_page(
			'cbm-bookings',
			'Settings',
			'Settings',
			'manage_options',
			'cbm-settings',
			[ $this, 'render_settings' ]
		);
	}

	public function enqueue_assets( $hook ) {
		if ( strpos( $hook, 'cbm' ) === false ) {
			return;
		}

		wp_enqueue_style( 'cbm-admin-css', CBM_PLUGIN_URL . 'admin/css/admin-styles.css', [], CBM_VERSION );
		
		if ( $hook === 'bookings_page_cbm-calendar' ) {
			wp_enqueue_script( 'sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true );
			wp_enqueue_script( 'fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js', [], '6.1.11', true );
			wp_enqueue_script( 'cbm-admin-js', CBM_PLUGIN_URL . 'admin/js/admin-calendar.js', [ 'fullcalendar', 'jquery', 'sweetalert2' ], CBM_VERSION, true );
			
			// Get bookings for calendar
			$bookings = $this->db->get_bookings();
			$events = [];
			foreach ( $bookings as $b ) {
				$color = '#f1c40f'; // pending - yellow
				if ( $b->status === 'approved' ) $color = '#2ecc71'; // green
				if ( $b->status === 'rejected' ) $color = '#e74c3c'; // red
				
				$start = $b->booking_date . 'T' . $b->booking_time;
				$events[] = [
					'id' => $b->id,
					'title' => ucfirst($b->booking_type) . ': ' . $b->full_name,
					'start' => $start,
					'color' => $color,
					'extendedProps' => [
						'status' => $b->status,
						'details' => $b->booking_details
					]
				];
			}

			wp_localize_script( 'cbm-admin-js', 'cbm_calendar_data', [
				'events' => $events
			] );
		}
	}

	public function render_dashboard() {
		$status_filter = null;
		$page = $_GET['page'];
		
		if ( $page === 'cbm-bookings-pending' ) $status_filter = 'pending';
		if ( $page === 'cbm-bookings-approved' ) $status_filter = 'approved';
		if ( $page === 'cbm-bookings-rejected' ) $status_filter = 'rejected';

		$list_table = new BookingsListTable( $this->db, $status_filter );
		$list_table->prepare_items();
		
		include CBM_PLUGIN_DIR . 'admin/views/dashboard-view.php';
	}

	public function render_calendar() {
		include CBM_PLUGIN_DIR . 'admin/views/calendar-view.php';
	}

	public function render_settings() {
		include CBM_PLUGIN_DIR . 'admin/views/settings-view.php';
	}
	
	public function handle_status_update() {
		if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
		check_admin_referer( 'cbm_update_status' );
		
		$id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
		$status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
		
		if ( $id && in_array( $status, ['approved', 'rejected'] ) ) {
			$this->db->update_status( $id, $status );
			$booking = $this->db->get_booking( $id );
			
			if ( $status === 'approved' ) {
				// Create google event
				$event_id = $this->google_calendar->create_event( $booking );
				if ( $event_id ) {
					$this->db->update_google_event_id( $id, $event_id );
				}
				$this->mailer->send_booking_approved( (array) $booking );
			} elseif ( $status === 'rejected' ) {
				$this->mailer->send_booking_rejected( (array) $booking );
			}
		}
		
		wp_redirect( admin_url( 'admin.php?page=cbm-bookings' ) );
		exit;
	}
}
