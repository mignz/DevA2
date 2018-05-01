FROM alpine:3.7

LABEL maintainer="me@mnunes.com"

ENV NGINX_VERSION=1.12.2-r3
ENV SUPERVISOR_VERSION=3.3.3-r1
ENV PHP_VERSION=7.2
ENV PHALCON_VERSION=3.3.2

ADD https://php.codecasts.rocks/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub

RUN echo "@php https://php.codecasts.rocks/v3.7/php-${PHP_VERSION}" >> /etc/apk/repositories && \
    apk add --update nano curl ca-certificates && \
    apk upgrade && \
    apk --update add \
        supervisor=$SUPERVISOR_VERSION \
        nginx=$NGINX_VERSION \
        mysql \
        mysql-client \
        php@php \
        php-bcmath@php \
        php-bz2@php \
        php-calendar@php \
        php-ctype@php \
        php-curl@php \
        php-dba@php \
        php-dev@php \
        php-dom@php \
        php-enchant@php \
        php-exif@php \
        php-fpm@php \
        php-ftp@php \
        php-gd@php \
        php-gettext@php \
        php-gmp@php \
        php-iconv@php \
        php-imagick@php \
        php-imap@php \
        php-intl@php \
        php-json@php \
        php-ldap@php \
        php-mbstring@php \
        php-mysqli@php \
        php-mysqlnd@php \
        php-openssl@php \
        php-pdo@php \
        php-pdo_mysql@php \
        php-pdo_sqlite@php \
        php-phar@php \
        php-posix@php \
        php-pspell@php \
        php-redis@php \
        php-session@php \
        php-soap@php \
        php-sockets@php \
        php-sqlite3@php \
        php-sysvmsg@php \
        php-sysvsem@php \
        php-sysvshm@php \
        php-tidy@php \
        php-xdebug@php \
        php-xml@php \
        php-xmlreader@php \
        php-xsl@php \
        php-zip@php \
        php-zlib@php && \
    mkdir -p /var/log/supervisor && \
    cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.default && \
    cp /etc/php7/php-fpm.conf /etc/php7/php-fpm.conf.default && \
    rm -rf /var/www/localhost && \
    ln -s /usr/bin/php-config7 /usr/bin/php-config && \
    ln -s /usr/bin/php7 /usr/bin/php && \
    ln -s /usr/bin/phpize7 /usr/bin/phpize && \
    ln -s /usr/sbin/php-fpm7 /usr/sbin/php-fpm && \
    adduser -D -g 'www' www && \
    mysql_install_db --user=root && \
    rm -rf /var/cache/apk/*

ADD rootfs /

EXPOSE 80 443 3306

ENTRYPOINT ["supervisord"]
