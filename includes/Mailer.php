<?php
namespace CustomBookingManager;

class Mailer {
	public function send_new_booking_admin( $booking_id, $booking_data ) {
		$admin_email = get_option( 'admin_email' );
		$subject = 'New Booking Request: ' . ucfirst( $booking_data['booking_type'] );
		
		$message = "You have received a new booking request.\n\n";
		$message .= "Name: {$booking_data['full_name']}\n";
		$message .= "Email: {$booking_data['email']}\n";
		$message .= "Date: {$booking_data['booking_date']}\n";
		$message .= "Time: {$booking_data['booking_time']}\n";
		
		wp_mail( $admin_email, $subject, $message );
	}

	public function send_booking_received_customer( $booking_data ) {
		$subject = 'Booking Request Received';
		$message = "Hi {$booking_data['full_name']},\n\n";
		$message .= "We have received your booking request for {$booking_data['booking_date']} at {$booking_data['booking_time']}. ";
		$message .= "We will review it and get back to you shortly.\n\nThank you!";
		
		wp_mail( $booking_data['email'], $subject, $message );
	}

	public function send_booking_approved( $booking_data ) {
		$subject = 'Booking Request Approved';
		$message = "Hi {$booking_data['full_name']},\n\n";
		$message .= "Your booking request for {$booking_data['booking_date']} at {$booking_data['booking_time']} has been approved!\n\n";
		$message .= "Thank you!";
		
		wp_mail( $booking_data['email'], $subject, $message );
	}

	public function send_booking_rejected( $booking_data ) {
		$subject = 'Booking Request Rejected';
		$message = "Hi {$booking_data['full_name']},\n\n";
		$message .= "Unfortunately, we cannot accommodate your booking request for {$booking_data['booking_date']} at {$booking_data['booking_time']}.\n\n";
		$message .= "Please try another date/time.";
		
		wp_mail( $booking_data['email'], $subject, $message );
	}
}
