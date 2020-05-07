FROM php:fpm

RUN set -x \
    && apt-get update \
    && apt-get install -y libpq-dev libldap2-dev git\
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap

# Enable development php.ini config (Solve empty answer from token.php)
RUN ln -s /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
