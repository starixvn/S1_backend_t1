#!/bin/bash

if [ -z ${OG_BASEPATH+'x'} ]; then
	echo set OG_BASEPATH

  	export OG_BASEPATH=/home/vagrant/Code/og/admin
fi

echo "start clearSessions `date`"
$OG_BASEPATH/shell/command.sh clearSessions $1

echo "end clearSessions `date`"