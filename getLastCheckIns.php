#!/usr/bin/php
<?
	use Aws\DynamoDb\Marshaler;

	$marshaler = new Marshaler();
	$id = '08244630d14164caaa2fedc85d';
	$limit = 25;

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

		$actions[$i] = $tempArr;

		var_dump($actions[$i]);
		$i++;
	}

	$i = 0;
	$limitedArr = array();
	$actionCount = count($actions);

	while ($i <= $limit)
	{
		$limitedArr[$i] = $actions[$actionCount];

		$actionCount--;
		$i++;
	}

	echo json_encode($limitedArr);
?>
