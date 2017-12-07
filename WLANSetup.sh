#!/bin/sh

/bin/echo "Bringing down wlan0 for monitor mode enable."
/sbin/ifconfig wlan0 down

/bin/echo "Enabling monitor mode."
/sbin/iw dev wlan0 set monitor none

/bin/echo "Bringing up wlan0. Edit /etc/rc.local to change this behavior."
/sbin/ifconfig wlan0 up

#sudo /sbin/iw dev wlan0 del
#sudo /sbin/iw phy phy0 interface add mon0 type monitor
#sudo /sbin/ifconfig mon0 up

