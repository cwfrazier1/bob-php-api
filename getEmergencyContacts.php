<?
	$id = $_POST['userId'];

	if (empty($id))
		$id = '08244630d14164caaa2fedc85d';

	$user = '';
	$returnMessage = array();
	$emergencyContacts = array();

	$iterator = $ddb->getIterator('Query', array('TableName' => 'accounts', 'KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'))));

	foreach ($iterator as $item) 
	{
		$user = $item['firstName']['S'];
		$emergencyContacts = json_decode($item['emergencyContacts']['S'], true);
	}
echo $user;
	$names =$_POST['contactName'];
	$numbers = $_POST['contactNumber'];

	if (empty($names))
		$names = 'Chester';

	if (empty($numbers))
		$numbers = '6613685684';

	if (!empty($names) || !empty($numbers))
	{
		$add = true;
		$numberCheck = 0;
		$existingCount = count($emergencyContacts);
		while ($numberCheck < $existingCount)
		{
			if ($emergencyContacts[$numberCheck]['number'] == $numbers)
			{
				$add = false;
			}
			$numberCheck++;
		}
 
		if ($add == true)
		{
			$monitorId = uniqueId();
			$contact = array('id' => $monitorId, 'name' => $names, 'number' => $numbers, 'verified' => 0);
			$emergencyContacts[] = $contact;
			sendSms($numbers, "$user would like to add you as an emergency contact. Please click the following link to confirm: https://api.checkonmine.com/verifyEmergencyContact.php?userId=$id&mId=$monitorId");
		}	
		
		$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'emergencyContacts',],'ExpressionAttributeValues' => [':y' => ['S' => json_encode($emergencyContacts),],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);
	}
	else
	{
//		$returnMessage['error'] = 'Names and/or numbers are empty.';
	}

	echo json_encode($returnMessage);
?>
