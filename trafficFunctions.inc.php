<?php
function isCaptureRunning() {
	
	global $DB_NAME, $DEBUG, $CAPTURE_TO_DB_CMD;
	
	$CMD = "/usr/bin/pgrep -fl ".$CAPTURE_TO_DB_CMD;
	
	$output = shell_exec($CMD);
	
	//array of items back separate by \n
	$OUTPUT_ARRAY = explode("\n",$output);
	
	//there is a rogue sh command and an empty that strip off
	//so if it is > 0 then we have it running!
	
	$OUTPUT_COUNT = count($OUTPUT_ARRAY)-2;
	
	if($DEBUG)
		echo "OUTPUT COUNT: ".$OUTPUT_COUNT. " ";
	
	if($DEBUG) {
		foreach ($OUTPUT_ARRAY as $pid) {
			logEntry("output pid: ".$pid);
			
		}
	}
	
	echo "CAPTURE IS: ";
	if($OUTPUT_COUNT > 0) {
		echo "RUNNING \n";
		return true;
	} else {
		echo "NOT RUNNING \n";
		return false;
	}
	echo "<br/> \n";
	
	return false;
	
}
function showMACWhitelist() {

global $DB_NAME, $DEBUG, $pluginName;
	$db = new SQLite3($DB_NAME) or die("Unable to open ".$pluginName." database");
				
				
				
				$MACWhitelistQuery = "SELECT * FROM MACWhitelist";
				
				if($DEBUG) {
					logEntry("MAC Whitelist Query: ".$MACWhitelistQuery);
				}
				$MACWhitelistResult = $db->query($MACWhitelistQuery) or die('Query failed, please try again');
				
				if($MACWhitelistResult->num_rows == 0) {
					echo "No MACS in whitelist \n";
					return;
				}
				while ($row = $MACWhitelistResult->fetchArray()) {
					
					
					
					echo $row['MAC']." ".$row['Note'];
					echo "<br/> \n";
					
					}
		}
	
function showUniqueVisits() {
			
			global $DB_NAME, $DEBUG, $pluginName;
			$db = new SQLite3($DB_NAME) or die("Unable to open ".$pluginName." database");
			
			$UNIQUE_COUNT = 0;
			
			$uniqueVisitQuery = "SELECT FirstSeen, LastSeen,MAC, Pings,
		Cast (( JulianDay(LastSeen) - JulianDay(FirstSeen)) * 24 * 60 As Integer) + 1 AS Mins, 1.0 * Pings / (Cast (( JulianDay(LastSeen) - JulianDay(FirstSeen)) * 24 * 60 As Integer) + 1) AS PingsPerMin
	FROM Visit
	WHERE Cast (( JulianDay(LastSeen) - JulianDay(FirstSeen)) * 24 * 60 As Integer) >= 1
	ORDER BY LastSeen;";
			
			if($DEBUG) {
				logEntry("Unique Visit Query: ".$uniqueVisitQuery);
			}
			$uniqueVisitResult = $db->query($uniqueVisitQuery) or die('Query failed please try again');
			
			echo "<table border=\"1\"> \n";
			
			echo "<tr> \n";
			echo "<td> \n";
			echo "First seen \n";
			echo "</td> \n";
			echo "<td> \n";
			echo "Last Seen \n";
			echo "</td> \n";
			echo "<td> \n";
			echo "MAC Address \n";
			echo "</td> \n";
			echo "<td> \n";
			echo "PINGS \n";
			echo "</td> \n";
			echo "<td> \n";
			echo "MINS \n";
			echo "</td> \n";
			echo "<td> \n";
			echo "Pings / Min \n";
			echo "</td> \n";
			echo "</tr> \n";
			while ($row = $uniqueVisitResult->fetchArray()) {
				echo "<tr> \n";
				
				
				echo "<td> \n";
				echo $row['FirstSeen'];
				echo "</td> \n";
				echo "<td> \n";
				echo $row['LastSeen'];
				echo "</td> \n";
				echo "<td> \n";
				echo $row['MAC'];
				echo "</td> \n";
				echo "<td> \n";
				echo $row['Pings'];
				echo "</td> \n";
				echo "<td> \n";
				echo $row['Mins'];
				echo "</td> \n";
				echo "<td> \n";
				echo $row['PingsPerMin'];
				echo "</td> \n";
				echo "</tr> \n";
				$UNIQUE_COUNT++;
				
				
			}
			echo "</table> \n";
			
			echo "<p/> \n";
			echo "<b> Total count: ".($UNIQUE_COUNT - 1);
			echo "</b> \n";
		}
			
function showDayVisits($START_DATE, $START_HOUR, $END_DATE, $END_HOUR) {
			
			global $DB_NAME, $DEBUG, $pluginName;
			$db = new SQLite3($DB_NAME) or die("Unable to open ".$pluginName." database");
			
			$UNIQUE_COUNT = 0;
			
			$uniqueVisitQuery = "SELECT DISTINCT(MAC) FROM Visit WHERE LastSeen >= '".$START_DATE." ".$START_HOUR."' AND LastSeen <= '".$END_DATE." ".$END_HOUR."' AND Cast (( JulianDay(LastSeen) - JulianDay(FirstSeen)) * 24 * 60 As Integer) >= 1;";
			
			if($DEBUG) {
				logEntry("Unique Visit Query: ".$uniqueVisitQuery);
			}
			$uniqueVisitResult = $db->query($uniqueVisitQuery) or die('Query failed please try again');
			
			
			
			while ($row = $uniqueVisitResult->fetchArray()) {
				$UNIQUE_COUNT++;
			}
			//$TOTAL_COUNT = count($row);
			
			return $UNIQUE_COUNT;
			
}
?>