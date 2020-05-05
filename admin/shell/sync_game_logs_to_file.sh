#!/bin/bash

#test root user
# if [ $EUID -ne 0 ]; then
# 	echo please run on root
# 	exit 1
# fi

if [ -z ${OG_BASEPATH+'x'} ]; then
	echo set OG_BASEPATH

  	export OG_BASEPATH=/home/vagrant/Code/og/admin
fi

PHP_PATH=$(if [ -f /usr/bin/php7.2 ]; then echo /usr/bin/php7.2; else echo /usr/bin/php; fi)

#call sync records
# php $OG_BASEPATH/public/index.php cli/sync_game_logs_to_file/index
$PHP_PATH $OG_BASEPATH/public/index.php cli/sync_game_records/sync_game_logs_to_file "$1" "$2" "$3"

#chmod logs
# chmod a+w $OG_BASEPATH/application/logs/*

echo "Sync_game_logs_to_file: done"