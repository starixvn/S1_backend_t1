#!/bin/bash
#set HOME
#current file directory

PROJECT_HOME=$(dirname $( readlink -f $0 ))
cd $PROJECT_HOME

# if [ -f "$HOME/Code/relative_link" ]; then
#     OGHOME="."
# else
    OGHOME=$(dirname $( readlink -f $0 ))
# fi

# d1=$(dirname $(readlink -f $0))
# d2=$(dirname $d1)
# split_symbol=$(basename $d2)/$(basename $d1)

# function real_path {
#     if [ "$OGHOME" == "." ]; then
#         p=$(pwd | awk -F"$split_symbol" '{print $2}')
#         n=$(expr $(echo $p | sed 's/[^\/]//g' | tr --delete '\n'  | wc -c) + $(echo $2 | sed 's/^\.\///' | sed 's/[^\/]//g' | tr --delete '\n'  | wc -c))
#         parents_path=$(for (( c=1; c<=n; c++)) ; do echo -n "../" ; done)
#         echo "$parents_path$1 $2"
#     else
#         echo "$1 $2"
#     fi
# }

# function ln {
# #    pwd
# #    echo /usr/bin/ln $1 $(real_path $2 $3)
#     /bin/ln $1 $(real_path $2 $3)
# }

# OGHOME=$(pwd)
#/home/vagrant/Code/og
OGADMIN_HOME=$OGHOME/admin
OGPLAYER_HOME=$OGHOME/player
OGAFF_HOME=$OGHOME/aff
OGAGENCY_HOME=$OGHOME/agency

echo "create links in $OGHOME"
echo "OGADMIN_HOME=$OGADMIN_HOME"
echo "OGPLAYER_HOME=$OGPLAYER_HOME"
echo "OGAFF_HOME=$OGAFF_HOME"
echo "OGAGENCY_HOME=$OGAGENCY_HOME"

#add hook to git
# if [ -d $OGHOME/.git/hooks ]; then
#   # git branch | grep \* | cut -d ' ' -f2- > $OGHOME/version
#   echo "create link $OGHOME/git_hook_post_merge"
#   ln -sf $OGHOME/git_hook_post_merge $OGHOME/.git/hooks/post-merge

#   # init submodule

#   # bash $OGHOME/sync_submodule.sh
# else
#   echo ".git/hooks doesn't exist"
# fi

SUBMODULES="$OGHOME/submodules"

#init sub module
# if [ -d $OGHOME/.git ]; then
#   bash $OGHOME/sync_submodule.sh
# fi

if [ -d $OGHOME/submodules/core-lib ]; then
  #create links
  if [ -d $OGADMIN_HOME/system ]; then
    rm -rf $OGADMIN_HOME/system
  else
    rm -f $OGADMIN_HOME/system
  fi
  ln -sf $SUBMODULES/core-lib/system $OGADMIN_HOME/system

  if [ -d $OGADMIN_HOME/application/migrations ]; then
    rm -rf $OGADMIN_HOME/application/migrations
  else
    rm -f $OGADMIN_HOME/application/migrations
  fi
  ln -sf $SUBMODULES/core-lib/application/migrations $OGADMIN_HOME/application/migrations

  if [ -d $OGADMIN_HOME/application/libraries/vendor ]; then
    rm -rf $OGADMIN_HOME/application/libraries/vendor
  else
    rm -f $OGADMIN_HOME/application/libraries/vendor
  fi
  if [ -d $OGADMIN_HOME/application/libraries/scheduler ]; then
    rm -rf $OGADMIN_HOME/application/libraries/scheduler
  else
    rm -f $OGADMIN_HOME/application/libraries/scheduler
  fi
  if [ -d $OGADMIN_HOME/application/libraries/third_party ]; then
    rm -rf $OGADMIN_HOME/application/libraries/third_party
  else
    rm -f $OGADMIN_HOME/application/libraries/third_party
  fi
  ln -sf $SUBMODULES/core-lib/application/libraries/vendor $OGADMIN_HOME/application/libraries/vendor
  ln -sf $SUBMODULES/core-lib/application/libraries/scheduler $OGADMIN_HOME/application/libraries/scheduler
  ln -sf $SUBMODULES/core-lib/application/libraries/third_party $OGADMIN_HOME/application/libraries/third_party

  ln -sf $SUBMODULES/core-lib/application/config/apis.php $OGADMIN_HOME/application/config/apis.php
  ln -sf $SUBMODULES/core-lib/application/config/external_system_list.xml $OGADMIN_HOME/application/config/external_system_list.xml
  ln -sf $SUBMODULES/core-lib/application/config/operator_settings.xml $OGADMIN_HOME/application/config/operator_settings.xml
  ln -sf $SUBMODULES/core-lib/application/config/permissions.json $OGADMIN_HOME/application/config/permissions.json
  ln -sf $SUBMODULES/core-lib/application/config/standard_roles.json $OGADMIN_HOME/application/config/standard_roles.json
  ln -sf $SUBMODULES/core-lib/application/config/standard_t1_players.json $OGADMIN_HOME/application/config/standard_t1_players.json
  ln -sf $SUBMODULES/core-lib/application/config/migration.php $OGADMIN_HOME/application/config/migration.php
  ln -sf $SUBMODULES/core-lib/application/models/system_feature.php $OGADMIN_HOME/application/models/system_feature.php

  #payment lib
  if [ -d $OGADMIN_HOME/application/libraries/payment ]; then
    rm -rf $OGADMIN_HOME/application/libraries/payment
  else
    rm -f $OGADMIN_HOME/application/libraries/payment
  fi
  ln -sf $SUBMODULES/payment-lib/payment $OGADMIN_HOME/application/libraries/payment

  #game lib
  if [ -d $OGADMIN_HOME/application/libraries/game_platform ]; then
    rm -rf $OGADMIN_HOME/application/libraries/game_platform
  else
    rm -f $OGADMIN_HOME/application/libraries/game_platform
  fi
  if [ -d $OGADMIN_HOME/application/models/game_description ]; then
    rm -rf $OGADMIN_HOME/application/models/game_description
  else
    rm -f $OGADMIN_HOME/application/models/game_description
  fi
  rm -f $OGADMIN_HOME/application/libraries/game_platform
  ln -sf $SUBMODULES/game-lib/game_platform $OGADMIN_HOME/application/libraries/game_platform
  rm -f $OGADMIN_HOME/application/models/game_description
  ln -sf $SUBMODULES/game-lib/models/game_description $OGADMIN_HOME/application/models/game_description

  #link base model
  rm -f $SUBMODULES/core-lib/application/models/base_model.php
  # ln -sf $OGADMIN_HOME/application/models/base_model.php $SUBMODULES/core-lib/application/models/base_model.php
  rm -f $SUBMODULES/game-lib/models/base_model.php
  # ln -sf $OGADMIN_HOME/application/models/base_model.php $SUBMODULES/game-lib/models/base_model.php

  #create logs link
  if [ -d $SUBMODULES/core-lib/application/logs ]; then
    rm -rf $SUBMODULES/core-lib/application/logs
  else
    rm -f $SUBMODULES/core-lib/application/logs
  fi
  ln -sf $OGADMIN_HOME/application/logs $SUBMODULES/core-lib/application/logs

  # ln -sf $OGADMIN_HOME/application/libraries/abstract_external_system_manager.php $SUBMODULES/payment-lib/abstract_external_system_manager.php
  # ln -sf $OGADMIN_HOME/application/libraries/abstract_external_system_manager.php $SUBMODULES/game-lib/abstract_external_system_manager.php

  # ln -sf $OGADMIN_HOME/application/libraries/ProxySoapClient.php $SUBMODULES/payment-lib/ProxySoapClient.php
  # ln -sf $OGADMIN_HOME/application/libraries/ProxySoapClient.php $SUBMODULES/game-lib/ProxySoapClient.php

  # rm -f $SUBMODULES/payment-lib/unencrypt
  # rm -f $SUBMODULES/game-lib/unencrypt
  # ln -sf $OGADMIN_HOME/application/libraries/unencrypt $SUBMODULES/payment-lib/unencrypt
  # ln -sf $OGADMIN_HOME/application/libraries/unencrypt $SUBMODULES/game-lib/unencrypt

fi

rm -f $OGHOME/version

HOSTNAME=`hostname`

HOST_ID=$HOSTNAME
# HOST_ID="jw-og-sidfdks"

if [[ $HOST_ID == *"-"* ]]; then

  HOST_ID_1=`echo $HOST_ID | cut -d '-' -f 1`
  HOST_ID_2=`echo $HOST_ID | cut -d '-' -f 2`

  HOST_ID="$HOST_ID_1-$HOST_ID_2"

# else

#   HOST_ID=""

fi

if [[ $HOST_ID == *"_"* ]]; then

  HOST_ID_1=`echo $HOST_ID | cut -d '_' -f 1`
  HOST_ID_2=`echo $HOST_ID | cut -d '_' -f 2`

  HOST_ID="${HOST_ID_1}_${HOST_ID_2}"

# else

#   HOST_ID=""

fi

echo "first param is $1, HOST_ID is $HOST_ID"

if [[ "$1" == "BUILD_IMAGE" ]]; then
  HOST_ID=""
fi

# if [[ "$KUBERNETES_PORT" != "" ]]; then
#   HOST_ID=""
# fi

echo "$HOSTNAME to $HOST_ID"

#create folder
VAR_DATA=/var

if [ ! -d $VAR_DATA/log/response_results ]; then
sudo mkdir -p $VAR_DATA/log/response_results
sudo chmod -R 777 $VAR_DATA/log/response_results
fi

VAR_GAME_DATA=/var/game_platform

if [ ! -d $VAR_GAME_DATA/php ]; then
sudo mkdir -p $VAR_GAME_DATA/php
sudo chmod 777 $VAR_GAME_DATA/php
fi

if [ ! -d $VAR_GAME_DATA/nginx ]; then
sudo mkdir -p $VAR_GAME_DATA/nginx
sudo chmod 777 $VAR_GAME_DATA/nginx
fi

if [ ! -d $VAR_GAME_DATA/ag ]; then
sudo mkdir -p $VAR_GAME_DATA/ag
sudo chmod 777 $VAR_GAME_DATA/ag
fi

if [ ! -d $VAR_GAME_DATA/entwine ]; then
sudo mkdir -p $VAR_GAME_DATA/entwine
sudo chmod 777 $VAR_GAME_DATA/entwine
fi

if [ ! -d $VAR_GAME_DATA/imslots ]; then
sudo mkdir -p $VAR_GAME_DATA/imslots
sudo chmod 777 $VAR_GAME_DATA/imslots
fi

if [ ! -d $VAR_GAME_DATA/impt ]; then
sudo mkdir -p $VAR_GAME_DATA/impt
sudo chmod 777 $VAR_GAME_DATA/impt
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_idr1 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_idr1
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_idr1
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_idr2 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_idr2
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_idr2
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_idr3 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_idr3
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_idr3
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_idr4 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_idr4
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_idr4
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_idr5 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_idr5
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_idr5
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_idr6 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_idr6
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_idr6
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_idr7 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_idr7
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_idr7
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_thb2 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_thb2
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_thb2
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_vnd2 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_vnd2
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_vnd2
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_cny2 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_cny2
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_cny2
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_myr2 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_myr2
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_myr2
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_thb1 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_thb1
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_thb1
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_vnd1 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_vnd1
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_vnd1
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_cny1 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_cny1
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_cny1
fi

if [ ! -d $VAR_GAME_DATA/pragmaticplay_myr1 ]; then
sudo mkdir -p $VAR_GAME_DATA/pragmaticplay_myr1
sudo chmod 777 $VAR_GAME_DATA/pragmaticplay_myr1
fi

if [ ! -d $VAR_GAME_DATA/mg ]; then
sudo mkdir -p $VAR_GAME_DATA/mg
sudo chmod 777 $VAR_GAME_DATA/mg
fi

TMP_CLOCKWORK=/tmp/clockwork
if [ ! -d $TMP_CLOCKWORK ]; then

  sudo rm -rf $TMP_CLOCKWORK
  sudo mkdir -p $TMP_CLOCKWORK || mkdir -p $TMP_CLOCKWORK
  sudo chmod 777 $TMP_CLOCKWORK || chmod 777 $TMP_CLOCKWORK

else
  find $TMP_CLOCKWORK -mtime +2 -type f -exec rm -f {} \;

fi

bash ./admin/shell/noroot_command.sh fix_upload_path_on_mdb "$HOST_ID"

# TMP_PUB_DIR=$(dirname $PROJECT_HOME)/pub
# echo "create/check pub dir $TMP_PUB_DIR"
# if [ ! -d $TMP_PUB_DIR ]; then
#   mkdir -p $TMP_PUB_DIR || sudo mkdir -p $TMP_PUB_DIR
#   chmod 777 $TMP_PUB_DIR || sudo chmod 777 $TMP_PUB_DIR
# fi

# ln -sfn ${TMP_PUB_DIR}/$HOST_ID $PROJECT_HOME/admin/storage
# ln -sfn ${TMP_PUB_DIR}/$HOST_ID $PROJECT_HOME/player/storage

# make sharing upload dir
# SHARING_UPLOAD=$TMP_PUB_DIR/sharing_upload
# if [ ! -d $SHARING_UPLOAD ]; then
#   #sudo rm -rf $TMP_PUB_DIR/reports
#   mkdir -p $SHARING_UPLOAD || sudo mkdir -p $SHARING_UPLOAD
#   chmod 777 $SHARING_UPLOAD || sudo chmod 777 $SHARING_UPLOAD
# # else
#   # find $SHARING_UPLOAD -mtime +3 -type f -exec rm -f {} \;
# fi

# ln -sfn ${SHARING_UPLOAD} $PROJECT_HOME/admin/sharing_upload
# ln -sfn ${SHARING_UPLOAD} $PROJECT_HOME/player/sharing_upload
# ln -sfn ${SHARING_UPLOAD} $PROJECT_HOME/aff/sharing_upload
# ln -sfn ${SHARING_UPLOAD} $PROJECT_HOME/agency/sharing_upload

# make public reports dir
# REPORTS_DIR=$TMP_PUB_DIR/$HOST_ID/reports
# if [ ! -d $REPORTS_DIR ]; then
#   #sudo rm -rf $TMP_PUB_DIR/reports
#   mkdir -p $REPORTS_DIR || sudo mkdir -p $REPORTS_DIR
#   chmod 777 $REPORTS_DIR || sudo chmod 777 $REPORTS_DIR
# else
#   find $REPORTS_DIR -mtime +2 -type f -exec rm -f {} \;
# fi

# UPLOAD_DIR=$TMP_PUB_DIR/$HOST_ID/upload
# echo "create upload $UPLOAD_DIR"
# if [ ! -d $UPLOAD_DIR ]; then
#   mkdir -p $UPLOAD_DIR || sudo mkdir -p $UPLOAD_DIR
#   chmod 777 $UPLOAD_DIR || sudo chmod 777 $UPLOAD_DIR
# fi
# SHARING_REPORTS_DIR=$SHARING_UPLOAD/remote_reports
# if [ ! -d $SHARING_REPORTS_DIR ]; then

#   mkdir -p $SHARING_REPORTS_DIR || sudo mkdir -p $SHARING_REPORTS_DIR
#   chmod 777 $SHARING_REPORTS_DIR || sudo chmod 777 $SHARING_REPORTS_DIR
# else
#   find $SHARING_REPORTS_DIR -mtime +2 -type f -exec rm -f {} \;
# fi

# SHARING_LOGS_DIR=$SHARING_UPLOAD/remote_logs
# if [ ! -d $SHARING_LOGS_DIR ]; then

#   mkdir -p $SHARING_LOGS_DIR || sudo mkdir -p $SHARING_LOGS_DIR
#   chmod 777 $SHARING_LOGS_DIR || sudo chmod 777 $SHARING_LOGS_DIR
# else
#   find $SHARING_LOGS_DIR -mtime +2 -type f -exec rm -f {} \;
# fi

# UPLOAD_DIR=$TMP_PUB_DIR/$HOST_ID/upload
# echo "create upload $UPLOAD_DIR"
# if [ ! -d $UPLOAD_DIR ]; then
#   mkdir -p $UPLOAD_DIR || sudo mkdir -p $UPLOAD_DIR
#   chmod 777 $UPLOAD_DIR || sudo chmod 777 $UPLOAD_DIR
# fi

# PLAYER_PUB_DIR=$TMP_PUB_DIR/$HOST_ID/player_pub
# echo "create upload $PLAYER_PUB_DIR"
# if [ ! -d $PLAYER_PUB_DIR ]; then
#   mkdir -p $PLAYER_PUB_DIR || sudo mkdir -p $PLAYER_PUB_DIR
#   chmod 777 $PLAYER_PUB_DIR || sudo chmod 777 $PLAYER_PUB_DIR
# fi

# ln -sfn $PLAYER_PUB_DIR $PROJECT_HOME/admin/public/resources/player/built_in

# PLAYER_DIR=$TMP_PUB_DIR/$HOST_ID/player
# echo "create upload $PLAYER_DIR"
# if [ ! -d $PLAYER_DIR ]; then
#   mkdir -p $PLAYER_DIR || sudo mkdir -p $PLAYER_DIR
#   chmod 777 $PLAYER_DIR || sudo chmod 777 $PLAYER_DIR
# fi

# PLAYER_INTERNAL_DIR=$TMP_PUB_DIR/$HOST_ID/player/internal
# echo "create upload $PLAYER_INTERNAL_DIR"
# if [ ! -d $PLAYER_INTERNAL_DIR ]; then
#   mkdir -p $PLAYER_INTERNAL_DIR || sudo mkdir -p $PLAYER_INTERNAL_DIR
#   chmod 777 $PLAYER_INTERNAL_DIR || sudo chmod 777 $PLAYER_INTERNAL_DIR
# fi

#ln -sf $SHARING_LOGS_DIR  $PLAYER_INTERNAL_DIR/remote_logs

cd $PROJECT_HOME

rm -f $OGADMIN_HOME/public/all_version
rm -f $PROJECT_HOME/submodules/all_version
ln -sf $PROJECT_HOME/submodules/all_version.json $OGADMIN_HOME/public/all_version.json

# mkdir -p $UPLOAD_DIR/banner
# mkdir -p $UPLOAD_DIR/notifications
# mkdir -p $UPLOAD_DIR/themes
# mkdir -p $UPLOAD_DIR/themes/kgvip/img
# mkdir -p $UPLOAD_DIR/themes/liwei/img
# mkdir -p $UPLOAD_DIR/themes/lequ/img
# mkdir -p $UPLOAD_DIR/player/profile_picture/kgvip
# mkdir -p $UPLOAD_DIR/player/profile_picture/olobet
# mkdir -p $UPLOAD_DIR/player/profile_picture/lequ
# mkdir -p $UPLOAD_DIR/player/profile_picture/beteast
# mkdir -p $UPLOAD_DIR/player/profile_picture/player_dashboard3
# sudo chmod -R 777 $OGHOME/upload
# sudo chmod 777 $UPLOAD_DIR

# mkdir -p $UPLOAD_DIR/shared_images/account
# mkdir -p $UPLOAD_DIR/shared_images/banner
# mkdir -p $UPLOAD_DIR/shared_images/depositslip
# sudo chmod 777 $UPLOAD_DIR/shared_images/account
# sudo chmod 777 $UPLOAD_DIR/shared_images/banner
# sudo chmod 777 $UPLOAD_DIR/shared_images/depositslip

# RES_IMAGE_ACCOUNT="$OGADMIN_HOME/public/resources/images/account"

# if [ ! \( -L "$RES_IMAGE_ACCOUNT" \) ]; then
#     echo "it's not link $RES_IMAGE_ACCOUNT"
#     if [ -d "$RES_IMAGE_ACCOUNT" ]; then
#         echo "move $RES_IMAGE_ACCOUNT"
#         mv $RES_IMAGE_ACCOUNT $OGADMIN_HOME/public/resources/images/account_bak
#         cp $OGADMIN_HOME/public/resources/images/account_bak/* $UPLOAD_DIR/shared_images/account/
#     fi
# fi
# rm -f $OGADMIN_HOME/public/resources/images/account
# ln -sf $UPLOAD_DIR/shared_images/account $OGADMIN_HOME/public/resources/images/account

# rm -f $OGPLAYER_HOME/public/resources/images/account
# ln -sf $UPLOAD_DIR/shared_images/account $OGPLAYER_HOME/public/resources/images/account
# rm -f $OGAFF_HOME/public/resources/images/account
# ln -sf $UPLOAD_DIR/shared_images/account $OGAFF_HOME/public/resources/images/account

# RES_IMAGE_BANNER="$OGADMIN_HOME/public/resources/images/banner"

# if [ ! \( -L "$RES_IMAGE_BANNER" \) ]; then
#     echo "it's not link $RES_IMAGE_BANNER"

#     if [ -d "$RES_IMAGE_BANNER" ]; then
#         echo "move $RES_IMAGE_BANNER"
#         mv $OGADMIN_HOME/public/resources/images/banner $OGADMIN_HOME/public/resources/images/banner_bak
#         cp $OGADMIN_HOME/public/resources/images/banner_bak/* $UPLOAD_DIR/shared_images/banner
#     fi
# fi
# rm -f $OGADMIN_HOME/public/resources/images/banner
# ln -sf $UPLOAD_DIR/shared_images/banner $OGADMIN_HOME/public/resources/images/banner

# rm -f $OGPLAYER_HOME/public/resources/images/banner
# ln -sf $UPLOAD_DIR/shared_images/banner $OGPLAYER_HOME/public/resources/images/banner
# rm -f $OGAFF_HOME/public/resources/images/banner
# ln -sf $UPLOAD_DIR/shared_images/banner $OGAFF_HOME/public/resources/images/banner

# RES_IMAGE_DEPOSITSLIP="$OGADMIN_HOME/public/resources/depositslip"

# if [ ! \( -L "$RES_IMAGE_DEPOSITSLIP" \) ]; then
#     echo "it's not link $RES_IMAGE_DEPOSITSLIP"
#     if [ -d "$RES_IMAGE_DEPOSITSLIP" ]; then
#         echo "move $RES_IMAGE_DEPOSITSLIP"
#         mv $OGADMIN_HOME/public/resources/depositslip $OGADMIN_HOME/public/resources/depositslip_bak
#         cp $OGADMIN_HOME/public/resources/depositslip_bak/* $UPLOAD_DIR/shared_images/depositslip
#     fi
# fi
# rm -f $OGADMIN_HOME/public/resources/depositslip
# ln -sf $UPLOAD_DIR/shared_images/depositslip $OGADMIN_HOME/public/resources/depositslip

# rm -f $OGPLAYER_HOME/public/resources/depositslip
# ln -sf $UPLOAD_DIR/shared_images/depositslip $OGPLAYER_HOME/public/resources/depositslip
# rm -f $OGAFF_HOME/public/resources/depositslip
# ln -sf $UPLOAD_DIR/shared_images/depositslip $OGAFF_HOME/public/resources/depositslip

# rm -f $OGADMIN_HOME/public/upload
# rm -f $OGPLAYER_HOME/public/upload
# rm -f $OGAFF_HOME/public/upload
# rm -f $OGAGENCY_HOME/public/upload
# ln -sf $UPLOAD_DIR $OGADMIN_HOME/public/upload
# ln -sf $UPLOAD_DIR $OGPLAYER_HOME/public/upload
# ln -sf $UPLOAD_DIR $OGAFF_HOME/public/upload
# ln -sf $UPLOAD_DIR $OGAGENCY_HOME/public/upload

# rm -f $OGADMIN_HOME/public/banner
# rm -f $OGPLAYER_HOME/public/banner
# rm -f $OGAFF_HOME/public/banner
# rm -f $OGAGENCY_HOME/public/banner
# ln -sf $UPLOAD_DIR/banner $OGADMIN_HOME/public/banner
# ln -sf $UPLOAD_DIR/banner $OGPLAYER_HOME/public/banner
# ln -sf $UPLOAD_DIR/banner $OGAFF_HOME/public/banner
# ln -sf $UPLOAD_DIR/banner $OGAGENCY_HOME/public/banner

# mkdir -p $OGADMIN_HOME/public/resources/depositslip
# mkdir -p $OGADMIN_HOME/public/resources/images/account
# mkdir -p $OGADMIN_HOME/public/resources/images/banner
mkdir -p $OGADMIN_HOME/public/resources/images/uploaded_logo
# mkdir -p $OGPLAYER_HOME/public/player_dashboard3/img/profile_picture/
# mkdir -p $OGPLAYER_HOME/public/olobet/img/profile_picture/
# mkdir -p $OGPLAYER_HOME/public/kgvip/img/profile_picture/
# mkdir -p $OGPLAYER_HOME/public/beteast/img/profile_picture/
# mkdir -p $OGPLAYER_HOME/public/liwei/img/profile_picture/
# mkdir -p $OGPLAYER_HOME/public/lequ/img/profile_picture/
# mkdir -p $OGADMIN_HOME/public/reports
# sudo chmod 777 $OGADMIN_HOME/public/resources/images/account
# sudo chmod 777 $OGADMIN_HOME/public/resources/images/banner
sudo chmod 777 $OGADMIN_HOME/public/resources/images/uploaded_logo
sudo chmod 777 $OGADMIN_HOME/public/resources/images/tutorial
sudo chmod 777 $OGADMIN_HOME/public/resources/images/themes
# sudo chmod 777 $OGPLAYER_HOME/public/player_dashboard3/img/profile_picture/
# sudo chmod 777 $OGPLAYER_HOME/public/player_dashboard3/styles/
# sudo chmod 777 $OGPLAYER_HOME/public/player_dashboard3/img/logo
# sudo chmod 777 $OGPLAYER_HOME/application/views/olobet/includes
# sudo chmod 777 $OGPLAYER_HOME/application/views/player_dashboard3/includes
# sudo chmod 777 $OGPLAYER_HOME/application/views/player_dashboard3/includes/dynamic_top_nav
# sudo chmod 777 $OGPLAYER_HOME/public/olobet/img/profile_picture/
# sudo chmod 777 $OGPLAYER_HOME/public/olobet/styles/
# sudo chmod 777 $OGPLAYER_HOME/public/olobet/img/logo
# sudo chmod 777 $OGPLAYER_HOME/application/views/olobet/includes/dynamic_top_nav
# sudo chmod 777 $OGPLAYER_HOME/application/views/player_dashboard3/includes/dynamic_main_footer
# sudo chmod 777 $OGPLAYER_HOME/application/views/olobet/includes/dynamic_main_footer
# sudo chmod 777 $OGPLAYER_HOME/public/olobet/img
# sudo chmod 777 $OGPLAYER_HOME/public/player_dashboard3/img
# sudo chmod 777 $OGPLAYER_HOME/application/views/beteast/includes
# sudo chmod 777 $OGPLAYER_HOME/public/beteast/img/profile_picture/
# sudo chmod 777 $OGPLAYER_HOME/public/beteast/styles/
# sudo chmod 777 $OGPLAYER_HOME/public/beteast/img/logo
# sudo chmod 777 $OGPLAYER_HOME/application/views/beteast/includes/dynamic_top_nav
# sudo chmod 777 $OGPLAYER_HOME/application/views/beteast/includes/dynamic_main_footer
# sudo chmod 777 $OGPLAYER_HOME/public/beteast/img
# sudo chmod 777 $OGPLAYER_HOME/application/views/kgvip/includes
# sudo chmod 777 $OGPLAYER_HOME/public/kgvip/img/profile_picture/
# sudo chmod 777 $OGPLAYER_HOME/public/kgvip/styles/
# sudo chmod 777 $OGPLAYER_HOME/public/kgvip/img
# sudo chmod 777 $OGPLAYER_HOME/public/liwei/img/profile_picture/
# sudo chmod 777 $OGPLAYER_HOME/public/liwei/styles/
# sudo chmod 777 $OGPLAYER_HOME/public/liwei/img
# sudo chmod 777 $OGPLAYER_HOME/application/views/lequ/includes
# sudo chmod 777 $OGPLAYER_HOME/public/lequ/img/profile_picture/
# sudo chmod 777 $OGPLAYER_HOME/public/lequ/styles/
# sudo chmod 777 $OGPLAYER_HOME/public/lequ/img
# sudo chmod 777 $OGPLAYER_HOME/application/views/betmaster/includes
# sudo chmod 777 $OGPLAYER_HOME/public/betmaster/img/profile_picture/
# sudo chmod 777 $OGPLAYER_HOME/public/betmaster/styles/
# sudo chmod 777 $OGPLAYER_HOME/public/betmaster/img/logo
# sudo chmod 777 $OGPLAYER_HOME/application/views/betmaster/includes/dynamic_top_nav
# sudo chmod 777 $OGPLAYER_HOME/application/views/betmaster/includes/dynamic_main_footer
# sudo chmod 777 $OGPLAYER_HOME/public/betmaster/img

# rm -f $OGAFF_HOME/public/resources/images/banner
# ln -sf $OGADMIN_HOME/public/resources/images/banner $OGAFF_HOME/public/resources/images/banner

# cd $OGADMIN_HOME/public
# sudo rm -rf reports
# /bin/ln -sf $REPORTS_DIR reports
# cd ../..

# cd $OGADMIN_HOME/public
# sudo rm -rf reports
# /bin/ln -sf $SHARING_REPORTS_DIR reports
# cd ../..

# cd $OGPLAYER_HOME/public
# rm -f reports
# /bin/ln -sf $REPORTS_DIR reports
# cd ../..

# cd $OGAFF_HOME/public
# rm -f reports
# /bin/ln -sf $REPORTS_DIR reports
# cd ../..

# cd $OGAGENCY_HOME/public
# rm -f reports
# /bin/ln -sf $REPORTS_DIR reports
# cd ../..
# cd $OGAFF_HOME/public
# sudo rm -rf reports
# /bin/ln -sf $SHARING_REPORTS_DIR reports
# cd ../..

# cd $OGAGENCY_HOME/public
# sudo rm -rf reports
# /bin/ln -sf $SHARING_REPORTS_DIR reports
# cd ../..

mkdir -p $OGADMIN_HOME/application/logs
sudo chmod -R 777 $OGADMIN_HOME/application/logs
rm -f $OGADMIN_HOME/application/logs/logs

rm -f $OGHOME/logs
ln -sf $OGADMIN_HOME/application/logs $OGHOME/logs

if [ -d $OGHOME/service ]; then
rm -rf $OGHOME/service
else
rm -f $OGHOME/service
fi

cd $PROJECT_HOME
#ln -sf $OGHOME/service_api service
rm -f $OGHOME/service_logs
#mkdir -p $OGHOME/service_api/storage/logs
#chmod 777 $OGHOME/service_api/storage/logs
#ln -sf $OGHOME/service_api/storage/logs $OGHOME/service_logs


echo "sync .env file"
if [ -d $OGHOME/service_api ]; then
  echo "call $OGADMIN_HOME/shell/noroot_command.sh sync_service_api_env"
  # sync .env file from config
  bash $OGADMIN_HOME/shell/noroot_command.sh sync_service_api_env
fi

# echo $OGPLAYER_HOME/public/resources/images/account
#for version
if [ -f $OGHOME/version ]; then
  ln -sf $OGHOME/version $OGADMIN_HOME/public/version.html
  ln -sf $OGHOME/version $OGPLAYER_HOME/public/version.html
  ln -sf $OGHOME/version $OGAFF_HOME/public/version.html
  ln -sf $OGHOME/version $OGAGENCY_HOME/public/version.html
else
  rm -f $OGADMIN_HOME/public/version.html
  rm -f $OGPLAYER_HOME/public/version.html
  rm -f $OGAFF_HOME/public/version.html
  rm -f $OGAGENCY_HOME/public/version.html
fi

# cd $PROJECT_HOME/..
# rm -f site
# rm -f ./mobile_site
# ln -sf $PROJECT_HOME/sites/black_and_red site
# ln -sf $PROJECT_HOME/sites/mobile_site mobile_site
# cd $PROJECT_HOME

# cd $PROJECT_HOME/sites/black_and_red
# ln -sf $PROJECT_HOME/sites/mobile_site m
# cd $PROJECT_HOME


# ln -sf $OGADMIN_HOME/public/resources/player/loadjsfile.js $OGHOME/sites/black_and_red/loadjsfile.js

#for player center resources
if [ -d $OGHOME/sites/black_and_red ]; then

  cd $OGHOME/sites/black_and_red
  rm -f player_res
  ln -sf $OGPLAYER_HOME/public player_res

  cd ../..

fi

cd $OGHOME/admin/public
rm -f vendor
ln -sf $OGADMIN_HOME/application/libraries/vendor vendor
cd ../..

cd $OGHOME/player/public
rm -f vendor
ln -sf $OGADMIN_HOME/application/libraries/vendor vendor
cd ../..

cd $OGHOME/aff/public
rm -f vendor
ln -sf $OGADMIN_HOME/application/libraries/vendor vendor
cd ../..

cd $OGHOME/agency/public
rm -f vendor
ln -sf $OGADMIN_HOME/application/libraries/vendor vendor
cd ../..

cd $OGHOME/player
rm -f system
ln -sf $OGADMIN_HOME/system system
cd ..

# cd $OGHOME/integration
# rm -f vendor
# ln -sf $OGADMIN_HOME/application/libraries/vendor vendor
# cd ..

cd $OGHOME/aff
rm -f system
ln -sf $OGADMIN_HOME/system system
cd ..

cd $OGHOME/agency
rm -f system
ln -sf $OGADMIN_HOME/system system
cd ..

# rm -rf $OGPLAYER_HOME/public/resources/images/account
# ln -sf $OGADMIN_HOME/public/resources/images/account $OGPLAYER_HOME/public/resources/images/account

# for promothumbnails createlink to player resources
rm -rf $OGPLAYER_HOME/public/resources/images/promothumbnails
ln -sf $OGADMIN_HOME/public/resources/images/promothumbnails $OGPLAYER_HOME/public/resources/images/promothumbnails

# for shopthumbnails createlink to player resources
rm -rf $OGPLAYER_HOME/public/resources/images/shopping_banner
ln -sf $OGADMIN_HOME/public/resources/images/shopping_banner $OGPLAYER_HOME/public/resources/images/shopping_banner

# for tutorial createlink to player resources
rm -rf $OGPLAYER_HOME/public/resources/images/tutorial
ln -sf $OGADMIN_HOME/public/resources/images/tutorial $OGPLAYER_HOME/public/resources/images/tutorial

# for vipCoverthumbnails createlink to player resources
rm -rf $OGPLAYER_HOME/public/resources/images/vip_cover
ln -sf $OGADMIN_HOME/public/resources/images/vip_cover $OGPLAYER_HOME/public/resources/images/vip_cover

# for tutorialthumbnails createlink to player resources
rm -rf $OGPLAYER_HOME/public/resources/images/vip_cover
ln -sf $OGADMIN_HOME/public/resources/images/vip_cover $OGPLAYER_HOME/public/resources/images/vip_cover

# for promo cms createlink to player resources
rm -rf $OGPLAYER_HOME/public/resources/images/promo_cms
ln -sf $OGADMIN_HOME/public/resources/images/promo_cms $OGPLAYER_HOME/public/resources/images/promo_cms

#for constants
cd $OGHOME/player/application/config
ln -sf $OGADMIN_HOME/application/config/constants.php constants.php
cd ../../..

#for helper
cd $OGHOME/player/application/helpers
ln -sf $OGADMIN_HOME/application/helpers/error_helper.php error_helper.php
ln -sf $OGADMIN_HOME/application/helpers/mattermost_notification_helper.php mattermost_notification_helper.php
cd ../../..

#for model
cd $OGHOME/player/application/models

ln -sf $OGADMIN_HOME/application/models/boomingseamless_game_logs.php boomingseamless_game_logs.php
ln -sf $OGADMIN_HOME/application/models/boomingseamless_history_logs.php boomingseamless_history_logs.php
ln -sf $OGADMIN_HOME/application/models/rwb_game_logs.php rwb_game_logs.php
ln -sf $OGADMIN_HOME/application/models/rwb_game_transactions.php rwb_game_transactions.php
ln -sf $OGADMIN_HOME/application/models/base_model.php base_model.php
ln -sf $OGADMIN_HOME/application/models/base_game_logs_model.php base_game_logs_model.php
ln -sf $OGADMIN_HOME/application/models/original_game_logs_model.php original_game_logs_model.php
ln -sf $OGADMIN_HOME/application/models/static_site.php static_site.php
ln -sf $OGADMIN_HOME/application/models/external_system.php external_system.php
ln -sf $OGADMIN_HOME/application/models/sale_order.php sale_order.php
ln -sf $OGADMIN_HOME/application/models/sale_orders_status_history.php sale_orders_status_history.php
ln -sf $OGADMIN_HOME/application/models/game_provider_auth.php game_provider_auth.php
ln -sf $OGADMIN_HOME/application/models/response_result.php response_result.php
ln -sf $OGADMIN_HOME/application/models/response_error.php response_error.php
ln -sf $OGADMIN_HOME/application/models/player_login_token.php player_login_token.php
ln -sf $OGADMIN_HOME/application/models/queue_result.php queue_result.php
ln -sf $OGADMIN_HOME/application/models/duplicate_account_setting.php duplicate_account_setting.php
ln -sf $OGADMIN_HOME/application/models/transactions.php transactions.php
ln -sf $OGADMIN_HOME/application/models/player_model.php player_model.php
ln -sf $OGADMIN_HOME/application/models/daily_player_trans.php daily_player_trans.php
ln -sf $OGADMIN_HOME/application/models/affiliatemodel.php affiliatemodel.php
ln -sf $OGADMIN_HOME/application/models/sms_verification.php sms_verification.php
ln -sf $OGADMIN_HOME/application/models/point_transactions.php point_transactions.php

ln -sf $OGADMIN_HOME/application/models/payment.php payment.php
ln -sf $OGADMIN_HOME/application/models/promo_model.php promo_model.php
ln -sf $OGADMIN_HOME/application/models/game_description_model.php game_description_model.php
ln -sf $OGADMIN_HOME/application/models/users.php users.php
ln -sf $OGADMIN_HOME/application/models/bank_list.php bank_list.php
ln -sf $OGADMIN_HOME/application/models/wallet_model.php wallet_model.php
ln -sf $OGADMIN_HOME/application/models/daily_balance.php daily_balance.php
ln -sf $OGADMIN_HOME/application/models/payment_account.php payment_account.php
ln -sf $OGADMIN_HOME/application/models/banktype.php banktype.php
ln -sf $OGADMIN_HOME/application/models/http_request.php http_request.php
ln -sf $OGADMIN_HOME/application/models/group_level.php group_level.php
ln -sf $OGADMIN_HOME/application/models/playerbankdetails.php playerbankdetails.php
ln -sf $OGADMIN_HOME/application/models/withdraw_condition.php withdraw_condition.php
ln -sf $OGADMIN_HOME/application/models/transfer_condition.php transfer_condition.php
ln -sf $OGADMIN_HOME/application/models/random_bonus_history.php random_bonus_history.php
ln -sf $OGADMIN_HOME/application/models/player_promo.php player_promo.php
ln -sf $OGADMIN_HOME/application/models/operatorglobalsettings.php operatorglobalsettings.php
ln -sf $OGADMIN_HOME/application/models/vipsetting.php vipsetting.php
ln -sf $OGADMIN_HOME/application/models/game_logs.php game_logs.php
ln -sf $OGADMIN_HOME/application/models/promorules.php promorules.php
ln -sf $OGADMIN_HOME/application/models/promo_games.php promo_games.php
ln -sf $OGADMIN_HOME/application/models/roles.php roles.php
ln -sf $OGADMIN_HOME/application/models/report_model.php report_model.php
ln -sf $OGADMIN_HOME/application/models/player_friend_referral.php player_friend_referral.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_minute.php total_player_game_minute.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_hour.php total_player_game_hour.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_day.php total_player_game_day.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_month.php total_player_game_month.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_year.php total_player_game_year.php
ln -sf $OGADMIN_HOME/application/models/internal_message.php internal_message.php
ln -sf $OGADMIN_HOME/application/models/common_token.php common_token.php
ln -sf $OGADMIN_HOME/application/models/game_type_model.php game_type_model.php
ln -sf $OGADMIN_HOME/application/models/responsible_gaming.php responsible_gaming.php
ln -sf $OGADMIN_HOME/application/models/responsible_gaming_history.php responsible_gaming_history.php
ln -sf $OGADMIN_HOME/application/models/registration_setting.php registration_setting.php
ln -sf $OGADMIN_HOME/application/models/external_common_tokens.php external_common_tokens.php
ln -sf $OGADMIN_HOME/application/models/ip_whitelist_model.php ip_whitelist_model.php
ln -sf $OGADMIN_HOME/application/models/country_whitelist_model.php country_whitelist_model.php
ln -sf $OGADMIN_HOME/application/models/reports.php reports.php
ln -sf $OGADMIN_HOME/application/models/country_rules.php country_rules.php
ln -sf $OGADMIN_HOME/application/models/system_feature.php system_feature.php
ln -sf $OGADMIN_HOME/application/models/log_model.php log_model.php
ln -sf $OGADMIN_HOME/application/models/transaction_notes.php transaction_notes.php
ln -sf $OGADMIN_HOME/application/models/ebet_game_logs.php ebet_game_logs.php
ln -sf $OGADMIN_HOME/application/models/ebet_th_game_logs.php ebet_th_game_logs.php
ln -sf $OGADMIN_HOME/application/models/ebet_usd_game_logs.php ebet_usd_game_logs.php
ln -sf $OGADMIN_HOME/application/models/ebet2_game_logs.php ebet2_game_logs.php
ln -sf $OGADMIN_HOME/application/models/cashback_request.php cashback_request.php
ln -sf $OGADMIN_HOME/application/models/total_cashback_player_game.php total_cashback_player_game.php
ln -sf $OGADMIN_HOME/application/models/cashback_settings.php cashback_settings.php
ln -sf $OGADMIN_HOME/application/models/player_kyc.php player_kyc.php
ln -sf $OGADMIN_HOME/application/models/risk_score_model.php risk_score_model.php
ln -sf $OGADMIN_HOME/application/models/risk_score_history_logs.php risk_score_history_logs.php
ln -sf $OGADMIN_HOME/application/models/kyc_status_model.php kyc_status_model.php
ln -sf $OGADMIN_HOME/application/models/shopping_center.php shopping_center.php
ln -sf $OGADMIN_HOME/application/models/shopper_list.php shopper_list.php
ln -sf $OGADMIN_HOME/application/models/new_player_tutorial.php new_player_tutorial.php
ln -sf $OGADMIN_HOME/application/models/agency_model.php agency_model.php
ln -sf $OGADMIN_HOME/application/models/favorite_game_model.php favorite_game_model.php
ln -sf $OGADMIN_HOME/application/models/png_game_logs.php png_game_logs.php
ln -sf $OGADMIN_HOME/application/models/ebetmg_game_logs.php ebetmg_game_logs.php
ln -sf $OGADMIN_HOME/application/models/ebetqt_game_logs.php ebetqt_game_logs.php
ln -sf $OGADMIN_HOME/application/models/affiliate.php affiliate.php
ln -sf $OGADMIN_HOME/application/models/player_contact_us.php player_contact_us.php
ln -sf $OGADMIN_HOME/application/models/xyzblue_game_logs.php xyzblue_game_logs.php
ln -sf $OGADMIN_HOME/application/models/livechat_setting_model.php livechat_setting_model.php
ln -sf $OGADMIN_HOME/application/models/extreme_live_gaming_game_logs.php extreme_live_gaming_game_logs.php
ln -sf $OGADMIN_HOME/application/models/daily_currency.php daily_currency.php
ln -sf $OGADMIN_HOME/application/models/ls_casino_game_logs.php ls_casino_game_logs.php
ln -sf $OGADMIN_HOME/application/models/financial_account_setting.php financial_account_setting.php
ln -sf $OGADMIN_HOME/application/models/email_template_model.php email_template_model.php
ln -sf $OGADMIN_HOME/application/models/golden_race_transactions.php golden_race_transactions.php
ln -sf $OGADMIN_HOME/application/models/gd_seamless_wallet_transactions.php gd_seamless_wallet_transactions.php


ln -sf $OGADMIN_HOME/application/models/riskscore_kyc_chart_management_model.php riskscore_kyc_chart_management_model.php
ln -sf $OGADMIN_HOME/application/models/player_attached_proof_file_model.php player_attached_proof_file_model.php
ln -sf $OGADMIN_HOME/application/models/gbg_logs_model.php gbg_logs_model.php
ln -sf $OGADMIN_HOME/application/models/cms_model.php cms_model.php
ln -sf $OGADMIN_HOME/application/models/super_report.php super_report.php
ln -sf $OGADMIN_HOME/application/models/fcm_model.php fcm_model.php
ln -sf $OGADMIN_HOME/application/models/cmsbanner_model.php cmsbanner_model.php
ln -sf $OGADMIN_HOME/application/models/acuris_logs_model.php acuris_logs_model.php
ln -sf $OGADMIN_HOME/application/models/sale_orders_notes.php sale_orders_notes.php
ln -sf $OGADMIN_HOME/application/models/walletaccount_notes.php walletaccount_notes.php
ln -sf $OGADMIN_HOME/application/models/player_api_verify_status.php player_api_verify_status.php
ln -sf $OGADMIN_HOME/application/models/walletaccount_timelog.php walletaccount_timelog.php
ln -sf $OGADMIN_HOME/application/models/sale_orders_timelog.php sale_orders_timelog.php

# for player friend referral daily report
ln -sf $OGADMIN_HOME/application/models/player_earning.php player_earning.php

ln -sfn $OGADMIN_HOME/application/models/player_preference.php player_preference.php
ln -sfn $OGADMIN_HOME/application/models/player_notification.php player_notification.php
ln -sfn $OGADMIN_HOME/application/models/common_cashback_multiple_range_rules_model.php common_cashback_multiple_range_rules_model.php
ln -sfn $OGADMIN_HOME/application/models/common_cashback_multiple_range_settings_model.php common_cashback_multiple_range_settings_model.php
ln -sfn $OGADMIN_HOME/application/models/common_cashback_multiple_range_templates_model.php common_cashback_multiple_range_templates_model.php

# OGP-5841 api_common game extension
ln -sf $OGADMIN_HOME/application/models/comapi_games.php comapi_games.php
# OGP-6822 api_common settings/cache
ln -sf $OGADMIN_HOME/application/models/comapi_settings_cache.php comapi_settings_cache.php
# OGP-9815
ln -sf $OGADMIN_HOME/application/models/comapi_reports.php comapi_reports.php

# OGP-7025 [IOM] player's communication preferences page
ln -sf $OGADMIN_HOME/application/models/communication_preference_model.php communication_preference_model.php
# OGP-8374
ln -sf $OGADMIN_HOME/application/models/gl_game_tokens.php gl_game_tokens.php
ln -sf $OGADMIN_HOME/application/models/ole_reward_model.php ole_reward_model.php

ln -sf $OGADMIN_HOME/application/models/multiple_db_model.php multiple_db_model.php
ln -sf $OGADMIN_HOME/application/models/currencies.php currencies.php

ln -sf $OGADMIN_HOME/application/models/reports.php reports.php

rm -f ./modules
#ln -sf $OGADMIN_HOME/application/controllers/modules modules
ln -sf $OGADMIN_HOME/application/models/modules modules

rm -f ./customized_promo_rules
ln -sf $OGADMIN_HOME/application/models/customized_promo_rules customized_promo_rules

cd ../../..

#for controller
cd $OGHOME/player/application/controllers
ln -sf $OGADMIN_HOME/application/controllers/BaseController.php BaseController.php
ln -sf $OGADMIN_HOME/application/controllers/callback.php callback.php
ln -sf $OGADMIN_HOME/application/controllers/redirect.php redirect.php
ln -sf $OGADMIN_HOME/application/controllers/async.php async.php
ln -sf $OGADMIN_HOME/application/controllers/api.php api.php
ln -sf $OGADMIN_HOME/application/controllers/smartbackend.php smartbackend.php
ln -sf $OGADMIN_HOME/application/controllers/echoinfo.php echoinfo.php
ln -sf $OGADMIN_HOME/application/controllers/game_description.php game_description.php
ln -sf $OGADMIN_HOME/application/controllers/clockwork_controller.php clockwork_controller.php
ln -sf $OGADMIN_HOME/application/controllers/export_data.php export_data.php
ln -sf $OGADMIN_HOME/application/controllers/iovation.php iovation.php
ln -sf $OGADMIN_HOME/application/controllers/livechat_management.php livechat_management.php
ln -sf $OGADMIN_HOME/application/controllers/extremelivegaming_service_api.php extremelivegaming_service_api.php
ln -sf $OGADMIN_HOME/application/controllers/gamegateway.php gamegateway.php
ln -sfn $OGADMIN_HOME/application/controllers/player_internal.php player_internal.php
ln -sf $OGADMIN_HOME/application/controllers/ls_casino_service_api.php ls_casino_service_api.php
ln -sf $OGADMIN_HOME/application/controllers/betsoft_service_api.php betsoft_service_api.php
ln -sf $OGADMIN_HOME/application/controllers/rwb_service_api.php rwb_service_api.php
ln -sf $OGADMIN_HOME/application/controllers/booming_service_api.php booming_service_api.php
ln -sf $OGADMIN_HOME/application/controllers/golden_race_service_api.php golden_race_service_api.php
ln -sf $OGADMIN_HOME/application/controllers/gold_deluxe_service_api.php gold_deluxe_service_api.php
ln -sf $OGADMIN_HOME/application/controllers/flow_gaming_service_api.php flow_gaming_service_api.php

ln -sf $OGADMIN_HOME/application/controllers/hogaming_seamless_service_api.php hogaming_seamless_service_api.php

cd ../../..

cd $OGHOME/player/application/controllers/cli
ln -sf $OGADMIN_HOME/application/controllers/cli/base_testing.php base_testing.php
cd ../../../..

cd $OGHOME/player/application/controllers
rm -f ./modules
ln -sf $OGADMIN_HOME/application/controllers/modules modules
cd ../../..

#for view
cd $OGHOME/player/application/views/player
ln -sf $OGADMIN_HOME/application/views/player/redirect.php redirect.php
ln -sf $OGADMIN_HOME/application/views/player/callback_error.php callback_error.php
ln -sf $OGADMIN_HOME/application/views/player/callback_success.php callback_success.php
ln -sf $OGADMIN_HOME/application/views/player/request_pending.php request_pending.php
ln -sf $OGADMIN_HOME/application/views/player/qrcode.php qrcode.php
cd ../../../..

cd $OGHOME/player/application/views
ln -sf $OGADMIN_HOME/application/views/test_result.php test_result.php
cd ../../..

cd $OGHOME/player/application/views
rm -f ./share
ln -sf $OGADMIN_HOME/application/views/share share
rm -f ./includes
ln -sf $OGADMIN_HOME/application/views/includes includes
cd ../../..

#for lib
cd $OGHOME/player/application/libraries
rm -f ./game_platform
ln -sf $OGADMIN_HOME/application/libraries/game_platform/ game_platform
rm -f ./payment
ln -sf $OGADMIN_HOME/application/libraries/payment/ payment
rm -f ./telephone
ln -sf $OGADMIN_HOME/application/libraries/telephone/ telephone
rm -f ./PHPMailer
rm -f ./captcha
ln -sf $OGADMIN_HOME/application/libraries/captcha/ captcha
rm -f ./threads
ln -sf $OGADMIN_HOME/application/libraries/threads/ threads
rm -f ./phpexcel
ln -sf $OGADMIN_HOME/application/libraries/phpexcel/ phpexcel
rm -f ./sms
ln -sf $OGADMIN_HOME/application/libraries/sms/ sms
rm -f ./email_manager
ln -sf $OGADMIN_HOME/application/libraries/email_manager/ email_manager
rm -f ./scheduler
ln -sf $OGADMIN_HOME/application/libraries/scheduler/ scheduler
rm -f ./third_party
ln -sf $OGADMIN_HOME/application/libraries/third_party/ third_party
rm -f ./shorturl
ln -sf $OGADMIN_HOME/application/libraries/third_party/ shorturl
rm -f ./external_login
ln -sf $OGADMIN_HOME/application/libraries/external_login/ external_login

ln -sf $OGADMIN_HOME/application/libraries/utils.php utils.php
ln -sf $OGADMIN_HOME/application/libraries/abstract_external_system_manager.php abstract_external_system_manager.php
ln -sf $OGADMIN_HOME/application/libraries/email_setting.php email_setting.php
ln -sf $OGADMIN_HOME/application/libraries/salt.php salt.php
ln -sf $OGADMIN_HOME/application/libraries/lib_queue.php lib_queue.php
ln -sf $OGADMIN_HOME/application/libraries/duplicate_account.php duplicate_account.php
ln -sf $OGADMIN_HOME/application/libraries/transactions_library.php transactions_library.php
ln -sf $OGADMIN_HOME/application/libraries/player_library.php player_library.php
ln -sf $OGADMIN_HOME/application/libraries/promo_library.php promo_library.php
ln -sf $OGADMIN_HOME/application/libraries/ProxySoapClient.php ProxySoapClient.php
ln -sf $OGADMIN_HOME/application/libraries/rolesfunctions.php rolesfunctions.php
ln -sf $OGADMIN_HOME/application/libraries/data_tables.php data_tables.php
ln -sf $OGADMIN_HOME/application/libraries/lhSecurity.php lhSecurity.php
ln -sf $OGADMIN_HOME/application/libraries/runtime.php runtime.php
ln -sf $OGADMIN_HOME/application/libraries/game_description_library.php game_description_library.php
ln -sf $OGADMIN_HOME/application/libraries/triple_des.php triple_des.php
ln -sf $OGADMIN_HOME/application/libraries/lib_gearman.php lib_gearman.php
ln -sf $OGADMIN_HOME/application/libraries/lib_livechat.php lib_livechat.php
ln -sf $OGADMIN_HOME/application/libraries/whitelist_library.php whitelist_library.php
ln -sf $OGADMIN_HOME/application/libraries/tutorial_manager.php tutorial_manager.php
ln -sf $OGADMIN_HOME/application/libraries/player_manager.php player_manager.php
ln -sf $OGADMIN_HOME/application/libraries/gbg_api.php gbg_api.php
ln -sf $OGADMIN_HOME/application/libraries/shorturl.php shorturl.php
ln -sfn $OGADMIN_HOME/application/libraries/minify minify
ln -sf $OGADMIN_HOME/application/libraries/comapi_lib.php comapi_lib.php
ln -sf $OGADMIN_HOME/application/libraries/cmsbanner_library.php cmsbanner_library.php
ln -sfn $OGADMIN_HOME/application/libraries/player_security_library.php player_security_library.php
ln -sfn $OGADMIN_HOME/application/libraries/player_message_library.php player_message_library.php
ln -sfn $OGADMIN_HOME/application/libraries/player_responsible_gaming_library.php player_responsible_gaming_library.php
ln -sfn $OGADMIN_HOME/application/libraries/player_main_js_library.php player_main_js_library.php
ln -sfn $OGADMIN_HOME/application/libraries/player_main_js player_main_js
ln -sfn $OGADMIN_HOME/application/libraries/player_template player_template
ln -sf $OGADMIN_HOME/application/libraries/player_notification_library.php player_notification_library.php
ln -sf $OGADMIN_HOME/application/libraries/player_cashback_library.php player_cashback_library.php
ln -sf $OGADMIN_HOME/application/libraries/gl_game_lib.php gl_game_lib.php
ln -sf $OGADMIN_HOME/application/libraries/game_list_lib.php game_list_lib.php
ln -sf $OGADMIN_HOME/application/libraries/ole_reward_lib.php ole_reward_lib.php
ln -sf $OGADMIN_HOME/application/libraries/report_functions.php report_functions.php
ln -sf $OGADMIN_HOME/application/libraries/acuris_api.php acuris_api.php
ln -sf $OGADMIN_HOME/application/libraries/gisfromstring.php gisfromstring.php

if [[ -f "$OGHOME/player/application/libraries/multiple_image_uploader.php" ]]; then
    rm -f $OGHOME/player/application/libraries/multiple_image_uploader.php
fi
ln -sf $OGADMIN_HOME/application/libraries/Multiple_image_uploader.php multiple_image_uploader.php

#composer
rm -f composer.json
rm -f composer.lock
# ln -sf $OGADMIN_HOME/application/libraries/composer.json composer.json
# ln -sf $OGADMIN_HOME/application/libraries/composer.lock composer.lock
rm -f ./vendor
ln -sf $OGADMIN_HOME/application/libraries/vendor/ vendor

cd ../../..

#for js
cd $OGHOME/player/public/resources
rm -f ./player
ln -sf $OGADMIN_HOME/public/resources/player player
if [[ -L "third_party" && -d "third_party" ]]
then
  rm -f ./third_party
else
  rm -rf ./third_party
fi
ln -sf $OGADMIN_HOME/public/resources/third_party third_party
# rm -f ./web-socket-js
# ln -sf $OGADMIN_HOME/public/resources/js/web-socket-js web-socket-js
rm -f ./datatables
ln -sf $OGADMIN_HOME/public/resources/datatables datatables
cd ../../..

cd $OGHOME/player/public/resources/js
ln -sf $OGADMIN_HOME/public/resources/player/require.js require.js
ln -sf $OGADMIN_HOME/public/resources/player/underscore-min.js underscore-min.js
ln -sf $OGADMIN_HOME/public/resources/js/polyfiller.js polyfiller.js
ln -sf $OGADMIN_HOME/public/resources/js/jquery-1.11.1.min.js jquery-1.11.1.min.js
ln -sf $OGADMIN_HOME/public/resources/js/jquery-2.1.4.min.js jquery-2.1.4.min.js
ln -sf $OGADMIN_HOME/public/resources/js/datatables.min.js datatables.min.js
ln -sf $OGADMIN_HOME/public/resources/js/bootstrap-switch.min.js bootstrap-switch.min.js
ln -sf $OGADMIN_HOME/public/resources/player/loadjsfile.js loadjsfile.js

# for sbtech
ln -sf $OGADMIN_HOME/public/resources/sbtech/sbt_ole777.js sbt_ole777.js

rm -f ./shims
ln -sf $OGADMIN_HOME/public/resources/js/shims shims
cd ../../../..

#for css
cd $OGHOME/player/public/resources/css
ln -sf $OGADMIN_HOME/public/resources/css/datatables.min.css datatables.min.css
ln -sf $OGADMIN_HOME/public/resources/css/bootstrap-switch.min.css bootstrap-switch.min.css
# rm -f ./player
# ln -sf $OGADMIN_HOME/public/resources/css/player player
cd ../../../..

#for QRCode
cd $OGHOME/player/public/resources/
rm -f ./qrcode
ln -sf $OGADMIN_HOME/public/resources/qrcode qrcode

cd ../../..

# for leyin
# cd $OGHOME/player/public/leyin/css
# rm -f ./google_fonts
# ln -sf $OGADMIN_HOME/public/resources/css/themes/google_fonts google_fonts
# cd ../../../..

#for html
cd $OGHOME/player/public
ln -sf $OGADMIN_HOME/public/test_player.html .
ln -sf $OGADMIN_HOME/public/test_iframe.html .
ln -sf $OGADMIN_HOME/public/favicon.ico favicon.ico
cd ../..

#for error
cd $OGHOME/player/application
if [ -d "./errors"  ]; then
  if ! [ -L "./errors"  ]; then
    rm -f ./errors/*
    rmdir ./errors
  fi
fi
rm -f ./errors
ln -sf $OGADMIN_HOME/application/errors errors
cd ../..

#for logs
cd $OGHOME/player/application
rm -f ./logs
ln -sf $OGADMIN_HOME/application/logs logs
cd ../..

#for lang
cd $OGHOME/player/application
rm -f ./language
ln -sf $OGADMIN_HOME/application/language language
cd ../..

#for resources path
cd $OGHOME/admin/public
mkdir -p resources/images/promothumbnails
sudo chmod 777 resources/images/promothumbnails
sudo chmod 777 resources/images/shopping_banner
sudo chmod 777 resources/images/vip_badge
sudo chmod 777 resources/images/vip_cover

# mkdir resources/images/account
# chmod 777 resources/images/account
cd ../..

cd $OGHOME/admin/public/resources/css
rm -f ./fonts
ln -sf $OGADMIN_HOME/public/resources/fonts fonts
cd ../../../..

# for themes
cd $OGHOME/player/public/resources/css/
rm -f ./themes
ln -sf $OGADMIN_HOME/public/resources/css/themes/ themes
ln -sf $OGADMIN_HOME/public/resources/css/themes/bootstrap.paper.css bootstrap.paper.css
cd ../../../..

# for glyphicons
cd $OGHOME/player/public/resources/css/
rm -f ./fonts
ln -sf $OGADMIN_HOME/public/resources/css/fonts/ fonts
cd ../../../..


#==============================================for affiliate================================================

#for constants
cd $OGHOME/aff/application/config
ln -sf $OGADMIN_HOME/application/config/constants.php constants.php
cd ../../..

#for logs
cd $OGHOME/aff/application
rm -f ./logs
ln -sf $OGADMIN_HOME/application/logs logs
cd ../..

#for lang
cd $OGHOME/aff/application
rm -rf ./language
ln -sf $OGADMIN_HOME/application/language language
cd ../..

#for controllers
cd $OGHOME/aff/application/controllers
ln -sf $OGADMIN_HOME/application/controllers/BaseController.php BaseController.php
ln -sf $OGADMIN_HOME/application/controllers/callback.php callback.php
ln -sf $OGADMIN_HOME/application/controllers/redirect.php redirect.php
ln -sf $OGADMIN_HOME/application/controllers/api.php api.php
ln -sf $OGADMIN_HOME/application/controllers/clockwork_controller.php clockwork_controller.php
ln -sf $OGADMIN_HOME/application/controllers/export_data.php export_data.php

rm -f ./modules
ln -sf $OGADMIN_HOME/application/controllers/modules modules

cd ../../..

#for helper
cd $OGHOME/aff/application/helpers
ln -sf $OGADMIN_HOME/application/helpers/error_helper.php error_helper.php
ln -sf $OGADMIN_HOME/application/helpers/aff_helper.php aff_helper.php
ln -sf $OGADMIN_HOME/application/helpers/player_helper.php player_helper.php
cd ../../..

#for model
test -d ./aff/application/models || mkdir -p ./aff/application/models
cd $OGHOME/aff/application/models
ln -sf $OGADMIN_HOME/application/models/static_site.php static_site.php
ln -sf $OGADMIN_HOME/application/models/player.php player.php
ln -sf $OGADMIN_HOME/application/models/player_model.php player_model.php
ln -sf $OGADMIN_HOME/application/models/operatorglobalsettings.php operatorglobalsettings.php
ln -sf $OGADMIN_HOME/application/models/promorules.php promorules.php
ln -sf $OGADMIN_HOME/application/models/external_system.php external_system.php
ln -sf $OGADMIN_HOME/application/models/game_logs.php game_logs.php
ln -sf $OGADMIN_HOME/application/models/transactions.php transactions.php
ln -sf $OGADMIN_HOME/application/models/daily_player_trans.php daily_player_trans.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_minute.php total_player_game_minute.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_hour.php total_player_game_hour.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_day.php total_player_game_day.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_month.php total_player_game_month.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_year.php total_player_game_year.php
ln -sf $OGADMIN_HOME/application/models/affiliatemodel.php affiliatemodel.php
ln -sf $OGADMIN_HOME/application/models/wallet_model.php wallet_model.php
ln -sf $OGADMIN_HOME/application/models/payment.php payment.php
ln -sf $OGADMIN_HOME/application/models/users.php users.php
ln -sf $OGADMIN_HOME/application/models/response_result.php response_result.php
ln -sf $OGADMIN_HOME/application/models/game_provider_auth.php game_provider_auth.php
ln -sf $OGADMIN_HOME/application/models/game_description_model.php game_description_model.php
ln -sf $OGADMIN_HOME/application/models/http_request.php http_request.php
ln -sf $OGADMIN_HOME/application/models/report_model.php report_model.php
ln -sf $OGADMIN_HOME/application/models/player_friend_referral.php player_friend_referral.php
ln -sf $OGADMIN_HOME/application/models/internal_message.php internal_message.php
ln -sf $OGADMIN_HOME/application/models/common_token.php common_token.php
# ln -sf $OGADMIN_HOME/application/models/static_site.php static_site.php
ln -sf $OGADMIN_HOME/application/models/queue_result.php queue_result.php
ln -sf $OGADMIN_HOME/application/models/roles.php roles.php
ln -sf $OGADMIN_HOME/application/models/withdraw_condition.php withdraw_condition.php
ln -sf $OGADMIN_HOME/application/models/transfer_condition.php transfer_condition.php
ln -sf $OGADMIN_HOME/application/models/player_promo.php player_promo.php
ln -sf $OGADMIN_HOME/application/models/affiliate_earnings.php affiliate_earnings.php
ln -sf $OGADMIN_HOME/application/models/game_type_model.php game_type_model.php
ln -sf $OGADMIN_HOME/application/models/system_feature.php system_feature.php
ln -sf $OGADMIN_HOME/application/models/log_model.php log_model.php
ln -sf $OGADMIN_HOME/application/models/transaction_notes.php transaction_notes.php
ln -sf $OGADMIN_HOME/application/models/banktype.php banktype.php
ln -sf $OGADMIN_HOME/application/models/agency_model.php agency_model.php
ln -sf $OGADMIN_HOME/application/models/country_rules.php country_rules.php
ln -sf $OGADMIN_HOME/application/models/multiple_db_model.php multiple_db_model.php
ln -sf $OGADMIN_HOME/application/models/currencies.php currencies.php
ln -sf $OGADMIN_HOME/application/models/daily_currency.php daily_currency.php
ln -sf $OGADMIN_HOME/application/models/ebet_game_logs.php ebet_game_logs.php
ln -sf $OGADMIN_HOME/application/models/communication_preference_model.php communication_preference_model.php
ln -sf $OGADMIN_HOME/application/models/sale_order.php sale_order.php
ln -sf $OGADMIN_HOME/application/models/email_template_model.php email_template_model.php
ln -sf $OGADMIN_HOME/application/models/player_api_verify_status.php player_api_verify_status.php
ln -sf $OGADMIN_HOME/application/models/walletaccount_timelog.php walletaccount_timelog.php
ln -sf $OGADMIN_HOME/application/models/sale_orders_timelog.php sale_orders_timelog.php
ln -sf $OGADMIN_HOME/application/models/common_category.php common_category.php
ln -sf $OGADMIN_HOME/application/models/player_kyc.php player_kyc.php
ln -sf $OGADMIN_HOME/application/models/walletaccount_notes.php walletaccount_notes.php

rm -f ./modules
#ln -sf $OGADMIN_HOME/application/controllers/modules modules
ln -sf $OGADMIN_HOME/application/models/modules modules

cd ../../..

#for view
cd $OGHOME/aff/application/views
rm -f ./share
ln -sf $OGADMIN_HOME/application/views/share share
rm -f ./includes
ln -sf $OGADMIN_HOME/application/views/includes includes
cd ../../..


#for lib
cd $OGHOME/aff/application/libraries
rm -f ./game_platform
ln -sf $OGADMIN_HOME/application/libraries/game_platform/ game_platform

rm -f ./payment
ln -sf $OGADMIN_HOME/application/libraries/payment/ payment

rm -f ./telephone
ln -sf $OGADMIN_HOME/application/libraries/telephone/ telephone

rm -f ./PHPMailer

rm -f ./phpexcel
ln -sf $OGADMIN_HOME/application/libraries/phpexcel/ phpexcel

rm -f ./external_login
ln -sf $OGADMIN_HOME/application/libraries/external_login/ external_login

rm -f composer.json
rm -f composer.lock
# ln -sf $OGADMIN_HOME/application/libraries/composer.json composer.json
# ln -sf $OGADMIN_HOME/application/libraries/composer.lock composer.lock

rm -f ./captcha
ln -sf $OGADMIN_HOME/application/libraries/captcha/ captcha

rm -f ./vendor
ln -sf $OGADMIN_HOME/application/libraries/vendor/ vendor

rm -f ./scheduler
ln -sf $OGADMIN_HOME/application/libraries/scheduler/ scheduler

rm -f ./third_party
ln -sf $OGADMIN_HOME/application/libraries/third_party/ third_party

rm -f ./shorturl
ln -sf $OGADMIN_HOME/application/libraries/third_party/ shorturl

rm -f ./email_manager
ln -sf $OGADMIN_HOME/application/libraries/email_manager/ email_manager

ln -sf $OGADMIN_HOME/application/libraries/Affiliate_commission.php affiliate_commission.php
ln -sf $OGADMIN_HOME/application/libraries/utils.php utils.php
ln -sf $OGADMIN_HOME/application/libraries/abstract_external_system_manager.php abstract_external_system_manager.php
ln -sf $OGADMIN_HOME/application/libraries/email_setting.php email_setting.php
ln -sf $OGADMIN_HOME/application/libraries/salt.php salt.php
ln -sf $OGADMIN_HOME/application/libraries/lib_queue.php lib_queue.php
ln -sf $OGADMIN_HOME/application/libraries/duplicate_account.php duplicate_account.php
ln -sf $OGADMIN_HOME/application/libraries/transactions_library.php transactions_library.php
ln -sf $OGADMIN_HOME/application/libraries/player_library.php player_library.php
ln -sf $OGADMIN_HOME/application/libraries/promo_library.php promo_library.php
ln -sf $OGADMIN_HOME/application/libraries/ProxySoapClient.php ProxySoapClient.php
ln -sf $OGADMIN_HOME/application/libraries/rolesfunctions.php rolesfunctions.php
ln -sf $OGADMIN_HOME/application/libraries/data_tables.php data_tables.php
ln -sf $OGADMIN_HOME/application/libraries/lhSecurity.php lhSecurity.php
ln -sf $OGADMIN_HOME/application/libraries/runtime.php runtime.php
ln -sf $OGADMIN_HOME/application/libraries/triple_des.php triple_des.php
ln -sf $OGADMIN_HOME/application/libraries/lib_gearman.php lib_gearman.php
ln -sf $OGADMIN_HOME/application/libraries/lib_livechat.php lib_livechat.php
ln -sf $OGADMIN_HOME/application/libraries/permissions.php permissions.php
ln -sf $OGADMIN_HOME/application/libraries/authentication.php authentication.php
ln -sf $OGADMIN_HOME/application/libraries/history.php history.php
ln -sf $OGADMIN_HOME/application/libraries/shorturl.php shorturl.php
ln -sf $OGADMIN_HOME/application/libraries/game_list_lib.php game_list_lib.php

cd ../../..

#for error
cd $OGHOME/aff/application
if [ -d "./errors"  ]; then
  if ! [ -L "./errors"  ]; then
    rm -f ./errors/*
    rmdir ./errors
  fi
fi
rm -f ./errors
ln -sf $OGADMIN_HOME/application/errors errors
cd ../..

cd $OGHOME/aff/public
ln -sf $OGADMIN_HOME/public/favicon.ico favicon.ico
cd ../..

cd $OGHOME/aff/public/resources
if [[ -L "third_party" && -d "third_party" ]]
then
  rm -f ./third_party
else
  rm -rf ./third_party
fi
ln -sf $OGADMIN_HOME/public/resources/third_party third_party
cd ../../..

# for image
cd $OGHOME/aff/public/resources/images
ln -sf $OGADMIN_HOME/public/resources/images/og-login-logo.png og-login-logo.png
cd ../../../..

cd $OGHOME/aff/public/resources/images
rm -f ./static_sites
ln -sf $OGADMIN_HOME/public/resources/images/static_sites static_sites
cd ../../../..

cd $OGHOME/aff/public/resources/js
ln -sf $OGADMIN_HOME/public/resources/js/polyfiller.js polyfiller.js
ln -sf $OGADMIN_HOME/public/resources/js/jquery-1.11.1.min.js jquery-1.11.1.min.js
ln -sf $OGADMIN_HOME/public/resources/js/jquery-2.1.4.min.js jquery-2.1.4.min.js
rm -f ./shims
ln -sf $OGADMIN_HOME/public/resources/js/shims shims
rm -f pub
ln -sf $OGADMIN_HOME/public/resources/js/pub pub

ln -sf $OGADMIN_HOME/public/resources/js/highlight.pack.js highlight.pack.js
ln -sf $OGADMIN_HOME/public/resources/js/json2.min.js json2.min.js
cd ../../../..

# for themes
cd $OGHOME/aff/public/resources/css
rm -rf ./themes
ln -sf $OGADMIN_HOME/public/resources/css/themes/ themes
ln -sf $OGADMIN_HOME/public/resources/css/hljs.tomorrow.css hljs.tomorrow.css
cd ../../../..

# for glyphicons
cd $OGHOME/aff/public/resources/css
rm -rf ./fonts
ln -sf $OGADMIN_HOME/public/resources/css/fonts/ fonts
ln -sf $OGADMIN_HOME/public/resources/css/bootstrap-switch.min.css bootstrap-switch.min.css
cd ../../../..

# for fonts
cd $OGHOME/aff/public/resources/
rm -rf ./fonts
ln -sf $OGADMIN_HOME/public/resources/fonts fonts
rm -f ./datatables
ln -sf $OGADMIN_HOME/public/resources/datatables datatables
cd ../../..

# for utils lang
cd $OGHOME/aff/public/resources/js/
ln -sf $OGADMIN_HOME/public/resources/js/bootstrap-switch.min.js bootstrap-switch.min.js
rm -rf ./lang
ln -sf $OGADMIN_HOME/public/resources/js/lang lang
cd ../../../..

#==============================================for agency================================================

#for constants
cd $OGHOME/agency/application/config
ln -sf $OGADMIN_HOME/application/config/constants.php constants.php
cd ../../..

#for logs
cd $OGHOME/agency/application
rm -f ./logs
ln -sf $OGADMIN_HOME/application/logs logs
cd ../..

#for lang
cd $OGHOME/agency/application
rm -rf ./language
ln -sf $OGADMIN_HOME/application/language language
cd ../..

#for controllers
cd $OGHOME/agency/application/controllers
ln -sf $OGADMIN_HOME/application/controllers/BaseController.php BaseController.php
ln -sf $OGADMIN_HOME/application/controllers/callback.php callback.php
ln -sf $OGADMIN_HOME/application/controllers/redirect.php redirect.php
ln -sf $OGADMIN_HOME/application/controllers/api.php api.php
ln -sf $OGADMIN_HOME/application/controllers/export_data.php export_data.php
ln -sf $OGADMIN_HOME/application/controllers/clockwork_controller.php clockwork_controller.php
ln -sf $OGADMIN_HOME/application/controllers/echoinfo.php echoinfo.php

rm -f ./modules
ln -sf $OGADMIN_HOME/application/controllers/modules modules

cd ../../..

#for helper
cd $OGHOME/agency/application/helpers
ln -sf $OGADMIN_HOME/application/helpers/error_helper.php error_helper.php
cd ../../..

#for model
test -d ./agency/application/models || mkdir -p ./agency/application/models
cd $OGHOME/agency/application/models
ln -sf $OGADMIN_HOME/application/models/daily_balance.php daily_balance.php
ln -sf $OGADMIN_HOME/application/models/static_site.php static_site.php
ln -sf $OGADMIN_HOME/application/models/player.php player.php
ln -sf $OGADMIN_HOME/application/models/player_model.php player_model.php
ln -sf $OGADMIN_HOME/application/models/operatorglobalsettings.php operatorglobalsettings.php
ln -sf $OGADMIN_HOME/application/models/promorules.php promorules.php
ln -sf $OGADMIN_HOME/application/models/external_system.php external_system.php
ln -sf $OGADMIN_HOME/application/models/game_logs.php game_logs.php
ln -sf $OGADMIN_HOME/application/models/transactions.php transactions.php
ln -sf $OGADMIN_HOME/application/models/daily_player_trans.php daily_player_trans.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_minute.php total_player_game_minute.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_hour.php total_player_game_hour.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_day.php total_player_game_day.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_month.php total_player_game_month.php
ln -sf $OGADMIN_HOME/application/models/total_player_game_year.php total_player_game_year.php
ln -sf $OGADMIN_HOME/application/models/agency_model.php agency_model.php
ln -sf $OGADMIN_HOME/application/models/group_level.php group_level.php
ln -sf $OGADMIN_HOME/application/models/affiliate.php affiliate.php
ln -sf $OGADMIN_HOME/application/models/game_type_model.php game_type_model.php
ln -sf $OGADMIN_HOME/application/models/wallet_model.php wallet_model.php
ln -sf $OGADMIN_HOME/application/models/payment.php payment.php
ln -sf $OGADMIN_HOME/application/models/users.php users.php
ln -sf $OGADMIN_HOME/application/models/response_result.php response_result.php
ln -sf $OGADMIN_HOME/application/models/game_provider_auth.php game_provider_auth.php
ln -sf $OGADMIN_HOME/application/models/game_description_model.php game_description_model.php
ln -sf $OGADMIN_HOME/application/models/http_request.php http_request.php
ln -sf $OGADMIN_HOME/application/models/report_model.php report_model.php
ln -sf $OGADMIN_HOME/application/models/player_friend_referral.php player_friend_referral.php
ln -sf $OGADMIN_HOME/application/models/internal_message.php internal_message.php
ln -sf $OGADMIN_HOME/application/models/common_token.php common_token.php
ln -sf $OGADMIN_HOME/application/models/banktype.php banktype.php
# ln -sf $OGADMIN_HOME/application/models/static_site.php static_site.php
ln -sf $OGADMIN_HOME/application/models/queue_result.php queue_result.php
ln -sf $OGADMIN_HOME/application/models/roles.php roles.php
ln -sf $OGADMIN_HOME/application/models/vipsetting.php vipsetting.php
ln -sf $OGADMIN_HOME/application/models/withdraw_condition.php withdraw_condition.php
ln -sf $OGADMIN_HOME/application/models/transfer_condition.php transfer_condition.php
ln -sf $OGADMIN_HOME/application/models/player_promo.php player_promo.php
ln -sf $OGADMIN_HOME/application/models/ip.php ip.php
ln -sf $OGADMIN_HOME/application/models/affiliatemodel.php affiliatemodel.php
ln -sf $OGADMIN_HOME/application/models/system_feature.php system_feature.php
ln -sf $OGADMIN_HOME/application/models/log_model.php log_model.php
ln -sf $OGADMIN_HOME/application/models/transaction_notes.php transaction_notes.php
ln -sf $OGADMIN_HOME/application/models/agency_player_details.php agency_player_details.php
ln -sf $OGADMIN_HOME/application/models/country_rules.php country_rules.php
ln -sf $OGADMIN_HOME/application/models/ebet_game_logs.php ebet_game_logs.php
ln -sf $OGADMIN_HOME/application/models/ebet_th_game_logs.php ebet_th_game_logs.php
ln -sf $OGADMIN_HOME/application/models/ebet_usd_game_logs.php ebet_usd_game_logs.php
ln -sf $OGADMIN_HOME/application/models/ebet2_game_logs.php ebet2_game_logs.php
ln -sf $OGADMIN_HOME/application/models/multiple_db_model.php multiple_db_model.php
ln -sf $OGADMIN_HOME/application/models/currencies.php currencies.php
ln -sf $OGADMIN_HOME/application/models/player_attached_proof_file_model.php player_attached_proof_file_model.php
ln -sf $OGADMIN_HOME/application/models/player_kyc.php player_kyc.php
ln -sf $OGADMIN_HOME/application/models/kyc_status_model.php kyc_status_model.php
ln -sf $OGADMIN_HOME/application/models/riskscore_kyc_chart_management_model.php riskscore_kyc_chart_management_model.php
ln -sf $OGADMIN_HOME/application/models/gameapi.php gameapi.php
ln -sf $OGADMIN_HOME/application/models/communication_preference_model.php communication_preference_model.php
ln -sf $OGADMIN_HOME/application/models/player_api_verify_status.php player_api_verify_status.php
ln -sf $OGADMIN_HOME/application/models/agency_agent_report.php agency_agent_report.php
ln -sf $OGADMIN_HOME/application/models/walletaccount_timelog.php walletaccount_timelog.php
ln -sf $OGADMIN_HOME/application/models/sale_orders_timelog.php sale_orders_timelog.php

rm -f ./modules
#ln -sf $OGADMIN_HOME/application/controllers/modules modules
ln -sf $OGADMIN_HOME/application/models/modules modules

cd ../../..

#for view
cd $OGHOME/agency/application/views
rm -f ./share
ln -sf $OGADMIN_HOME/application/views/share share
rm -f ./includes
ln -sf $OGADMIN_HOME/application/views/includes includes
rm -f ./marketing_management
ln -sf $OGADMIN_HOME/application/views/marketing_management marketing_management
cd ../../..


#for lib
cd $OGHOME/agency/application/libraries
rm -f ./game_platform
ln -sf $OGADMIN_HOME/application/libraries/game_platform/ game_platform

rm -f ./payment
ln -sf $OGADMIN_HOME/application/libraries/payment/ payment

rm -f ./telephone
ln -sf $OGADMIN_HOME/application/libraries/telephone/ telephone

rm -f ./PHPMailer

rm -f ./phpexcel
ln -sf $OGADMIN_HOME/application/libraries/phpexcel/ phpexcel

rm -f ./external_login
ln -sf $OGADMIN_HOME/application/libraries/external_login/ external_login

rm -f composer.json
rm -f composer.lock
# ln -sf $OGADMIN_HOME/application/libraries/composer.json composer.json
# ln -sf $OGADMIN_HOME/application/libraries/composer.lock composer.lock

rm -f ./captcha
ln -sf $OGADMIN_HOME/application/libraries/captcha/ captcha

rm -f ./vendor
ln -sf $OGADMIN_HOME/application/libraries/vendor/ vendor

rm -f ./scheduler
ln -sf $OGADMIN_HOME/application/libraries/scheduler/ scheduler

rm -f ./third_party
ln -sf $OGADMIN_HOME/application/libraries/third_party/ third_party

rm -f ./shorturl
ln -sf $OGADMIN_HOME/application/libraries/third_party/ shorturl

ln -sf $OGADMIN_HOME/application/libraries/utils.php utils.php
ln -sf $OGADMIN_HOME/application/libraries/abstract_external_system_manager.php abstract_external_system_manager.php
ln -sf $OGADMIN_HOME/application/libraries/email_setting.php email_setting.php
ln -sf $OGADMIN_HOME/application/libraries/salt.php salt.php
ln -sf $OGADMIN_HOME/application/libraries/lib_queue.php lib_queue.php
ln -sf $OGADMIN_HOME/application/libraries/duplicate_account.php duplicate_account.php
ln -sf $OGADMIN_HOME/application/libraries/transactions_library.php transactions_library.php
ln -sf $OGADMIN_HOME/application/libraries/player_library.php player_library.php
ln -sf $OGADMIN_HOME/application/libraries/promo_library.php promo_library.php
ln -sf $OGADMIN_HOME/application/libraries/ProxySoapClient.php ProxySoapClient.php
ln -sf $OGADMIN_HOME/application/libraries/rolesfunctions.php rolesfunctions.php
ln -sf $OGADMIN_HOME/application/libraries/data_tables.php data_tables.php
ln -sf $OGADMIN_HOME/application/libraries/lhSecurity.php lhSecurity.php
ln -sf $OGADMIN_HOME/application/libraries/runtime.php runtime.php
ln -sf $OGADMIN_HOME/application/libraries/triple_des.php triple_des.php
ln -sf $OGADMIN_HOME/application/libraries/lib_gearman.php lib_gearman.php
ln -sf $OGADMIN_HOME/application/libraries/vipsetting_manager.php vipsetting_manager.php
ln -sf $OGADMIN_HOME/application/libraries/agency_library.php agency_library.php
ln -sf $OGADMIN_HOME/application/libraries/permissions.php permissions.php
ln -sf $OGADMIN_HOME/application/libraries/authentication.php authentication.php
ln -sf $OGADMIN_HOME/application/libraries/history.php history.php
ln -sf $OGADMIN_HOME/application/libraries/user_functions.php user_functions.php
ln -sf $OGADMIN_HOME/application/libraries/ip_manager.php ip_manager.php
ln -sf $OGADMIN_HOME/application/libraries/shorturl.php shorturl.php
ln -sf $OGADMIN_HOME/application/libraries/game_list_lib.php game_list_lib.php
ln -sf $OGADMIN_HOME/application/libraries/player_manager.php player_manager.php
ln -sf $OGADMIN_HOME/application/libraries/game_ag_api.php game_ag_api.php
ln -sf $OGADMIN_HOME/application/libraries/Multiple_image_uploader.php multiple_image_uploader.php

cd ../../..

#for error
cd $OGHOME/agency/application
if [ -d "./errors"  ]; then
  if ! [ -L "./errors"  ]; then
    rm -f ./errors/*
    rmdir ./errors
  fi
fi
rm -f ./errors
ln -sf $OGADMIN_HOME/application/errors errors
cd ../..

cd $OGHOME/agency/public
ln -sf $OGADMIN_HOME/public/favicon.ico favicon.ico
cd ../..

cd $OGHOME/agency/public/resources
if [[ -L "third_party" && -d "third_party" ]]
then
  rm -f ./third_party
else
  rm -rf ./third_party
fi
ln -sf $OGADMIN_HOME/public/resources/third_party third_party
cd ../../..

# for image
cd $OGHOME/agency/public/resources/images
ln -sf $OGADMIN_HOME/public/resources/images/og-login-logo.png og-login-logo.png
cd ../../../..

cd $OGHOME/agency/public/resources/images
rm -f ./static_sites
ln -sf $OGADMIN_HOME/public/resources/images/static_sites static_sites
cd ../../../..

cd $OGHOME/agency/public/resources/js
ln -sf $OGADMIN_HOME/public/resources/js/polyfiller.js polyfiller.js
ln -sf $OGADMIN_HOME/public/resources/js/jquery-1.11.1.min.js jquery-1.11.1.min.js
ln -sf $OGADMIN_HOME/public/resources/js/jquery-2.1.4.min.js jquery-2.1.4.min.js
rm -f ./shims
ln -sf $OGADMIN_HOME/public/resources/js/shims shims
rm -f pub
ln -sf $OGADMIN_HOME/public/resources/js/pub pub

ln -sf $OGADMIN_HOME/public/resources/js/highlight.pack.js highlight.pack.js
ln -sf $OGADMIN_HOME/public/resources/js/json2.min.js json2.min.js
cd ../../../..

# for themes
cd $OGHOME/agency/public/resources/css
rm -rf ./themes
ln -sf $OGADMIN_HOME/public/resources/css/themes/ themes
ln -sf $OGADMIN_HOME/public/resources/css/hljs.tomorrow.css hljs.tomorrow.css
ln -sf $OGADMIN_HOME/public/resources/css/dashboard.css
cd ../../../..

# for glyphicons
cd $OGHOME/agency/public/resources/css
rm -rf ./fonts
ln -sf $OGADMIN_HOME/public/resources/css/fonts/ fonts
ln -sf $OGADMIN_HOME/public/resources/css/bootstrap-switch.min.css bootstrap-switch.min.css
cd ../../../..

# for fonts
cd $OGHOME/agency/public/resources/
rm -rf ./fonts
ln -sf $OGADMIN_HOME/public/resources/fonts fonts
rm -f ./datatables
ln -sf $OGADMIN_HOME/public/resources/datatables datatables
cd ../../..

# for utils lang
cd $OGHOME/agency/public/resources/js/
ln -sf $OGADMIN_HOME/public/resources/js/bootstrap-switch.min.js bootstrap-switch.min.js
rm -rf ./lang
ln -sf $OGADMIN_HOME/public/resources/js/lang lang
cd ../../../..

# remove legacy symbolic link
rm -f $OGPLAYER_HOME/application/models/restful_model.php

# pwd

# check config_local
for topic in admin aff player agency; do
    file_path=$OGHOME/$topic/application/config/config_local.php
    test -f $file_path || test -L $file_path || cp $OGHOME/config_local_sample.php $file_path
    test -f $file_path || test -L $file_path || chmod 666 $file_path
done

# check config_secret_local
secret_file_path=$OGHOME/secret_keys/config_secret_local.php
test -f $secret_file_path || cp $OGHOME/config_secret_local_sample.php $secret_file_path
chmod 666 $secret_file_path

chmod 777 $OGHOME/secret_keys

# check vendor permission
sudo chown vagrant:vagrant $OGHOME/admin/application/libraries/vendor -R
# sudo chown vagrant:vagrant $OGHOME/player/application/libraries/vendor -R
# sudo chown vagrant:vagrant $OGHOME/aff/application/libraries/vendor -R

#if [ -d $OGHOME/service_api/public ]; then
#
#cd $OGHOME/service_api/public
#rm -f service
#ln -sf $OGHOME/service_api/public service
#cd ../..
#
#fi

rm -f $PROJECT_HOME/sites/mobile_site/mobile_site

cd $PROJECT_HOME/..
if ! [ -d site ]; then
  # rm -f site
  # rm -f ./mobile_site
  ln -sf $PROJECT_HOME/sites/black_and_red site
  ln -sf $PROJECT_HOME/sites/mobile_site mobile_site
fi
cd $PROJECT_HOME

if [ -d site ]; then
  cd $PROJECT_HOME/sites/black_and_red
  ln -sf $PROJECT_HOME/sites/mobile_site m
  cd $PROJECT_HOME
fi

HOME_ROOT="$PROJECT_HOME/../.."
echo "check $HOME_ROOT/site"
if ! [ -d $HOME_ROOT/site ]; then
  echo "create $HOME_ROOT/site"
  sudo mkdir $HOME_ROOT/site
fi

# sudo chmod -R 777 $HOME_ROOT/site
rm -f $HOME_ROOT/Code/site
if [ -d $HOME_ROOT/Code ]; then
    if [ -d $HOME_ROOT/site ]; then
        ln -sf $HOME_ROOT/site $HOME_ROOT/Code/site
        mkdir -p $HOME_ROOT/site/live
        mkdir -p $HOME_ROOT/site/mobile_live
    fi
fi

sudo touch /var/log/php_fpm_errors.log
sudo chmod 777 /var/log/php_fpm_errors.log

bash minify.sh
bash player_main_js.sh

bash ./admin/shell/noroot_command.sh test_cache


echo "DONE"

if [[ "$1" != "BUILD_IMAGE" ]]; then

if [[ "$1" != "AUTO" ]]; then

if [ -d $OGHOME/.git ]; then

    LINK_BROKEN=`find ! \( -path "*/vendor" -o -path "*/local_dev_docker" \) -o -type l -print0 | xargs -0 file | grep broken`

    # check link
    if [ -z "$LINK_BROKEN" ]; then
        exit 0
    else
        echo "some links are broken"
        echo $LINK_BROKEN
        exit 1
    fi

fi

fi

fi
