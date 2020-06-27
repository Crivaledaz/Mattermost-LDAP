# Image mattermostldap
FROM php:apache

RUN set -x \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        libpq-dev \
        libldap2-dev \
        git \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap

# Enable development php.ini config (Solve empty answer from token.php)
RUN ln -s /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

ARG BUILD_BRANCH=master
ARG BUILD_URL=https://github.com/crivaledaz/Mattermost-LDAP.git

# Get Mattermost-LDAP project
# Install server Oauth
# Get config file
RUN set -ex; \
    git clone --branch "${BUILD_BRANCH}" "${BUILD_URL}" /opt/Mattermost-LDAP/; \
    cp -r /opt/Mattermost-LDAP/oauth/ /var/www/html/ ; \
    cp \
        /var/www/html/oauth/config_db.php.example \
        /var/www/html/oauth/config_db.php\
    ; \
    cp \
        /var/www/html/oauth/LDAP/config_ldap.php.example \
        /var/www/html/oauth/LDAP/config_ldap.php \
    ;

# Open and expose port 80 for Apache server
EXPOSE 80
