#!/bin/bash
if [ -z ${OG_BASEPATH+'x'} ]; then
	echo set OG_BASEPATH

  	export OG_BASEPATH=/home/vagrant/Code/og/admin
fi

TEMPLATE=black_and_red

#generate live
#first copy
TARGET=black_and_red_live
ASSETS_URL="http%3A%2F%2F7xl49x.com1.z0.glb.clouddn.com"
PLAYER_URL="%2F%2Fplayer.jbyl777.com"
AFF_URL="http%3A%2F%2F120.26.131.7%3A6789"

rm -rf $OG_BASEPATH/../sites/$TARGET
cp -Rf $OG_BASEPATH/../sites/$TEMPLATE $OG_BASEPATH/../sites/$TARGET
php $OG_BASEPATH/public/index.php cli/html_template_processer/template_to/$TEMPLATE/$TARGET/$ASSETS_URL/$PLAYER_URL/$AFF_URL

#generate live asia
TARGET=black_and_red_live_asia
ASSETS_URL="http%3A%2F%2Fasia.jbyl777.com"
PLAYER_URL="%2F%2Fasiaplayer.jbyl777.com:8002"
AFF_URL="http%3A%2F%2F139.162.16.136%3A6789"

rm -rf $OG_BASEPATH/../sites/$TARGET
cp -Rf $OG_BASEPATH/../sites/$TEMPLATE $OG_BASEPATH/../sites/$TARGET
php $OG_BASEPATH/public/index.php cli/html_template_processer/template_to/$TEMPLATE/$TARGET/$ASSETS_URL/$PLAYER_URL/$AFF_URL

#generate stage
TARGET=black_and_red_stage
ASSETS_URL="http%3A%2F%2Fstage.jbyl777.com"
PLAYER_URL="%2F%2Fstageplayer.jbyl777.com"
AFF_URL="http%3A%2F%2Flb-1-1920521624.ap-southeast-1.elb.amazonaws.com%3A6789"

rm -rf $OG_BASEPATH/../sites/$TARGET
cp -Rf $OG_BASEPATH/../sites/$TEMPLATE $OG_BASEPATH/../sites/$TARGET
php $OG_BASEPATH/public/index.php cli/html_template_processer/template_to/$TEMPLATE/$TARGET/$ASSETS_URL/$PLAYER_URL/$AFF_URL

