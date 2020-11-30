<?
	$id = $_POST['userId'];

	if (empty($id))
		$id = '08244630d14164caaa2fedc85d';

	$user = '';
	$returnMessage = array();

	$iterator = $ddb->getIterator('Query', array('TableName' => 'accounts', 'ConsistentRead' => true, 'KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'))));

	foreach ($iterator as $item) 
	{
		$notes = $item['emergencyNotes']['S'];
	}

	echo $notes;
?>
