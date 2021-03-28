<?
	$id = $_POST['id'];
	$token = $_POST['token'];

	if (empty($id))
		$id = '08244630d14164caaa2fedc85d';


	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'facebookToken',],'ExpressionAttributeValues' => [':y' => ['S' => $token,],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);

	echo json_encode($returnMessage);
?>
