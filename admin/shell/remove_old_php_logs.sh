#!/usr/bin/env bash
# Description:
#     1. remove log-YYYY-mm-dd.php big than 7 days
#

#test root user
if [ $EUID -ne 0 ]; then
	echo please run on root
	exit 1
fi

# Define logs path
CURRENT_DIR="$(dirname $(readlink -f $0))"
LOG_DIR="$(dirname $CURRENT_DIR)/application/logs"

echo "`date` Start remove old log files."

file_pattern='^log-\d\d\d\d-\d\d-\d\d.php'
date_pattern='\d\d\d\d-\d\d-\d\d'

# Define VALID_TIMESTAMP
VALID_TIMESTAMP=$(for i in `seq 0 6`; do date -d "$i days ago" +%Y-%m-%d; done)
cd $LOG_DIR
for log_file in `ls $LOG_DIR | grep -P "$file_pattern"` ; do
    file_create_date=`echo $log_file | grep -Po "$date_pattern"`
    EXPIRE_FLAG='Y'
    for valid_date in $VALID_TIMESTAMP; do
        if [[ "$file_create_date" =~ ^$valid_date.* ]]; then
            EXPIRE_FLAG='N'
            break
        fi
    done
    if [ $EXPIRE_FLAG == 'Y' ]; then
        [ "$log_file" != "/" ] && [ "$log_file" != "" ] && rm -f $log_file
    fi
done
echo "`date` Complete"
