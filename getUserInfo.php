
<?
$phoneNumber = $_POST['phoneNumber'];

if (empty($phoneNumber)) 
	$phoneNumber = '6614475919';

$params = [
    'TableName' => 'accounts'
];

$userData = array();

try {
    while (true) {
        $result = $ddb->scan($params);

	foreach ($result['Items'] as $i) 
	{
		$account = $marshaler->unmarshalItem($i);

		if ($account['phoneNumber'] == $phoneNumber)
		{
			$userData['id'] = $account['id'];
			$userData['firstName'] = $account['firstName'];
			$userData['lastName'] = $account['lastName'];
			$userData['address'] = $account['address'];
			$userData['addressLineTwo'] = $account['addressLineTwo'];
			$userData['city'] = $account['city'];
			$userData['emailAddress'] = $account['emailAddress'];
			$userData['zipCode'] = $account['zipCode'];
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

echo json_encode($userData);
?>
