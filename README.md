Mattermost-LDAP Module
======================

This module provides an external LDAP authentication in Mattermost for the Team Edition (free).

## Overview

Currently, LDAP authentication in Mattermost is not featured in the Team Edition (only in the Enterprise Edition). Thus, the only way to get LDAP authentication in Mattermost is to install Gitlab and use its Single Sign On (SSO) feature. Gitlab allows LDAP authentication and transmits user data to Mattermost. So, anyone who wishes to use LDAP with Mattermost must run Gitlab, even if he does not use it, for the SSO feature. 

However, although Gitlab is a nice software, it is resources-consuming and a bit complicated to manage if you just want the SSO feature. That's the reason why, this module provides an oauth server to only reproduce the Gitlab SSO feature and allows a simple and secure LDAP authentication to Mattermost.

The Mattermost-LDAP project uses the Gitlab authentication feature from Mattermost and substitute Gitlab to LDAP interaction. The main advantage of this module is to provide a light and easy to use LDAP connector for Mattermost not to need Gitlab.

## Module Description

This module provides an Oauth2 server designed for php, a LDAP connector for PHP and some files for automatic configuration. Once installed and configured with Mattermost, the module allows LDAP authentication by replacing Gitlab SSO. This module allows many configuration settings to try to comply with your settings and configuration. Mattermost-LDAP can be used with MySQL or PostgreSQL database on many operating systems. See Limitation section for more information. 

## Setup 
### Requirements
This module requires the following : 

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
```
#For PostgreSQL
sudo yum -y --nogpgcheck install httpd php postgresql-server postgresql php-ldap php-pdo php-pgsql git 

#For MySQL
sudo yum -y --nogpgcheck install httpd php mariadb-server mariadb php-ldap php-pdo php-mysql git
```
* For Debian, ubuntu, Mint :
```
#For PostgreSQL
sudo apt-get -y install httpd php postgresql-server postgresql php-ldap php-pdo php-pgsql git 

#For MySQL
sudo apt-get -y install httpd php mariadb-server mariadb php-ldap php-pdo php-mysql git
```
Setup your SQL server with the following command :
```
#For PostgreSQL (create a new database cluster)
sudo postgresql-setup initdb

#For MySQL (optional configuration for a secure MySQL server)
sudo mysql_secure_installation
```
By default, PostgreSQL does not allow client authentication on the server or a database. So we need to enable it by editing pg_hba.conf file (in /var/lib/pgsql). Open this file and replace 'ident' by 'md5' on the first three lines (local, host 127.0.0.1 and host ::1/128). It's recommended to backup the original file before editing it.

Then, start and enable service for Apache and Database (for all distribution using systemd):
```
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


## Install
Clone (or download and extract) this repository in your /var/www/html (or your httpd root directory) :
```
cd ~
git clone https://github.com/crivaledaz/Mattermost-LDAP.git
cd Mattermost-LDAP
cp -r oauth/ /var/www/html/
```

You need to create a database for the oauth server. For this purpose, you can use the script "init_postgres.sh" or "init_mysql.sh". These scripts try to configure your database automatically, by creating a new user and a new database associated for the oauth server. Scripts also create all tables necessary for the module. If script failed, please report here, and try to configure manually your database by adapting command in scripts. Before running the script you can change the default settings by editing the config_init.sh file and modifying configuration variables. For postgresql, you can copy and paste following lines :
```
nano config_init.sh
./init_postgres.sh
```

This script will automatically create and add a new client in the oauth server, returning a client id and a client secret. You need to keep these two token to configure Mattermost. Please be sure the client secret remained secret. The redirect url in the script must comply with the hostname of your Mattermost server, else Mattermost could not get data from the Oauth server.


## Configuration

Configuration files are provided with examples and default values. Each config file has an ".example" extension, so you need to copy and to rename them without this extension. You can find a detailed description of each parameters available below.

* Init script configuration :
#### oauth_user
Oauth user in the database. This user must have right on the oauth database to store oauth tokens. By default : oauth	
#### oauth_pass
Oauth user password in the database. By default, oauth_secure-pass
#### ip
Hostname or IP address of the database. By default : 127.0.0.1 		
#### port
The port to connect to the database. By default : 5432 (postgres) 		
#### oauth_db_name
Database name for oauth server. By default	: oauth_db	
#### client_id	
The application ID shared with mattermost. This ID should be a random token. You can use openssl to generate this token (openssl rand -hex 32). By default, this variable contain the openssl command, which use the openssl package. The token will be printed at the end of the script. 
#### client_secret
The application secret shared with mattermost. This secret should be a random token. You can use openssl to generate this token (openssl rand -hex 32). By default, this variable contain the openssl command, which use the openssl package. The token will be printed at the end of the script. Secret must be different of the client ID.
#### redirect_uri
The callback address where oauth will send tokens to Mattermost. Normally it should be http://mattermost.company.com/signup/gitlab/complete
#### grant_types
The type of authentification use by Mattermost. It should be "authorization_code".
#### scope
The scope of authentification use by Mattermost. It should be "api".
#### user_id
The username of the user who create the Mattermost client in Oauth. This field has no impact, and could be used as a commentary field. By default this field is empty.

* Mattermost :
Active Gitlab authentication in system console > Gitlab (or config.json on server) and fill application id and secret with the two token got during install section. For the next fields use this :
```
User API Endpoint : http://HOSTNAME/oauth/resource.php
Auth Endpoint: http://HOSTNAME/oauth/authorize.php
Token Endpoint: http://HOSTNAME/oauth/token.php
```
Change HOSTNAME by hostname or ip of the server where you have installed Mattermost-LDAP module.

In Mattermost 4.9, these fields are disable  in admin panel, so you need to edit directly the configuration file config.json. 

* Database credentials
Edit oauth/config_db.php and adapt, with your settings, to set up database in PHP.

#### $host
Hostname or IP address of the database. (ex : localhost)
#### $port
The port of your database to connect. (ex : 5432 for postgres)
#### $name
Database name for oauth server. If you use init script make sure to use the same database name. (ex : oauth_db) 
#### $type
Database type to adapt PDO to your database server. Should be mysql or pgsql.
#### $username
Oauth user in the database. This user must have right on the oauth database to store oauth tokens. If you use init script make sure to use the same database user. (ex : oauth)
#### $password
Oauth user password in the database. If you use init script make sure to use the same database user. (ex : oauth_secure-pass)

* LDAP config
Edit oauth/LDAP/config_ldap.php : 
1. Provide your ldap address, port and version.
2. Change the base directory name ($base) and the filter ($filter) to comply with your LDAP configuration.
3. Change the user ID attribute ($ldap_attribute) to comply with your LDAP configuration (uid, sAMAccountName, email, cn ..).
4. If necessary, you can provide a LDAP account to allow search in LDAP (only restrictive LDAP).  

#### $hostname
Your LDAP hostname or LDAP IP, to connect to the LDAP server.
#### $port
Your LDAP port, to connect to the LDAP server. By default : 389.
#### $ldap_version
Your LDAP version, or protocol version used by your server. By default : 3. This parameter avoid LDAP blind error with LDAP 3 (issue )
#### $search_attribute
The attribute used to identify user on your LDAP. Should be uid, email, cn or sAMAccountName.
#### $base
The base directory name of your LDAP server. (ex : ou=People,o=Company)
#### $filter
Additional filters to search in LDAP (used to get user informations). (ex : objectClass=person)
#### $bind_dn
The LDAP Directory Name of an service account to allow LDAP search. This ption is required if your LDAP is restrictive, else put an empty string (""). (ex : cn=mattermost_ldap,dc=Example,dc=com)
#### $bind_pass
The password associated to the service account to allow LDAP search. This ption is required if your LDAP you provide an bind user, else put an empty string ("").



To try your configuration you can use ldap.php available at the root of this project which use the LDAP library for PHP or you can use ldapsearch command in a shell.

Configure LDAP is certainly the most difficult step.

## Usage
If you have succeeded previous step you only have to go to the login page of your Mattermost server and click on the Gitlab Button. You will be redirected to a form asking for your LDAP credentials. If your credentials are valid, you will be asked to authorize Oauth to give your information to Mattermost. After authorizing you should be redirected on Mattermost connected with your account.

Keep in mind this will create a new account on your Mattermost server with information from LDAP. The process will fail if an existing user already use your LDAP email. To bind a user to the LDAP authentication, sign in mattermost with this user account, go in account settings > security > sign-in method and "switch to using Gitlab SSO".


## Limitation
This module has been tested on Centos 7, Fedora and Ubuntu with PostgreSQL and Mattermost Community Edition version 4.1 and 4.9.

Others operating systems has not been tested yet but should work fine. 

MySQL has not really been tested so it is possible there is some bugs with.


## To do list
 * Gathering LDAP config
 * Add CSS to make a beautiful interface for Oauth server
 * Create an associated Puppet module
 * Change Gitlab button
 * Security audit

## Thanks

I wish to thank my company and my colleagues for their help and support. Also, I thank Brent Shaffer for his Oauth-server-php project and its documentation.


## Known issues
 * LDAP authentication failed
 Try to restart httpd service. If this persists verify your LDAP configuration or your credentials.

 * PHP date timezone error
 Edit php.ini to set up date.timezone option and restart httpd service, or use the date_default_timezone_set() function in config_db.php

 * Token request failed
 Try to add a new rule in your firewall (or use iptables -F on both Mattermost server and Oauth server)

 * .htaccess does not work
 Add following lines to your httpd.conf and restart httpd service.
 ```
 <Directory "/var/www/html/oauth">
    AllowOverride All
</Directory>
 ```



