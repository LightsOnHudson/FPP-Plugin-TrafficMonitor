<?php
$START_DATE = $_GET['START_DATE'];
$START_HOUR = $_GET['START_HOUR'];
$END_DATE = $_GET['END_DATE'];
$END_HOUR = $_GET['END_HOURE'];
include_once "../trafficFunctions.inc.php";
include('phpgraphlib.php');
$graph = new PHPGraphLib(500, 450);
$data = array("Today" => showDayVisits($START_DATE, $START_HOUR, $END_DATE, $END_HOUR));//, "Feb" => -1200, "Mar" => -100, "Apr" => -1925,
//		"May" => -1444, "Jun" => -957, "Jul" => -364, "Aug" => -221,
//		"Sep" => -1300, "Oct" => -848, "Nov" => -719, "Dec" => -114);
$graph->addData($data);
$graph->setBarColor('255,255,204');
$graph->setTitle('Money Made at XYZ Corp');
$graph->setTextColor('gray');
$graph->createGraph();
//showDayVisits($START_DATE, $START_HOUR, $END_DATE, $END_HOUR);
?>