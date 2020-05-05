#!/bin/bash

if [ -z ${OG_BASEPATH+'x'} ]; then
	echo set OG_BASEPATH

  	export OG_BASEPATH=/home/vagrant/Code/og/admin
fi

echo "start totalCashbackDaily `date`"
$OG_BASEPATH/shell/command.sh totalCashbackDaily '$1'

echo "end totalCashbackDaily `date`"