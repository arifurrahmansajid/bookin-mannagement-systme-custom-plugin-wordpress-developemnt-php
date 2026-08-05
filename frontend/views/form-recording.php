<div class="cbm-form-container">
	<h3>Book a Recording Session</h3>
	<div class="cbm-form-message"></div>
	<form id="cbm-recording-form" class="cbm-booking-form">
		<?php wp_nonce_field( 'cbm_booking_nonce', 'cbm_nonce' ); ?>
		<input type="hidden" name="booking_type" value="recording">
		
		<div class="cbm-form-group">
			<label>Full Name *</label>
			<input type="text" name="full_name" required>
		</div>

		<div class="cbm-form-group">
			<label>Email *</label>
			<input type="email" name="email" required>
		</div>

		<div class="cbm-form-group">
			<label>Phone *</label>
			<input type="tel" name="phone" required>
		</div>

		<div class="cbm-form-group cbm-form-row">
			<div class="cbm-col">
				<label>Preferred Date *</label>
				<input type="date" name="booking_date" required>
			</div>
			<div class="cbm-col">
				<label>Preferred Time *</label>
				<input type="time" name="booking_time" required>
			</div>
		</div>

		<div class="cbm-form-group">
			<label>Session Details</label>
			<textarea name="booking_details" rows="4"></textarea>
		</div>

		<button type="submit" class="cbm-submit-btn">Submit Booking Request</button>
	</form>
</div>
