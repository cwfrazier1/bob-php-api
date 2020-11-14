<?
	$phoneNumber = $_REQUEST['phoneNumber'];
	$metric = $_REQUEST['metric'];
	$value = $_REQUEST['value'];
	$longitude = $_REQUEST['longitude'];
	$latitude = $_REQUEST['latitude'];

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
			'value' => $addressArr,
			'addressResult' => json_encode($data)
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
	else
	{
		$json = json_encode([
			'phoneNumber' => $phoneNumber,
			'ts' => $ts,
			'metric' => $metric,
			'value' => $value
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


	$iterator = $ddb->getIterator('Query',array('TableName' => 'accounts','KeyConditions' => array('phoneNumber' => array('AttributeValueList' => array(array('S' => '6612031768')),'ComparisonOperator' => 'EQ'))));
	$data=array();

	foreach ($iterator as $item)
	{
		$sleepAverage = $item['sleepAverage']['N'];
		$lastCheckedIn = $item['last_checked_in']['N'];
		$lastWakeAlert = $item['last_wake_alert']['N'];

		$send = false;

		if (empty($lastWakeAlert))
			$send = true;

		if (($ts - $lastWakAlert) > 36000) //10 hours
			$send = true;

		$metrics = array();
		$checkinCount = 0;

		$consecutive = $ddb->getIterator('Query', array('TableName' => 'actions','KeyConditions' => array('phoneNumber' => array('AttributeValueList' => array(array('S' => $account['phoneNumber'])),'ComparisonOperator' => 'EQ'),'ts' => array('AttributeValueList' => array(array('N' => $ts - 600)), 'ComparisonOperator' => 'GT'))));

		foreach ($consecutive as $checkin) 
		{
			$dts=$icheckin['metric']['S'];
			
			if (!in_array($dts, $metrics))
			{
				$metrics[]=$dts;
			}

			$checkinCount++;
		}

		if (count($metrics) <= 1 || $checkinCount <= 4)
		{
			$send = false;
		}

		$difference = $sleepAverage * .15;
		$upper = $sleepAverage + $difference;	
		$lower = $sleepAverage - $difference;

		$timeout = $ts - $lastCheckedIn;
		
		if (between($timeout, $lower, $upper) && $send)
		{

			$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'last_wake_alert',],'ExpressionAttributeValues' => [':y' => ['N' => (string)$ts,],],'Key' => ['phoneNumber' => ['S' => $phoneNumber,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);
			$number='6612031768';
			$message='It appears as if Cee has awoken. Beware.';
			include 'sendSms.php';
			$number='6617474517';
			include 'sendSms.php';
		}

	}

	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'last_checked_in',],'ExpressionAttributeValues' => [':y' => ['N' => (string)$ts,],],'Key' => ['phoneNumber' => ['S' => $phoneNumber,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);
?>
