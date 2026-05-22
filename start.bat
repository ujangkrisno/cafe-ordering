@echo off
echo Starting Cafe Ordering Server on LAN...
echo Access from phone: http://<LAN_IP>:8091
php -S 0.0.0.0:8091 -t C:\laragon\www\cafe-ordering
pause
