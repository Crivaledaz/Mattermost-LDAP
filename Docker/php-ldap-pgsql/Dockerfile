FROM php:8.3.4-fpm-alpine

RUN set -x \
    && apk update \
    && apk add --no-cache libpq-dev git \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN set -x \
    && apk add --no-cache --virtual .build-dependencies-in-virtual-world openldap-dev \
    && apk add --no-cache libldap \
    && docker-php-ext-install ldap \
    && docker-php-ext-enable  ldap \
    && apk del .build-dependencies-in-virtual-world

# Enable development php.ini config (Solve empty answer from token.php)
RUN ln -s /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
