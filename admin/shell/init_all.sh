#!/bin/bash

#init or reinit or fix system

#test root user
if [ $EUID -ne 0 ]; then
	echo please run on root
	exit 1
fi

if [ -z ${OG_BASEPATH+'x'} ]; then
	echo set OG_BASEPATH

  	export OG_BASEPATH=/home/vagrant/Code/og/admin
fi

#prepare
$OG_BASEPATH/shell/prepare.sh

#install cronjob: geoipupdate, sync_ag_ftp
$OG_BASEPATH/shell/install_cronjob.sh


#install supervisor: web push server, queue worker , sync_game_records_service
$OG_BASEPATH/shell/install_service.sh

#create config local if don't exists
cp -n $OG_BASEPATH/../config_local_sample.php $OG_BASEPATH/application/config/config_local.php
cp -n $OG_BASEPATH/../config_local_sample.php $OG_BASEPATH/../player/application/config/config_local.php
cp -n $OG_BASEPATH/../config_local_sample.php $OG_BASEPATH/../aff/application/config/config_local.php

cp -n $OG_BASEPATH/../config_secret_local_sample.php $OG_BASEPATH/../secret_keys/config_secret_local.php

chown vagrant:vagrant $OG_BASEPATH/application/config/config_local.php
chown vagrant:vagrant $OG_BASEPATH/../player/application/config/config_local.php
chown vagrant:vagrant $OG_BASEPATH/../aff/application/config/config_local.php
chown vagrant:vagrant $OG_BASEPATH/../secret_keys/config_secret_local.php

#create links
cd $OG_BASEPATH/..
./create_links.sh

cd $OG_BASEPATH
./migrate.sh

cd $OG_BASEPATH
sudo ./shell/command.sh change_auto_increment

echo "all done"