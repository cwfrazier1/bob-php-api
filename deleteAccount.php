<?
	$id = $_REQUEST['id'];

	$params = ['TableName' => 'accounts','Key' => ['id' => $id]];

	try 
	{
		$result = $ddb->deleteItem(['TableName' => 'accounts','Key' => ['id' => ['S' => $id]]]);
		echo "Deleted item.\n";
	} 
	catch (DynamoDbException $e) 
	{
		echo "Unable to delete item:\n";
		echo $e->getMessage() . "\n";
	}
?>
