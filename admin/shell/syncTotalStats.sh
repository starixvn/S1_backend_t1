#!/bin/bash

if [ -z ${OG_BASEPATH+'x'} ]; then
	echo set OG_BASEPATH

  	export OG_BASEPATH=/home/vagrant/Code/og/admin
fi

echo "start syncTotalStats `date`"
$OG_BASEPATH/shell/command.sh syncTotalStats "$1" "$2" "$3" "$4" "$5"

echo "end syncTotalStats `date`"