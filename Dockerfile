FROM debian:stretch

COPY init_postgres.sh /
COPY files/ /

RUN apt-get update && \
	apt-get -y -o Dpkg::Options::="--force-confold" install sudo nginx-light php-fpm postgresql-client postgresql php-ldap php-pdo php-pgsql git runit daemontools

COPY oauth/ /var/www/html/oauth

CMD /entrypoint.sh
