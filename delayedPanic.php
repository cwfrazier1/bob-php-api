<?
	$id = $_GET['id'];

	$t = strval(time());

	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'delayedPanic',],'ExpressionAttributeValues' => [':y' => ['S' => $t,],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);

	$iterator = $ddb->getIterator('Query',array('TableName' => 'accounts','KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'))));

	foreach ($iterator as $item)
	{
		$delayedPanic = $item['delayedPanic']['S'];
	}

	if ($delayedPanic == $t)
	{
		echo 'send alert';
	}
	else
	{
		echo 'user cancelled';
	}
