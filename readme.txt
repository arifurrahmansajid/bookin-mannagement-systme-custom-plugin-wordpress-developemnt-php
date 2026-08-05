=== Custom Booking Manager ===
Contributors: Your Name
Donate link: https://example.com/
Tags: booking, appointments, calendar, google calendar
Requires at least: 5.8
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A custom booking system with admin approval workflow, Google Calendar integration, and frontend forms.

== Description ==

Custom Booking Manager is a complete solution for taking booking requests and approving them via the WordPress dashboard. 
When a user submits a request, it stays as 'pending' until an administrator approves it. Upon approval, an event is automatically created in Google Calendar, and the customer is notified by email.

### Features
* Custom shortcodes for Recording and Performance forms.
* AJAX-powered submission with no page reloads.
* WP List Table dashboard to view, approve, and reject bookings.
* Google Calendar API integration.
* FullCalendar admin view.
* CSV Export for bookings.
* Custom REST API endpoints.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/custom-booking-manager` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->Custom Booking Manager menu to configure your Google API credentials.
4. Place the shortcode `[recording_booking_form]` or `[performance_booking_form]` on any page or post.

== Frequently Asked Questions ==

= How do I get Google API Credentials? =

You need to create a project in the Google Cloud Console, enable the Google Calendar API, and create OAuth 2.0 Client IDs. Set the redirect URI to your WordPress admin settings page.

== Changelog ==

= 1.0.0 =
* Initial Release.
