#!/bin/bash

#test root user
if [ $EUID -ne 0 ]; then
	echo please run on root
	exit 1
fi

PHP_PATH=$(if [ -f /usr/bin/php7.2 ]; then echo /usr/bin/php7.2; elif [ -f /usr/bin/php7 ]; then echo /usr/bin/php7; elif [ -f /usr/local/bin/php7 ]; then echo /usr/local/bin/php7; else echo /usr/bin/php; fi)

SHELL_DIR="$(dirname $(readlink -f $0))"
OG_BASEPATH="$SHELL_DIR/.."
echo "SHELL_DIR=$SHELL_DIR"
echo "OG_BASEPATH=$OG_BASEPATH"

echo "start $1 `date`"

start_time=$(date "+%s")
$PHP_PATH $SHELL_DIR/ci_cli.php "cli/queue_server/start"
end_time=$(date "+%s")

#chmod logs
chmod a+w $OG_BASEPATH/application/logs/*

echo "Total run time: $(expr $end_time - $start_time) (s)"
echo "done $1 `date`"
