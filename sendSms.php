<?
	require '/home/ubuntu/bob-php-api/twilio/vendor/autoload.php';
	use Twilio\Rest\Client;

	$account_sid = 'ACc53fa684f8f23605aa3fefe40600b946';
	$auth_token = 'c509253a8d8248f7d98adc26c050e198';
	$twilio_number = "+14063447616";
	$twilioClient = new Client($account_sid, $auth_token);

	$number = $_POST['to'];
	$message = unserialize($_POST['message']);

	$twilioClient->messages->create($number,array('from' => $twilio_number,'body' => $message));
	$json = json_encode([
		'ts' => time(),
		'type' => 'sms',
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
