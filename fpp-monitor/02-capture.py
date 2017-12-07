#!/usr/bin/env python

import sys
import time
import datetime
from scapy.all import *
def PacketHandler(pkt) :
	if pkt.haslayer(Dot11) :
		if pkt.type == 0 and pkt.subtype == 4 :
			print "%s - Client with MAC: %s" % (datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S.%f"), pkt.addr2)
			sys.stdout.flush()
sniff(iface="wlan0", prn = PacketHandler)
