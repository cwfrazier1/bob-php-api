<?
	$s='6612031768';
	$t=(string)strtotime("-24 hours");

	$iterator = $ddb->getIterator('Query', array(
    'TableName'     => 'actions',
    'KeyConditions' => array(
        'phoneNumber' => array(
            'AttributeValueList' => array(
                array('S' => $s)
            ),
            'ComparisonOperator' => 'EQ'
        ),
        'ts' => array(
            'AttributeValueList' => array(
                array('N' => $t)
            ),
            'ComparisonOperator' => 'GT'
        )
    )
));

	foreach ($iterator as $item) 
	{
		$ts=$item['ts']['N'];
		$ts=date('l, m/d/y h:i s a', $ts);
		$metric = $item['metric']['S'];
		$value = json_decode($item['value'], true);

		if ($metric == 'Location')
		{
			echo "$ts: $metric\n";
			var_dump($value);
		}
	}

?>
