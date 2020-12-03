<?
require '/home/ubuntu/bob-php-api/twilio/vendor/autoload.php';
use Twilio\Rest\Client;

// Find your Account Sid and Auth Token at twilio.com/console
// DANGER! This is insecure. See http://twil.io/secure
$sid    = "ACc53fa684f8f23605aa3fefe40600b946";
$token  = "c509253a8d8248f7d98adc26c050e198";
$twilio = new Client($sid, $token);

$id = "08244630d14164caaa2fedc85d";

$binding = $twilio->notify->v1->services("ISf73402a379262abe29795d43e8ff1830")->bindings->create($id, "apn", "a083a527ce10c4238c0558915d7a0e3141ab909e962e6d6c12aa2f468716a041");
$notification = $twilio->notify->v1->services("ISf73402a379262abe29795d43e8ff1830")
                                   ->notifications
                                   ->create([
                                                "body" => "Hello Bob",
                                                "identity" => [$id]
                                            ]
                                   );

print($notification->sid);
print($binding->sid);

