<?php
require_once ("settings.php");
	$conn = @mysqli_connect($host,
	$user,
	$pwd,
	$db
) or die("<p>The application failed to connect to the database server</p>");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$max_samples = 600; // this is the maximum number of rows to send to the client, this is also the max number of pixels wide the graph can be

// this cuts down the mass of retrieved data, down to the maximum visible in the graph which is the same as the number of pixels wide
function shrinkData ($data,$shrinkFactor) {
	$no_samples = sizeof($data);
	$shrunkArray = array();

	$maxsamples = floor($no_samples / $shrinkFactor);

	for($i=0; $i < $maxsamples; $i++) {
			$shrunkArray[] = $data[$i*$shrinkFactor];
	}
	return $shrunkArray;
}

function getDay ($conn,$nodeSensorId,$max_samples) { // for some reason I can only get 1733 entries from the database, is this a limitation of the db setup or is it php's fault
	$dataArray = array();
	if ( $dataQuery = mysqli_query($conn, "SELECT UNIX_TIMESTAMP( NodeTime ) AS  `nodeTime` , nodeSensorValue
											FROM  `sensorData`
											WHERE nodeSensorId = $nodeSensorId
											ORDER BY  `sensorData`.`nodeTime` ASC
											") ) // to limit use LIMIT 0 , 3000
		{
		while ($sample = mysqli_fetch_row($dataQuery) ) {
				$dataArray[] = $sample;
		};
		$no_samples = sizeof($dataArray);

		if ($max_samples < $no_samples) { // see if the max number of allowed rows is lower than that actually retrieved
			$no_samples_to_skip = round($no_samples / $max_samples); // rounds down
			echo json_encode(shrinkData($dataArray, $no_samples_to_skip));
			//echo "array size was: ".sizeof($dataArray);
		} else {
			echo json_encode($dataArray);
		}
	} else {
		echo "query failed";
	}
	/* free result set */
$dataQuery->free();

/* close connection */
$conn->close();
}

// this function gets the last sensor value for a given sensorId
function getLastValue($conn,$nodeSensorId) {
	$dataArray = array();
	if ( $dataQuery = mysqli_query($conn, "SELECT nodeSensorValue
											FROM  `sensorData`
											WHERE nodeSensorId = $nodeSensorId
											ORDER BY  `sensorData`.`nodeTime` DESC
											LIMIT 1
											") )															{
		while ($sample = mysqli_fetch_row($dataQuery) ) {
				$dataArray[] = $sample;
		};
		echo $dataArray[0][0];

	} else {
		echo "query failed";
	}
	/* free result set */
	$dataQuery->free();

	/* close connection */
	$conn->close();
}

if ( isset ( $_POST["dataRequest_Type"]) ) {
    if ($_POST["dataRequest_Type"] == "getSensor" && isset($_POST["nodeSensorId"]) ) { // get a days (24 hours) worth of data
      getDay($conn,$_POST["nodeSensorId"],$max_samples);
    } else if ($_POST["dataRequest_Type"] == "getLastSensor" && isset($_POST["nodeSensorId"])) {
			getLastValue($conn,$_POST["nodeSensorId"],$max_samples);
		} else {
			echo "bad request type";
		}
} else {
	echo "bad post details\n";
	var_dump($_POST);
}
?>
