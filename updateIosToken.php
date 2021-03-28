<?
	$id = $_POST['userId'];
	$iOsToken = $_POST['iOSToken'];

	if (empty($id))
		$id = '08244630d14164caaa2fedc85d';


	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'iOSToken',],'ExpressionAttributeValues' => [':y' => ['S' => $iOsToken,],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);

	echo json_encode($returnMessage);
?>
