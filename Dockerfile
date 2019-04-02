FROM debian:stretch

COPY init_postgres.sh /
COPY files/ /

RUN apt-get update && \
	apt-get -y -o Dpkg::Options::="--force-confold" install sudo nginx-light php-fpm postgresql-client postgresql php-ldap php-pdo php-pgsql git runit daemontools && \
	sed -i "s/^session.gc_maxlifetime.*/session.gc_maxlifetime = 60/" /etc/php/7.0/fpm/php.ini

COPY oauth/ /var/www/html/oauth
COPY images/ /var/www/html/images

CMD /entrypoint.sh
