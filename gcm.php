<?php
/**
 * Created by PhpStorm.
 * the class that create a connection to the GCM and send a request
 * to the GCM server to send a push notifications to specific users.
 * User: matant
 * Date: 10/27/2015
 * Time: 10:12 AM
 */

class GCM {

    function __construct() {

    }
    /**
     * Sending Push Notification
     */
    public function send_notification($registatoin_ids, $data) {
        // include config
        include_once 'connection.php';

        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data'=> $data,
        );

        $headers = array(
            'Authorization: key=' .GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
        return $result;
    }
}
?>
