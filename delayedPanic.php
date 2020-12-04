<?
	$id = "08244630d14164caaa2fedc85d";
	$t = strval(time());

	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'delayedPanic',],'ExpressionAttributeValues' => [':y' => ['S' => $t,],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);

	ignore_user_abort(true);
	set_time_limit(0);

	ob_start();
	header('Connection: close');
	header('Content-Length: '.ob_get_length());
	ob_end_flush();
	ob_flush();
	flush();
	
	sleep(300);
	
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
