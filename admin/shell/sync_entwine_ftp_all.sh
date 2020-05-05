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
# mkdir -p /var/game_platform/entwine
# chmod a+r /var/game_platform/entwine

#edit ~/.netrc
#machine gameinfo.ea3-mission.com
#
#    login <username>
#
#    password <password>

#chmod 600 ~/.netrc

echo start `date`

LOCAL_DIR=/var/game_platform/entwine

mkdir -p $LOCAL_DIR
chmod 755 $LOCAL_DIR

/usr/local/bin/lftp -e "
set ftp:list-options -a;

set xfer:log 1;

set xfer:log-file /var/log/sync_entwine_ftp_transfer.log;

set net:timeout 5;

set net:max-retries 2;

set net:reconnect-interval-base 3;

open gameinfo.ea3-mission.com;

lcd $LOCAL_DIR;

cd /;

mirror --dereference --use-cache --verbose --no-umask --parallel=5 --newer-than='now-7days' --log=/var/log/sync_entwine_ftp_mirror.log --exclude-glob .svn;

exit;
"

echo end `date`
