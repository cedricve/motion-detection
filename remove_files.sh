#!/bin/bash
#remove all files older than 1 days

date +%H:%M:%S  >> /home/pi/opencv/log_upload
echo "removed all files, older than 1days" >> /home/pi/opencv/log_upload
find /home/pi/opencv/pics/* -mmin +1440 -exec sudo rm -rf {} \; >>  /home/pi/opencv/log_upload
printf "\n\n" >> /home/pi/opencv/log_upload
