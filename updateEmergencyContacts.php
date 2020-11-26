<?
	$id = $_POST['userId'];

	if (empty($id))
		$id = '08244630d14164caaa2fedc85d';

	$user = '';
	$returnMessage = array();
	$emergencyContacts = array();

	$emergencyContacts = $_POST['emergencyContacts'];

	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'emergencyContacts',],'ExpressionAttributeValues' => [':y' => ['S' => json_encode($emergencyContacts),],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);

	echo json_encode($returnMessage);
?>
