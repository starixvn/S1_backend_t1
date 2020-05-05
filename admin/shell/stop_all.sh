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


# stop all services
service supervisor stop
service sync_game_records_service stop

echo stop all : done
