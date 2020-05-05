#!/bin/bash

#test root user
if [ $EUID -ne 0 ]; then
    echo please run on root
    exit 1
fi

# if [ -z ${OG_BASEPATH+'x'} ]; then
#   export OG_BASEPATH=/home/vagrant/Code/og/admin
# fi

PHP_PATH=$(if [ -f /usr/bin/php5.6 ]; then echo /usr/bin/php5.6; elif [ -f /usr/bin/php5 ]; then echo /usr/bin/php5; elif [ -f /usr/local/bin/php5 ]; then echo /usr/local/bin/php5; else echo /usr/bin/php; fi)

SHELL_DIR="$(dirname $(readlink -f $0))"
OG_BASEPATH="$SHELL_DIR/.."
echo "SHELL_DIR=$SHELL_DIR"
echo "OG_BASEPATH=$OG_BASEPATH"

if [ -z "$2" ]; then
    echo "usage: run_ci_cli.sh <controller> <function name> [parameters]"
    exit 1
fi

echo "start $1 `date`"

start_time=$(date "+%s")
echo $PHP_PATH $SHELL_DIR/ci_cli.php "cli/$1/$2" "$3" "$4" "$5" "$6" "$7" "$8" "$9"

$PHP_PATH $SHELL_DIR/ci_cli.php "cli/$1/$2" "$3" "$4" "$5" "$6" "$7" "$8" "$9"
end_time=$(date "+%s")

#chmod logs
chmod a+w $OG_BASEPATH/application/logs/*

echo "Total run time: $(expr $end_time - $start_time) (s)"
echo "done $1 `date`"
