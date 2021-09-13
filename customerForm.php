<?php
require '../../../wp-load.php'; 

if ( $_POST) {
    global $wpdb;
    
    $tablename = 'wp_customer_leads';

    $data = array(
        'name' => sanitize_text_field($_POST['cf-name']),
        'email' => sanitize_text_field($_POST['cf-email']),
        'phone' => sanitize_text_field($_POST['cf-phone']),
        'service_required' => sanitize_text_field($_POST['cf-service']),
    );
    
    $wpdb->insert( $tablename, $data);
    return true;
}

?>