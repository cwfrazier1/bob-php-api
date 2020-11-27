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
	function sendSms($numbers, $message)
	{	
		$url = 'https://api.checkonmine.com/sendSms.php';
		$data = array('to' => $numbers, 'message' => serialize($message));
		$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",'method'  => 'POST','content' => http_build_query($data)));
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		//if ($result === FALSE) 
		//{ /* Handle error */ }
	}
/////S
/////U
	function uniqueId($lenght = 26) 
	{
		if (function_exists("random_bytes")) 
		{
			$bytes = random_bytes(ceil($lenght / 2));
		} 
		elseif (function_exists("openssl_random_pseudo_bytes")) 
		{
			$bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
		} 
		else 
		{
			throw new Exception("no cryptographically secure random function available");
		}
		return substr(bin2hex($bytes), 0, $lenght);
	}	
/////U
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
	
//######################################CHECK FOR CREDS AND LOG##############################
	if (empty($_REQUEST['id']) && empty($_REQUEST['userId']))
	{
		exit;
	}

	$id = $_REQUEST['id'];

	if (empty($id))
 		$id = $_REQUEST['userId'];
 
	$id = '08244630d14164caaa2fedc85d';
	$foundUser = false;

	$params = [
	    'TableName' => 'accounts'
	];

	try {
		while (true) {
	        	$result = $ddb->scan($params);

			foreach ($result['Items'] as $i)
			{
				$account = $marshaler->unmarshalItem($i);
	
				if ($account['id'] == $id)
				{
					$foundUser = true;
					break;
				}
        		}

	        		if (isset($result['LastEvaluatedKey'])) {
        	    		$params['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
        		} else {
            			break;
	        	}
	    	}
	} 
	catch (DynamoDbException $e) {
		echo "Unable to scan:\n";
		echo $e->getMessage() . "\n";
	}

	if ($foundUser == false)
	{
		exit;
	}

	$json = json_encode([
		'id' => $id,
		'ts' => time(),
		//'GLOBALS' => serialize($GLOBALS),
		'SERVER' => serialize($_SERVER),
		'GET' => serialize($_GET),
		'POST' => serialize($_POST),
		'FILES' => serialize($_FILES),
		'REQUEST' => serialize($_REQUEST),
		'SESSION' => serialize($_SESSION),
		'ENV' => serialize($_ENV),
		'COOKIE' => serialize($_COOKIE),
		'php-errormsg' => serialize($php_errormsg),
		'http-response-header' => serialize($http_response_header),
		'argc' => serialize($argc),
		'argv' => serialize($argv)
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
	
//######################################CHECK FOR CREDS AND LOG##############################

?>
