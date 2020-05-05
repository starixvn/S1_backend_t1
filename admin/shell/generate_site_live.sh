#!/bin/bash
if [ -z ${OG_BASEPATH+'x'} ]; then
  	export OG_BASEPATH=/home/vagrant/Code/og/admin

	echo "set OG_BASEPATH to $OG_BASEPATH"

fi

if [ -z "$1" ]; then
	echo "usage: generate_site_live.sh <template name>"
	exit 1
fi

TEMPLATE=$1

#generate live
#first copy
TARGET=${TEMPLATE}_live
ASSETS_URL=`cat $OG_BASEPATH/../sites/$TEMPLATE/ASSETS_URL`
PLAYER_URL=`cat $OG_BASEPATH/../sites/$TEMPLATE/PLAYER_URL`
AFF_URL=`cat $OG_BASEPATH/../sites/$TEMPLATE/AFF_URL`
PLAYER_FULL_URL=`cat $OG_BASEPATH/../sites/$TEMPLATE/PLAYER_FULL_URL`

rm -rf $OG_BASEPATH/../sites/$TARGET
cp -Rf $OG_BASEPATH/../sites/$TEMPLATE $OG_BASEPATH/../sites/$TARGET
php $OG_BASEPATH/public/index.php cli/html_template_processer/replace_template/$TEMPLATE/$TARGET/$ASSETS_URL/$PLAYER_URL/$AFF_URL/$PLAYER_FULL_URL

rm $OG_BASEPATH/../sites/$TARGET/ASSETS_URL $OG_BASEPATH/../sites/$TARGET/PLAYER_URL $OG_BASEPATH/../sites/$TARGET/AFF_URL $OG_BASEPATH/../sites/$TARGET/PLAYER_FULL_URL
