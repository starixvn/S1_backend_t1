FROM ogbasephp7:latest

ENV CODE tripleonetech
ENV DOCKER_RUN php-fpm
ENV OG_USER vagrant
ENV OG_UID 1010

# RUN find /etc/apt/ -type f -exec sed -i 's/deb.debian.org/ftp.tw.debian.org/g' {} \;
RUN cp /usr/share/zoneinfo/Asia/Hong_Kong /etc/localtime

ADD docker/php.ini /usr/local/etc/php/php.ini

RUN useradd -ms /bin/bash $OG_USER -u $OG_UID && \
    echo "vagrant ALL=(ALL:ALL) NOPASSWD:ALL" >> /etc/sudoers.d/vagrant && \
    mkdir -p /home/vagrant/Code && \
    chown -R vagrant.vagrant /home/vagrant

ADD docker/nginx/ /usr/local/openresty/nginx/
WORKDIR /usr/local/openresty/nginx

ENV PATH="/usr/local/openresty/nginx/sbin:${PATH}"
#####
ADD docker/supervisor/ /root/
ADD docker/config/ /usr/local/etc/php-fpm.d/

ADD --chown=vagrant:vagrant . /home/vagrant/Code/og
RUN rm -f /home/vagrant/Code/og/admin/application/config/config_local.php && \
    rm -f /home/vagrant/Code/og/aff/application/config/config_local.php && \
    rm -f /home/vagrant/Code/og/agency/application/config/config_local.php && \
    rm -f /home/vagrant/Code/og/player/application/config/config_local.php && \
    ln -sf /home/vagrant/Code/config_local/admin-config-local.php /home/vagrant/Code/og/admin/application/config/config_local.php && \
    ln -sf /home/vagrant/Code/config_local/aff-config-local.php /home/vagrant/Code/og/aff/application/config/config_local.php && \
    ln -sf /home/vagrant/Code/config_local/agency-config-local.php /home/vagrant/Code/og/agency/application/config/config_local.php && \
    ln -sf /home/vagrant/Code/config_local/player-config-local.php /home/vagrant/Code/og/player/application/config/config_local.php && \
    mkdir -p /home/vagrant/Code/og/admin/application/logs && \
    mkdir -p /var/game_platform && \
    chmod 777 /var/game_platform

# RUN chown vagrant.vagrant /home/vagrant/Code -R
EXPOSE 80 443
ADD docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["og"]
