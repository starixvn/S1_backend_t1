#!/usr/bin/env bash
# Purpose: Restore database
# Descript: Restore database from backup data.
# SYNOPSIS: restore_database.sh <origin data directory | origin data tarball>

# Test root user
if [ $EUID -ne 0 ]; then
    echo "Please run on root" >&2
    exit 1
fi
# Test argument
if [ -z $1 ]; then
    echo "ERROR!" >&2
    echo "$0 <origin data directory | origin data tarball>" >&2
    exit 2
fi

TIMESTAMP=`date +%Y%m%d%H%M%S`
PREFIX_OLD_MYSQL=mysqldb
TEMP_DIR="/tmp/backup$TIMESTAMP"

# Clean mysql data directory, because xtrabackup need
MYSQL_DATA_DIR='/var/lib/mysql'

if [ -d $1 ]; then
    ORIGIN_DATA_DIR=$1
else
    mkdir -p $TEMP_DIR
    echo "tar xf $1 -C $TEMP_DIR"
    tar xf $1 -C $TEMP_DIR
    ORIGIN_DATA_DIR="$TEMP_DIR/$(ls $TEMP_DIR | grep $PREFIX_OLD_MYSQL)"
fi

# Restore
service mysql stop
# following innobackupex command not work successful, os replace use rsync.
#innobackupex --copy-back $ORIGIN_DATA_DIR
EXCLUDE_ARGS='--exclude="debian-*.flag" --exclude="xtrabackup_binary" --exclude="xtrabackup_logfile" --exclude="xtrabackup_checkpoints" --exclude="mysql_upgrade_info"'
echo "rsync -av $ORIGIN_DATA_DIR/ $MYSQL_DATA_DIR/ $EXCLUDE_ARGS"
rsync -av $ORIGIN_DATA_DIR/ $MYSQL_DATA_DIR/ $EXCLUDE_ARGS
chown -R mysql:mysql $MYSQL_DATA_DIR
service mysql start

# Clean temporary data
if [[ $MYSQL_DATA_DIR != '' && $MYSQL_DATA_DIR != '/' ]]; then
    #rm -fr $TEMP_DIR
    echo "rm"
fi
