<?php


include_once "/opt/fpp/www/common.php";
include_once "functions.inc.php";
include_once "commonFunctions.inc.php";
include_once "trafficFunctions.inc.php";

$pluginName = "TrafficMonitor";
$pluginVersion ="1.0";


$CAPTURE_TO_DB_CMD = "captureToDBWLAN0.py";

$DB_NAME = "/home/fpp/media/plugindata/visitorTracker.db";

$logFile = $settings['logDirectory']."/".$pluginName.".log";

$pluginUpdateFile = $settings['pluginDirectory']."/".$pluginName."/"."pluginUpdate.inc";


$gitURL = "https://github.com/LightsOnHudson/FPP-Plugin-TrafficMonitor.git";

logEntry("plugin update file: ".$pluginUpdateFile);

if(isset($_POST['updatePlugin']))
{
	logEntry("updating plugin...");
	$updateResult = updatePluginFromGitHub($gitURL, $branch="master", $pluginName);

	echo $updateResult."<br/> \n";
}


if(isset($_POST['submit'])){

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
		logEntry("Getting pids to kill");
		
		
		$output = shell_exec($CMD);
		
		//array of items back separate by \n
		$OUTPUT_ARRAY = explode("\n",$output);
		
		//there is a rogue sh command and an empty that strip off
		//so if it is > 0 then we have it running!
		
		if($DEBUG) {
			logEntry("KILLING PIDS");
		}
			foreach ($OUTPUT_ARRAY as $pid) {
				
				
				logEntry("output pid: ".$pid);
				
				//now explode each one by a space and get the pid number
				$PID_PARTS = explode(" ",trim($pid));
				$PID_TO_KILL = $PID_PARTS[0];
				
				//cmd to kill
				$CMD_TO_KILL = "/usr/bin/pkill ".$PID_TO_KILL;
				exec($CMD_TO_KILL);
				
				if($DEBUG) {
					logEntry(" Killing PID: ".$PID_TO_KILL);
				}
				
			}
			
			sleep(1);
		break;
}



	



WriteSettingToFile("DEBUG",urlencode("true"),$pluginName);
WriteSettingToFile("DB_NAME",urlencode($DB_NAME),$pluginName);
sleep(1);

$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;
if (file_exists($pluginConfigFile))
	$pluginSettings = parse_ini_file($pluginConfigFile);
	
	$DEBUG = urldecode($pluginSettings['DEBUG']);
	
	$DB_NAME = urldecode($pluginSettings['DB_NAME']);
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
$CAPTURE_RUNNING = isCaptureRunning();
echo "Is output running: count: ".$CAPTURE_RUNNING;
echo "<p/> \n";
//show the mac whitlist
showMACWhitelist();

echo "<p/> \n";
showUniqueVisits();

?>

<form method="post" action="http://<? echo $_SERVER['SERVER_ADDR'].":".$_SERVER['SERVER_PORT']?>/plugin.php?plugin=<?echo $pluginName;?>&page=plugin_setup.php">


<?
if($CAPTURE_RUNNING) {
	echo "<input type=\"submit\" name=\"CAPTURE\" value=\"STOP\"> \n";
	echo "<input type=\"submit\" name=\"START_CAPTURE\" value=\"START CAPTURE\" disabled> \n";
} else {
	echo "<input type=\submit\" name=\"KILL_CAPTURE\" value=\"STOP CAPTURE\" disabled> \n";
	echo "<input type=\"submit\" name=\"START_CAPTURE\" value=\"START CAPTURE\" > \n";
	
}
echo "<p/> \n";


$restart=0;
$reboot=0;

echo "ENABLE PLUGIN: ";

//if($ENABLED== 1 || $ENABLED == "on") {
//		echo "<input type=\"checkbox\" checked name=\"ENABLED\"> \n";
PrintSettingCheckbox("Matrix Message Plugin", "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName = $pluginName, $callbackName = "");
//	} else {
//		echo "<input type=\"checkbox\"  name=\"ENABLED\"> \n";
//}

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



</fieldset>
</div>
<br />
</html>

