#!/bin/bash

if [ $EUID -ne 0 ]; then
    echo please run on root
    exit 1
fi

if [ -z "$2" ]; then
    echo "usage: parallel_command.sh <how many processes> <function name> [parameters]"
    exit 1
fi

PHP_PATH=$(if [ -f /usr/bin/php7.2 ]; then echo /usr/bin/php7.2; elif [ -f /usr/bin/php7 ]; then echo /usr/bin/php7; elif [ -f /usr/local/bin/php7 ]; then echo /usr/local/bin/php7; else echo /usr/bin/php; fi)

SHELL_DIR="$(dirname $(readlink -f $0))"
OG_BASEPATH="$SHELL_DIR/.."
echo "SHELL_DIR=$SHELL_DIR"
echo "OG_BASEPATH=$OG_BASEPATH"

echo "start `date`"

echo "parallel count: $1"

start_time=$(date "+%s")

for i in `seq 1 $1` ; do {

    echo $PHP_PATH $SHELL_DIR/ci_cli.php "cli/command/$2" "$3" "$4" "$5" "$6" "$7" "$8" "$9"

    $PHP_PATH $SHELL_DIR/ci_cli.php "cli/command/$2" "$3" "$4" "$5" "$6" "$7" "$8" "$9"

} & done

wait

end_time=$(date "+%s")
echo "Total run time: $(expr $end_time - $start_time) (s)"

echo "done `date`"
