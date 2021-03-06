<?
	use Aws\DynamoDb\Marshaler;

	$marshaler = new Marshaler();
	$id = $_REQUEST['id'];
	$limit = 100;

	$ts = (string)strtotime('-1 hour');

	$iterator = $ddb->getIterator('Query', array('TableName' => 'actions','KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'),'ts' => array('AttributeValueList' => array(array('N' => $ts)), 'ComparisonOperator' => 'GT'))));

	$actions = array();
	$i = 0;

	foreach ($iterator as $item) 
	{
		$tempArr = array();
		$tempArr['date'] = date('m/d/y h:i a', $item['ts']['N']);
		$tempArr['metric'] = $item['metric']['S'];
		$tempArr['value'] = $item['value']['S'];

		if ($tempArr['metric'] == 'Location')
		{
			$addressInfo = $item['value'];
			$address = $addressInfo['M']['address'];
			//var_dump($address);
			$tempArr['value'] = $address['S'];
		}

		$actions[$i] = $tempArr;

		$i++;
	}

	$i = 0;
	$limitedArr = array();
	$actionCount = count($actions);

	if ($limit > $actionCount)
		$limit = $actionCount;

	while ($i < $limit)
	{
		$limitedArr[$i] = $actions[$actionCount];

		$actionCount--;
		$i++;
	}

	unset($limitedArr[0]);
	echo json_encode($limitedArr);
?>
