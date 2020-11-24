<?
	$id = $_GET['userId'];
	$mId = $_GET['mId'];

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

	$emergencyContactsCount = count($emergencyContacts);
	$i = 0;

	while ($i < $emergencyContactsCount)
	{
		if ($emergencyContacts[$i]['id'] == $mId)
		{
			$emergencyContacts[$i]['verified'] = 1;
		}

		$i++;
	}
	
	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'emergencyContacts',],'ExpressionAttributeValues' => [':y' => ['S' => json_encode($emergencyContacts),],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);
?>
Verified

