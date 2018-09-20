FROM alpine:3.8

LABEL maintainer="me@mnunes.com"

ENV NGINX_VERSION=1.14.0-r1
ENV S6_VERSION=2.7.1.1-r1
#ENV PHP_VERSION=7.2
ENV PHALCON_VERSION=3.4.1
ENV MARIADB_VERSION=10.2.15-r0
ENV REDIS_VERSION=4.0.11-r0
ENV SSMTP_VERSION=2.64-r13

RUN apk update && \
    apk add --no-cache curl \
        ssmtp=$SSMTP_VERSION \
        redis=$REDIS_VERSION \
        ca-certificates \
        nginx=$NGINX_VERSION \
        s6=$S6_VERSION \
        openssl \
        mysql=$MARIADB_VERSION \
        mysql-client=$MARIADB_VERSION \
        php7 \
        php7-bcmath \
        php7-bz2 \
        php7-calendar \
        php7-ctype \
        php7-curl \
        php7-dba \
        php7-dev \
        php7-dom \
        php7-enchant \
        php7-exif \
        php7-fpm \
        php7-ftp \
        php7-gd \
        php7-gettext \
        php7-gmp \
        php7-iconv \
        php7-imagick \
        php7-imap \
        php7-intl \
        php7-json \
        php7-ldap \
        php7-mbstring \
        php7-mysqli \
        php7-mysqlnd \
        php7-openssl \
        php7-pdo \
        php7-pdo_mysql \
        php7-pdo_sqlite \
        php7-phar \
        php7-posix \
        php7-pspell \
        php7-redis \
        php7-session \
        php7-soap \
        php7-sockets \
        php7-sqlite3 \
        php7-sysvmsg \
        php7-sysvsem \
        php7-sysvshm \
        php7-tidy \
        php7-xdebug \
        php7-xml \
        php7-xmlreader \
        php7-xsl \
        php7-zip \
        gcc \
        make \
        autoconf \
        libc-dev \
        libpcre32 \
        pcre-dev \
        file \
        re2c && \
    cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.default && \
    rm -rf /var/www/localhost && \
    mysql_install_db --user=root > /dev/null 2>&1 && \
    echo -e "USE mysql;\nFLUSH PRIVILEGES;\nCREATE USER 'root'@'%';" > /tmp/deva.sql && \
    echo -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '';\nFLUSH PRIVILEGES;" >> /tmp/deva.sql && \
    mkdir -p /run/mysqld && \
    /usr/bin/mysqld --user=root --bootstrap --verbose=0 < /tmp/deva.sql && \
    rm -f /tmp/deva.sql && \
    cp /etc/php7/php.ini /etc/php7/php.ini.default && \
    cp /etc/php7/php-fpm.conf /etc/php7/php-fpm.conf.default && \
    [ -f /usr/bin/php-config ] || ln -s /usr/bin/php-config7 /usr/bin/php-config && \
    [ -f /usr/bin/php ] || ln -s /usr/bin/php7 /usr/bin/php && \
    [ -f /usr/bin/phpize ] || ln -s /usr/bin/phpize7 /usr/bin/phpize && \
    [ -f /usr/bin/php-fpm ] || ln -s /usr/sbin/php-fpm7 /usr/sbin/php-fpm && \
    echo "zend_extension=xdebug.so" > /etc/php7/conf.d/xdebug.ini && \
    adduser -D -g 'www' www && \
    curl -LOs https://github.com/phalcon/cphalcon/archive/v${PHALCON_VERSION}.tar.gz && \
    tar xzf v${PHALCON_VERSION}.tar.gz && cd cphalcon-${PHALCON_VERSION}/build && sh install && \
    echo "extension=phalcon.so" > /etc/php7/conf.d/20_phalcon.ini && \
    rm -rf /v${PHALCON_VERSION}.tar.gz /cphalcon-${PHALCON_VERSION} && \
    rm -rf /var/cache/apk/*

ADD files /

RUN chmod +x /etc/s6/mysql/* && \
    chmod +x /etc/s6/nginx/* && \
    chmod +x /etc/s6/php-fpm/* && \
    chmod +x /etc/s6/redis/* && \
    SAN=DNS:localhost openssl req -newkey rsa:2048 -x509 -nodes -keyout /etc/nginx/deva/ssl/localhost.key -new -out /etc/nginx/deva/ssl/localhost.crt -subj /CN=localhost -extensions san_env -config /etc/nginx/deva/ssl/san.cnf -sha256 -days 3650 && \
    SAN=DNS:cp.test,DNS:localhost openssl req -newkey rsa:2048 -x509 -nodes -keyout /etc/nginx/deva/ssl/cp.test.key -new -out /etc/nginx/deva/ssl/cp.test.crt -subj /CN=cp.test -extensions san_env -config /etc/nginx/deva/ssl/san.cnf -sha256 -days 3650

VOLUME /var/www
VOLUME /var/lib/mysql

EXPOSE 80 443 3306

ENTRYPOINT ["s6-svscan", "/etc/s6"]
