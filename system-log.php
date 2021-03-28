#!/usr/bin/php
<?
	use Aws\DynamoDb\Marshaler;

	$marshaler = new Marshaler();
	$id = '08244630d14164caaa2fedc85d';

	while (true)
	{
		$ts = (string)strtotime('-5 minutes');

		$iterator = $ddb->getIterator('Query', array('TableName' => 'system-log','KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'),'ts' => array('AttributeValueList' => array(array('N' => $ts)), 'ComparisonOperator' => 'GT'))));

		foreach ($iterator as $item) 
		{
			$date=date('m/d/y h:i a', $item['ts']['N']);
			$server=unserialize($item['SERVER']['S']);
			$post=unserialize($item['POST']['S']);
			$get=unserialize($item['GET']['S']);
			$fileName = $server['SCRIPT_NAME'];
			
			echo "$date\nFile: $fileName\nGET: ";
			var_dump($get);
			echo "\nPOST: ";
			var_dump($post);
		}

		sleep(10);
	}
?>
