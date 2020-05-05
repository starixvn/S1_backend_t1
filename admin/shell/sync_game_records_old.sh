#!/bin/bash

#test root user
if [ $EUID -ne 0 ]; then
	echo please run on root
	exit 1
fi

# if [ -z ${OG_BASEPATH+'x'} ]; then
# 	echo set OG_BASEPATH

#   	export OG_BASEPATH=/home/vagrant/Code/og/admin
# fi

SHELL_DIR="$(dirname $(readlink -f $0))"
OG_BASEPATH="$SHELL_DIR/.."
echo "SHELL_DIR=$SHELL_DIR"
echo "OG_BASEPATH=$OG_BASEPATH"

echo "start `date`"

#call sync records
php $OG_BASEPATH/public/index.php cli/sync_game_records/sync_all "$1" "$2" "$3"

#chmod logs
chmod a+w $OG_BASEPATH/application/logs/*

echo "done `date`"
