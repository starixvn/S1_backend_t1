#!/usr/bin/env bash
# Description:
#     1. Backup log file name format <log file>_YYYY-mm-dd
#     2. Only keep last 7 days backup. Archive old files to logs/archive(create it if doesn't exist.)
#

#test root user
if [ $EUID -ne 0 ]; then
	echo please run on root
	exit 1
fi

# Define logs path
CURRENT_DIR="$(dirname $(readlink -f $0))"
LOG_DIR="$(dirname $CURRENT_DIR)/application/logs"
OLD_BACKUP_DIR="archive"

echo "`date` Start backup log files."

# Define backup file name
TIMESTAMP=`date +%Y-%m-%d`
FILE_NAME="old_logs"
BACKUP_FILE_NAME="${FILE_NAME}_$TIMESTAMP"
# Define LOGS for log file name.
LOGS[0]="app_debug.log"
LOGS[1]="command.log"
LOGS[2]="game_api.log"
LOGS[3]="payment_error.log"
LOGS[4]="queue_error.log"
LOGS[5]="refresh_session.log"
LOGS[6]="sync_balance_service.log"
LOGS[7]="sync_game_records_service.log"
LOGS[8]="webpushserver_stdout.log"
LOGS[9]="worker_stdout.log"
LOGS[10]="worker_stderr.log"
LOGS[11]="webpushserver_stderr.log"
LOGS[12]="log-????-??-??.php"
PHP_LOG_TODAY="log-${TIMESTAMP}.php"

# Package log files
cd $LOG_DIR
BACKUP_PACKAGE="$BACKUP_FILE_NAME.tar"

for log in ${LOGS[@]}; do
    if [ "$log" == "$PHP_LOG_TODAY" ]; then
        continue
    fi
    test -f $log && tar rf $BACKUP_PACKAGE $log --remove-files && echo "Add $log to $BACKUP_PACKAGE"
done

test -f $BACKUP_PACKAGE && gzip $BACKUP_PACKAGE && echo "Compress $BACKUP_PACKAGE"

# Define VALID_TIMESTAMP
VALID_TIMESTAMP=$(for i in `seq 0 6`; do date -d "$i days ago" +%Y-%m-%d; done)
for log_file in `ls $LOG_PATH | grep "^$FILE_NAME"` ; do
    file_create_date=${log_file#${FILE_NAME}_}
    EXPIRE_FLAG='Y'
    for valid_date in $VALID_TIMESTAMP; do
        if [[ "$file_create_date" =~ ^$valid_date.* ]]; then
            EXPIRE_FLAG='N'
            break
        fi
    done
    if [ $EXPIRE_FLAG == 'Y' ]; then
        test -d $OLD_BACKUP_DIR || mkdir -p $OLD_BACKUP_DIR
        mv $log_file "$OLD_BACKUP_DIR/"
        echo mv $log_file "$OLD_BACKUP_DIR/"
    fi
done
echo "`date` Complete"
