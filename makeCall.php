<?
	require '/home/ubuntu/bob-php-api/twilio/vendor/autoload.php';
	use Twilio\Rest\Client;

	$account_sid = 'ACc53fa684f8f23605aa3fefe40600b946';
	$auth_token = 'c509253a8d8248f7d98adc26c050e198';
	$twilio_number = "+14063447616";
	$twilioClient = new Client($account_sid, $auth_token);

	$id = $_POST['userId'];
	$number = $_POST['to'];
	$message = unserialize($_POST['message']);

	$call = $twilioClient->calls
               ->create("+1$number", // to
                        "+14063447616", // from
                        [
                            "twiml" => "<Response><Say loop=\"3\">$message</Say></Response>"
                        ]
               );
	
	$json = json_encode([
		'id' => $id,
		'ts' => time(),
		'type' => 'phone call',
		'to' => $number,
		'message' => $message
	]);

	$params = [
		'TableName' => 'system-log',
		'Item' => $marshaler->marshalJson($json)
	];
		
	try 
	{
		$result = $ddb->putItem($params);
	} 
	catch (DynamoDbException $e) 
	{
		echo $e->getMessage() . "\n";
	}
?>
