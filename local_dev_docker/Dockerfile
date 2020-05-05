# FROM php:5.6-fpm
FROM ogbasephp:dev-php7

ENV CODE tripleonetech
ENV DOCKER_RUN php-fpm
ENV OG_USER vagrant
ENV OG_UID 1010

# for local dev============
RUN apt-get update && \
    apt-get install -y \
    git \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    && rm -rf /var/lib/apt/lists/*
RUN pecl install xdebug \
    && rm -rf /tmp/*
ADD xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
# for local dev============

# RUN find /etc/apt/ -type f -exec sed -i 's/deb.debian.org/ftp.tw.debian.org/g' {} \;
RUN cp /usr/share/zoneinfo/Asia/Hong_Kong /etc/localtime

ADD php.ini /usr/local/etc/php/php.ini

RUN useradd -ms /bin/bash $OG_USER -u $OG_UID && \
    echo "vagrant ALL=(ALL:ALL) NOPASSWD:ALL" >> /etc/sudoers.d/vagrant && \
    mkdir -p /home/vagrant/Code && \
    chown -R vagrant.vagrant /home/vagrant

ADD nginx/ /usr/local/openresty/nginx/
WORKDIR /usr/local/openresty/nginx

ENV PATH="/usr/local/openresty/nginx/sbin:${PATH}"
#####
ADD supervisor/ /root/
ADD php-fpm-config/ /usr/local/etc/php-fpm.d/

# don't include code in local dev
# ADD --chown=vagrant:vagrant . /home/vagrant/Code/og
# RUN rm -f /home/vagrant/Code/og/admin/application/config/config_local.php && \
#     rm -f /home/vagrant/Code/og/aff/application/config/config_local.php && \
#     rm -f /home/vagrant/Code/og/agency/application/config/config_local.php && \
#     rm -f /home/vagrant/Code/og/player/application/config/config_local.php && \
#     ln -sf /home/vagrant/Code/config_local/admin-config-local.php /home/vagrant/Code/og/admin/application/config/config_local.php && \
#     ln -sf /home/vagrant/Code/config_local/aff-config-local.php /home/vagrant/Code/og/aff/application/config/config_local.php && \
#     ln -sf /home/vagrant/Code/config_local/agency-config-local.php /home/vagrant/Code/og/agency/application/config/config_local.php && \
#     ln -sf /home/vagrant/Code/config_local/player-config-local.php /home/vagrant/Code/og/player/application/config/config_local.php && \
#     mkdir -p /home/vagrant/Code/og/admin/application/logs && \
#     mkdir -p /var/game_platform && \
#     chmod 777 /var/game_platform

# RUN chown vagrant.vagrant /home/vagrant/Code -R
EXPOSE 80 443
ADD entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["og"]
