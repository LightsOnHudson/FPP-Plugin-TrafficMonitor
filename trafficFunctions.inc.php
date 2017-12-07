<?php

function isCaptureRunning() {
	
	global $DB_NAME, $DEBUG, $CAPTURE_TO_DB_CMD;
	
	$CMD = "/usr/bin/pgrep -fl ".$CAPTURE_TO_DB_CMD;
	
	$output = shell_exec($CMD);
	
	//array of items back separate by \n
	$OUTPUT_ARRAY = explode("\n",$output);
	
	$OUTPUT_COUNT = count($OUTPUT_ARRAY);
	
	echo "OUTPUT COUNT: ".$OUTPUT_COUNT;
	
	
}
function showMACWhitelist() {

global $DB_NAME, $DEBUG;
	$db = new SQLite3($DB_NAME) or die("Unable to open ".$pluginName." database");
				
				
				
				$MACWhitelistQuery = "SELECT * FROM MACWhitelist";
				
				if($DEBUG) {
					logEntry("MAC Whitelist Query: ".$MACWhitelistQuery);
				}
				$MACWhitelistResult = $db->query($MACWhitelistQuery) or die('Query failed');
				
				while ($row = $MACWhitelistResult->fetchArray()) {
					
					
					
					echo $row['MAC']." ".$row['Note'];
					echo "<br/> \n";
					
					}
		}
	
		function showUniqueVisits() {
			
			global $DB_NAME, $DEBUG;
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
			$uniqueVisitResult = $db->query($uniqueVisitQuery) or die('Query failed');
			
			echo "<table border=\"1\"> \n";
			echo "<tr> \n";
			
			while ($row = $uniqueVisitResult->fetchArray()) {
				
				
				
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
					
?>