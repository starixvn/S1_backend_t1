#set HOME
#current file directory
OGHOME=$(dirname $( readlink -f $0 ))

OGADMIN_HOME=$OGHOME/admin

PHP_PATH=$(if [ -f /usr/bin/php7.2 ]; then echo /usr/bin/php7.2; else echo /usr/bin/php; fi)

echo $PHP_PATH

$PHP_PATH $OGADMIN_HOME/public/index.php cli/migrate
$PHP_PATH $OGADMIN_HOME/public/index.php cli/command/incrementCMSVersion
#$PHP_PATH $OGADMIN_HOME/public/index.php cli/command/clearCache

# cd $OGHOME/api
# php artisan migrate
# cd $OGHOME
