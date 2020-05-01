Mattermost-LDAP Module
======================

This module provides an external LDAP authentication in Mattermost for the Team Edition (free).

## Overview

Currently, LDAP authentication in Mattermost is not featured in the Team Edition (only in the Enterprise Edition). Thus, the only way to get LDAP authentication in Mattermost is to install Gitlab and use its Single Sign On (SSO) feature. Gitlab allows LDAP authentication and transmits user data to Mattermost. So, anyone who wishes to use LDAP with Mattermost must run Gitlab, even if he does not use it, for the SSO feature.

However, although Gitlab is a nice software, it is resources-consuming and a bit complicated to manage if you just want the SSO feature. That's the reason why, this module provides an oauth server to only reproduce the Gitlab SSO feature and allows a simple and secure LDAP authentication to Mattermost.

The Mattermost-LDAP project uses the Gitlab authentication feature from Mattermost and substitute Gitlab to LDAP interaction. The main advantage of this module is to provide a light and easy to use LDAP connector for Mattermost not to need Gitlab.

## Module Description

This module provides an Oauth2 server designed for PHP, an LDAP connector for PHP and some files for automatic configuration. Once installed and configured with Mattermost, the module allows LDAP authentication by replacing Gitlab SSO. This module allows many configuration settings to try to comply with your settings and configuration. Mattermost-LDAP can be used with MySQL or PostgreSQL database on many operating systems.

See Limitation section for more information.

## Docker-Compose setup

The easiest way to setup Mattermost-LDAP is using the docker-compose implementation. Two docker-compose files are available in this repository :
- `Demo/docker-compose.yaml` : To test and try Mattermost-LDAP. This docker-compose file instantiate a Mattermost Server from the official preview image provides by Mattermost and a Mattemrost-LDAP pre-configured server with a PostgreSQL database.
- `docker-compose.yaml` : For production use. This docker-compose file only setup Mattermost-LDAP with an Apache server and a PostgreSQL database. This implementation uses an embedded Oauth server, which can be configured by environment variables.

### Requirements

To use docker-compose implementation, you need to install Docker and Docker compose. For CentOS 8 and Fedora, it is recommended to use Podman and Podman compose instead of Docker and Docker compose.

For more information about Docker installation, see official guide : https://docs.docker.com/engine/install/

For more information about Podman installation, see official documentation : https://podman.io/getting-started/installation.html

### Preparation

First, you need to clone (or download and extract) this repository on your server :
```bash
git clone https://github.com/Crivaledaz/Mattermost-LDAP
cd Mattermost-LDAP

# For Demo (optional)
cd Demo
```

Then, before running the docker-compose file, you need to adapt LDAP and DB parameters. All parameters are gathered in the `env.example` file and they are passed to Postgres and Oauth server by environment variables.

Copy the `env.example` file to `.env` and edit it to change with your values. For demo, parameters are directly in the `docker-compose.yaml` file, so you need to edit this file instead of `.env`.

**Warning** : Postgres root password and database Oauth password must be changed. Client and secret tokens must be generated randomly, using `openssl rand -hex 32`.

For more information about parameters, see beelow the configuration section of this documentation.

Otherwise, for production, you need to create a directory to store PostgreSQL data. This directory will contain the Oauth database and allows data persistence, even if containers are stopped or restarted. By dafault, this Mattermost-LDAP implementation uses folder `data/` next to the `docker-compose.yaml` file to store data. This folder need to be created before running Docker compose :
```bash
mkdir data
```      

For demo, you need to rename example configuration file without the example extension.
```bash
cd Mattermost-LDAP/
cp -p oauth/config_db.php.example oauth/config_db.php
cp -p oauth/LDAP/config_ldap.php.example oauth/LDAP/config_ldap.php
```

To use Mattermost-LDAP with you own Mattermost server, you need to configure your Mattermost instance as described in subsection "Mattermost" in section "Configuration"

### Usage

Once the `.env` file have been adapted, you can run the docker-compose file with the following commands :
```bash
docker-compose build
docker-compose up -d
```

The build command allows Docker compose to build necessary image. Images use are available in the [Docker/](Docker) directory of this repository. The up command starts all services described in the Docker compose file.

Once all services are started, go to Mattermost server. For the demo, Mattermost should be available on localhost : http://localhost. Click on GitLab button to login with LDAP credential on Mattermost-LDAP. Then, if you login successfully and authorize Mattermost-LDAP to transmit your data to Mattermost, you should be log on Mattermost.

*Note* : In demo, Mattermost server is available after few seconds.

To stop Mattermost server and Mattermost-LDAP, use the following command :
```bash
# With Docker
docker-compose down docker-compose.yaml

# With Podman
podman-compose down docker-compose.yaml
```

*Note* : Docker compose setup replaces Bare-Metal setup, but configuration remains necessary.

## Bare-Metal setup

### Requirements

Mattermost-LDAP requires the following :

* PHP (minimum 5.3.9)
* php-ldap
* php-pdo
* php-pgsql or php-mysql
* httpd
* postgresql or mariadb (mysql)
* postgresql-server or mariadb-server
* git

Obviously, you must have a Mattermost Server installed and be administrator on it, and a LDAP server configured.

### Pre-install

Install required packages :

* For Centos 7, RHEL 7 and Fedora :
```bash
#For PostgreSQL
sudo yum -y --nogpgcheck install httpd php postgresql-server postgresql php-ldap php-pdo php-pgsql git

#For MySQL
sudo yum -y --nogpgcheck install httpd php mariadb-server mariadb php-ldap php-pdo php-mysql git
```

* For Debian, ubuntu, Mint :
```bash
#For PostgreSQL
sudo apt-get -y install httpd php postgresql-server postgresql php-ldap php-pdo php-pgsql git

#For MySQL
sudo apt-get -y install httpd php mariadb-server mariadb php-ldap php-pdo php-mysql git
```

Setup your SQL server with the following command :
```bash
#For PostgreSQL (create a new database cluster)
sudo postgresql-setup initdb

#For MySQL (optional configuration for a secure MySQL server)
sudo mysql_secure_installation
```
By default, PostgreSQL does not allow client authentication on the server or a database. So we need to enable it by editing pg_hba.conf file (in `/var/lib/pgsql`). Open this file and replace `ident` by `md5` on the first three lines (local, host 127.0.0.1 and host ::1/128). It's recommended to backup the original file before editing it.

Then, start and enable service for Apache and Database (for all distribution using systemd):
```bash
#For PostgreSQL
sudo systemctl start httpd
sudo systemctl start postgresql
sudo systemctl enable httpd
sudo systemctl enable postgresql


#For MySQL
sudo systemctl start httpd
sudo systemctl start mariadb
sudo systemctl enable httpd
sudo systemctl enable mariadb
```

Your system is ready to install and run Mattermost-LDAP module.

### Install

Clone (or download and extract) this repository and move `oauth` directory in `/var/www/html` (or your httpd root directory) :
```bash
cd ~
git clone https://github.com/crivaledaz/Mattermost-LDAP.git
cd Mattermost-LDAP
cp -r oauth/ /var/www/html/
```

You need to create a database for the Oauth server. For this purpose, you can use the script `init_postgres.sh` or `init_mysql.sh`, available in `db_init` directory. These scripts try to configure your database automatically, by creating a new user and a new database associated for the Oauth server. Scripts also create all tables necessary for the module. If script failed, please report here, and try to configure manually your database by adapting command in scripts. Before running the script you can change the default settings by editing the `db_init/config_init.sh` file and modifying configuration variables. For PostgreSQL, you can copy and paste following lines :
```bash
cd db_init
vim config_init.sh
./init_postgres.sh
```

This script will automatically create and add a new client in the Oauth server, returning a client id and a client secret. You need to keep these two token to configure Mattermost. Please be sure the client secret remained secret. The redirect url in the script must comply with the hostname of your Mattermost server, else Mattermost could not get data from the Oauth server.

## Configuration

Configuration files are provided with examples and default values. Each config file has an `example` extension, so you need to copy and to rename them without this extension. You can find a detailed description of each parameters available below.

### Init script parameters

| Parameter     | Description                                                         | Default value                                        |
| ------------- | ------------------------------------------------------------------- | ---------------------------------------------------- |
| db_user       | Oauth user in the database.                                         | `oauth`                                              |
| db_pass       | Oauth user password in the database.                                | `oauth_secure-pass`                                  |
| db_host       | Hostname or IP address of the database.                             | `127.0.0.1`                                          |
| db_port       | The port to connect to the database.                                | `5432` (Postgres)                                    |
| db_name       | Database name for Oauth server.                                     | `oauth_db`                                           |
| client_id     | The application ID shared with mattermost.                          | `openssl rand -hex 32`                               |
| client_secret | The application secret shared with mattermost.                      | `openssl rand -hex 32`                               |
| redirect_uri  | The callback address where Oauth will send tokens to Mattermost.    | http://mattermost.company.com/signup/gitlab/complete |
| grant_types   | The type of authentification use by Mattermost.                     | `authorization_code`                                 |
| scope         | The scope of authentification use by Mattermost.                    | `api`                                                |
| user_id       | The username of the user who create the Mattermost client in Oauth. |                                                      |

*Note* : The `oauth_user` must have all privilege on the Oauth database to manage Oauth tokens.

The `client_id` and `client_secret` should be different and random tokens. You can use openssl to generate these tokens (`openssl rand -hex 32`). By default, these variables contain the `openssl` command, which use the openssl package. Tokens will be generated and printed at the end of the script.

The var `user_id` has no impact, and could be used as a commentary field. By default this field is empty.

### Mattermost

Active Gitlab authentication in `System Console > Gitlab` (or `config.json`) and fill application id and secret with the two tokens got during install section. For the next fields use this :
```
User API Endpoint : http://HOSTNAME/oauth/resource.php
Auth Endpoint: http://HOSTNAME/oauth/authorize.php
Token Endpoint: http://HOSTNAME/oauth/token.php
```
Change `HOSTNAME` by hostname or ip of the server where you have installed Mattermost-LDAP module.

Since Mattermost 4.9, these fields are disabled in admin panel, so you need to edit directly section `GitLabSettings` in the Mattermost configuration file `config.json`.

### Database credentials

Edit `oauth/config_db.php` and adapt, with your settings, to set up database in PHP.

| Parameter | Description                                           | Default value     |
| --------- | ----------------------------------------------------- | ----------------- |
| db_host   | Hostname or IP address of the database server         | `127.0.0.1`         |
| db_port   | The port of your database to connect                  | `5432`              |
| db_type   | Database type to adapt PDO. Should be pgsql or mysql. | `pgsql`             |
| db_user   | User who manages Oauth database                       | `oauth`             |
| db_pass   | User's password to manage Oauth database              | `oauth_secure-pass` |
| db_name   | Database name for Oauth server                        | `oauth_db`          |

If you use the init script, make sure to use the same values for database parameters.

*Note* : The 'db_user' must have all privilege on the Oauth database to manage Oauth tokens.

### LDAP configuration

Edit `oauth/LDAP/config_ldap.php` and adapt prameters with your LDAP configuration :

| Parameter             | Description                                                         | Default value              |
| --------------------- | ------------------------------------------------------------------- | -------------------------- |
| ldap_host             | URL or IP to connect LDAP server                                    | `ldap://ldap.company.com/` |
| ldap_port             | Port used to connect LDAP server                                    | `389`                      |
| ldap_version          | LDAP version or protocol version used by LDAP server                | `3`                        |
| ldap_search_attribute | Attribute used to identify a user on the LDAP                       | `uid`                      |
| ldap_filter           | Additional filter for LDAP search                                   | `(objectClass=*)`          |
| ldap_base_dn          | The base directory name of your LDAP server                         | `ou=People,o=Company`      |
| ldap_bind_dn          | The LDAP Directory Name of an service account to allow LDAP search  |                            |
| ldap_bind_pass        | The password associated to the service account to allow LDAP search |                            |

For openLDAP server, the 'ldap_search_attribute' should be `uid`, and for AD server this must be `sAMAccountName`. Nevertheless, 'email' or 'cn' could be used, this depends on your LDAP configuration.

Parameters 'ldap_bind_dn' and 'ldap_bind_pass' are required if your LDAP is restrictive, else put an empty string ("").

**Wraning** : Mattermost-LDAP V2 has changed 'ldap_filter' syntax. Now, the ldap filter must respect the LDAP syntax and need to be included into parenthesis.

*Note* : 'ldap_version' avoid LDAP blind error with LDAP 3 (issue #14)

To try your configuration you can use `ldap.php` available at the root of this project which use the LDAP library for PHP or you can use `ldapsearch` command in a shell.

## Usage

If you have succeeded previous step you only have to go to the login page of your Mattermost server and click on the Gitlab Button. You will be redirected to a form asking for your LDAP credentials. If your credentials are valid, you will be asked to authorize Oauth to give your information to Mattermost. After authorizing you should be redirected on Mattermost connected with your account.

Keep in mind this will create a new account on your Mattermost server with information from LDAP. The process will fail if an existing user already use your LDAP email. To bind an existing user to the LDAP authentication, sign in Mattermost with this user account, go in `account settings > security > sign-in method and "switch to using Gitlab SSO"`.

## Limitation

This module has been tested on Centos 7, Fedora and Ubuntu with PostgreSQL and Mattermost Community Edition version 4.1, 4.9, 5.0.1, 5.10, 5.15.1, 5.51.0 and 5.22.0. Mattermost-LDAP is compliant with Mattermost Team Edition 4.x.x and 5.x.x.

Others operating systems has not been tested yet but should work fine.

MySQL has not really been tested so it is possible there is some bugs with.

## To do list
 * HTTPS support
 * Add CSS to make a beautiful interface for Oauth server
 * Change Gitlab button
 * Security audit

## Thanks

I wish to thank CS SI and my colleagues for their help and support. Also, I thank Brent Shaffer for his [Oauth-server-php](https://github.com/bshaffer/oauth2-server-php) project and its [documentation](https://bshaffer.github.io/oauth2-server-php-docs/).

## Known issues

 * LDAP authentication failed
 Try to restart httpd service. If this persists verify your LDAP configuration or your credentials.

 * PHP date timezone error
 Edit `php.ini` to set up date.timezone option and restart httpd service, or use the `date_default_timezone_set()` function in `config_db.php`

 * Token request failed
 Try to add a new rule in your firewall (or use `iptables -F` on both Mattermost server and Oauth server)

 * .htaccess does not work
 Add following lines to your `httpd.conf` and restart httpd service.
 ```
 <Directory "/var/www/html/oauth">
    AllowOverride All
</Directory>
 ```
