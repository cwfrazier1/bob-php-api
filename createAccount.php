<?
	$id = uniqueId();
	$ts = time();

	$json = json_encode([
			'id' => $id,
			'phoneNumber' => '6614475919',
			'ts' => $ts,
			'firstName' => 'Chester',
			'lastName' => 'Frazier',
			'address' => '514 South Kern St',
			'city' => 'Maricopa',
			'password' => md5('krvg797C8T'),
			'emailAddress' => 'cwfrazier@cwfrazier.com',
			'zipCode' => '93252'
		]);

		$params = [
			'TableName' => 'accounts',
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
