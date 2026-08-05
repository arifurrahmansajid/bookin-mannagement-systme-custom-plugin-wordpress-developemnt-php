# Custom Booking Manager

A production-ready WordPress plugin for managing recording and performance booking requests.

## Architecture

This plugin follows standard WordPress coding standards, is completely object-oriented, and utilizes a custom PSR-4 autoloader. 
It does not require Composer to function, avoiding dependency issues in shared hosting environments.

## Features

- **Custom Table**: Uses `wp_custom_bookings` for safe, separate data storage.
- **AJAX Forms**: Shortcodes `[recording_booking_form]` and `[performance_booking_form]` deploy styled, responsive, asynchronous booking forms.
- **Admin Dashboard**: Utilizes `WP_List_Table` for displaying bookings, filtering by status, and inline actions (Approve/Reject).
- **Google Calendar API**: Uses native `wp_remote_post` functions for direct REST interaction to handle OAuth 2.0 and event creation without bloating the plugin with the full Google PHP Client Library.
- **Email Notifications**: Automated lifecycle emails for both the administrator and the customer.
- **FullCalendar View**: Uses the modern FullCalendar JS library for visual representation of bookings in the admin dashboard.
- **REST API Endpoint**: Exposes a `cbm/v1/bookings` endpoint for external integration.
- **Export to CSV**: Download all your bookings instantly.

## Security Practices Used

- Database preparation using `$wpdb->prepare()` to prevent SQL injection.
- Form nonces with `wp_nonce_field()` and `check_ajax_referer()` for CSRF protection.
- String sanitization with `sanitize_text_field()`, `sanitize_email()`, and `wp_kses_post()`.
- Direct file access prevention.
- Strict capability checks (`manage_options`).

## Installation Guide

1. Download or clone this repository into your WordPress `wp-content/plugins/` directory.
2. Log in to your WordPress admin dashboard and navigate to **Plugins**.
3. Locate **Custom Booking Manager** and click **Activate**.
4. The database table `wp_custom_bookings` will be created automatically.
5. Navigate to the new **Bookings -> Settings** menu to configure your Google Calendar API credentials.

## Testing Locally

If you are using Local WP (formerly Local by Flywheel) or XAMPP:
- Ensure your Google Cloud Console OAuth 2.0 Credentials have their **Authorized redirect URIs** matching your exact local environment URL.
- E.g., `http://my-local-site.local/wp-admin/admin.php?page=cbm-settings`
