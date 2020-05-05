#!/usr/bin/env bash
# Descript: This script for aotomatic output csv from mysql SQL command.
# SYNOPSIS: mysql_result_to_csv.sh -d <database> -u <user> -e <SQL command file> [-t <store CSV directory>] [-h <mysql host name>] [-p <mysql password>]
#           -d: MySQL database name
#           -u: MySQL user name
#           -e: MySQL "select" SQL file
#           -h: MySQL host name
#           -p: MySQL password
#           -t: Translate's CSV file directory defualt use /tmp

function error_info {
    echo "$0 -d <database> -u <user> -e <SQL command file> [-t <store CSV directory>] [-h <mysql host name>] [-p <mysql password>]" >&2
    echo '-d: MySQL database name'
    echo '-u: MySQL user name'
    echo '-e: MySQL "select" SQL file'
    echo '-h: MySQL host name'
    echo '-p: MySQL password'
    echo "-t: Translate's CSV file directory defualt use /tmp"
}
while getopts ":d:u:h:p:t:e:" opt; do
    case $opt in
        d)
            MYSQL_DATABASE=$OPTARG
            ;;
        t)
            CSV_DIR=$OPTARG
            ;;
        u)
            MYSQL_USER=$OPTARG
            ;;
        h)
            MYSQL_HOST=$OPTARG
            ;;
        p)
            MYSQL_PASSWORD=$OPTARG
            ;;
        e)
            MYSQL_SQL_FILE=$OPTARG
            ;;
        \?)
            error_info
            ;;
    esac
done

if [ -z "$MYSQL_DATABASE" ] || [ -z "$MYSQL_USER" ] || [ -z "$MYSQL_SQL_FILE" ]; then
    echo "Argument error" >&2
    error_info
    exit 2
fi

if [ ! -z "$CSV_DIR" ] && [ -d $CSV_DIR ]; then
    echo "Set directory $CSV_DIR"
else
  CSV_DIR="/tmp"
fi

if [ -z "$MYSQL_HOST" ]; then
    MYSQL_HOST="localhost"
fi

while true;
do
    TIMESTAMP=`date +%Y_%m_%d_%h_%m_%s`
    OUTFILE_NAME=`readlink -f "$CSV_DIR/${TIMESTAMP}.csv"`
    echo "INFO: Generate temporary csv file name '$OUTFILE_NAME'"
    if [ -f $OUTFILE_NAME ]; then
        echo "Warning: $OUTFILE_NAME exist, regenerate file name."
        sleep 1
    else
        break
    fi
done
if [ -z "$MYSQL_PASSWORD" ]; then
    mysql -u $MYSQL_USER $MYSQL_DATABASE -h $MYSQL_HOST -e "`cat $MYSQL_SQL_FILE`" > $OUTFILE_NAME && sed -i 's/^/"/' $OUTFILE_NAME && sed -i 's/$/"/' $OUTFILE_NAME && sed -i 's/\t/","/g' $OUTFILE_NAME
else
    mysql -u $MYSQL_USER $MYSQL_DATABASE -h $MYSQL_HOST -p$MYSQL_PASSWORD -e "`cat $MYSQL_SQL_FILE`" > $OUTFILE_NAME && sed -i 's/^/"/' $OUTFILE_NAME && sed -i 's/$/"/' $OUTFILE_NAME && sed -i 's/\t/","/g' $OUTFILE_NAME
fi

if [ $? == 0 ]; then
    echo "INFO: Generate $OUTFILE_NAME complete."
fi
