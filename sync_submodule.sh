#!/bin/bash

PROJECT_HOME=$(dirname $0)
echo "enter $PROJECT_HOME"
cd $PROJECT_HOME

echo "project home: $PROJECT_HOME"

#git branch | grep \* | cut -d ' ' -f2-

git submodule sync --recursive
git submodule init
git submodule update
# git submodule foreach git pull origin master

# php $PROJECT_HOME/admin/shell/Cli.php sync_submodule

bash $PROJECT_HOME/run_cli.sh sync_submodule
