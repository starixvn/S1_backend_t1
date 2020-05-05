#!/usr/bin/env bash
# Purpose: Thise script purpose is auto generate the logrotate config into logrotate.d/og
# Usage: <script> <dir 1> [dir 2] .... [dir n]
#      dir: Need rotate's dir
if [ $EUID -ne 0 ]; then
	echo please run on root
	exit 1
fi

if [ -z "$1" ]; then
    echo "Argument error"
    echo "$0 <dir 1> [dir 2] [dir 3] ..."
    exit 1
fi

LOGROTATE="/etc/logrotate.d/og"
if [ -f $LOGROTATE ]; then
    echo "logrotate alreadly exist."
    exit 1
fi

path="/home/jack/Code/og/admin/application/logs"
path=$1
CONF_FILE="su root root"

for p in "$@"; do
    path=$p
    if [ ! -d $path ]; then
        echo "Directory is not exist."
        exit 2
    fi
    script_dir="$(dirname $(dirname $path))/shell"
    auto_remove_script="$script_dir/remove_old_php_logs.sh"
    if [ ! -f $auto_remove_script ]; then
        echo "Remove script is not exist."
        exit 2
    fi
    read -r -d '' logrotate_conf <<- EOM
$path/*.log {
    maxage 7
    rotate 7
    daily
    compress
    missingok
    notifempty

}

$path/log-????-??-??.php {
    maxage 7
    rotate 7
    daily
    compress
    sharedscripts
    prerotate
        $auto_remove_script
    endscript
}
EOM
    CONF_FILE="$CONF_FILE\n$logrotate_conf"
done

echo -e "$CONF_FILE" > $LOGROTATE
