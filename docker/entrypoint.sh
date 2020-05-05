#!/usr/bin/env bash
set -x

if [ "$1" == 'og' ]; then

    # change logs permission
    chmod a+w -R /home/vagrant/Code/og/admin/application/logs

    service cron stop
    service ntp start

    su - vagrant -c "cd /home/vagrant/Code/og; ./create_links.sh; exit 0"
    THISHOST=`hostname`
    NGINXLOGDIR="/var/game_platform/nginx"
    NGINX_ERROR_LOG="/var/game_platform/nginx/nginx-error-$THISHOST.log"
    NGINX_ACCESS_LOG="/var/game_platform/nginx/nginx-access-$THISHOST.log"
    NGINX_STDOUT_LOG="/var/game_platform/nginx/nginx-stdout-$THISHOST.log"
    NGINX_STDERR_LOG="/var/game_platform/nginx/nginx-stderr-$THISHOST.log"
    mkdir -p $NGINXLOGDIR
    chmod a+w $NGINXLOGDIR
    touch $NGINX_ERROR_LOG
    chmod a+w $NGINX_ERROR_LOG
    touch $NGINX_ACCESS_LOG
    chmod a+w $NGINX_ACCESS_LOG
    touch $NGINX_STDOUT_LOG
    chmod a+w $NGINX_STDOUT_LOG
    touch $NGINX_STDERR_LOG
    chmod a+w $NGINX_STDERR_LOG
    ln -s $NGINX_ERROR_LOG /var/log/nginx-error.log
    ln -s $NGINX_ACCESS_LOG /var/log/nginx-access.log
    ln -s $NGINX_STDOUT_LOG /var/log/nginx-stdout.log
    ln -s $NGINX_STDERR_LOG /var/log/nginx-stderr.log

    PHPLOGDIR="/var/game_platform/php"
    PHP_ERROR_LOG="$PHPLOGDIR/php-error-$THISHOST.log"
    PHP_FPM_STDOUT_LOG="$PHPLOGDIR/fpm-stdout-$THISHOST.log"
    PHP_FPM_STDERR_LOG="$PHPLOGDIR/fpm-stderr-$THISHOST.log"
    PHP_WWW_SLOW_LOG="$PHPLOGDIR/slow-www-$THISHOST.log"
    PHP_ADMIN_SLOW_LOG="$PHPLOGDIR/slow-admin-$THISHOST.log"
    mkdir -p $PHPLOGDIR
    chmod a+w $PHPLOGDIR
    touch $PHP_ERROR_LOG
    chmod a+w $PHP_ERROR_LOG
    touch $PHP_FPM_STDOUT_LOG
    chmod a+w $PHP_FPM_STDOUT_LOG
    touch $PHP_FPM_STDERR_LOG
    chmod a+w $PHP_FPM_STDERR_LOG
    touch $PHP_WWW_SLOW_LOG
    chmod a+w $PHP_WWW_SLOW_LOG
    touch $PHP_ADMIN_SLOW_LOG
    chmod a+w $PHP_ADMIN_SLOW_LOG

    ln -s $PHP_ERROR_LOG /var/log/php-error.log
    ln -s $PHP_FPM_STDOUT_LOG /var/log/fpm-stdout.log
    ln -s $PHP_FPM_STDERR_LOG /var/log/fpm-stderr.log
    ln -s $PHP_WWW_SLOW_LOG /var/log/slow-www.log
    ln -s $PHP_ADMIN_SLOW_LOG /var/log/slow-admin.log

    if [ "$DOCKER_RUN" == "php-fpm" ]; then
        cp /root/php-fpm.conf /etc/supervisor/conf.d/
        cp /root/nginx.conf /etc/supervisor/conf.d/
    fi
    while /bin/true; do
        /usr/bin/supervisord -c /etc/supervisor/supervisord.conf -n
    done
fi

exec "$@"
