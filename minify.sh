#set HOME
#current file directory
OGHOME=$(dirname $( readlink -f $0 ))

OGADMIN_HOME=$OGHOME/admin

PHP_PATH=$(if [ -f /usr/bin/php7.2 ]; then echo /usr/bin/php7.2; elif [ -f /usr/bin/php7 ]; then echo /usr/bin/php7; elif [ -f /usr/local/bin/php ]; then echo /usr/local/bin/php; else echo php; fi)

$PHP_PATH $OGADMIN_HOME/public/index.php cli/command/createMinifyFile/player

# cd $OGHOME/api
# php artisan migrate
# cd $OGHOME
