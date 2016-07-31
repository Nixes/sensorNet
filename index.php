<?php
require_once ("settings.php");
	$conn = @mysqli_connect($host,
	$user,
	$pwd,
	$db
) or die("<p>The application failed to connect to the database server</p>");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Graph Test</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script type="text/javascript" src="http://code.jquery.com/jquery-2.2.0.min.js"></script>

	<!-- bootstrap -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <!--Load the AJAX API-->
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>

	<!-- load page specific files -->
	<script type="text/javascript" src="./js/main.js"></script>
	<link rel="stylesheet" type="text/css" href="./style/main.css" />
</head>

<body>

<div id=container>

<h1 class="header center-block">
  <span class="glyphicon glyphicon-stats" aria-hidden="true"></span>
   Sensor Hub
</h1>

<?php

function generateNodeSummary($conn,$nodeId){
	if ( $dataQuery = mysqli_query($conn, "SELECT nodeSensorName, nodeSensorId	FROM `nodeSensorList` WHERE nodeId=$nodeId") )  {
			//$sensorList = array();
			while ($sensor = mysqli_fetch_row($dataQuery) ) {
				echo '<div class="panel panel-default column-variable">
								<div class="panel-heading clearfix">'.$sensor[0].'</div>
								<div class="panel-body">';
				echo 		'<h2 class="sensor-value text-center" id='.$sensor[1].'></h2>';
				echo 	'</div>
						</div>';
		}
	}
}

function generateSummaryPage ($conn,$nodeList) {
	echo '<div id="summary-tab" class="tab-pane fade in active">
					<div class="panel tab-panel panel-default">
						<div class="panel-body">';
						foreach($nodeList as $node) {
							echo '<div class="panel panel-default">
											<div class="panel-heading">'.$node[1].'</div>
											<div class="panel-body-variable clearfix">'; //<div class="panel-body">
							generateNodeSummary($conn,$node[0]);
							echo 	'</div>
									</div>';
						}
				echo 	'</div>
					</div>
				</div>';
}

function generateChart ($conn,$nodeId) {
	if ( $dataQuery = mysqli_query($conn, "SELECT nodeSensorName, nodeSensorId	FROM `nodeSensorList` WHERE nodeId=$nodeId") )  {
			//$sensorList = array();
			while ($sensor = mysqli_fetch_row($dataQuery) ) {
					//$sensorList[] = $sensor;
					echo'<div class="panel panel-default">
									<div class="panel-heading clearfix">'.$sensor[0].'     <button type="button" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Refresh</button>
									</div>
									<div class="panel-body">
										<div class="chart" id="'.$sensor[1].'" >
											<div class="progress">
												<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100"
												aria-valuemin="0" aria-valuemax="100" style="width:100%">
													<span class="sr-only">70% Complete</span>
												</div>
											</div>
										</div>
								</div>
							</div>';
			};
	}
}

// render list of nodes
if ( $dataQuery = mysqli_query($conn, "SELECT nodeId, nodeName	FROM `nodeList`") )  {
		$nodeList = array();
		while ($sample = mysqli_fetch_row($dataQuery) ) {
				$nodeList[] = $sample;
		};

		// generate node tabs
		echo '<ul class="nav nav-tabs">';

		echo '<li class="active"><a href="#summary-tab" data-toggle="tab">Summary</a></li>';
		foreach($nodeList as $node) {
			echo '<li><a href="#'.$node[0].'-tab" data-toggle="tab">'.$node[1].' <span class="label label-success"><span class="glyphicon glyphicon-signal" aria-hidden="true"></span></span></a></li>';
		}
		echo '</ul>';

		// generate tab contents
		echo '<div class="tab-content">';
		generateSummaryPage($conn,$nodeList);
		foreach($nodeList as $node) {
			echo '<div id="'.$node[0].'-tab" class="tab-pane fade">
							<div class="panel tab-panel panel-default">
								<div class="panel-body">';
									// generate charts
									generateChart($conn,$node[0]);
			echo 			'</div>
							</div>
						</div>';
		}
		echo '</div>';

} else {
	echo "<p>NO NODE(s) FOUND</p>";
}
/* close connection */
$conn->close();
?>

</div>

<script type="text/javascript">
	document.addEventListener("load", loadSummary());

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	  loadCharts();
		loadSummary();
	})
</script>

</body>
</html>
