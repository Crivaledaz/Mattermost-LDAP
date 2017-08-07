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
* postgresql-server or maridb-server
* git

Obviously, you must have a Mattermost Server installed and be administrator on it, and a LDAP server configured.

### Pre-install
* For Centos 7, RHEL 7 and Fedora :
Install required packages :
```#For PostgreSQL
sudo yum -y --nogpgcheck install httpd php postgresql-server postgresql php-ldap php-pdo php-psql git 

#For MySQL
sudo yum -y --nogpgcheck install httpd php mariadb-server mariadb php-ldap php-pdo php-mysql git```

Start and enable service for Apache and Database :
```#For PostgreSQL
sudo systemctl start httpd
sudo systemctl start postgresql
sudo systemctl enable httpd
sudo systemctl enable postgresql


#For MySQL
sudo systemctl start httpd
sudo systemctl start mariadb
sudo systemctl enable httpd
sudo systemctl enable mariadb```

Your system is ready to install and run Mattermost-LDAP module.


## Install
Clone (or download and extract) this repository in your /var/www/html (or your httpd root directory) :
```git clone https://github.com/crivaledaz/Mattermost-LDAP.git```

You need to create a database for the oauth server. For this purpose, you can use the script "init_postgres.sh" or "init_mysql.sh". These scripts try to configure your database automatically, by creating a new user and a new database associated for the oauth server. Scripts also create all tables necessary for the module. If script failed, please report here, and try to configure manually your database by adapting command in scripts. Before running the script you can change the default settings by editing the .sh file and modifying configuration variables at the beginning of the file.

This script will automatically create and add a new client in the oauth server, returning a client id and a client secret. You need to keep these two token to configure Mattermost. Please be sure the client secret remained secret. The redirect url in the script must comply with the hostname of your Mattermost server, else Mattermost could not get data from the Oauth server.

### configuration
* Mattermost :
Active Gitlab authentication in system console > Gitlab (or config.json on server) and fill application id and secret with the two token got during install section. For the next fields use this :
```User API Endpoint : http://HOSTNAME/oauth/resource.php
Auth Endpoint: http://HOSTNAME/oauth/authorize.php
Token Endpoint: http://HOSTNAME/oauth/token.php
```
Change HOSTNAME by hostname or ip of the server where you have installed Mattermost-LDAP module. 

* Database credential
Edit oauth/server.php and adapt, with your settings, variables for database connection :
```$dsn      = 'pgsql:dbname=oauth_db;host=localhost;port=5432';
$username = 'oauth';
$password = 'oauth_secure-pass';
```

* LDAP config
Edit oauth/LDAP/ldap_config.php to provide your ldap address and port.
Edit oauth/resource.php to change the base directory name ($base) and the filter ($filter) to comply with your LDAP configuration.
Edit oauth/connexion.php to change the relative directory name ($rdn) to comply with your LDAP configuration.

To try your configuration you can use the LDAP library for PHP or ldapsearch command in a shell.

Configure LDAP is certainly the most difficult step.

## Usage
If you have succeeded previous step you only have to go to the login page of your Mattermost server and click on the Gitlab Button. You will be redirected to a form asking for your LDAP credentials. If your credentials are valid, you will be asked to authorize Oauth to give your information to Mattermost. After authorizing you should be redirected on Mattermost connected with your account.

Keep in mind this will create a new account on your Mattermost server with information from LDAP. The process will fail if an existing user already use your LDAP email. To bind a user to the LDAP authentication, sign in mattermost with this user account, go in account settings > security > sign-in method and "switch to using Gitlab SSO".


## Limitation
This module has been tested on Centos 7, Fedora and Ubuntu with PostgreSQL.

Others operating systems has not been tested yet but should work fine. 

MySQL has not really been tested so it is possible there is some bugs with.


## To do list
 -> Gathering LDAP config
 -> Add CSS to make a beautiful interface for Oauth server
 -> Create an associated Puppet module
 -> Change Gitlab button
 -> Security audit

## Thanks

I wish to thank my company and my colleagues for their help and support. Also, I thank Brent Shaffer for his Oauth-server-php project and its documentation.


## Known issues
 * LDAP authentication failed
 Try to restart httpd service. If this persists verify your LDAP configuration or your credentials.

 * PHP date timezone error
 Edit php.ini to set up date.timezone option and restart httpd service.

 * Token request failes
 Try to add a new rule in your firewall (or use iptables -F on both Mattermost server and Oauth server)

 * .htaccess does not work
 Add following lines to your php.ini and restart httpd service.
 ```<Directory "/var/www/html/oauth">
    AllowOverride All
</Directory>
 ```









