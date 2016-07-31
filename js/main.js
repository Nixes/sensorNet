// Load the Visualization API and the piechart package.
google.load('visualization', '1.0', {'packages':['corechart']});
google.setOnLoadCallback(console.log("google charts loaded"));

function setSummaryValue(responseText,sensorValueElement) {
  sensorValueElement.innerHTML = responseText;
  console.log(responseText);
}

function getSummaryValue(sensorValueElement) {
  var sensorId = sensorValueElement.id;

  var xhttp = new XMLHttpRequest();
  xhttp.open("POST", "http://see-bry.com/dev/sensorNet/sensorQuery.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhttp.onreadystatechange = function() {
   if (xhttp.readyState == 4 && xhttp.status == 200) {
     setSummaryValue(xhttp.responseText,sensorValueElement);
   }
 };
  xhttp.send("dataRequest_Type=getLastSensor&nodeSensorId="+sensorId);
}

function loadSummary() {
  var element = document.querySelector('.tab-pane.active');
  var sensorValueElement = element.getElementsByClassName("sensor-value");
  for (var i=0; i < sensorValueElement.length;i++) {
    getSummaryValue(sensorValueElement[i]);
  }
  if (sensorValueElement.length > 0) {
    console.log("loadSummary has been scheduled: "+sensorValueElement.length);
    setTimeout(loadSummary, 30000);
  }
  console.log('finished refreshing values');
}

 function convertToDate (UNIX_Timestamp) {
   return new Date(UNIX_Timestamp * 1000);
 }

function processData (data) {
  var dataToProcess = data;
  for (var i=0; i < dataToProcess.length;i++) {
    dataToProcess[i][0] = convertToDate( parseInt( dataToProcess[i][0] ) );
    dataToProcess[i][1] = parseFloat( dataToProcess[i][1] );
  }
  console.log("Number of Rows "+dataToProcess.length);
  dataToProcess.unshift(['Time', 'Temperature °C']);
  return dataToProcess;
}

function drawChart(responseText,chart) {
  console.log("Query Response Was: "+responseText);
  var jsonData = JSON.parse(responseText);
  var processedData = processData(jsonData);
  //console.log(processedData);
  var data = google.visualization.arrayToDataTable( processedData );

  var options = {
    legend: { position: 'none' },
    animation: {
      duration:1000,
      easing:'linear'
    },
    explorer: {
       zoomDelta:1,
       axis:'horizontal',
       keepInBounds: true
   },
   chartArea: {
     top:5,right:0,
     width: '90%', height: '85%'
   },
   height: 200
  };
  var chart = new google.visualization.LineChart(chart);

  chart.draw(data, options);
}

function getChart(chart) {
  var sensorId = chart.id;

  var xhttp = new XMLHttpRequest();
  xhttp.open("POST", "http://see-bry.com/dev/sensorNet/sensorQuery.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhttp.onreadystatechange = function() {
   if (xhttp.readyState == 4 && xhttp.status == 200) {
     drawChart(xhttp.responseText,chart);
   }
 };
  xhttp.send("dataRequest_Type=getSensor&nodeSensorId="+sensorId);
}


function loadCharts() {
  var element = document.querySelector('.tab-pane.active');
  var charts = element.getElementsByClassName("chart");
  for (var i=0; i < charts.length;i++) {
    getChart(charts[i]);
  }
  console.log('finished loading charts');
}
