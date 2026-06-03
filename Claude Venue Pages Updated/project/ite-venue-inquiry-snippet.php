<?php
/**
 * ITE Venue Inquiry — custom AJAX endpoint
 * Paste this into Code Snippets (Run Everywhere) and activate.
 */
add_action('wp_ajax_ite_venue_inquiry',        'ite_handle_venue_inquiry');
add_action('wp_ajax_nopriv_ite_venue_inquiry', 'ite_handle_venue_inquiry');

function ite_handle_venue_inquiry() {
    $name        = sanitize_text_field($_POST['name']        ?? '');
    $email       = sanitize_email(     $_POST['email']       ?? '');
    $event_date  = sanitize_text_field($_POST['event_date']  ?? '');
    $guest_count = sanitize_text_field($_POST['guest_count'] ?? '');
    $venue       = sanitize_text_field($_POST['venue']       ?? '');

    if ( ! $name || ! is_email($email) ) {
        wp_send_json(['success' => false, 'message' => 'Name and email are required.']);
    }

    global $wpdb;
    $form_id  = 17;
    $response = wp_json_encode([
        'name'        => $name,
        'email'       => $email,
        'event_date'  => $event_date,
        'guest_count' => $guest_count,
        'venue'       => $venue,
    ]);

    $inserted = $wpdb->insert(
        $wpdb->prefix . 'fluentform_submissions',
        [
            'form_id'      => $form_id,
            'response'     => $response,
            'source_url'   => esc_url_raw($_SERVER['HTTP_REFERER'] ?? home_url()),
            'ip'           => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
            'browser'      => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'status'       => 'unread',
            'is_favourite' => 0,
            'user_id'      => null,
            'created_at'   => current_time('mysql'),
            'updated_at'   => current_time('mysql'),
        ]
    );

    if ( ! $inserted ) {
        wp_send_json(['success' => false, 'message' => 'Could not save your inquiry. Please try again.']);
    }

    $insert_id = $wpdb->insert_id;

    // Fire Fluent Forms hooks so admin email notifications trigger
    $form = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}fluentform_forms WHERE id = %d", $form_id)
    );
    if ( $form ) {
        do_action('fluentform_submission_inserted', $insert_id, json_decode($response, true), $form);
    }

    wp_send_json(['success' => true]);
}
