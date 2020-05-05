#!/usr/bin/env bash

PROJECT_HOME=$(dirname $( readlink -f $0 ))
cd $PROJECT_HOME

git pull

./create_links.sh

./migrate.sh

echo "deploy self done"
