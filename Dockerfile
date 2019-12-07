FROM alpine:3.10

LABEL maintainer="me@mnunes.com"

ENV NGINX_VERSION=1.16.1-r1
ENV S6_VERSION=2.8.0.1-r0
ENV PHP_VERSION=7.3.11-r0
ENV PHALCON_VERSION=3.4.5
ENV MARIADB_VERSION=10.3.20-r0
ENV REDIS_VERSION=5.0.5-r0
ENV SSMTP_VERSION=2.64-r14

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
        php7=$PHP_VERSION \
        php7-bcmath=$PHP_VERSION \
        php7-bz2=$PHP_VERSION \
        php7-calendar=$PHP_VERSION \
        php7-ctype=$PHP_VERSION \
        php7-curl=$PHP_VERSION \
        php7-dba=$PHP_VERSION \
        php7-dev=$PHP_VERSION \
        php7-dom=$PHP_VERSION \
        php7-enchant=$PHP_VERSION \
        php7-exif=$PHP_VERSION \
        php7-fpm=$PHP_VERSION \
        php7-ftp=$PHP_VERSION \
        php7-gd=$PHP_VERSION \
        php7-gettext=$PHP_VERSION \
        php7-gmp=$PHP_VERSION \
        php7-iconv=$PHP_VERSION \
        php7-imagick \
        php7-imap=$PHP_VERSION \
        php7-intl=$PHP_VERSION \
        php7-json=$PHP_VERSION \
        php7-ldap=$PHP_VERSION \
        php7-mbstring=$PHP_VERSION \
        php7-mysqli=$PHP_VERSION \
        php7-mysqlnd=$PHP_VERSION \
        php7-openssl=$PHP_VERSION \
        php7-pdo=$PHP_VERSION \
        php7-pdo_mysql=$PHP_VERSION \
        php7-pdo_sqlite=$PHP_VERSION \
        php7-phar=$PHP_VERSION \
        php7-posix=$PHP_VERSION \
        php7-pspell=$PHP_VERSION \
        php7-redis \
        php7-session=$PHP_VERSION \
        php7-soap=$PHP_VERSION \
        php7-sockets=$PHP_VERSION \
        php7-sqlite3=$PHP_VERSION \
        php7-sysvmsg=$PHP_VERSION \
        php7-sysvsem=$PHP_VERSION \
        php7-sysvshm=$PHP_VERSION \
        php7-tidy=$PHP_VERSION \
        php7-xdebug \
        php7-xml=$PHP_VERSION \
        php7-xmlreader=$PHP_VERSION \
        php7-xsl=$PHP_VERSION \
        php7-zip=$PHP_VERSION \
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
    mysql_install_db --user=root --datadir='/var/lib/mysql' > /dev/null 2>&1 && \
    echo -e "USE mysql;\nFLUSH PRIVILEGES;\nCREATE USER 'root'@'%';" > /tmp/deva.sql && \
    echo -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '';\nFLUSH PRIVILEGES;" >> /tmp/deva.sql && \
    mkdir -p /run/mysqld && \
    /usr/bin/mysqld --user=root --datadir='/var/lib/mysql' --bootstrap --verbose=0 < /tmp/deva.sql && \
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
