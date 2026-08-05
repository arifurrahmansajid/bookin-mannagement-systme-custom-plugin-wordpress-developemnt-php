<div class="wrap">
	<h1>Custom Booking Manager Settings</h1>
	
	<?php
	// Check if Google OAuth code is present in URL to process it
	if ( isset( $_GET['code'] ) ) {
		$google = new \CustomBookingManager\GoogleCalendar();
		$success = $google->authenticate( sanitize_text_field( $_GET['code'] ) );
		if ( $success ) {
			echo '<div class="notice notice-success is-dismissible"><p>Successfully authenticated with Google Calendar!</p></div>';
		} else {
			echo '<div class="notice notice-error is-dismissible"><p>Failed to authenticate with Google Calendar. Please check your credentials.</p></div>';
		}
	}
	?>

	<form method="post" action="options.php">
		<?php
			settings_fields( 'cbm_settings_group' );
			do_settings_sections( 'cbm_settings_group' );
		?>

		<h2>Google Calendar API Settings</h2>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Client ID</th>
				<td><input type="text" name="cbm_google_client_id" value="<?php echo esc_attr( get_option('cbm_google_client_id') ); ?>" class="regular-text" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">Client Secret</th>
				<td><input type="password" name="cbm_google_client_secret" value="<?php echo esc_attr( get_option('cbm_google_client_secret') ); ?>" class="regular-text" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">Recording Calendar ID</th>
				<td>
					<input type="text" name="cbm_google_recording_calendar" value="<?php echo esc_attr( get_option('cbm_google_recording_calendar') ); ?>" class="regular-text" />
					<p class="description">Leave blank for 'primary', or enter the exact Calendar ID (e.g. your_email@domain.com).</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Performance Calendar ID</th>
				<td>
					<input type="text" name="cbm_google_performance_calendar" value="<?php echo esc_attr( get_option('cbm_google_performance_calendar') ); ?>" class="regular-text" />
				</td>
			</tr>
		</table>
		
		<?php
		$client_id = get_option('cbm_google_client_id');
		$client_secret = get_option('cbm_google_client_secret');
		if ( !empty($client_id) && !empty($client_secret) ) {
			$google = new \CustomBookingManager\GoogleCalendar();
			$auth_url = $google->get_auth_url();
			
			$token = get_option('cbm_google_access_token');
			if ( $token ) {
				echo '<p style="color: green; font-weight: bold;">✓ Google Calendar is Connected.</p>';
				echo '<a href="' . esc_url( $auth_url ) . '" class="button">Reconnect Google Calendar</a>';
			} else {
				echo '<a href="' . esc_url( $auth_url ) . '" class="button button-primary">Connect with Google Calendar</a>';
			}
		} else {
			echo '<p class="description">Save your Client ID and Secret first to connect your Google Calendar.</p>';
		}
		?>
		
		<hr>
		
		<h2>reCAPTCHA v3 Settings (Optional)</h2>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Site Key</th>
				<td><input type="text" name="cbm_recaptcha_site_key" value="<?php echo esc_attr( get_option('cbm_recaptcha_site_key') ); ?>" class="regular-text" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">Secret Key</th>
				<td><input type="password" name="cbm_recaptcha_secret_key" value="<?php echo esc_attr( get_option('cbm_recaptcha_secret_key') ); ?>" class="regular-text" /></td>
			</tr>
		</table>

		<hr>
		
		<h2>Form Customizer & Appearance</h2>
		<p class="description">Customize the look and feel of your frontend booking forms.</p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Form Background Color</th>
				<td>
					<input type="color" name="cbm_form_bg_color" value="<?php echo esc_attr( get_option('cbm_form_bg_color', '#ffffff') ); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Primary Color (Buttons & Accents)</th>
				<td>
					<input type="color" name="cbm_form_primary_color" value="<?php echo esc_attr( get_option('cbm_form_primary_color', '#3498db') ); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Custom CSS</th>
				<td>
					<textarea name="cbm_form_custom_css" rows="6" class="large-text code"><?php echo esc_textarea( get_option('cbm_form_custom_css', '') ); ?></textarea>
					<p class="description">Add your own CSS here to override any default styles.</p>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
	
	<div class="card" style="margin-top: 30px; max-width: 800px;">
		<h2>Live Preview & Shortcodes</h2>
		<p>To use a booking form, you must first create one in the <strong>Bookings > Form Builder</strong> menu.</p>
		<p>Once created, you will see a unique shortcode generated for it, such as:</p>
		<ul>
			<li><code>[cbm_form id="123"]</code></li>
		</ul>
		<br>
		<p><strong>Want to see how your form looks with the current colors?</strong></p>
		<p>
			Create a test page, paste your generated shortcode, and click "Preview" in the WordPress page editor to see your styles live!
		</p>
	</div>
</div>
