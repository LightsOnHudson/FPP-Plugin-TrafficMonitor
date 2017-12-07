<?php
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
			
			
			
			$uniqueVisitQuery = "SELECT FirstSeen, LastSeen,MAC, Pings,
		Cast (( JulianDay(LastSeen) - JulianDay(FirstSeen)) * 24 * 60 As Integer) + 1 AS Mins, 1.0 * Pings / (Cast (( JulianDay(LastSeen) - JulianDay(FirstSeen)) * 24 * 60 As Integer) + 1) AS PingsPerMin
	FROM Visit
	WHERE Cast (( JulianDay(LastSeen) - JulianDay(FirstSeen)) * 24 * 60 As Integer) >= 1
	ORDER BY LastSeen;";
			
			if($DEBUG) {
				logEntry("Unique Visit Query: ".$uniqueVisitQuery);
			}
			$uniqueVisitResult = $db->query($uniqueVisitQuery) or die('Query failed');
			
			while ($row = $uniqueVisitResult->fetchArray()) {
				
				
				
				echo $row['FirstSeen']."\t".$row['LastSeen']."\t".$row['MAC']."\t".$row['Pings']."\t".$row['Mins']."\t".$row['PingsPerMin'];
				
				echo "<br/> \n";
				
			}
		}
					
?>