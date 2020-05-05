#!/usr/bin/env bash
# Purpose: Backup live web site pages to cloud.
# Descript: Compress live web site
#           and check md5 with last backup file if anything changed,
#           upload to rackspace cloud never delete backup file.
# SYNOPSIS: upload_to_rackspace.sh <directory> <site name>

# Parse arguments
if [ ! -z "$1" ]; then
    if [ -d "$1" ]; then
        FULL_BACKUP_DIR=`readlink -f $1`
    else
        echo "$0 <directory> <site name>"
        exit 1
    fi
else
    FULL_BACKUP_DIR=`readlink -f /home/sftp/live`
fi

if [ ! -z "$2" ]; then
    SITE_NAME="$2"
else
    SITE_NAME=$(hostname)
fi

# Define compress format
COMPRESS_SUFFIX="tar.bz2"
COMPRESS_ARGS="jcf"
CONTENT_TYPE_XZ="application/x-xz"
CONTENT_TYPE_BZIP="application/x-bzip"
COUNT=5
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

RS_BACKUP_DIR='og'
RS_CONTAINER='auto_backup'

TIMESTAMP=`date +%Y%m%d%H%M`
BACKUP_PARENT_DIR=`dirname $FULL_BACKUP_DIR`
BACKUP_DIR=`echo $FULL_BACKUP_DIR | awk -F/ '{print $NF }'`
BACKUP_FILENAME=${BACKUP_DIR}_$TIMESTAMP.$COMPRESS_SUFFIX
TEMP_DIR="/tmp"
mkdir $TEMP_DIR >/dev/null 2>&1
FULL_BACKUP_FILENAME=$TEMP_DIR/$BACKUP_FILENAME
echo "backup file: $FULL_BACKUP_FILENAME"

# Drop upload flag
UPLOAD_FLAG="N"

# Prepare upload archive file
cd $BACKUP_PARENT_DIR
tar $COMPRESS_ARGS $FULL_BACKUP_FILENAME $BACKUP_DIR
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
OBJECT_STORE_HKG=`echo $JSON | grep -Po '"serviceCatalog".*endpoints".*?].*?]' | sed 's/"serviceCatalog"://' | grep -Po '{.*?endpoints.*?].*?}' | grep object-store | grep -Po "{.*?}" | sed 's/.*endpoints":\[//' | grep 'HKG'`
PUBLIC_URL=`echo $OBJECT_STORE_HKG | grep -Po '"publicURL":".*?"' | grep -Po '"http.*?"'`
ENDPOINT=$(echo ${PUBLIC_URL:1:-1} | sed 's/\\//g')
echo "Token: $TOKEN"
echo "Endpoint: $ENDPOINT"

# Check md5 checksum with last backup file
# Download file list
INSPECT_DIR_JSON=`curl -i -X GET $ENDPOINT/$RS_CONTAINER -H "X-Auth-Token: $TOKEN" -H "Accept: application/json"`

IFS_BK=$IFS
IFS=$'\n'

# Get last upload item
last_modified_time=0
last_modified_item=""

# Found out last data
cloud_files=()
for item in `echo $INSPECT_DIR_JSON | grep -Po '{.*?}'`;
do
    rs_file_path=`echo $item | grep -Po '"name":.*?".*?"' | grep -Po '".*?"' | grep -v '"name"' | sed 's/"//g'` 
    if [[ `echo $rs_file_path | grep -Po '/' | wc -l ` -le 1 ]]; then
        continue
    fi
    site=`echo $rs_file_path | awk -F/ '{print $(NF-1)}'`
    if [[ "$site" != "$SITE_NAME" ]]; then
        # Bypass check other's site data
        continue
    fi
    modified_time=$(date --date=`echo $item | grep -Po '"last_modified":.*?".*?"' | grep -Po '".*?"' | grep -v last_modified | sed 's/"//g'` +%s)
    cloud_files+=(`echo $item | grep -Po '"name":.+?(?=,)' | sed 's/"name":\s\+//' | sed 's/"//g'`)
    if [[ $modified_time -gt $last_modified_time ]]; then
        last_modified_time=$modified_time
        item_last_modified=`echo $item | grep -Po '"last_modified":.*?".*?"' `
        item_hash=`echo $item | grep -Po '"hash":.*?".*?"'`
        item_name=`echo $item | grep -Po '"name":.*?".*?"'`
        last_modified_item="$item_last_modified $item_hash $item_name\n"
    fi
done
IFS=$IFS_BK

# Than md5 checksum with last upload file
same_hash=`echo $last_modified_item | grep $BACKUP_FILE_MD5`

if [ ! -z "$same_hash" ]; then
    UPLOAD_FLAG="Y"
    echo -n -e "Md5 checksum match last file:\n --- $same_hash"
    echo "With last upload rackspace file same."
fi

count=0
if [[ $UPLOAD_FLAG == "N" ]]; then
    # Upload object
    echo "Uploading"
    target_url=$ENDPOINT/$RS_CONTAINER/$RS_BACKUP_DIR/$SITE_NAME/$BACKUP_FILENAME
    curl -i -X PUT -T $FULL_BACKUP_FILENAME $target_url -H "X-Auth-Token: $TOKEN" -H "Content-Type: $CONTENT_TYPE_BZIP"
    if [[ $? == 0 ]]; then
        echo "Upload complete file \"$BACKUP_FILENAME\" into \"$target_url\""
        count=$(expr $count + 1)
    else
        echo "Upload failed." >&2
    fi
fi

# Clean temporary files
rm -f $FULL_BACKUP_FILENAME

# delete old files

IFS=$'\n'
sorted_cloud_files=($(sort -r <<<"${cloud_files[*]}"))
for filename in ${sorted_cloud_files[@]}; do
    count=$(expr $count + 1)
    echo "$count: $filename"
    if [ $count -gt $COUNT ]; then
        echo "delete $ENDPOINT/$RS_CONTAINER/$filename"
        curl -X DELETE -H "X-Auth-Token: $TOKEN" $ENDPOINT/$RS_CONTAINER/$filename
    fi
done
