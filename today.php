<?php
error_reporting(0);
$DB_NAME = "/home/fpp/media/plugindata/visitorTracker.db";
$START_DATE = $_GET['START_DATE'];
$START_HOUR = $_GET['START_HOUR'];
$END_DATE = $_GET['END_DATE'];
$END_HOUR = $_GET['END_HOURE'];
include_once "trafficFunctions.inc.php";
include_once 'functions.inc.php';
include_once 'commonFunctions.inc.php';
include('phpgraphlib.php');
$VISITS = showDayVisits($START_DATE, $START_HOUR, $END_DATE, $END_HOUR);
logEntry("Visits: ".$VISITS);
$graph = new PHPGraphLib(500, 450);
$data = array("Today" => $VISITS);//, "Feb" => -1200, "Mar" => -100, "Apr" => -1925,
//		"May" => -1444, "Jun" => -957, "Jul" => -364, "Aug" => -221,
//		"Sep" => -1300, "Oct" => -848, "Nov" => -719, "Dec" => -114);
$graph->addData($data);
$graph->setBarColor('255,255,204');
$graph->setTitle('Money Made at XYZ Corp');
$graph->setTextColor('gray');
$graph->createGraph();
//showDayVisits($START_DATE, $START_HOUR, $END_DATE, $END_HOUR);
?>