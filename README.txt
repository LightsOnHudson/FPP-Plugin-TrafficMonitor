
1) setup the Pi to log packets:

	sudo apt-get -y install python-scapy iw tcpdump sqlite3

2) configure SQL database

	mkdir -p /home/fpp/media/plugindata
	sqlite3 /home/fpp/media/plugindata/visitorTracker.db < setup.sql

3) test capture logging to standard out

	sudo ./testCapture.py

4) Capture to DB (leave running, I run inside a 'screen' session)

	sudo ./captureToDB.py

5) Run queries from queries.sql to try to get something useful


==============

Optional:

- Whitelist a MAC address (run SQL and then restart the capture script). There
  is a query in queries.sql to automatically do this based on certain criteria.

    INSERT INTO MACWhitelist VALUES ( "f8:ca:b8:2b:bf:fd", "Note about MAC" );

==============

NOTE: ios8 and above use MAC address randomization during probes.  The
      random MAC changes every time the phone is put to sleep.  This means
      that it is almost impossible to detect return visitors with iPhones.
      Also, if some visitors are on their phone during the show, it may
	  double count them.

NOTE: MAC Vendors can be looked up via a web browser or an API, but Apple's
      random MAC's are truly random, not just Apple-MACs.

	https://macvendors.com/
	https://macvendors.com/api
	https://api.macvendors.com/38:f2:3e:54:b9:20

