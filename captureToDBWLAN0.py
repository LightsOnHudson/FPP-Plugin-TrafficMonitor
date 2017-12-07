#!/usr/bin/env python

import sys
from datetime import datetime, timedelta
import time
import sqlite3 as lite

import logging
logging.getLogger("scapy.runtime").setLevel(logging.ERROR)

from scapy.all import *

#####################################

vtVer = "0.3"
staleSecs = 300
activeSecs = 30
idCache = {}
lastCache = {}
countCache = {}
macWhitelist = {}
capIface = 'wlan0'

conf.iface = capIface

vdb  = lite.connect('/home/fpp/media/plugindata/visitorTracker.db')

#####################################
def Stats() :
	print "Put some stats here, table sizes, etc.."

#####################################
def LoadMACWhitelist() :
	wlCount = 0

	with vdb:
		vdbc = vdb.cursor()
		for row in vdb.execute("SELECT MAC FROM MACWhitelist") :
			macWhitelist[row[0]] = 1
			wlCount = wlCount + 1

	print "%d MAC's whitelisted" % wlCount

#####################################
def ShowMACStats(mac, vdbc) :
	vdbc.execute("SELECT COUNT(MAC) FROM Visit WHERE MAC = '%s'" % (mac))
	vresult = vdbc.fetchone()

	print "%s - Tracking MAC: %s, v: %d" % (datetime.now().strftime("%Y-%m-%d %H:%M:%S.%f"), mac, vresult[0])

#####################################
def CleanupCache() :
	for mac, last in lastCache.items() :
		if last < (datetime.now()-timedelta(seconds = staleSecs)) :
			#lastStr = last.strftime("%Y-%m-%d %H:%M:%S.%f")
			#print "Deleting '%s' mac last seen at '%s'" % (mac, lastStr)
			del idCache[mac]
			del lastCache[mac]
			del countCache[mac]

#####################################
def TrackMAC(mac) :
	CleanupCache()

	curTime    = datetime.now()
	curTimeStr = curTime.strftime("%Y-%m-%d %H:%M:%S.%f")

	if mac in macWhitelist :
		#print "%s - Mac '%s' is in macWhitelist" % (curTimeStr, mac)
		return

	with vdb:
		vdbc = vdb.cursor()

		if mac in idCache :
			if lastCache[mac] < (datetime.now()-timedelta(seconds = activeSecs)) :
				countCache[mac] = countCache[mac] + 1
				vdbc.execute("UPDATE Visit SET LastSeen = '%s', Pings = %d WHERE Id = %d" % (curTimeStr, countCache[mac], idCache[mac]))
				lastCache[mac] = curTime
				print "Mac '%s' found in idCache, updated LastSeen to '%s'" % (mac, curTimeStr)
			else :
				print "Mac '%s' found in idCache, but newer than %d seconds old" % (mac, activeSecs)
		else :
			vdbc.execute("INSERT INTO Visit ( MAC, FirstSeen, LastSeen, Pings) VALUES ( '%s', '%s', '%s', 1)" % (mac, curTimeStr, curTimeStr))
			idCache[mac] = vdbc.lastrowid
			lastCache[mac] = curTime
			countCache[mac] = 1

			print "Mac '%s' NOT found in firstCache, new record inserted with first/last of '%s' with Id = %d" % (mac, curTimeStr, idCache[mac])

		ShowMACStats(mac, vdbc)

	sys.stdout.flush()

#####################################
def PacketHandler(pkt) :
	if pkt.haslayer(Dot11) :
		if pkt.type == 0 and pkt.subtype == 4 :
			#print "%s - Client with MAC: %s" % (datetime.now().strftime("%Y-%m-%d %H:%M:%S.%f"), pkt.addr2)
			#sys.stdout.flush()
			TrackMAC(pkt.addr2)

##############################################################################

print "FPP Visitor Tracker v%s" % vtVer

Stats()

LoadMACWhitelist()

sniff(iface = capIface, prn = PacketHandler, filter='type mgt subtype probe-resp or subtype probe-req', store = 0)

