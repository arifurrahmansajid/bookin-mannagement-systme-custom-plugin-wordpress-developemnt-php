<?php
if ( ! isset( $form_title ) || ! isset( $form_type ) || ! isset( $post_id ) ) {
	return; // Missing required dynamic data
}

$schema_json = get_post_meta( $post_id, '_cbm_form_schema', true );
$fields = json_decode( $schema_json, true );

if ( empty( $fields ) || ! is_array( $fields ) ) {
	echo '<p>This form has no fields configured.</p>';
	return;
}
?>
<div class="cbm-form-container">
	<h3><?php echo esc_html( $form_title ); ?></h3>
	<div class="cbm-form-message"></div>
	<form id="cbm-<?php echo esc_attr( $form_type ); ?>-form" class="cbm-booking-form">
		<?php wp_nonce_field( 'cbm_booking_nonce', 'cbm_nonce' ); ?>
		<input type="hidden" name="booking_type" value="<?php echo esc_attr( $form_type ); ?>">
		
		<?php foreach ( $fields as $field ) : 
			$required = $field['required'] ? 'required' : '';
			$req_asterisk = $field['required'] ? ' *' : '';
		?>
			<div class="cbm-form-group">
				<label><?php echo esc_html( $field['label'] . $req_asterisk ); ?></label>
				
				<?php if ( $field['type'] === 'textarea' ) : ?>
					<textarea name="dynamic_<?php echo esc_attr( $field['name'] ); ?>" rows="4" <?php echo $required; ?>></textarea>
				<?php else : ?>
					<input type="<?php echo esc_attr( $field['type'] ); ?>" name="dynamic_<?php echo esc_attr( $field['name'] ); ?>" <?php echo $required; ?>>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>

		<button type="submit" class="cbm-submit-btn">Submit Booking Request</button>
	</form>
</div>
