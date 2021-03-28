<?
	$id = $_POST['id'];

	$t = strval(time());

	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'delayedPanic',],'ExpressionAttributeValues' => [':y' => ['S' => $t,],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);

	$iterator = $ddb->getIterator('Query',array('TableName' => 'accounts','KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'))));

	foreach ($iterator as $item)
	{
		$delayedPanic = $item['delayedPanic']['S'];
	}

	if ($delayedPanic == $t)
	{
		$url = 'https://api.checkonmine.com/sendAlert.php';
		$data = array('id' => $id);
		$options = array('http' => array('header'  => "Content-type: application/x-www-form-urlencoded\r\n",'method'  => 'POST','content' => http_build_query($data)));
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		
	}
	else
	{
		echo 'user cancelled';
	}
