#!/usr/bin/env bash
# Purpose: Archive old game log files
# Descript: mg log check file modify date if old UNHANDLE_DAYS ago date archive.
#           game log check directory name.

if [ $EUID -ne 0 ]; then
    echo "Please run on root" >&2
    exit 1
fi

LOG_DIR="/var/game_platform"
LOG_ARCHIVE_DIR="/var/game_platform/archive"
GAME_LOG_DIR="/var/log/response_results"
UNHANDLE_DAYS=5
TAR_ARGS="zcf"
TAR_EXTENSION="tar.gz"
UNHANDLE_DATES=$(for i in $(seq 0 $UNHANDLE_DAYS); do date -d "$i days ago" +"%Y-%m-%d";done)

echo "Follow dates not handle"
echo $UNHANDLE_DATES

# Check and handle GAME_LOG_DIR

if [ -d $GAME_LOG_DIR ]; then
    echo "change directory $GACE_LOG_DIR"
    cd $GAME_LOG_DIR
    pwd
    for list_content in $(ls); do
        if [ ! -d $list_content ]; then continue; fi
        file_modify_day=$(ls -ld --time-style=long-iso $list_content | awk '{print $6}')
        file_name=$(ls -ld --time-style=long-iso $list_content | awk '{print $8}')
        unhandle_flag="N"
        for unhandle_d in $UNHANDLE_DATES; do
            if [ "$unhandle_d" == "$file_modify_day" ]; then
                unhandle_flag="Y"
                break
            fi
        done
        if [ "$unhandle_flag" == "N" ]; then
            tar_file="$GAME_LOG_DIR/$file_name.$TAR_EXTENSION"
            if [ ! -f $tar_file ]; then
                echo "tar $TAR_ARGS $tar_file $file_name && rm -fr $file_name"
                tar $TAR_ARGS $tar_file $file_name && rm -fr $file_name
            else
                tar_file="${tar_file}_$(date +%s)"
                echo "tar $TAR_ARGS $tar_file $file_name && rm -fr $file_name"
                tar $TAR_ARGS $tar_file $file_name && rm -fr $file_name
            fi
        fi
    done
fi

# Check and handle LOG_DIR
if [ -d $LOG_DIR ]; then
    created_dirs=()
    if [ ! -d $LOG_ARCHIVE_DIR ]; then
        mkdir $LOG_ARCHIVE_DIR
    fi
    echo "Change directory"
    cd $LOG_DIR
    pwd
    handle_files=$(ls -l --time-style=long-iso | egrep -v "$(echo $UNHANDLE_DATES | sed 's/ /|/g')" | awk '{print $8}' | sort | uniq)
    echo "mv $LOG_ARCHIVE_DIR"
    for handle_f in $handle_files; do
        if [ -d $handle_f ]; then continue; fi
        file_date=$(ls -ld --time-style=long-iso $handle_f | awk '{print $6}')
        archive_dir="$LOG_ARCHIVE_DIR/$file_date"
        if [ ! -d $archive_dir ]; then
            mkdir $archive_dir
            created_dirs+=($archive_dir)
        fi
        mv $handle_f $archive_dir
    done
    echo "Change directory"
    cd $LOG_ARCHIVE_DIR
    pwd
    for dir in ${created_dirs[@]}; do
        echo "tar $TAR_ARGS $dir.$TAR_EXTENSION $(basename $dir) && rm -fr $dir"
        tar $TAR_ARGS $dir.$TAR_EXTENSION $(basename $dir) && rm -fr $dir
    done
fi
