#!/usr/bin/env bash

if [ $UID != "0" ]; then
    echo "Please use root execute!"
    exit 1
fi

count=$(netstat -antup 2>/dev/null | grep TIME_WAIT | grep :3306 | wc -l)

if [ $count -gt 1000 ]; then
    service php7.2-fpm restart
fi
