<?
	$id = $_POST['userId'];

	if (empty($id))
		$id = '08244630d14164caaa2fedc85d';

	$user = '';
	$returnMessage = array();
	$notes = array();
	$currentMedicalInsurance = $_POST['currentMedicalInsurance'];
	$specialNotes = $_POST['specialNotes'];

	$notes['currentMedicalInsurance'] = $currentMedicalInsurance;
	$notes['specialNotes'] = $specialNotes;

	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'emergencyNotes',],'ExpressionAttributeValues' => [':y' => ['S' => json_encode($notes),],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);

	echo json_encode($returnMessage);
?>
