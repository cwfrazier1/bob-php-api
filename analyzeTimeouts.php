#!/usr/bin/php
<?
	$ts=array();

	$timeFrame='';

	$timeout=(60*60)*2;

	if (empty($argv[1]))
		$timeFrame=(string)strtotime('-10 years');
	else
		$timeFrame=(string)strtotime('-'.$argv[1].' days');

	$id='';

	use Aws\DynamoDb\Marshaler;

	$marshaler = new Marshaler();
	$params = ['TableName' => 'accounts'];
	try 
	{
        		$result = $ddb->scan($params);

			foreach ($result['Items'] as $i) 
			{
				$account = $marshaler->unmarshalItem($i);
				$id = (string)$account['id'];

				$iterator = $ddb->getIterator('Query', array('TableName' => 'actions','KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'),'ts' => array('AttributeValueList' => array(array('N' => $timeFrame)), 'ComparisonOperator' => 'GT'))));

				foreach ($iterator as $item) 
				{
					$dts=$item['ts']['N'];
					$ts[]=$dts;
					
				}

			}
	}
 	catch (DynamoDbException $e) 
	{
		echo "Unable to scan:\n";
		echo $e->getMessage() . "\n";
	}

	rsort($ts);
	$c=count($ts);
	$max=0;
	$i=$c;
	$checkins=array();

	while ($i != 1)
	{
		if (!empty($ts[$i-1]) && !empty($ts[$i]))
		{
			$delay = $ts[$i-1] - $ts[$i];
			echo "$delay\n";
			if ($delay > $max)
			{
				if ($delay < 9999990)
				{
					$max = $delay;
				}
			}

			$checkins[$i]['end']=$ts[$i-1];
			$checkins[$i]['begin']=$ts[$i];
			$checkins[$i]['delay']=$delay;
		}
		$i--;
	}

	//usort($checkins,'sortByTime');

	$i=0;
	$average=0;
	$sleepAverage=0;
	$sleepCount=0;
	$beginArr=array();
	$endArr=array();
	$csv='';

	foreach ($checkins as $c)
	{
		if ($c['delay'] > $timeout)
		{
			echo convertSeconds($c['delay'])." ".date('l, m/d/y h:i a', $c['begin']).' '.date('l, m/d/y h:i a', $c['end'])."\n";
			
			$beginArr[]=date('H:i:s', $c['begin']);
			$endArr[]=date('H:i:s', $c['end']);
			
			$sleepAverage=$sleepAverage+$c['delay'];
			$sleepCount++;
		}

		$average=$average+$c['delay'];
		$i++;
	}

	$sleepAverage=round($sleepAverage/$sleepCount);
	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'sleepAverage',],'ExpressionAttributeValues' => [':y' => ['S' => (string)$sleepAverage,],],'Key' => ['id' => ['S' => '08244630d14164caaa2fedc85d',],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);
?>
