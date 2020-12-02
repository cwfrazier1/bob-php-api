<?
	$id = '';//$_REQUEST['userId'];

	if (empty($id))
		$id = '08244630d14164caaa2fedc85d';

	$user = '';
	$returnMessage = array();
	$emergencyContacts = array();

	$iterator = $ddb->getIterator('Query', array('TableName' => 'accounts', 'ConsistentRead' => true, 'KeyConditions' => array('id' => array('AttributeValueList' => array(array('S' => $id)),'ComparisonOperator' => 'EQ'))));

	foreach ($iterator as $item) 
	{
		$firstName = $item['firstName']['S'];
		$emergencyContacts = json_decode($item['emergencyContacts']['S']);
		$address = $item['address']['S'];
		$addressLineTwo = $item['addressLineTwo']['S'];
		$city = $item['city']['S'];
		$zipCode = $item['zipCode']['S'];
		$state = $item['state']['S'];
		$emergencyNotes = json_decode($item['emergencyNotes']['S']);
		$lastKnownLocation = json_decode($item['lastKnownLocation']['S']);
	}

	$i = 0;

	$emergencyContactsString = '';

	while ($i < count($emergencyContacts))
	{
		$contact = $emergencyContacts[$i];
		$emergencyContactsString .= $contact->name.': '.$contact->number."\n";
		
		$i++;
	}

	$lastKnownLocationAddress = $lastKnownLocation->address;
	$lastKnownLocationLongitude = $lastKnownLocation->longitude;
	$lastKnownLocationLattitude = $lastKnownLocation->latitude;

	$currentMedicalInsurance = $emergencyNotes->currentMedicalInsurance;
	$specialNotes = $emergencyNotes->specialNotes;

	$textMessage = "We have received an emergency alert from $firstName. Their details are as follows. Could you please check on them?\n\n
		Current Residence: $address $addressLineTwo, $city, $state $zipCode\n
		Last Known Location: $lastKnownLocationAddress (https://maps.google.com/?q=$lastKnownLocationLattitude,$lastKnownLocationLongitude";

	if (!empty($currentMedicalInsurance))
		$textMessage .= "Current Medical Insurance: $currentMedicalInsurance\n";

	if (!empty($specialNotes))
		$textMessage .= "Special Notes: $specialNotes\n";

	$textMessage .= $textMessage."Emergency Contacts:\n$emergencyContactsString";

	echo $textMessage;
?>
