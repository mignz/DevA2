FROM alpine:3.7

LABEL maintainer="me@mnunes.com"

ENV NGINX_VERSION=1.12.2-r3
ENV SUPERVISOR_VERSION=3.3.3-r1
ENV PHP_VERSION=7.2
ENV PHALCON_VERSION=3.3.2

ADD https://php.codecasts.rocks/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub

RUN apk add --update \
        nano \
        curl \
        ca-certificates && \
    apk upgrade

RUN apk --update add supervisor=$SUPERVISOR_VERSION && \
    mkdir -p /var/log/supervisor

RUN apk --update add nginx=$NGINX_VERSION openssl && \
    cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.default && \
    mkdir -p /etc/nginx/deva/ssl && \
    openssl req -x509 -nodes -days 3652 -newkey rsa:2048 -keyout /etc/nginx/deva/ssl/nginx.key -out /etc/nginx/deva/ssl/nginx.crt -subj "/C=PT/ST=PDL/L=PDL/O=DevA/OU=IT/CN=localhost" && \
    rm -rf /var/www/localhost

RUN apk --update add mysql mysql-client && \
    mysql_install_db --user=root > /dev/null 2>&1

RUN echo "@php https://php.codecasts.rocks/v3.7/php-${PHP_VERSION}" >> /etc/apk/repositories && \
    apk --update add \
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
    cp /etc/php7/php.ini /etc/php7/php.ini.default && \
    cp /etc/php7/php-fpm.conf /etc/php7/php-fpm.conf.default && \
    ln -s /usr/bin/php-config7 /usr/bin/php-config && \
    ln -s /usr/bin/php7 /usr/bin/php && \
    ln -s /usr/bin/phpize7 /usr/bin/phpize && \
    ln -s /usr/sbin/php-fpm7 /usr/sbin/php-fpm && \
    adduser -D -g 'www' www

RUN apk --update add \
        gcc \
        make \
        autoconf \
        libc-dev \
        libpcre32 \
        pcre-dev \
        file \
        re2c && \
    curl -LOs https://github.com/phalcon/cphalcon/archive/v${PHALCON_VERSION}.tar.gz && \
    tar xzf v${PHALCON_VERSION}.tar.gz && cd cphalcon-${PHALCON_VERSION}/build && sh install && \
    echo "extension=phalcon.so" > /etc/php7/conf.d/20_phalcon.ini && \
    rm -rf /v${PHALCON_VERSION}.tar.gz /cphalcon-${PHALCON_VERSION}

RUN rm -rf /var/cache/apk/*

ADD rootfs /

EXPOSE 80 443 3306

#CREATE USER 'root'@'%'
#GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '';
#FLUSH PRIVILEGES;

ENTRYPOINT ["supervisord"]
