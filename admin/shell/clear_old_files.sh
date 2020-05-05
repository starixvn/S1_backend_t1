
FILE_HOME=$(dirname $( readlink -f $0 ))

REPORTS_DIR="$FILE_HOME/../../../pub/reports"
echo "delete file from $REPORTS_DIR"

if [ -d $REPORTS_DIR ]; then
  find $REPORTS_DIR -mtime +2 -type f -exec rm -f {} \;
else
  echo "lost $REPORTS_DIR"
fi

TMP_CLOCKWORK="/tmp/clockwork/"
echo "delete file from $TMP_CLOCKWORK"
if [ -d $REPORTS_DIR ]; then
  find $TMP_CLOCKWORK -mtime +2 -type f -exec rm -f {} \;
else
  echo "lost $TMP_CLOCKWORK"
fi
