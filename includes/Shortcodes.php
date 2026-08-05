<?php
namespace CustomBookingManager;

class Shortcodes {
	public function init() {
		add_shortcode( 'cbm_form', [ $this, 'render_dynamic_form' ] );
		
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function enqueue_assets() {
		wp_register_style( 'cbm-frontend-css', CBM_PLUGIN_URL . 'frontend/css/form-styles.css', [], CBM_VERSION );
		wp_register_script( 'sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true );
		wp_register_script( 'cbm-frontend-js', CBM_PLUGIN_URL . 'frontend/js/booking-form.js', [ 'jquery', 'sweetalert2' ], CBM_VERSION, true );
		
		// Custom Appearance Settings
		$bg_color = get_option( 'cbm_form_bg_color', '#ffffff' );
		$primary_color = get_option( 'cbm_form_primary_color', '#3498db' );
		$custom_css = get_option( 'cbm_form_custom_css', '' );
		
		$inline_css = "
			.cbm-form-container { background-color: {$bg_color} !important; }
			.cbm-submit-btn { background-color: {$primary_color} !important; }
			.cbm-form-group input:focus, .cbm-form-group textarea:focus { border-color: {$primary_color} !important; box-shadow: 0 0 0 3px {$primary_color}33 !important; }
			{$custom_css}
		";
		wp_add_inline_style( 'cbm-frontend-css', $inline_css );
		
		wp_localize_script( 'cbm-frontend-js', 'cbm_ajax', [
			'url' => admin_url( 'admin-ajax.php' )
		] );
	}

	public function render_dynamic_form( $atts ) {
		$atts = shortcode_atts( [
			'id' => 0,
		], $atts, 'cbm_form' );

		$post_id = intval( $atts['id'] );
		if ( ! $post_id || get_post_type( $post_id ) !== 'cbm_form' ) {
			return '<p>Form not found.</p>';
		}

		$post = get_post( $post_id );
		$form_title = $post->post_title;
		$form_type = sanitize_title( $form_title );

		wp_enqueue_style( 'cbm-frontend-css' );
		wp_enqueue_script( 'cbm-frontend-js' );

		ob_start();
		include CBM_PLUGIN_DIR . 'frontend/views/form-template.php';
		return ob_get_clean();
	}
}
