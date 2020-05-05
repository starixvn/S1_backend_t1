#!/bin/bash

#test root user
if [ $EUID -ne 0 ]; then
	echo please run on root
	exit 1
fi

# export OG_BASEPATH=/home/vagrant/Code/og/admin
SHELL_DIR="$(dirname $(readlink -f $0))"
OG_BASEPATH="$SHELL_DIR/.."
echo "SHELL_DIR=$SHELL_DIR"
echo "OG_BASEPATH=$OG_BASEPATH"

#how to set: http://ogdev.ddns.net:8090/display/OG/Sync+Remote+Ftp+Server+to+Local
# mkdir -p /var/game_platform/agin
# chmod a+r /var/game_platform/agin

#edit ~/.netrc
#machine ftp.agingames.com
#
#    login <username>
#
#    password <password>

#chmod 600 ~/.netrc

echo start `date`
# while [ : ]
# do
  # echo run sync ftp

SUB_PLATFORM=(AGIN  BBIN  BG  HG  HUNTER  NYX  PT  SABAH  TTG  XIN)

TODAY=`date +%Y%m%d`
YESTERDAY=`date --date='yesterday' +%Y%m%d`

for platform_name in ${SUB_PLATFORM[*]} ; do

LOCAL_DIR=/var/game_platform/ag/$platform_name
REMOTE_DIR=/$platform_name/$TODAY
REMOTE_DIR2=/$platform_name/$YESTERDAY
REMOTE_DIR_LOSTFOUND=/$platform_name/lostAndfound

echo "copy $REMOTE_DIR $REMOTE_DIR2 to $LOCAL_DIR"

mkdir -p $LOCAL_DIR
chmod 777 $LOCAL_DIR

/usr/local/bin/lftp -e "
set ftp:list-options -a;

set xfer:log 1;

set xfer:log-file /var/log/sync_ag_ftp_transfer.log;

set net:timeout 5;

set net:max-retries 2;

set net:reconnect-interval-base 3;

set mirror:parallel-directories 1;

open xml.agingames.com;

lcd $LOCAL_DIR;

cd /;

mirror -F $REMOTE_DIR -F $REMOTE_DIR2 -F $REMOTE_DIR_LOSTFOUND --dereference --use-cache --verbose --no-umask --parallel=5 --newer-than='now-3days' --log=/var/log/sync_ag_ftp_mirror.log --exclude-glob .svn;

exit;

"

done

  # lftp -f $OG_BASEPATH/shell/sync_ag_ftp_commands
  # echo sleep
  # sleep 10
# done

echo end `date`
