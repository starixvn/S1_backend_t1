#!/bin/bash

CURRENT_DIR=`pwd`

if [ -d "/tmp/core-lib" ]; then
	echo "pull /tmp/core-lib"
	cd /tmp/core-lib
	git pull
	cd $CURRENT_DIR
	rm -f vendor
	ln -sf /tmp/core-lib/vendor/ vendor
	rm -f Cli.php
	cp -f /tmp/core-lib/Cli.php Cli.php
else
	echo "clone /tmp/core-lib"
	cd /tmp
	git clone git@git.smartbackend.com:core/core-lib.git
	cd $CURRENT_DIR
	rm -f vendor
	ln -sf /tmp/core-lib/vendor/ vendor
	rm -f Cli.php
	cp -f /tmp/core-lib/Cli.php Cli.php
fi

echo $*

php Cli.php $*
