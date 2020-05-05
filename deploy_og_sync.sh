#!/bin/bash

ssh vagrant@${APISYNC_SERVER}  "cd /home/vagrant/Code/${CLIENT_NAME}/og_sync; cp secret_keys/config_secret_local.php secret_keys/config_secret_local.php_`date "+%Y%m%d_%H%M"`"
#rsync -av ./ vagrant@${APISYNC_SERVER}:/home/vagrant/Code/${CLIENT_NAME}/og_sync/ --exclude=.git/* --exclude=.gitmodules
ssh vagrant@${APISYNC_SERVER}  "cd /home/vagrant/Code/${CLIENT_NAME}/og_sync; sudo chown vagrant.vagrant -R .git; git pull; ./create_links.sh; ./migrate.sh; git clean -f"
