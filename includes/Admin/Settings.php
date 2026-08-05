<?php
namespace CustomBookingManager\Admin;

class Settings {
	public function init() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	public function register_settings() {
		register_setting( 'cbm_settings_group', 'cbm_google_client_id' );
		register_setting( 'cbm_settings_group', 'cbm_google_client_secret' );
		register_setting( 'cbm_settings_group', 'cbm_google_recording_calendar' );
		register_setting( 'cbm_settings_group', 'cbm_google_performance_calendar' );
		register_setting( 'cbm_settings_group', 'cbm_recaptcha_site_key' );
		register_setting( 'cbm_settings_group', 'cbm_recaptcha_secret_key' );
		
		// Appearance Settings
		register_setting( 'cbm_settings_group', 'cbm_form_bg_color' );
		register_setting( 'cbm_settings_group', 'cbm_form_primary_color' );
		register_setting( 'cbm_settings_group', 'cbm_form_custom_css' );
	}
}
