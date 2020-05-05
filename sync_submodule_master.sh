#!/bin/bash

PROJECT_HOME=$(dirname $0)
echo "enter $PROJECT_HOME"
cd $PROJECT_HOME

echo "project home: $PROJECT_HOME"

git submodule sync --recursive
git submodule init
git submodule update
git submodule foreach git pull origin master
