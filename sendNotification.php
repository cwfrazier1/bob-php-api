<?
require '/home/ubuntu/bob-php-api/twilio/vendor/autoload.php';
use Twilio\Rest\Client;

$userId = $_POST['userId'];		
$type = $_POST['type'];		
$message = unserialize($_POST['message']);
$subject = $_POST['subject'];		
$to = '';

$iterator = $ddb->getIterator('Query',array('TableName' => 'accounts','KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $userId)),'ComparisonOperator' => 'EQ'))));

	foreach ($iterator as $item)
	{
		$phoneNumber = $item['phoneNumber']['S'];
		$emailAddress = $item['emailAddress']['S'];
		$iOSToken = $item['iOSToken']['S'];
	}

$sid    = "ACc53fa684f8f23605aa3fefe40600b946";
$token  = "c509253a8d8248f7d98adc26c050e198";
$twilio = new Client($sid, $token);
$is = "ISf73402a379262abe29795d43e8ff1830";
$twilio_number = "+14063447616";

if ($type == 'apn')
{
	$binding = $twilio->notify->v1->services($is)->bindings->create($userId, "apn", $iOSToken);
	$notification = $twilio->notify->v1->services($is)->notifications->create(["body" => $message,"identity" => [$userId], 'binding_type' => 'apn']);
	$to = $iOSToken;
}

if ($type == 'sms')
{
	$twilio->messages->create("+1".$phoneNumber,array('from' => $twilio_number,'body' => $message));
	$to = $phoneNumber;
}

if ($type == 'call')
{
	$call = $twilio->calls->create("+1".$phoneNumber, $twilio_number,["twiml" => "<Response><Pause length=\"2\"/><Say>$message</Say></Response>"]);
	$to = $phoneNumber;
}

if ($type == 'email')
{
	$char_set = 'UTF-8';

	try 
	{
		$result = $ses->sendEmail(['Destination' => ['ToAddresses' => array($emailAddress),],'ReplyToAddresses' => ['no-reply@checkonmine.com'],'Source' => 'no-reply@checkonmine.com','Message' => ['Body' => ['Text' => ['Charset' => $char_set,'Data' => $message,],], 'Subject' => ['Charset' => $char_set,'Data' => $subject,],],]);
		$messageId = $result['MessageId'];
	} 
	catch (AwsException $e) 
	{
		echo $e->getMessage();
		echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
		echo "\n";
	}
	$to = $emailAddress;
}

$json = json_encode(['id' => $userId,'ts' => time(),'type' => $type,'to' => $to,'message' => $message,'subject' => $subject]);
$params = ['TableName' => 'system-log',	'Item' => $marshaler->marshalJson($json)];
		
try 
{
	$result = $ddb->putItem($params);
} 
catch (DynamoDbException $e) 
{
	echo $e->getMessage() . "\n";
}
?>
