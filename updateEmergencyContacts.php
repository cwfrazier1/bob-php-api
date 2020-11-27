<?
	$id = $_POST['userId'];

	if (empty($id))
		$id = '08244630d14164caaa2fedc85d';

	$user = '';
	$returnMessage = array();
	$emergencyContacts = array();
	$names = $_POST['names'];
	$numbers = $_POST['numbers'];

	$i = 0;

	while ($i < count($numbers))
	{
		$emergencyContacts[$i]['name'] = trim($names[$i]);
		$emergencyContacts[$i]['number'] = trim($numbers[$i]);
		$i++;
	}

	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'emergencyContacts',],'ExpressionAttributeValues' => [':y' => ['S' => json_encode($emergencyContacts),],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);

	echo json_encode($returnMessage);
?>
