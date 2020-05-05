#!/usr/bin/env bash
# Perpose: Automatic deploy shell
# Description:
#     1. create a backup for directory "og", use date time as filename.
#     2. git fetch code from target branch
#     3. run create_links.sh and migrate.sh
# SYNOPSIS: auto_deploy.sh <repository> <branch> <release target>
#     repository: Set target repository.
#     branch : Set branch name.
#     release target: Release code's location.
# Example: auto_deploy.sh bitbucket 8.8.8_encrypt /home/vagrant/Code/og

USER="vagrant"
if [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ]; then
    echo "$0 <repository> <branch> <release target>" >&2
    exit 2
fi

REPOSITORY=$1
BRANCH_NAME=$2
OG_RELEASE_ROOT=$(readlink -f $3)
VERSION_FILE="$OG_RELEASE_ROOT/version"
TIMESTAMP=`date +%Y%m%d%H%M%S`
OG_ROOT=$(readlink -f $(dirname $(readlink -f $0))/../..)
EXCLUDE_FLIE="$OG_ROOT/.gitignore"
EXCLUDE_GIT_DIR=".git"
OG_PARENT_DIR=$(dirname $OG_ROOT)
OG_RELEASE_PARENT_DIR=$(dirname $OG_RELEASE_ROOT)
OG_RELEASE_DIR_NAME=$(basename $OG_RELEASE_ROOT)
TARBALL_EXTENSION="tar.gz"
OG_BACKUP="${OG_RELEASE_ROOT}_${TIMESTAMP}.$TARBALL_EXTENSION"
log_dir="admin/application/logs"

# Check
if [ ! -f $EXCLUDE_FLIE ]; then
    echo "Ignore file not found."
    exit 1
fi

if [ -z $EXCLUDE_GIT_DIR ]; then
    echo "Directory .git not set exclude been released."
    exit 1
fi

# Archive the OG_BACKUP
cd $OG_RELEASE_PARENT_DIR
echo "Start backup to $OG_BACKUP"
echo tar zcf $OG_BACKUP $OG_RELEASE_DIR_NAME
tar zcf $OG_BACKUP $OG_RELEASE_DIR_NAME --exclude "$OG_RELEASE_DIR_NAME/$log_dir/*"

if [ -f $OG_BACKUP ]; then
    # Delpoy code
    cd $OG_ROOT
    git fetch $REPOSITORY $BRANCH_NAME
    if [ ! $? == 0 ]; then echo "ERROR: fetch error." >&2; exit 1; fi
    git checkout $BRANCH_NAME
    if [ ! $? == 0 ]; then echo "ERROR: checkout error." >&2; exit 1; fi
    git pull $REPOSITORY $BRANCH_NAME
    if [ ! $? == 0 ]; then echo "ERROR: pull error." >&2; exit 1; fi
    special_dir="admin/application/migrations"
    rm -f $OG_RELEASE_ROOT/$special_dir/*
    rsync -a $OG_ROOT/ $OG_RELEASE_ROOT/ --exclude-from=$EXCLUDE_FLIE --exclude=$EXCLUDE_GIT_DIR

    cd $OG_RELEASE_ROOT
    echo "chmod 666 $OG_RELEASE_ROOT/$log_dir/*"
    sudo chmod 666 $OG_RELEASE_ROOT/$log_dir/*
    # Link libraries and migrate database.
    ./create_links.sh
    ./migrate.sh

    echo "Deploy done!"
else
    echo "ERROR: backup file error!!!!" >& 2
    exit 1
fi

# Check all backup files to remove expired backup files.
valid_days=$(for i in `seq 0 6`;do date -d "$i days ago" +%Y%m%d; done)

for backup_file in $(ls $OG_RELEASE_PARENT_DIR | grep -P "^${OG_RELEASE_DIR_NAME}_\d\d\d\d\d\d\d\d.*\.$TARBALL_EXTENSION" ); do
    remove_flag='Y'
    backup_file_date=${backup_file#${OG_RELEASE_DIR_NAME}_}
    for valid_day in $valid_days;
    do
        if [[ "$backup_file_date" =~ ^${valid_day}.* ]]; then
            remove_flag=''
            break
        fi
    done
    if [ "$remove_flag" == 'Y' ]; then
        if [[ "$backup_file" != '' && "$backup_file" != '/' ]]; then
            echo "remove $OG_RELEASE_PARENT_DIR/$backup_file"
            rm -fr $OG_RELEASE_PARENT_DIR/$backup_file
        fi
    fi
done

cd $OG_ROOT
if [ ! -f $OG_ROOT/version ]; then
    echo "fetch --tags $REPOSITORY $BRANCH_NAME"
    git fetch --tags $REPOSITORY $BRANCH_NAME

    echo "get version"
    git describe --tags --long > $VERSION_FILE
    git branch >> $VERSION_FILE
    git log | head -n 1 >> $VERSION_FILE
fi

echo "update composer"
sudo nohup $OG_RELEASE_ROOT/admin/shell/composer_update.sh >/dev/null 2>&1 &
