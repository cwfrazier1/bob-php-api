<?
	$id = $_POST['id'];

	if (empty($id))
		$id = '08244630d14164caaa2fedc85d';

	$user = '';
	$returnMessage = array();
	$emergencyContacts = array();

	$iterator = $ddb->getIterator('Query', array('TableName' => 'accounts', 'ConsistentRead' => true, 'KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'))));

	foreach ($iterator as $item) 
	{
		$user = $item['firstName']['S'];
		$emergencyContacts = $item['emergencyContacts']['S'];
	}

	echo $emergencyContacts;
?>
