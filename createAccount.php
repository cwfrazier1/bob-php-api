<?
	$id = uniqueId();
	$ts = time();

	$phoneNumber = $_POST['phoneNumber'];
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$address = $_POST['address'];
	$addressLineTwo= $_POST['addressLineTwo'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	$zipCode = $_POST['zipCode'];
	$emailAddress = $_POST['emailAddress'];
	$password = $_POST['password'];
	$newsletter = $_POST['newsletter'];
/*
	$phoneNumber = '6613685684';
	$firstName = 'John';
	$lastName = 'Doe';
	$address = '9001 Stockdale Hwy';
	$addressLineTwo= '';
	$city = 'Bakersfield';
	$state = 'California';
	$zipCode = '93309';
	$emailAddress = 'johndoe@cwfrazier.com';
	$password = md5('azazazaz');
	$newsletter = '1';
 */	
	$json = json_encode([
			'id' => $id,
			'phoneNumber' => $phoneNumber,
			'ts' => $ts,
			'firstName' => $firstName,
			'lastName' => $lastName,
			'address' => $address,
			'addressLineTwo' => $addressLineTwo,
			'state' => $state,
			'city' => $city,
			'password' => md5($password),
			'emailAddress' => $emailAddress,
			'zipCode' => $zipCode,
			'newsletter' => $newsletter
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
				$welcomeEmail = "Dear $firstName,\n\nThank you for signing up for the Check on Mine Beta!\n\nThe goal of this beta first and foremost is to identify any bugs that may have been overlooked in development. If you come across what you think to be a bug or something ought to be working differently, please send an email to support@checkonmine.com, a Facebook message (https://facebook.com/checkonmine), a tweet to @CheckOnMine or on the website at https://checkonmine.com/support\n\nAll of the above methods will automatically create a support ticket which will allow me to track bugs across users.\n\nThe second goal of this beta is to start tracking location information of the users so I can start building out the routine detection algorithms. Apple has recently disabled the ability for apps to request \"Always Allow\" location tracking so this needs to be turned on manually. For instructions on how to do this, go https://checkonmine.com/location\n\nI sincerely appreciate your assistance in helping me test out this new venture!\n\nSincerely,\nChester Frazier";

				$verificationCode = rand(100000, 999999);
				sendSms($phoneNumber, "Please enter the following code: $verificationCode", 'Hso9TMZVejesnBtD3Ixe');
				notify($id, 'email', $welcomeEmail, 'Welcome to the Check on Mine Beta!');
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
