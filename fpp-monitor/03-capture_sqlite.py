#!/usr/bin/env python

import sys
import time
import datetime
import sqlite3 as lite
from scapy.all import *

vdb  = lite.connect('/home/fpp/media/plugindata/visitorTracker.db')
cvdb = lite.connect(':memory:')

def PacketHandler(pkt) :
	if pkt.haslayer(Dot11) :
		print "t: %d, st: %d" % (pkt.type, pkt.subtype)
		if pkt.addr2 != '00:1d:09:8a:92:ad' :
			if pkt.type == 0 and pkt.subtype == 4 :
				print "%s - Client with MAC: %s" % (datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S.%f"), pkt.addr2)
				sys.stdout.flush()
	sys.stdout.flush()

sniff(iface="wlan0", prn = PacketHandler)
