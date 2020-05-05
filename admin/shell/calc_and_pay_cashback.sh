#!/bin/bash

if [ -z ${OG_BASE+'x'} ]; then

     echo set OG_BASEPATH

     export OG_BASEPATH=/home/vagrant/Code/og/admin

fi


echo "start calculateAndPayCashback `date`"
$OG_BASEPATH/shell/command.sh calculateAndPayCashback '$1'

echo "end calculateAndPayCashback `date`"


