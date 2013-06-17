#check if connection else restart network interfaces
date +%H:%M:%S  >> /home/pi/opencv/log_upload
if  ping -q -c 1 192.168.0.1 > /dev/null ; then
	#rsync -av -e 'ssh -p 4000' /home/pi/opencv/pics/. cedric1q@cedricve.be:public_html/secure/pics/ >> /home/pi/opencv/log_upload
	rsync -xav /home/pi/opencv/pics/ root@164.138.26.6:/home/admin/domains/cedricve.be/public_html/secure/pics >> /home/pi/opencv/log_upload
	printf "\n\n" >> /home/pi/opencv/log_upload;
else
	printf "connection lost\n\n" >> /home/pi/opencv/log_upload;
	sudo /etc/init.d/networking restart
fi




