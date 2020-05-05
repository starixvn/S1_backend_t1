#!/usr/bin/env bash
# Purpose: Backup database file to database
# Descript: Specify a file upload it
# SYNOPSIS: upload_file_to_rackspace.sh <directory> <site name>

# Parse arguments
if [ -f "$1" ]; then
    UPLOAD_FILE=`readlink -f $1`
    RS_LOCATION="$(hostname)/$(basename $UPLOAD_FILE)"
else
    echo "$0 <file name> <rackspace location> [count]"
    exit 2
fi

if [ -z "$3" ];then
    COUNT=5
else
    COUNT=$3
fi
# Define compress format
CONTENT_TYPE_BZIP="application/x-bzip"

# Setup some directories and files
WORK_DIR=$(dirname $(readlink -f $0))
SECRET_FILE='rackspace'
SECRET_DIR=`readlink -f $WORK_DIR/../../secret_keys`

if [ ! -d $SECRET_DIR ]; then
    SECRET_DIR=`readlink -f ~/Code/og/secret_keys`
fi

SECRET_FILE_PATH=$SECRET_DIR/$SECRET_FILE
RS_USER=`cat $SECRET_FILE_PATH | awk -F':=' '{print $1}'`
RS_APIKEY=`cat $SECRET_FILE_PATH | awk -F':=' '{print $2}'`

if [ -z $RS_USER ] || [ -z $RS_APIKEY ]; then
    echo "Rackspace secret error." >&2
    exit 2
fi

RS_BACKUP_DIR='syd'
RS_CONTAINER='syd'

FULL_BACKUP_FILENAME=$(readlink -f $UPLOAD_FILE)
echo "backup file: $FULL_BACKUP_FILENAME"

# Drop upload flag
UPLOAD_FLAG="N"

# Prepare upload archive file
cd $WORK_DIR
BACKUP_FILE_MD5=`md5sum $FULL_BACKUP_FILENAME | awk '{print $1}'`

# Get rackspace token and target URL
JSON=`curl -s -X POST https://identity.api.rackspacecloud.com/v2.0/tokens \
    -H "Content-Type: application/json" \
    -d '{
        "auth": {
            "RAX-KSKEY:apiKeyCredentials": {
                "username": "'$RS_USER'",
                "apiKey": "'$RS_APIKEY'"
            }
        }
    }'`
TOKEN=`echo $JSON | grep -P -o    '"token".*?id":"(.*?)"' | sed    's/.*id":"\(.*\)"/\1/'`
OBJECT_STORE_HKG=`echo $JSON | grep -Po '"serviceCatalog".*endpoints".*?].*?]' | sed 's/"serviceCatalog"://' | grep -Po '{.*?endpoints.*?].*?}' | grep object-store | grep -Po "{.*?}" | sed 's/.*endpoints":\[//' | grep 'SYD'`
PUBLIC_URL=`echo $OBJECT_STORE_HKG | grep -Po '"publicURL":".*?"' | grep -Po '"http.*?"'`
ENDPOINT=$(echo ${PUBLIC_URL:1:-1} | sed 's/\\//g')
echo "Token: $TOKEN"
echo "Endpoint: $ENDPOINT"

# Check md5 checksum with last backup file
# Download file list
INSPECT_DIR_JSON=`curl -i -X GET $ENDPOINT/$RS_CONTAINER -H "X-Auth-Token: $TOKEN" -H "Accept: application/json"`

IFS_BK=$IFS
IFS=$'\n'

# Found out filename
files=()
for item in `echo $INSPECT_DIR_JSON | grep -Po '{.*?}'`;
do
    file=$(echo $item | grep -Po '"name":.*?".*?"'| awk '{print $2}'| sed 's/"//g')
    host=$(echo $file | awk -F/ '{print $1}')
    if [ "$host" == "$(hostname)" ]; then
        if [ "$(basename $file)" == "$(basename $RS_LOCATION)" ]; then
            echo "ignore repeat"
            exit 0
        fi 
        echo $file
        files+=($file)
    fi
done

# Sorted files
IFS=$'\n'
sorted_files=($(sort -r <<<"${files[*]}"))
unset IFS

count=0
for filename in ${sorted_files[@]}; do
    count=$(expr $count + 1)
    echo $filename
    if [ $count > $COUNT ]; then
        echo "delete $ENDPOINT/$RS_CONTAINER/$filename"
        curl -X DELETE -H "X-Auth-Token: $TOKEN" $ENDPOINT/$RS_CONTAINER/$filename

    fi
done

if [[ $UPLOAD_FLAG == "N" ]]; then
    # Upload object
    echo "Uploading"
    target_url=$ENDPOINT/$RS_CONTAINER/$RS_LOCATION
    curl -i -X PUT -T $FULL_BACKUP_FILENAME $target_url -H "X-Auth-Token: $TOKEN" -H "Content-Type: $CONTENT_TYPE_BZIP"
    if [[ $? == 0 ]]; then
        echo "Upload complete file \"$BACKUP_FILENAME\" into \"$target_url\""
    else
        echo "Upload failed." >&2
    fi
fi
