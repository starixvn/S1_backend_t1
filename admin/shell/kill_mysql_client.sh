#!/bin/bash


for PID in `ps aux | grep -i mysql | grep pts |awk '{print $2}'`
do
echo $PID
kill -9 ${PID}
done

