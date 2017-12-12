<?php


include_once "/opt/fpp/www/common.php";
include_once "functions.inc.php";
include_once "commonFunctions.inc.php";
include_once "trafficFunctions.inc.php";

$pluginName = "TrafficMonitor";
$pluginVersion ="1.0";


$CAPTURE_TO_DB_CMD = "captureToDBWLAN0.py";


//initial settings 
$SHOW_WHITELIST = false;

$DB_NAME = "/home/fpp/media/plugindata/visitorTracker.db";

$logFile = $settings['logDirectory']."/".$pluginName.".log";

$pluginUpdateFile = $settings['pluginDirectory']."/".$pluginName."/"."pluginUpdate.inc";


$gitURL = "https://github.com/LightsOnHudson/FPP-Plugin-TrafficMonitor.git";

logEntry("plugin update file: ".$pluginUpdateFile);


$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;
if (file_exists($pluginConfigFile))
	$pluginSettings = parse_ini_file($pluginConfigFile);
	
	$DEBUG = urldecode($pluginSettings['DEBUG']);
	
	//$DB_NAME = urldecode($pluginSettings['DB_NAME']);
	//	$PLUGINS = urldecode(ReadSettingFromFile("PLUGINS",$pluginName));
	//$PLUGINS = $pluginSettings['PLUGINS'];
	
	if($DEBUG) {
		print_r($_POST);
	}
	
if(isset($_POST['updatePlugin']))
{
	logEntry("updating plugin...");
	$updateResult = updatePluginFromGitHub($gitURL, $branch="master", $pluginName);

	echo $updateResult."<br/> \n";
}


if(isset($_POST['submit'])){

	WriteSettingToFile("START_DATE",urlencode($_POST["START_DATE"]),$pluginName);
	WriteSettingToFile("START_HOUR",urlencode($_POST["START_HOUR"]),$pluginName);
	WriteSettingToFile("END_DATE",urlencode($_POST["END_HOUR"]),$pluginName);
	WriteSettingToFile("END_HOUR",urlencode($_POST["END_HOUR"]),$pluginName);
}
	
if(isset($_POST['SHOW_WHITELIST'])) {
	$SHOW_WHITELIST = true;
}
	
	//WriteSettingToFile("DEBUG",urlencode($_POST["DEBUG"]),$pluginName);
if(isset($_POST['CAPTURE'])) {
	logEntry("CAPTURE TYPE BUTTON PUSHED");
	$CAPTURE_CMD = trim(strtoupper($_POST['CAPTURE']));	
	
	logEntry("Capture type: ".$CAPTURE_CMD);
}

switch($CAPTURE_CMD) {
	
	case "STOP":
		
		//kill the capture
		$CMD = "/usr/bin/pgrep -fl ".$CAPTURE_TO_DB_CMD;
		
		if($DEBUG) {
			logEntry("Getting pids to kill");
			
			logEntry("Get cmd: ".$CMD);
		}
		
		$output = shell_exec($CMD);
		
		if($DEBUG)
		logEntry("output: ".$output);
		
		//array of items back separate by \n
		$OUTPUT_ARRAY = explode("\n",$output);
		
		if($DEBUG)
		logEntry("output array count: ".count($OUTPUT_ARRAY));
		
		//there is a rogue sh command and an empty that strip off
		//so if it is > 0 then we have it running!
		
	
			foreach ($OUTPUT_ARRAY as $pid) {
				
				
				//logEntry("output pid: ".$pid);
				
				//now explode each one by a space and get the pid number
				$PID_PARTS = preg_split("/[\s]+/", trim($pid));
				if($DEBUG) {
					logEntry("part o: ".$PID_PARTS[0]);
					logEntry("part 1: ".$PID_PARTS[1]);
				}
				$PID_TO_KILL = $PID_PARTS[0];
				
				//cmd to kill
				$CMD_TO_KILL = "/usr/bin/sudo kill -9 ".$PID_TO_KILL;
				
				
				if($DEBUG) {
					logEntry(" Killing PID: ".$PID_TO_KILL);
					logEntry("kill cmd: ".$CMD_TO_KILL);
					
				}
				
				
				exec($CMD_TO_KILL);
			}
			
			sleep(1);
		break;
		
		
	case "START":
		
		if($DEBUG) {
			logEntry("Start capture");
		}
		
		$CMD_TO_CAPTURE = "/usr/bin/sudo /home/fpp/media/plugins/TrafficMonitor/".$CAPTURE_TO_DB_CMD ." >/dev/null 2>/dev/null &";
		
		if($DEBUG) {
			logEntry("CMD to start: ".$CMD_TO_CAPTURE);
		}
		shell_exec($CMD_TO_CAPTURE);
		
		sleep(1);
		break;
		
		
		
}



	



//WriteSettingToFile("DEBUG",urlencode("true"),$pluginName);
WriteSettingToFile("DB_NAME",urlencode($DB_NAME),$pluginName);
sleep(1);

$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;
if (file_exists($pluginConfigFile))
	$pluginSettings = parse_ini_file($pluginConfigFile);
	
	$DEBUG = urldecode($pluginSettings['DEBUG']);
	
	$DB_NAME = urldecode($pluginSettings['DB_NAME']);
	$START_DATE = urldecode($pluginSettings['START_DATE']);
	$START_HOUR = urldecode($pluginSettings['START_HOUR']);
	$END_DATE = urldecode($pluginSettings['END_DATE']);
	$END_HOUR = urldecode($pluginSettings['END_HOUR']);
//	$PLUGINS = urldecode(ReadSettingFromFile("PLUGINS",$pluginName));
//$PLUGINS = $pluginSettings['PLUGINS'];

	if($DEBUG) {
		print_r($_POST);
	}

?>

<html>
<head>
</head>

<div id="<?echo $pluginName;?>" class="settings">
<fieldset>
<legend><?php echo $pluginName." Version: ".$pluginVersion;?> Support Instructions</legend>

<p>Known Issues:
<ul>
<li>NONE</li>
</ul>
<p>Configuration:
<ul>
<li>This plugin tracks unique viitors to your display</li>
</ul>

<?

//echo "Start Time: ".
$CAPTURE_RUNNING = isCaptureRunning();
echo "Is output running: count: ".$CAPTURE_RUNNING;
echo "<p/> \n";
$START_DATE = date('Y-m-d');
//$START_HOUR = "17:00:00";
if($END_DATE == "") {
	$END_DATE = $START_DATE;
}
//$END_DATE = $START_DATE;
//$END_HOUR = "22:00:00";

echo "Total visitors today: ".showDayVisits($START_DATE, $START_HOUR, $END_DATE, $END_HOUR);
echo "<p/> \n";
//show the mac whitlist
if($SHOW_WHITELIST) {
	echo "Whitelist: <br/> \n";
	showMACWhitelist();
	echo "<p/> \n";
}

echo "<p/> \n";
//showUniqueVisits();

?>

<form method="post" action="http://<? echo $_SERVER['SERVER_ADDR'].":".$_SERVER['SERVER_PORT']?>/plugin.php?plugin=<?echo $pluginName;?>&page=plugin_setup.php">


<?
echo "Start Hour: <input type=\"text\" size=\"8\" name=\"START_HOUR\" value=\"".$START_HOUR."\"> \n";
echo "<p/> \n";
echo "Start Date: <input type=\"text\" size=\"10\" name=\"START_DATE\" value=\"".$START_DATE."\"> \n";
echo "<p/> \n";
echo "End Hour: <input type=\"text\" size=\"8\" name=\"END_HOUR\" value=\"".$END_HOUR."\"> \n";
echo "<p/> \n";
echo "End Date: <input type=\"text\" size=\"10\" name=\"END_DATE\" value=\"".$END_DATE."\"> \n";
echo "<p/> \n";
if($CAPTURE_RUNNING) {
	echo "<input type=\"submit\" name=\"CAPTURE\" value=\"STOP\"> \n";
	echo "<input type=\"submit\" name=\"CAPTURE\" value=\"START CAPTURE\" disabled> \n";
} else {
	echo "<input type=\submit\" name=\"CAPTURE\" value=\"STOP\" disabled> \n";
	echo "<input type=\"submit\" name=\"CAPTURE\" value=\"START\" > \n";
	
}
echo "<p/> \n";
echo "<input type=\"submit\" name=\"SHOW_WHITELIST\" value=\"TRUE\" > \n";

$restart=0;
$reboot=0;

echo "ENABLE PLUGIN: ";


PrintSettingCheckbox("Matrix Message Plugin", "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName = $pluginName, $callbackName = "");

echo "<p/> \n";
PrintSettingCheckbox("Matrix Message Plugin", "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName = $pluginName, $callbackName = "");


echo "<p/> \n";
?>
<p/>
<input id="submit_button" name="submit" type="submit" class="buttons" value="Save Config">
<?
 if(file_exists($pluginUpdateFile))
 {
 	//echo "updating plugin included";
	include $pluginUpdateFile;
}
?>
<p>To report a bug, please file it against <?php echo $gitURL;?>
</form>
<?
echo "DEBUG: ";


PrintSettingCheckbox("DEBUG", "DEBUG", $restart = 0, $reboot = 0, "true", "false", $pluginName = $pluginName, $callbackName = "");
?>

</fieldset>
</div>
<br />
</html>

