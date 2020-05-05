#!/usr/bin/env bash
# Perpose: Automatic rollback shell
# Description:
#     1. backup current 'og' and remove
#     2. restore last backup file to 'og'
# SYNOPSIS: auto_rollback.sh <og platform basename>
# Example:
#          auto_rollback.sh og
#          auto_rollback.sh og_staging
#          auto_rollback.sh og_sync

if [ -z "$1" ]; then
    echo "ERROR: argument error" >&2
    echo "  $0 <og platform basename>" >&2
    echo "  Example: auto_rollback.sh og" >&2
    exit 1
fi
TIMESTAMP=`date +%Y%m%d%H%M%S`
OG=$1
OG_PARENT_DIR="$HOME/Code"
OG_ROOT="$OG_PARENT_DIR/$OG"
OG_BACKUP="${OG_ROOT}_${TIMESTAMP}.tar.gz"
cd $OG_PARENT_DIR
if [ -d $OG_ROOT ]; then
    echo "Backup $OG_ROOT to $OG_BACKUP"
    tar zcf $OG_BACKUP $OG
    if [[ "$OG_ROOT" != '' && "$OG_ROOT" != '/' ]]; then
        rm -fr $OG_ROOT
    fi
fi

# Found last backup file
RESTORE_FILE=`ls | grep -P "${OG}_\d{14}\.tar.gz" | sort | tail -2 | head -n 1`
echo "Restore: $RESTORE_FILE to $OG_ROOT" 

if [[ "$RESTORE_FILE" != '' && "$RESTORE_FILE" != '/' ]]; then
    tar xf $RESTORE_FILE && rm -fr $RESTORE_FILE >/dev/null
fi
echo "Done!!"
