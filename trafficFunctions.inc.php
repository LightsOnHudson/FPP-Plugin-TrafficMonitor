function getUniqueVisits() {

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
					
					