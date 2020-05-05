#!/bin/bash

#test root user
if [ $EUID -ne 0 ]; then
	echo please run on root
	exit 1
fi

SHELL_DIR="$(dirname $(readlink -f $0))"
OG_BASEPATH="$SHELL_DIR/.."
echo "SHELL_DIR=$SHELL_DIR"
echo "OG_BASEPATH=$OG_BASEPATH"

# if [ -z ${OG_BASEPATH+'x'} ]; then
# 	echo set OG_BASEPATH

#   	export OG_BASEPATH=/home/vagrant/Code/og/admin
# fi

if [ -z "$1" ]; then
	echo "usage: sync_game_records_for_one.sh <game platform id> [date time from] [date time to] [player username]"
	exit 1
fi

PHP_PATH=$(if [ -f /usr/bin/php7.2 ]; then echo /usr/bin/php7.2; else echo /usr/bin/php; fi)

#call sync records
$PHP_PATH $OG_BASEPATH/public/index.php cli/sync_game_records/sync_game_platform "$1" "$2" "$3" "$4"

#chmod logs
chmod a+w $OG_BASEPATH/application/logs/*

echo sync: done