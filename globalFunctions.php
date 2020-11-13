<?
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
?>
