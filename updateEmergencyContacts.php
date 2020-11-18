<?
	$id = $_POST['userId'];

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
	$names =$_POST['cpntactNames'];
	$numbers = $_POST['contactNumbers'];

	if (empty($names))
		$names = array('Chester');

	if (empty($numbers))
		$numbers = array('6613685684');

	if (!empty($names) || !empty($numbers))
	{
		if (count($names) == count($numbers))
		{
			$numberCount = count($numbers);
			$i = 0;

			while ($i < $numberCount)
			{
				$add = true;
				$numberCheck = 0;
				$existingCount = count($emergencyContacts);
/*
				while ($numberCheck < $existingCount)
				{
					if ($emergencyContacts[$i]['number'] == $numbers[$i])
					{
						$add = false;
					}

					$numberCheck++;
				}
 */
				//if ($add)
				//{
					$monitorId = uniqueId();
					$contact = array('id' => $monitorId, 'name' => $names[$i], 'number' => $numbers[$i], 'verified' => 0);
					$emergencyContacts[] = $contact;
					sendSms($numbers[$i], "$user would like to add you as an emergency contact. Please click the following link to confirm: https://api.checkonmine.com?id=$id&m=$monitorId");
				//}	
				$i++;
			}
			$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'emergencyContacts',],'ExpressionAttributeValues' => [':y' => ['S' => json_encode($emergencyContacts),],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);
		}
		else
		{
//			$returnMessage['error'] = 'Total number of names and total number of phone numbers do not match.';
		}
	}
	else
	{
//		$returnMessage['error'] = 'Names and/or numbers are empty.';
	}

	echo json_encode($returnMessage);
?>
