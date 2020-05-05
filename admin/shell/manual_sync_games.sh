#!/bin/bash

#test root user
if [ $EUID -ne 0 ]; then
    echo please run on root
    exit 1 
fi

if [ -z "$1" ] || [ -z "$2" ] || [ -z "$3" ]  ; then
    echo "please include parameters"
    echo "pattern: ./manual_sync_games.sh <label> <from_datetime> <to_datetime> <selected_game> <timelimit>"
    echo "ex: SELECTED GAMES: ./manual_sync_games.sh 'clientname' '2018-01-01 00:00:00' '2018-01-31 23:59:59'  '2 3 7' "
    echo "ex: SELECTED GAMES WITH TIMELIMIT: ./sync_games.sh 'clientname' '2018-01-01 00:00:00' '2018-01-31 23:59:59'  '2 3 7' 60 "
    echo "ex: ALL GAMES: ./manual_sync_games.sh 'clientname' '2018-01-01 00:00:00' '2018-01-31 23:59:59'  'all' "
    echo "ex: ALL GAMES WITH TIME LIMIT: ./manual_sync_games.sh 'clientname' '2018-01-01 00:00:00' '2018-01-31 23:59:59' 'all' 60 "
    exit 1
fi

if [ "$#" -gt 5 ]; then
    echo "please review your parameters- too many"
    exit 1
fi


FROM=$2
TO=$3
GAME_PLATPORM_IDS_STR=""
#STATUSES=""


SHELL_DIR="$(dirname $(readlink -f $0))"
OG_BASEPATH="$SHELL_DIR/."
#echo "$(dirname $(readlink -f $0))"

#CODE_DIR=$(dirname $(readlink -f $0) | rev | cut -d'/' -f4- | rev)
#CODE_DIR=$(dirname $(readlink -f $0) | sed 's/\(Code\).*/\1/g')
# LOG_DIR="$CODE_DIR/manual_sync_report"
# echo $LOG_DIR
# if [ ! -d "$LOG_DIR" ]; then
#      mkdir $LOG_DIR
#      echo 'LOG_DIR has been created'
# fi


#echo $SHELL_DIR | rev | cut -d'/' -f3- | rev

PHP_PATH=$(if [ -f /usr/bin/php7.2 ]; then echo /usr/bin/php7.2; else echo /usr/bin/php; fi)

GAME_PLATPORM_IDS_STR=$($PHP_PATH $SHELL_DIR/ci_cli.php "cli/command/getAllActiveGameApiIds")

if [ -n "$4" ] && [ "$4" != "all" ];
then
     GAME_PLATPORM_IDS_STR=$4
fi

# echo "GAME PLATFORMS " $GAME_PLATPORM_IDS_STR
# echo
GAMEIDS_STR=$(echo $GAME_PLATPORM_IDS_STR | tr -s ' ' | tr ' ' '_')

#LOG_FILE="$LOG_DIR/manual_sync_$1_${GAMEIDS_STR}_$(date +%Y-%m-%d_%H_%M).log"

# echo "============================================START MANUAL SYNC==========================================================" >> $LOG_FILE
# echo "GAME SYNC OUTPUT FILE: $LOG_FILE "  >> $LOG_FILE
# echo "EXECUTE PATH: $SHELL_DIR "  >> $LOG_FILE
# echo "GAME PLATFORMS SYNCED: $GAME_PLATPORM_IDS_STR"  >> $LOG_FILE
echo "============================================START MANUAL SYNC==========================================================" 
echo "EXECUTE PATH: $SHELL_DIR " 
echo "GAME PLATFORMS : $GAME_PLATPORM_IDS_STR" 
exec_path=$(echo $SHELL_DIR | sed -e 's/\//\&\#47;/g')
user="manual-sync"
channel="manual_sync"
label=$1
#echo "" >> $LOG_FILE

for i in ${GAME_PLATPORM_IDS_STR};
do  
    if [ -n "$4" ] && [ -n "$5" ]; 
        then
           #echo "Time executed:"  `date "+%Y-%m-%d %H:%M:%S"` >> $LOG_FILE
           #echo "$OG_BASEPATH/command.sh rebuild_single_game_by_timelimit $i $FROM  $TO $5 "  >> $LOG_FILE
           current_platform="$i"
           sync_command="rebuild_single_game_by_timelimit"
           time_executed=$(date "+%Y-%m-%d %H:%M:%S")
           notif_type="info"
           notif_message=" :information_source: $label &#13;&#10;PlatformIDs: $GAME_PLATPORM_IDS_STR &#13;&#10;Syncing: $i &#13;&#10;Path: $exec_path   &#13;&#10;Command: $sync_command $i $FROM $TO $5 &#13;&#10;Time executed: $time_executed "
           $PHP_PATH $SHELL_DIR/ci_cli.php "cli/command/sendNotificationToMattermost" "$user" "$channel" "$notif_message" "$notif_type" 
           sudo ./command.sh rebuild_single_game_by_timelimit  "$i"  "$FROM" "$TO" "$5"
        else
           #echo "Time executed:"  `date "+%Y-%m-%d %H:%M:%S"` >> $LOG_FILE
           #echo "sudo bash $OG_BASEPATH/command.sh rebuild_single_game_by_timelimit $i $FROM  $TO "  >> $LOG_FILE
           sync_command="rebuild_single_game_by_timelimit"
           time_executed=$(date "+%Y-%m-%d %H:%M:%S")
           notif_type="info"
           notif_message=" :information_source: $label &#13;&#10;PlatformIDs: $GAME_PLATPORM_IDS_STR &#13;&#10;Syncing: $i &#13;&#10;Path: $exec_path   &#13;&#10;Command: $sync_command $i $FROM $TO  &#13;&#10;Time executed: $time_executed "
           $PHP_PATH $SHELL_DIR/ci_cli.php "cli/command/sendNotificationToMattermost" "$user" "$channel" "$notif_message" "$notif_type" 
           sudo ./command.sh rebuild_single_game_by_timelimit  "$i"  "$FROM" "$TO"
    fi 
    # echo "Time executed:"  `date "+%Y-%m-%d %H:%M:%S"` >> $LOG_FILE
    # echo "sudo bash $OG_BASEPATH/command.sh sync_merge_game_logs_by_timelimit $i $FROM  $TO"  >> $LOG_FILE
    sync_command="sync_merge_game_logs_by_timelimit"
    time_executed=$(date "+%Y-%m-%d %H:%M:%S")
    notif_type="info"
    notif_message=" :information_source: $label &#13;&#10;PlatformIDs: $GAME_PLATPORM_IDS_STR &#13;&#10;Syncing: $i &#13;&#10;Path: $exec_path    &#13;&#10;Command: $sync_command $i $FROM $TO   &#13;&#10;Time executed: $time_executed "
    $PHP_PATH $SHELL_DIR/ci_cli.php "cli/command/sendNotificationToMattermost" "$user" "$channel" "$notif_message" "$notif_type" 
    sudo ./command.sh sync_merge_game_logs_by_timelimit "$i"  "$FROM" "$TO" 
done
wait
 # echo "Time executed:"  `date "+%Y-%m-%d %H:%M:%S"` >> $LOG_FILE
 # echo "sudo bash $OG_BASEPATH/command.sh rebuild_totals $FROM  $TO"  >> $LOG_FILE
 # echo "Time end:"  `date "+%Y-%m-%d %H:%M:%S"`  >> $LOG_FILE
 echo "Time executed:"  `date "+%Y-%m-%d %H:%M:%S"` 
 echo "sudo bash $OG_BASEPATH/command.sh rebuild_totals $FROM  $TO true true"
 echo "Time end:"  `date "+%Y-%m-%d %H:%M:%S"` 
 sync_command="rebuild_totals"
 time_executed=$(date "+%Y-%m-%d %H:%M:%S")
 notif_type="info"
 notif_message=" :information_source: $label &#13;&#10;PlatformIDs: $GAME_PLATPORM_IDS_STR &#13;&#10;Path: $exec_path    &#13;&#10;Command: $sync_command $FROM $TO true true  &#13;&#10;Time executed: $time_executed "
 $PHP_PATH $SHELL_DIR/ci_cli.php "cli/command/sendNotificationToMattermost" "$user" "$channel" "$notif_message" "$notif_type" 
 sudo ./command.sh rebuild_totals  "$FROM" "$TO" true true
 
 notif_message=":information_source: $label &#13;&#10;PlatformIDs: $GAME_PLATPORM_IDS_STR &#13;&#10;Path: $exec_path &#13;&#10;Time range: $FROM TO $TO &#13;&#10; :point_right: Resync Finished--------------------------------------------------------"
 $PHP_PATH $SHELL_DIR/ci_cli.php "cli/command/sendNotificationToMattermost" "$user" "$channel" "$notif_message" "$notif_type" 
 

# echo "GAME SYNC OUTPUT FILE: $LOG_FILE " 
# echo "EXECUTE PATH: $SHELL_DIR "  
# echo "GAME PLATFORMS SYNCED: $GAME_PLATPORM_IDS_STR" 
# echo "============================================END MANUAL SYNC==========================================================" >> $LOG_FILE
echo "EXECUTE PATH: $SHELL_DIR "  
echo "GAME PLATFORMS SYNCED: $GAME_PLATPORM_IDS_STR" 
echo "============================================END MANUAL SYNC==========================================================" 






