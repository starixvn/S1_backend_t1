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

PHP_PATH=$(if [ -f /usr/bin/php7.2 ]; then echo /usr/bin/php7.2; else echo /usr/bin/php; fi)

SHELL_DIR="$(dirname $(readlink -f $0))"
OG_BASEPATH="$SHELL_DIR/.."
echo "SHELL_DIR=$SHELL_DIR"
echo "OG_BASEPATH=$OG_BASEPATH"

echo "start `date`"

myarr=$1

#call sync records
# myarr=($(echo $1 | awk  -F',' '{
#   for (i = 0; ++i <= NF;)
#     print $i
#   }'))

echo "$1" "$2" "$3" 

echo $myarr

for i in $myarr ; do {

	# sleep 1
	echo "$PHP_PATH $OG_BASEPATH/shell/ci_cli.php cli/sync_after_balance/sync_game_logs_afterbalance $i \"$2\" \"$3\""

	$PHP_PATH $OG_BASEPATH/shell/ci_cli.php cli/sync_after_balance/sync_game_logs_afterbalance "$i" "$2" "$3" 

} & done

#chmod logs
chmod a+w $OG_BASEPATH/application/logs/*

echo "done `date`"
