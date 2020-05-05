#!/usr/bin/env bash
# Purpose: This script for generate cron jobs log logrotate cofnig file.

if [ $EUID -ne 0 ]; then
	echo please run on root
	exit 1
fi

LOGROTATE="/etc/logrotate.d/og_cronjobs"
if [ -f $LOGROTATE ]; then
    echo "logrotate alreadly exist."
    exit 1
fi

CONF_FILE="su root root"
paths=$(cat /etc/cron.d/* | grep -v "^#" | grep -Po ">>\s*.*?\s" | sed 's/>>\s\?//' | sort| uniq)
smtp_server="mail.smartbackend.com"

read -r -d '' logrotate_conf <<- EOM
{
    maxage 7
    rotate 7
    daily
    compress
    missingok
    create 666 root root
    ifempty
    sharedscripts
    prerotate
        host_info="$(hostname) $(ifconfig eth0 | grep 'inet addr' | awk '{print $2}' | sed 's/.*\?://g')"
        tools_dir="$(readlink -f $(dirname $0))"
        send_mail_tool="\$tools_dir/send_mail.py"
        user="noreply"
        password="sj2sf3sDF47"
        subject="[CRON LOG] \$host_info Log files"
        to_users="james@smartbackend.com,david@smartbackend.com,jack@smartbackend.com"
        files="$(echo $paths | xargs | sed 's/\s\+/,/g')"
        \$send_mail_tool -u \$user -s "\$subject" -p \$password -t \$to_users -f \$files --server $smtp_server
    endscript
}

EOM

CONF_FILE="$CONF_FILE\n$paths\n$logrotate_conf"
echo -e "$CONF_FILE"
echo -e "$CONF_FILE" > $LOGROTATE
