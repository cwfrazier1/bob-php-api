<?php
require '/home/ubuntu/bob-php-api/twilio/vendor/autoload.php';;
use Twilio\Rest\Client;

// Your Account SID and Auth Token from twilio.com/console
$account_sid = 'ACc53fa684f8f23605aa3fefe40600b946';
$auth_token = 'c509253a8d8248f7d98adc26c050e198';
// In production, these should be environment variables. E.g.:
// $auth_token = $_ENV["c509253a8d8248f7d98adc26c050e198"]

// A Twilio number you own with SMS capabilities
$twilio_number = "+14063447616";

$client = new Client($account_sid, $auth_token);
$client->messages->create(
    // Where to send a text message (your cell phone?)
    '+16612031768',
    array(
        'from' => $twilio_number,
        'body' => 'I sent this message in under 10 minutes!'
    )
);

