#!/bin/bash

#test root user
# if [ $EUID -ne 0 ]; then
# 	echo please run on root
# 	exit 1
# fi

if [ -z "$1" ]; then
	echo "usage: deploy_all.sh <repo name> <branch name>"
	exit 1
fi

SHELL_DIR="$(dirname $(readlink -f $0))"
OG_BASEPATH="$SHELL_DIR/.."
OG_ROOT="$OG_BASEPATH/.."
OG_DEPLOY_FLAG="$OG_BASEPATH/application/logs/deploy_flag"
echo "OG_BASEPATH=$OG_BASEPATH"
echo "OG_ROOT=$OG_ROOT"

if [ ! -f $OG_DEPLOY_FLAG ]; then
	echo "don't find deploy flag: $OG_DEPLOY_FLAG"
	exit 2
fi

echo `date`

#deploy
cd $OG_ROOT
git -C $OG_ROOT pull $1 $2

$OG_ROOT/migrate.sh

rm $OG_DEPLOY_FLAG

echo "deploy done"
echo `date`