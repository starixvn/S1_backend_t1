#!/bin/sh

#test root user

if [ $(id -u) -ne 0 ];
then
  echo please run on root
  exit 1
fi

PHP_PATH=$(if [ -f /usr/bin/php7.2 ]; then echo /usr/bin/php7.2; else echo /usr/bin/php; fi)

# SHELL_DIR="$(dirname $(readlink -f $0))"
# OG_BASEPATH="$SHELL_DIR/.."
# echo "SHELL_DIR=$SHELL_DIR"
# echo "OG_BASEPATH=$OG_BASEPATH"

# if [ -z ${OG_BASEPATH+'x'} ]; then
# 	echo set OG_BASEPATH

#   	export OG_BASEPATH=/home/vagrant/Code/og/admin
# fi

if [ -z "$1" ]; then
	echo "usage: sync_available_balance_for_one.sh <og_home> <game platform id>"
	exit 1
fi

if [ -z "$2" ]; then
	echo "usage: sync_available_balance_for_one.sh <og_home> <game platform id>"
	exit 1
fi

OG_BASEPATH=$1

echo $OG_BASEPATH >> $OG_BASEPATH/application/logs/sync_balance_service_$2.log

#call sync records
nohup $PHP_PATH $OG_BASEPATH/public/index.php cli/sync_balance/sync_available_once "$2" 2>&1 >> $OG_BASEPATH/application/logs/sync_balance_service_$2.log &

#chmod logs
# chmod a+w $OG_BASEPATH/application/logs/*

echo "sync: done" >> $OG_BASEPATH/application/logs/sync_balance_service_$2.log
