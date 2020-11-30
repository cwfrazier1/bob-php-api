<?
	$id = uniqueId();
	$ts = time();

	$phoneNumber = $_POST['phoneNumber'];
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$address = $_POST['addresss'];
	$addressLineTwo= $_POST['addressLineTwo'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	$zipCode = $_POST['zipCode'];
	$emailAddress = $_POST['emailAddress'];
	$password = $_POST['password'];
	
	$json = json_encode([
			'id' => $id,
			'phoneNumber' => $password,
			'ts' => $ts,
			'firstName' => $firstName,
			'lastName' => $lastName,
			'address' => $address,
			'addressLineTwo' => $addressLineTwo,
			'state' => $state,
			'city' => $city,
			'password' => md5($password),
			'emailAddress' => $emailAddress,
			'zipCode' => $zipCode
		]);

		$params = [
			'TableName' => 'accounts',
			'Item' => $marshaler->marshalJson($json)
		];
		
		try 
		{
			$result = $ddb->putItem($params);

			if ($phoneNumber != '1231231234')
			{
				$verificationCode = rand(100000, 999999);
				sendSms($phoneNumber, "Please enter the following code: $verificationCode");
			}
			else
			{
				$verificationCode = 999999;
			}
			$data=array('status' => 200, 'verificationCode' => $verificationCode);
			echo json_encode($data);
    		} 
		catch (DynamoDbException $e) 
		{
			echo $e->getMessage() . "\n";
		}
?>
