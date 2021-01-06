#!/usr/bin/php
<?
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
				$ts=array();

				$account = $marshaler->unmarshalItem($i);
				
				$id = (string)$account['id'];
				$phoneNumber = (string)$account['phoneNumber'];
				$iOSToken = (string)$account['iOSToken'];
				$firstName = (string)$account['firstName'];
				$lastCheckInAlert = (int)$account['lastCheckInAlert'];

				echo "--------------------------------------------------------------------------\n";

				echo $firstName;

				$iterator = $ddb->getIterator('Query', array('TableName' => 'actions','KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'),'ts' => array('AttributeValueList' => array(array('N' => $timeFrame)), 'ComparisonOperator' => 'GT'))));

				$i=0;

				foreach ($iterator as $item) 
				{
					$dts=$item['ts']['N'];
					$ts[$i]=(int)$dts;
					$i++;
				}

				$numOfCheckIn=count($ts);

				sort($ts);

				$lastCheckin = $ts[$numOfCheckIn-1];

				$now = time() - $lastCheckin;

				if ($now > 86400 && (empty($lastCheckInAlert) || (time() - $lastCheckInAlert > 86400)))
				{
					echo "\n+++SEND ALERT+++\n";
					$id = '08244630d14164caaa2fedc85d'; //FOR TESTING
					$message = "You have not checked in since ".date('m/d/y h:i a', $lastCheckin).". Please open the Check on Mine app to manually check in. For automatic check in options, please go to https://checkonmine.com/checkin-options";

					sendSms($phoneNumber, $message, $id);	
				}

				echo "\nFirst check in: ".date('m/d/y h:i a', $ts[0]);
				echo "\nLast check  in: ".date('m/d/y h:i a', $ts[$numOfCheckIn-1])."\n\n";

				echo "--------------------------------------------------------------------------\n";

			}
	}
 	catch (DynamoDbException $e) 
	{
		echo "Unable to scan:\n";
		echo $e->getMessage() . "\n";
	}

?>
