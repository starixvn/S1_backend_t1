#!/bin/bash

if [ -z ${OG_BASEPATH+'x'} ]; then
	echo set OG_BASEPATH

  	export OG_BASEPATH=/home/vagrant/Code/og/admin
fi

echo "start payCashback `date`"
$OG_BASEPATH/shell/command.sh payCashback '$1'

echo "end payCashback `date`"