<?
	$id = "08244630d14164caaa2fedc85d";
	$t = strval(0);

	$result = $ddb->updateItem(['ExpressionAttributeNames' => ['#Y' => 'delayedPanic',],'ExpressionAttributeValues' => [':y' => ['S' => $t,],],'Key' => ['id' => ['S' => $id,],],'TableName' => 'accounts','UpdateExpression' => 'SET #Y = :y',]);
