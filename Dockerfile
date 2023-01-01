FROM alpine:3.17

ENV \
    NGINX_VERSION=1.22.1-r0 \
    S6_VERSION=2.11.1.2-r0 \
    PHP_VERSION=8.1.13-r0 \
    PHALCON_VERSION=5.1.3 \
    MARIADB_VERSION=10.6.11-r0 \
    REDIS_VERSION=7.0.7-r0 \
    SSMTP_VERSION=2.64-r18

RUN set -x \
    && apk add --update --no-cache curl \
        ssmtp=$SSMTP_VERSION \
        redis=$REDIS_VERSION \
        ca-certificates \
        nginx=$NGINX_VERSION \
        s6=$S6_VERSION \
        openssl \
        mariadb=$MARIADB_VERSION \
        mariadb-client=$MARIADB_VERSION \
        bash \
        gcc \
        git \
        make \
        autoconf \
        libc-dev \
        libpcre32 \
        pcre-dev \
        pcre2 \
        pcre2-dev \
        file \
        re2c \
    && apk add --update --no-cache php81=$PHP_VERSION \
        php81-bcmath=$PHP_VERSION \
        php81-bz2=$PHP_VERSION \
        php81-calendar=$PHP_VERSION \
        php81-ctype=$PHP_VERSION \
        php81-curl=$PHP_VERSION \
        php81-dba=$PHP_VERSION \
        php81-dev=$PHP_VERSION \
        php81-dom=$PHP_VERSION \
        php81-enchant=$PHP_VERSION \
        php81-exif=$PHP_VERSION \
        php81-fpm=$PHP_VERSION \
        php81-ftp=$PHP_VERSION \
        php81-gd=$PHP_VERSION \
        php81-gettext=$PHP_VERSION \
        php81-gmp=$PHP_VERSION \
        php81-iconv=$PHP_VERSION \
        php81-imap=$PHP_VERSION \
        php81-intl=$PHP_VERSION \
        php81-ldap=$PHP_VERSION \
        php81-mbstring=$PHP_VERSION \
        php81-mysqli=$PHP_VERSION \
        php81-mysqlnd=$PHP_VERSION \
        php81-openssl=$PHP_VERSION \
        php81-pdo=$PHP_VERSION \
        php81-pdo_mysql=$PHP_VERSION \
        php81-pdo_sqlite=$PHP_VERSION \
        php81-pecl-imagick \
        php81-pecl-psr \
        php81-pecl-redis \
        php81-pecl-xdebug \
        php81-phar=$PHP_VERSION \
        php81-posix=$PHP_VERSION \
        php81-pspell=$PHP_VERSION \
        php81-session=$PHP_VERSION \
        php81-sodium=$PHP_VERSION \
        php81-soap=$PHP_VERSION \
        php81-sockets=$PHP_VERSION \
        php81-sqlite3=$PHP_VERSION \
        php81-sysvmsg=$PHP_VERSION \
        php81-sysvsem=$PHP_VERSION \
        php81-sysvshm=$PHP_VERSION \
        php81-tidy=$PHP_VERSION \
        php81-xml=$PHP_VERSION \
        php81-xmlreader=$PHP_VERSION \
        php81-xsl=$PHP_VERSION \
        php81-zip=$PHP_VERSION \
    && cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.default \
    && rm -rf /var/www/localhost \
    && mysql_install_db --user=root --datadir='/var/lib/mysql' > /dev/null 2>&1 \
    && echo -e "USE mysql;\nFLUSH PRIVILEGES;\nCREATE USER 'root'@'%';" > /tmp/deva.sql \
    && echo -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '';\nFLUSH PRIVILEGES;" >> /tmp/deva.sql \
    && sed -i 's/skip-networking/#skip-networking/' /etc/my.cnf.d/mariadb-server.cnf \
    && mkdir -p /run/mysqld /run/nginx \
    && /usr/bin/mysqld --user=root --datadir='/var/lib/mysql' --bootstrap --verbose=0 < /tmp/deva.sql \
    && rm -f /tmp/deva.sql \
    && cp /etc/php81/php.ini /etc/php81/php.ini.default \
    && cp /etc/php81/php-fpm.conf /etc/php81/php-fpm.conf.default \
    && [ -f /usr/bin/php-config ] || ln -s /usr/bin/php-config81 /usr/bin/php-config \
    && [ -f /usr/bin/php ] || ln -s /usr/bin/php81 /usr/bin/php \
    && [ -f /usr/bin/phpize ] || ln -s /usr/bin/phpize81 /usr/bin/phpize \
    && [ -f /usr/bin/php-fpm ] || ln -s /usr/sbin/php-fpm81 /usr/sbin/php-fpm \
    && adduser -D -g 'www' www \
    && rm -rf /var/cache/apk/* \
    && git clone --depth=1 --branch v$PHALCON_VERSION "https://github.com/phalcon/cphalcon.git" /root/cphalcon \
    && cd /root/cphalcon/build \
    && ./install \
    && echo "extension=phalcon.so" > /etc/php81/conf.d/phalcon.ini

ADD files /

RUN chmod +x /etc/s6/mysql/* \
    && chmod +x /etc/s6/nginx/* \
    && chmod +x /etc/s6/php-fpm/* \
    && chmod +x /etc/s6/redis/* \
    && SAN=DNS:localhost openssl req -newkey rsa:2048 -x509 -nodes -keyout /etc/nginx/deva/ssl/localhost.key -new -out /etc/nginx/deva/ssl/localhost.crt -subj /CN=localhost -extensions san_env -config /etc/nginx/deva/ssl/san.cnf -sha256 -days 3650 \
    && SAN=DNS:cp.test,DNS:localhost openssl req -newkey rsa:2048 -x509 -nodes -keyout /etc/nginx/deva/ssl/cp.test.key -new -out /etc/nginx/deva/ssl/cp.test.crt -subj /CN=cp.test -extensions san_env -config /etc/nginx/deva/ssl/san.cnf -sha256 -days 3650

EXPOSE 80/tcp 443/tcp 3306/tcp 6379/tcp

VOLUME /var/www
VOLUME /var/lib/mysql

HEALTHCHECK --interval=5m --timeout=5s CMD curl --silent --fail http://127.0.0.1/fpm-ping

ENTRYPOINT ["s6-svscan", "/etc/s6"]
