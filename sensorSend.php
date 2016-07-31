<?php
require_once ("settings.php");
	$conn = @mysqli_connect($host,
	$user,
	$pwd,
	$db
) or die("<p>The application failed to connect to the database server</p>");

error_reporting(E_ALL);
ini_set('display_errors', 1);

function removeQuotes ($inputstring) {
	$outputString = str_replace('\'','',$inputstring);
	return $outputString;
}

// todo: clean data, prevents bugs later
function insertData ($conn,$dataRaw) {
	$jsonObject = json_decode($dataRaw);
	// for length of nodeData keep adding rows
	$values = $jsonObject->{'nodeData'};// will probably be an array
	foreach ($values as $value) {
		$nodeSensorId = $value->{'nodeSensorId'};
		$nodeSensorValue = $value->{'nodeSensorValue'};
		$insertQuery = "INSERT INTO sensorData VALUES(
										'$nodeSensorId',
										'$nodeSensorValue',
										NOW()
									); ";
		$insertData = mysqli_query($conn, $insertQuery);
		var_dump($insertData);
	}
}

// check if notes table exists, if not create it
$result = mysqli_query($conn, "SELECT * FROM sensorData;");
if ($result !== FALSE) {
} else {
	echo "Table does not exist, it is being created.";
	mysqli_query($conn, "CREATE TABLE sensorData (
										nodeSensorId Varchar(20),
										nodeSensorValue FLOAT,
										nodeTime TIMESTAMP
										); " );
}

if ( isset ( $_GET["jsonData"]) and $_GET["jsonData"] != ""  ) {
	insertData($conn,$_GET["jsonData"] );
	echo "ok";
} else {
	echo "failed";
}
?>
