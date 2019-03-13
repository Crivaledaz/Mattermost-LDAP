#!/bin/bash

RND_CLIENT_ID=$(openssl rand -hex 32)
RND_CLIENT_SECRET=$(openssl rand -hex 32)

# Read environment variables or set default values
LDAP_URI=${LDAP_URI:-ldaps://ldap.company.com/}
LDAP_PORT=${LDAP_PORT:-636}
LDAP_SEARCH_ATTRIBUTE=${LDAP_SEARCH_ATTRIBUTE:-email}
LDAP_BASE=${LDAP_BASE:-ou=people,dc=company,dc=com}
LDAP_FILTER=${LDAP_FILTER:-objectClass=*}
LDAP_BIND_DN=${LDAP_BIND_DN:-uid=readonly,dc=company,dc=com}
LDAP_BIND_PASS=${LDAP_BIND_PASS:-password}
DB_USER=${DB_USER:-oauth}
DB_PASS=${DB_PASS:-oauth_secure-pass}
TZ_NAME=${TZ_NAME:-Europe/Paris}
MATTERMOST_URL=${MATTERMOST_URL:-https://mattermost.company.com}
CLIENT_ID=${CLIENT_ID:-$RND_CLIENT_ID}
CLIENT_SECRET=${CLIENT_SECRET:-$RND_CLIENT_SECRET}

#LDAP parameters
cat << EOF > /var/www/html/oauth/LDAP/config_ldap.php
<?php
// LDAP parameters
\$hostname = "$LDAP_URI";
\$port = $LDAP_PORT;
\$ldap_version = 3;
\$search_attribute = "$LDAP_SEARCH_ATTRIBUTE";
\$base = "$LDAP_BASE";
\$filter = "$LDAP_FILTER";
\$bind_dn = "$LDAP_BIND_DN";
\$bind_pass = "$LDAP_BIND_PASS";
EOF

#Local Oauth DB paramaters
cat << EOF > /var/www/html/oauth/config_db.php
<?php
\$port  	  = 5432;
\$host  	  = "localhost";
\$name  	  = "oauth_db";
\$type	  = "pgsql";
\$username = "$DB_USER";
\$password = "$DB_PASS";
\$dsn	  = \$type . ":dbname=" . \$name . ";host=" . \$host . ";port=" . \$port;
date_default_timezone_set ("$TZ_NAME");
EOF

#DB initialisation parameters
cat << EOF > /config_init.sh
client_id="$CLIENT_ID"
client_secret="$CLIENT_SECRET"
redirect_uri="$MATTERMOST_URL/signup/gitlab/complete"
grant_types="authorization_code"
scope="api"
user_id=""

#Database configuration
oauth_user="$DB_USER"
oauth_db_name="oauth_db"
oauth_pass="$DB_PASS"
ip="127.0.0.1"
port="5432"
EOF

if [ -f /etc/nginx/cert/key-no-password.pem -a -f /etc/nginx/cert/cert.pem ]; then
	ln -s /etc/nginx/sites-available/default-ssl /etc/nginx/sites-enabled/
else
	rm -f /etc/nginx/sites-enabled/default-ssl
fi

if [ ! -f /var/lib/postgresql/data/PG_VERSION ]; then
	mkdir -p /var/lib/postgresql/data
	chown postgres:postgres /var/lib/postgresql/data
	setuidgid postgres /usr/lib/postgresql/9.6/bin/initdb /var/lib/postgresql/data
	setuidgid postgres /usr/lib/postgresql/9.6/bin/postgres -D /var/lib/postgresql/data &
	sleep 5
	/init_postgres.sh
	killall postgres

	echo "Database initialised"
	echo "CLIENT_ID: $CLIENT_ID"
	echo "CLIENT_SECRET: $CLIENT_SECRET"
elif [ -d /var/lib/postgresql/data ]; then
	chown -R postgres:postgres /var/lib/postgresql/data
	echo "Database was pre-existing"
fi


runsvdir -P /etc/sv

