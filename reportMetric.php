<?
	$phoneNumber = $_POST['phoneNumber'];
	$metric = $_POST['metric'];
	$value = $_POST['value'];
	$longitude = $_POST['longitude'];
	$latitude = $_POST['latiitude'];

	$ts = time();

	if (empty($phoneNumber))
		$phoneNumber = "6612031768";

	if (empty($latitude)) 
		$latitude = 35.056990;

	if (empty($longitude)) 
		$longitude = -119.399750;

	if (empty($metric)) 
		$metric = "Location";

	if (empty($value)) 
		$value = "Location Changed";

	if ($metric == 'Location')
	{
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=AIzaSyBPUUIC3mXSlfSNsATFSskmbGNMFliAjJ4";

		$json = @file_get_contents($url);
		$data = json_decode($json);

		$status = $data->status;
	
		if($status=="OK") 
		{
        		for ($j=0;$j<count($data->results[0]->address_components);$j++) 
			{
				$cn=array($data->results[0]->address_components[$j]->types[0]);
			
				if(in_array("locality", $cn)) 
				{
					$address = $data->results[0]->formatted_address;
				}
			}
		} 
		else
		{
		       	echo 'Location Not Found';
		}

		$addressArr = array('address' => $address, 'longitude' => $longitude, 'latitude' => $latitude);

		$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'lastKnownLocation',],'ExpressionAttributeValues' => [':y' => ['S' => json_encode($addressArr),],],'Key' => ['phoneNumber' => ['S' => $phoneNumber,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);

		$json = json_encode([
			'phoneNumber' => $phoneNumber,
			'ts' => $ts,
			'metric' => $metric,
			'value' => $addressArr
		]);

		$params = [
			'TableName' => 'actions',
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
	}
?>
