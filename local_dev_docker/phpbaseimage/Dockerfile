FROM php:7.2.24-fpm-buster

ENV CODE tripleonetech
ENV DOCKER_RUN php-fpm
ENV OG_USER vagrant
ENV OG_UID 1010

RUN cp /usr/share/zoneinfo/Asia/Hong_Kong /etc/localtime

# set language environment
# RUN (apt-get update && apt-get install -y locales) || (find /etc/apt/ -type f -exec sed -i 's/deb.debian.org/ftp.tw.debian.org/g' {} \; && apt-get update && apt-get install -y locales)
# use tw mirror and remove old repo
# RUN (find /etc/apt/ -type f -exec sed -i '/jessie-updates/d' {} \; && find /etc/apt/ -type f -exec sed -i 's/deb.debian.org/ftp.tw.debian.org/g' {} \; && apt-get update && apt-get install -y locales)
# ENV LANG en_US.UTF-8
# ENV LANGUAGE en_US.UTF-8
# RUN locale-gen en_US.UTF-8
# ADD docker/php.ini /usr/local/etc/php/php.ini
RUN apt-get update && apt-get install -y \
        locales \
        apt-utils \
        libfreetype6-dev \
        libjpeg-dev \
        libmcrypt-dev \
        libpng-dev \
        libcurl4-openssl-dev \
        libxml2-dev \
        libxslt1-dev \
        libzip-dev \
        # libgearman-dev \
        libmemcached-dev \
        # libv8-dev \
        make \
        gcc \
        libssl-dev \
        libperl4-corelibs-perl \
        libpcre3-dev \
        dh-autoreconf \
        libgeoip-dev \
        net-tools \
        vim \
        sudo \
        # gearman-job-server \
        # gearman-tools \
        cron \
        # logrotate \
        git \
        ntp \
        supervisor \
        wget \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    && rm -rf /var/lib/apt/lists/*

ENV LANG en_US.UTF-8
ENV LANGUAGE en_US.UTF-8
RUN locale-gen en_US.UTF-8
RUN chmod a+w /var/log

#install php mod
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd mysqli curl xmlrpc xsl soap zip xml mbstring opcache bcmath \
    && rm -rf /tmp/*

#install from pecl
RUN pecl install mcrypt-1.0.3 && docker-php-ext-enable mcrypt \
    && pecl install memcached && docker-php-ext-enable memcached \
    && pecl install redis && docker-php-ext-enable redis \
    # && pecl install v8js && docker-php-ext-enable v8js
    # && pecl install swoole && docker-php-ext-enable swoole
    && rm -rf /tmp/*

#overwrite openssl.cnf
ADD openssl.cnf /etc/ssl/openssl.cnf

# RUN apt-get install -y git patchelf python libglib2.0-dev build-essential
# RUN cd /tmp \
#     \
#     && git clone https://chromium.googlesource.com/chromium/tools/depot_tools.git --progress --verbose \
#     && export PATH="$PATH:/tmp/depot_tools" \
#     \
#     && fetch v8 \
#     && cd v8 \
#     && git checkout 7.4.195 \
#     && gclient sync \
#     \
#     && tools/dev/v8gen.py -vv x64.release -- \
#     binutils_path=\"/usr/bin\" \
#     target_os=\"linux\" \
#     target_cpu=\"x64\" \
#     v8_target_cpu=\"x64\" \
#     v8_use_external_startup_data=false \
#     is_official_build=true \
#     is_component_build=true \
#     is_cfi=false \
#     is_clang=false \
#     use_custom_libcxx=false \
#     use_sysroot=false \
#     use_gold=false \
#     use_allocator_shim=false \
#     treat_warnings_as_errors=false \
#     symbol_level=0 \
#     \
#     && ninja -C out.gn/x64.release/ \
#     && mkdir -p /opt/libv8-7.4/lib \
#     && mkdir /opt/libv8-7.4/include \
#     && cp out.gn/x64.release/lib*.so out.gn/x64.release/icudtl.dat \
#     /opt/libv8-7.4/lib/ \
#     && cp -R include/* /opt/libv8-7.4/include/ \
#     && for A in /opt/libv8-7.4/lib/*.so; do patchelf --set-rpath '$ORIGIN' $A;done \
#     \
#     && cd /tmp \
#     && git clone https://github.com/phpv8/v8js.git \
#     && cd v8js \
#     && git checkout php7 \
#     && phpize \
#     && ./configure --with-v8js=/opt/libv8-7.4 \
#     && export NO_INTERACTION=1 \
#     && make \
#     && make test \
#     && make install \
#     && docker-php-ext-enable v8js

# Create vagrant User
# RUN apt-get update && \
#  apt-get install -y \
#     sudo \
#     gearman-job-server \
#     gearman-tools \
#     cron \
#     logrotate \
#     git \
#     wget \
#     supervisor
    # useradd -ms /bin/bash $OG_USER -u $OG_UID && \
    # echo "vagrant ALL=(ALL:ALL) NOPASSWD:ALL" >> /etc/sudoers.d/vagrant && \
    # mkdir -p /home/vagrant/Code && \
    # chown -R vagrant.vagrant /home/vagrant
##### openresty

# install openrestry
ADD libmaxminddb /tmp/libmaxminddb
ADD ngx_http_geoip2_module /usr/src/ngx_http_geoip2_module
RUN wget https://openresty.org/download/openresty-1.15.8.1.tar.gz -O /tmp/openresty-1.15.8.1.tar.gz && \
    cd /tmp/libmaxminddb && chmod +x bootstrap t/compile_c++_t.pl t/mmdblookup_t.pl && ./bootstrap && ./configure && make check && make install && ldconfig && \
    cd /tmp && \
    tar xvf openresty-1.15.8.1.tar.gz && \
    cd openresty-1.15.8.1 && \
    ./configure --with-luajit --with-http_geoip_module --add-module=/usr/src/ngx_http_geoip2_module --with-http_stub_status_module --with-http_v2_module --with-http_realip_module --with-ipv6 && \
    make -j$(nproc) && \
    make install && \
    rm -rf /tmp/*
# ADD docker/nginx/ /usr/local/openresty/nginx/

# RUN rm -rf /tmp/*
    # && apt-get remove -y git build-essential python libglib2.0-dev \
    # libmemcached-dev libfreetype6-dev libjpeg-dev libmcrypt-dev libpng-dev libcurl4-openssl-dev libxml2-dev libxslt1-dev libzip-dev libmemcached-dev libssl-dev libgeoip-dev \
    # && apt-get autoremove -y \
    # && apt-get clean \
    # && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /var/log/nginx/ && \
    mkdir -p /cache/nginx/www /cache/nginx/m
RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log
WORKDIR /usr/local/openresty/nginx

ENV PATH="/usr/local/openresty/nginx/sbin:${PATH}"
#####
RUN ln -s /usr/local/bin/php /usr/bin/php

CMD ["php-fpm"]
