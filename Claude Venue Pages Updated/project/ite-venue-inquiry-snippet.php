<?php
/**
 * ITE Venue Inquiry — custom AJAX endpoint
 * Paste this into Code Snippets (PHP, Run Everywhere) and activate.
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

    // ── Email notification ────────────────────────────────────────────
    $to      = get_option('admin_email');
    $subject = 'New Venue Inquiry: ' . $name;
    $body    = "<h2 style='margin:0 0 12px'>New Venue Inquiry</h2>
<table cellpadding='6' cellspacing='0' style='border-collapse:collapse;font-family:sans-serif;font-size:14px'>
  <tr><td><strong>Name</strong></td><td>{$name}</td></tr>
  <tr><td><strong>Email</strong></td><td><a href='mailto:{$email}'>{$email}</a></td></tr>
  <tr><td><strong>Event Date</strong></td><td>{$event_date}</td></tr>
  <tr><td><strong>Guest Count</strong></td><td>{$guest_count}</td></tr>
  <tr><td><strong>Venue / City</strong></td><td>{$venue}</td></tr>
</table>";
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];
    $sent = wp_mail($to, $subject, $body, $headers);

    // ── Fluent Forms entry (best-effort) ──────────────────────────────
    global $wpdb;
    $form_id  = 17;
    $response = wp_json_encode([
        'name'        => $name,
        'email'       => $email,
        'event_date'  => $event_date,
        'guest_count' => $guest_count,
        'venue'       => $venue,
    ]);
    $now = current_time('mysql');
    $wpdb->show_errors();
    $wpdb->insert(
        $wpdb->prefix . 'fluentform_submissions',
        [
            'form_id'       => $form_id,
            'serial_number' => 0,
            'response'      => $response,
            'source_url'    => esc_url_raw($_SERVER['HTTP_REFERER'] ?? home_url()),
            'ip'            => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
            'browser'       => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'status'        => 'unread',
            'is_favourite'  => 0,
            'user_id'       => 0,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]
    );
    $insert_id  = $wpdb->insert_id;
    $db_error   = $wpdb->last_error;
    if ( $insert_id ) {
        $form = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}fluentform_forms WHERE id = %d", $form_id
        ));
        if ( $form ) {
            do_action('fluentform_submission_inserted', $insert_id, json_decode($response, true), $form);
        }
    }

    // ── Respond ───────────────────────────────────────────────────────
    if ( $sent ) {
        wp_send_json(['success' => true, 'db_insert_id' => $insert_id, 'db_error' => $db_error]);
    } else {
        wp_send_json(['success' => false, 'message' => 'Could not send your inquiry — please email us directly at ' . get_option('admin_email') . '.']);
    }
}
