<?php
namespace CustomBookingManager;

class Core {
	public function run() {
		// Initialize the Database
		$db = new Database();

		// Initialize Mailer
		$mailer = new Mailer();

		// Initialize Google Calendar
		$google_calendar = new GoogleCalendar();

		// Initialize Ajax Handlers
		$ajax = new Ajax( $db, $mailer, $google_calendar );
		$ajax->init();

		// Initialize Form Manager
		$form_manager = new FormManager();
		$form_manager->init();

		// Initialize Shortcodes
		$shortcodes = new Shortcodes();
		$shortcodes->init();

		// Initialize Admin if we are in admin area
		if ( is_admin() ) {
			$admin = new Admin\Admin( $db, $mailer, $google_calendar );
			$admin->init();
		}
	}
}
