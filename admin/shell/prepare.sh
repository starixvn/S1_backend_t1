#!/bin/bash

#test root user
if [ $EUID -ne 0 ]; then
	echo please run on root
	exit 1
fi

if [ -z ${OG_BASEPATH+'x'} ]; then
	echo set OG_BASEPATH

  	export OG_BASEPATH=/home/vagrant/Code/og/admin
fi

#prepare directory, files
echo create directory
mkdir -p /var/log/response_results
chmod 777 /var/log/response_results

mkdir -p /var/game_platform/agin
chmod 777 /var/game_platform/agin

mkdir -p /var/game_platform/mg
chmod 777 /var/game_platform/mg

mkdir -p $OG_BASEPATH/application/logs
chmod 777 $OG_BASEPATH/application/logs

#create log files
touch $OG_BASEPATH/application/logs/payment_error.log
touch $OG_BASEPATH/application/logs/queue_error.log
touch $OG_BASEPATH/application/logs/app_debug.log
chmod a+r $OG_BASEPATH/application/logs/*
chmod a+w $OG_BASEPATH/application/logs/*

mkdir -p $OG_BASEPATH/application/depositslip
chmod 777 $OG_BASEPATH/application/depositslip

#install rsync
apt-get install rsync nginx mysql-server curl supervisor git vim memcached w3m php5 php5-mysql php5-mcrypt php5-curl php5-fpm php5-gearman php5-memcached php5-gd

echo update composer
$OG_BASEPATH/shell/composer_update.sh

echo prepare: done