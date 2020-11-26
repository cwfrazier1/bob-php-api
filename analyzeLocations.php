<?
function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);

    foreach ($array as $ii => $va) {
        $sorter[]=$va[$key];
    }

    arsort($sorter);

    foreach ($sorter as $ii => $va) {
        $ret[]=$array[$ii];
    }

    return $array=$ret;
}

$params = ['TableName' => 'actions'];
$locations = array();

try {
    while (true) {
        $result = $ddb->scan($params);

	foreach ($result['Items'] as $i) 
	{
		$actionData = $marshaler->unmarshalItem($i);

		$metric = $actionData['metric'];
		$value = $actionData['value'];
		$ts = $actionData['ts'];

		if ($metric == 'Location')
		{
			$addressDetails = $value;
			$address = $value['address'];

			$locationCount = count($locations);
			$i = 0;
			$found = false;

			while ($i < $locationCount)
			{
				if ($locations[$i]['address'] == $address)
				{
					$locations[$i]['count']++;

					if ($locations[$i]['ts'] < $ts)
						$locations['ts'] = $ts;

					$found = true;
				}

				$i++;
			}

			if (!$found)
			{
				$locations[] = array('address' => $address, 'count' => 1, 'ts' => $ts);
			}
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

usort($locations, function($a, $b) {
    return $a['count'] <=> $b['count'];
});

var_dump($locations);
?>
