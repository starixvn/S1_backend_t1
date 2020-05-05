#!/usr/bin/env bash


if [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ]; then
    echo
    echo usage: $0 [network-interface] [critical value\(KB\)] [interval\(s\)]
    echo
    echo e.g. $0 eth0 400 300
    echo
    exit
fi

IF=$1
CRITICAL=$2
INTERVAL=$3  # update interval in seconds
IP_ADDR=`ifconfig $IF | awk '/inet addr/{print substr($2,6)}'`

function notification () {
    bot_name="Network Monitor"
    message=$1
    emoji=""
    channel="#blockedip"
    webhook_url="https://hooks.slack.com/services/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"
    POST_DATA='{"channel": "'"$channel"'", "username": "'"$bot_name"'", "text": "'"$message"'", "icon_emoji": "'"$emoji"'"}'
    curl -X POST -d "$POST_DATA" $webhook_url
}

while true
do
    R1=`cat /sys/class/net/$1/statistics/rx_bytes`
    T1=`cat /sys/class/net/$1/statistics/tx_bytes`
    sleep $INTERVAL
    R2=`cat /sys/class/net/$1/statistics/rx_bytes`
    T2=`cat /sys/class/net/$1/statistics/tx_bytes`
    TBPS=`expr $T2 - $T1`
    RBPS=`expr $R2 - $R1`
    TKBPS=`expr $TBPS / 1024`
    RKBPS=`expr $RBPS / 1024`
    TKBPS=`expr $TKBPS / $INTERVAL`
    RKBPS=`expr $RKBPS / $INTERVAL`
    date_msg=`date +"%Y/%m/%d %H:%M:%S"`
    echo "$date_msg TX $1: $TKBPS kB/s RX $1: $RKBPS kB/s"
    if [ "$RKBPS" -gt "$CRITICAL" ]; then
        notification "Host: \`$IP_ADDR\` TX $1: \`$TKBPS\` kB/s RX $1: \`$RKBPS\` kB/s" &
    fi
done
