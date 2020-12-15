<?
	$id = $_REQUEST['id'];
	$metric = $_REQUEST['metric'];
	$value = $_REQUEST['value'];
	$longitude = $_REQUEST['longitude'];
	$latitude = $_REQUEST['latitude'];
	$address = trim($_REQUEST['address']);
	
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
		$lastKnownAddress = '';
		$iterator = $ddb->getIterator('Query',array('TableName' => 'accounts','KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'))));

		if (empty($address))
		{
			$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=AIzaSyBPUUIC3mXSlfSNsATFSskmbGNMFliAjJ4";
	 		$json = @file_get_contents($url);
 			$data = json_decode($json);
			$status = $data->status;

			if($status=="OK")
			{
		 		foreach ($iterator as $item)
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
			}
		}
 		else
 			$lastKnownLocation = $item['lastKnownLocation']['S'];
 			$lastKnownAddress = $lastKnownLocation['address'];
 		}

		}

		foreach ($iterator as $item)
		{
			$lastKnownLocation = $item['lastKnownLocation']['S'];
			$lastKnownAddress = $lastKnownLocation['address'];
		}
		
		if ($lastKnownAddress['S'] == $address)
		{
			exit;
		}


		$addressArr = array('address' => $address, 'longitude' => $longitude, 'latitude' => $latitude);
		
		$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'lastKnownLocation',],'ExpressionAttributeValues' => [':y' => ['S' => json_encode($addressArr),],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);

		$json = json_encode([
			'id' => $id,
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
			'id' => $id,
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


	$iterator = $ddb->getIterator('Query',array('TableName' => 'accounts','KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'))));
	$data=array();

	foreach ($iterator as $item)
	{
		$sleepAverage = $item['sleepAverage']['N'];
		$lastCheckedIn = $item['last_checked_in']['N'];
		$lastWakeAlert = $item['last_wake_alert']['N'];

		$send = false;

		if (empty($lastWakeAlert))
			$send = true;

		if (($ts - $lastWakeAlert) > 36000) //10 hours
			$send = true;

		$metrics = array();
		$checkinCount = 0;

		$consecutive = $ddb->getIterator('Query', array('TableName' => 'actions','KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'),'ts' => array('AttributeValueList' => array(array('N' => (string)($ts - 600))), 'ComparisonOperator' => 'GT'))));

		foreach ($consecutive as $checkin) 
		{
			$dts=$checkin['metric']['S'];
			
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

			$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'last_wake_alert',],'ExpressionAttributeValues' => [':y' => ['N' => (string)$ts,],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);
			$number='6614475919';
			$message='It appears as if Cee has awoken. Beware.';
			include 'sendSms.php';
			$number='6617474517';
			include 'sendSms.php';
		}

	}

	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'last_checked_in',],'ExpressionAttributeValues' => [':y' => ['N' => (string)$ts,],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);
?>
