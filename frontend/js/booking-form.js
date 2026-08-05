jQuery(document).ready(function($) {
	$('.cbm-booking-form').on('submit', function(e) {
		e.preventDefault();
		
		var $form = $(this);
		var $btn = $form.find('.cbm-submit-btn');
		
		var formData = $form.serialize();
		formData += '&action=cbm_submit_booking';
		formData += '&nonce=' + $form.find('input[name="cbm_nonce"]').val();

		$btn.prop('disabled', true).text('Submitting...');

		$.post(cbm_ajax.url, formData, function(response) {
			$btn.prop('disabled', false).text('Submit Booking Request');
			
			if (response.success) {
				Swal.fire({
					icon: 'success',
					title: 'Success!',
					text: response.data.message,
					confirmButtonColor: '#3498db'
				});
				$form[0].reset();
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: response.data.message,
					confirmButtonColor: '#3498db'
				});
			}
		}).fail(function() {
			$btn.prop('disabled', false).text('Submit Booking Request');
			Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: 'An unexpected error occurred. Please try again.',
				confirmButtonColor: '#3498db'
			});
		});
	});
});
