FROM php:8.3-fpm-bookworm

LABEL description="PHP 8.3 FPM on Debian Bookworm, with PostgreSQL and Redis drivers. Configured to serve a Laravel app."

ENV TZ="Europe/Rome"
ENV PHP_INI_DIR=/usr/local/etc/php
ENV LD_LIBRARY_PATH="/opt/oracle/instantclient_19_3:${LD_LIBRARY_PATH:-}"

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
	&& apt-get update \
	&& apt-get install -y \
	wget \
    git \
	nano \
	unzip \
    libpq-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
	libfreetype6-dev \
	libldap2-dev \
	libzip-dev \
	libaio1 \
	libxml2-dev \
	libcurl4-openssl-dev \
    libssl-dev \
    && mkdir /opt/wkhtml \
    && cd /opt/wkhtml \
    && wget -nv https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-3/wkhtmltox_0.12.6.1-3.bookworm_amd64.deb \
    && apt-get install -y ./wkhtmltox_0.12.6.1-3.bookworm_amd64.deb \
    && rm -rf /opt/wkhtml/wkhtmltox_0.12.6.1-3.bookworm_amd64.deb \
    && mkdir /opt/oracle \
    && cd /opt/oracle \
    && wget https://download.oracle.com/otn_software/linux/instantclient/193000/instantclient-basic-linux.x64-19.3.0.0.0dbru.zip \
    && wget https://download.oracle.com/otn_software/linux/instantclient/193000/instantclient-sdk-linux.x64-19.3.0.0.0dbru.zip \
    && unzip /opt/oracle/instantclient-basic-linux.x64-19.3.0.0.0dbru.zip -d /opt/oracle \
    && unzip /opt/oracle/instantclient-sdk-linux.x64-19.3.0.0.0dbru.zip -d /opt/oracle \
    && ln -s /opt/oracle/instantclient_12_1/libclntshcore.so.19.3 /opt/oracle/instantclient_19_3/libclntshcore.so \
    && rm -rf /opt/oracle/*.zip

COPY --from=composer:2.7.7 /usr/bin/composer /usr/bin/composer

RUN pear config-set php_ini "$PHP_INI_DIR/php.ini" \
	&& echo 'instantclient,/opt/oracle/instantclient_19_3/' | pecl install oci8 \
    && docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient_19_3,19.3 \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) pdo_oci \
    calendar \
    gd \
    ldap \
    zip \
    opcache \
    bcmath \
    pdo_mysql \
    pdo_pgsql \
    pcntl \
    soap \
    sockets \
    exif \
    intl \
    && pecl install redis-6.0.2 \
    && pecl install pecl_http \
    && docker-php-ext-enable http \
    && apt-get autoremove --yes && rm -rf /var/lib/{apt,dpkg,cache,log}/
