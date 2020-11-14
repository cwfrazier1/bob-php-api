<?
//######################################FUNCTIONS############################################
/////B
	function between($varToCheck, $low, $high) 
	{
		if ($varToCheck < $low) 
			return false;
		if ($varToCheck > $high) 
			return false;
		return true;
	}
/////B
/////C
	function convertSeconds($seconds) 
	{
  		$dt1 = new DateTime("@0");
 		$dt2 = new DateTime("@$seconds");
 		return $dt1->diff($dt2)->format('%a days, %h hours, %i minutes and %s seconds');
	}
/////C
/////S
	function sendSms($number, $message)
	{	
		$account_sid = 'ACc53fa684f8f23605aa3fefe40600b946';
		$auth_token = 'c509253a8d8248f7d98adc26c050e198';
		$twilio_number = "+14063447616";
		$client = new Client($account_sid, $auth_token);
		$client->messages->create(
    			$number,
			array(
				'from' => $twilio_number,
				'body' => $message
			)
		);
	}
/////S
//######################################FUNCTIONS############################################
	
//######################################AWS SETUP############################################
	require_once '/home/ubuntu/bob-php-api/aws/autoload.php';

	use Aws\DynamoDb\Exception\DynamoDbException;
	use Aws\DynamoDb\Marshaler;
	use Aws\Pinpoint\PinpointClient;
	use Aws\Exception\AwsException;

	$credentials = new Aws\Credentials\Credentials('AKIAJEDLXJ5DWOADKCGA', '0A+FwZ0UB1f0NSN1iqhKeKlEPvGhq9f6fX3u4EqR');
	$awsW = new Aws\Sdk(['region' => 'us-west-1', 'version' => 'latest', 'credentials' => $credentials]);	
	$awsE = new Aws\Sdk(['region' => 'us-east-1', 'version' => 'latest', 'credentials' => $credentials]);	
	$ddb = $awsW->createDynamoDb();
	$polly = new \Aws\Polly\PollyClient(['version' => 'latest', 'credentials' => $credentials, 'region' => 'us-east-1']);
	$s3 = new Aws\S3\S3Client(['version' => 'latest', 'region' => 'us-west-1', 'credentials' => $credentials]);
	$ec2 = new Aws\Ec2\Ec2Client(['version' => 'latest', 'region' => 'us-west-1', 'credentials' => $credentials]);
	$pinpointClient = new Aws\Pinpoint\PinpointClient(['version' => 'latest', 'region' => 'us-west-2', 'credentials' => $credentials]);

	$ddb = $awsW->createDynamoDb();
	$marshaler = new Marshaler();
//######################################AWS SETUP############################################

//####################################TWILIO  SETUP##########################################
	require '/home/ubuntu/bob-php-api/twilio/vendor/autoload.php';
	use Twilio\Rest\Client;
//####################################TWILIO  SETUP##########################################

?>
