#!/bin/bash

#test root user
if [ $EUID -ne 0 ]; then
	echo please run on root
	exit 1
fi

#./deploy_script.sh sync_yesterday_to_now.sh leyin,xcyl,blm,hansen,hxh,webet,ylapp,mgm,win007app,v8,slotworldsapp,3tbet
# if [ -z "$1" ]; then
# 	echo "usage: sync_yesterday_to_now.sh <OG_ROOT>"
# 	exit 1
# fi

# OG_ROOT=$1

OG_ROOT=/home/vagrant/Code/og_sync

TODAY=`date '+%Y-%m-%d %H:%M:%S'`
YESTERDAY=`date --date='yesterday' '+%Y-%m-%d 00:00:00'`

echo "$YESTERDAY to $TODAY"

# call sync
nohup $OG_ROOT/admin/shell/command.sh rebuild_all_game_logs_by_timelimit "$YESTERDAY" "$TODAY" "$2" 2>&1 >> /var/log/sync_yesterday_to_now.log &

