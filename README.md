Mattermost-LDAP Module
======================

This module provides an external LDAP authentication in Mattermost for the Team Edition (free).

## Overview

Currently, LDAP authentication in Mattermost is not featured in the Team Edition (only in the Enterprise Edition). Thus, the only way to get LDAP authentication in Mattermost is to install Gitlab and use its Single Sign On (SSO) feature. Gitlab allows LDAP authentication and transmits user data to Mattermost. So, anyone who wishes to use LDAP with Mattermost must run Gitlab, even if he does not use it, for the SSO feature.

However, although Gitlab is a nice software, it is resources-consuming and a bit complicated to manage if you just want the SSO feature. That's the reason why, this module provides an Oauth server to only reproduce the Gitlab SSO feature and allows a simple and secure LDAP authentication to Mattermost.

The Mattermost-LDAP project uses the Gitlab authentication feature from Mattermost and substitute Gitlab to LDAP interaction. The main advantage of this module is to provide a light and easy to use LDAP connector for Mattermost not to need Gitlab.

## Module Description

This module provides an Oauth2 server designed for PHP, an LDAP connector for PHP and some files for automatic configuration. Once installed and configured with Mattermost, the module allows LDAP authentication by replacing Gitlab SSO. This module allows many configuration settings to try to comply with your settings and configuration. Mattermost-LDAP can be used with MySQL or PostgreSQL database on many operating systems.

See Limitation section for more information.

## Quick Start - Demonstration

To test and try Mattermost-LDAP, you can use the demonstration available in the `Demo/` folder. This demonstration is based on a docker-compose implementation describe in the [`Demo/docker-compose.yaml` file](https://github.com/Crivaledaz/Mattermost-LDAP/blob/master/Demo/docker-compose.yaml).

This docker-compose file instantiate a Mattermost Server from the official preview image provides by Mattermost, a Mattemrost-LDAP pre-configured server with a PostgreSQL database and an OpenLDAP server with a test user : John DOE.

To try Mattermost-LDAP, please read the following instructions.

### Requirements

Firstly, to use docker-compose, you need to install Docker and Docker compose.

*Note* : For CentOS 8 and Fedora, it is recommended to use Podman and Podman compose instead of Docker and Docker compose.

For more information about Docker installation, see official guide : https://docs.docker.com/engine/install/

For more information about Podman installation, see official documentation : https://podman.io/getting-started/installation.html

### Preparation

First, you need to clone (or download and extract) this repository on your server :
```bash
git clone https://github.com/Crivaledaz/Mattermost-LDAP
cd Mattermost-LDAP/Demo
```

Then, you have to rename example configuration file without the example extension.
```bash
cp -p ../oauth/config_db.php.example ../oauth/config_db.php
cp -p ../oauth/LDAP/config_ldap.php.example ../oauth/LDAP/config_ldap.php
```

Optionnally, you can adapt deployment parameters by editing the `Demo/docker-compose.yaml` file, before running it. Parameters are passed to Postgres database, Oauth server and LDAP server by environment variables. They are gathered in the `environment` section for each container.

For more information about available parameters, see the configuration section of this documentation.

### Usage

To run the docker-compose file use the following command :
```bash
# With Docker
docker-compose up -d

# With Podman
podman-compose up -d
```

The up command starts all services described in the Docker compose file. The `-d` argument allows to start all container in background, in a detach mode.

Once all services are started, go to Mattermost server. Mattermost should be available after a few seconds on localhost : http://localhost.

On the Mattermost login page, click on GitLab button to login with LDAP credential on Mattermost-LDAP. Complete the login form with following credentials :

```
username: jdoe
password: test1234
```

Once you are logged in, you should authorize Mattermost-LDAP to transmit LDAP data to Mattermost. Then, you should be log on Mattermost with the John DOE user account and create a new team.

That's all, you are logged into Mattermost with an LDAP account !

To stop Mattermost server and Mattermost-LDAP, use the following command :
```bash
# With Docker
docker-compose down

# With Podman
podman-compose down
```

## Installation

Mattermost-LDAP can be installed using containers or directly on a bare metal server, depending on your environment. Note that the installation process is easier with containers.

To install Mattermost-LDAP using containers use this documentation - [Container.md](Container.md).

To install Mattermost-LDAP on Bare Metal use the following documentation - [BareMetal.md](BareMetal.md).

Both installations allow to set up Mattermost-LDAP for a production use.

## Configuration

Configuration files are provided with examples and default values. Each config file has an `example` extension, so you need to copy and to rename them without this extension.

You can find a detailed description of each parameters available below.

**Note** : For container, these variables are overload by environment variables define in the [`docker-compose.yaml`](https://github.com/Crivaledaz/Mattermost-LDAP/blob/master/docker-compose.yaml) file.

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
| ldap_start_tls        | LDAP over STARTTLS                                                  | `false`                    |
| ldap_search_attribute | Attribute used to identify a user on the LDAP                       | `uid`                      |
| ldap_filter           | Additional filter for LDAP search                                   | `(objectClass=*)`          |
| ldap_base_dn          | The base directory name of your LDAP server                         | `ou=People,o=Company`      |
| ldap_bind_dn          | The LDAP Directory Name of an service account to allow LDAP search  |                            |
| ldap_bind_pass        | The password associated to the service account to allow LDAP search |                            |
| ldap_secure           | LDAP over ldaps (ex:ldaps://ldap.google.com)                        | `false`                    |
| ldap_cert_path        | LDAP certificate file for secure LDAP                               | `/var/www/html/oauth/certs/certificate.crt`|
| ldap_key_path         | LDAP private key file for secure LDAP                               | `/var/www/html/oauth/certs/key.key`|

For openLDAP server, the 'ldap_search_attribute' should be `uid`, and for AD server this must be `sAMAccountName`. Nevertheless, 'email' or 'cn' could be used, this depends on your LDAP configuration.

Parameters 'ldap_bind_dn' and 'ldap_bind_pass' are required if your LDAP is restrictive, else put an empty string ("").

**Warning** : Mattermost-LDAP V2 has changed 'ldap_filter' syntax. Now, the ldap filter must respect the LDAP syntax and need to be included into parenthesis.

*Note* : 'ldap_version' avoid LDAP blind error with LDAP 3 (issue #14)

To try your configuration you can use `ldap.php` available at the root of this project which use the LDAP library for PHP or you can use `ldapsearch` command in a shell.

## Usage

If you have succeeded previous step you only have to go to the login page of your Mattermost server and click on the Gitlab Button. You will be redirected to a form asking for your LDAP credentials. If your credentials are valid, you will be asked to authorize Oauth to give your information to Mattermost. After authorizing you should be redirected on Mattermost connected with your account.

Keep in mind this will create a new account on your Mattermost server with information from LDAP. The process will fail if an existing user already use your LDAP email. To bind an existing user to the LDAP authentication, sign in Mattermost with this user account, go in `account settings > security > sign-in method and "switch to using Gitlab SSO"`.

## Limitation

This module has been tested on Centos (7, 8, 8 stream), Fedora and Ubuntu with PostgreSQL and Mattermost Community Edition version 4.1, 4.9, 5.0.1, 5.10, 5.15.1, 5.51.0, 5.22.0, 5.36.0 and 6.0.0. Mattermost-LDAP is compliant with Mattermost Team Edition 4.x.x, 5.x.x and 6.x.x.

Others operating systems has not been tested yet but should work fine.

MySQL has not really been tested so it is possible there is some bugs.

## To do list

* Support multi-branch LDAP [issue #74](https://github.com/Crivaledaz/Mattermost-LDAP/issues/74)
* Change Gitlab button [issue #46](https://github.com/Crivaledaz/Mattermost-LDAP/issues/46)
* Security audit

## Thanks

I wish to thank CS GROUP and my colleagues for their help and support. Also, I thank Brent Shaffer for his [Oauth-server-php](https://github.com/bshaffer/oauth2-server-php) project and its [documentation](https://bshaffer.github.io/oauth2-server-php-docs/).

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
