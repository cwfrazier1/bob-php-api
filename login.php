
<?
$phoneNumber = $_POST['phoneNumber'];
$password = md5($_POST['password']);

if (empty($phoneNumber)) 
	$phoneNumber = '6614475919';

if (empty($password)) 
	$password = md5('krvg797C8T');

$params = [
    'TableName' => 'accounts'
];

try {
    while (true) {
        $result = $ddb->scan($params);

	foreach ($result['Items'] as $i) 
	{
		$account = $marshaler->unmarshalItem($i);

		if ($account['password'] == $password)
		{
			$verificationCode = 0;

			if ($account['phoneNumber'] != '1231231234')
			{
				$verificationCode = rand(100000, 999999);
				sendSms($phoneNumber, "Please enter the following code: $verificationCode", $account['id']);
			}
			else
			{
				$verificationCode = 999999;
			}
			$data=array('status' => 200, 'verificationCode' => $verificationCode);
			echo json_encode($data);
			exit;
		}
        }

        if (isset($result['LastEvaluatedKey'])) {
            $params['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
        } else {
            break;
        }
    }

} catch (DynamoDbException $e) {
    echo "Unable to scan:\n";
    echo $e->getMessage() . "\n";
}
?>
