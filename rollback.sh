#set HOME
#current file directory
OGHOME=$(dirname $( readlink -f $0 ))

OGADMIN_HOME=$OGHOME/admin

php admin/public/index.php cli/migrate rollback $1
