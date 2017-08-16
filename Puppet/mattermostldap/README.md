Mattermost-LDAP Puppet Module
=============================

This is a puppet module to manage configuration and installation of Mattermost-LDAP.

## Overview

Mattermost-LDAP is a module which provides an external LDAP authentication in Mattermost for the Team Edition. Actually, Mattermost and LDAP are mainly used by companies which should manage their servers, services and configurations with automated processes. Many companies use Puppet, an open-source software configuration management tool, to automated their configuration deployement.

Mattermost-LDAP project provides a Puppet module to easily manage and configure the Oauth serveur and the LDAP for Mattermost.   

## Module Description

This module installs and configures Mattermost-LDAP, to provide LDAP support on Mattermost. For more information about Mattermost-LDAP please refer to : https://github.com/Crivaledaz/Mattermost-LDAP

The use of this Puppet module substitute to the standard installation and configuration steps described on the Mattermost-LDAP project page. See below to install and configure Mattermost-LDAP with Puppet.

The Puppet Mattermost-LDAP module installs the Oauth server and associated files from a release archive provided in this repository, create and configure a database for the Oauth server depending on your database server (PostgreSQL or MySQL), and configures the Oauth server to interact with LDAP according to settings you provide.


## Setup 
### Requirements
This module requires the following : 

* Puppet (3.8.7 min)
* puppet/archive
* puppetlabs/stdlib  
* git

To know the necessary dependencies for Mattermost-LDAP (which will be installed with this puppet module), please refer to : https://github.com/Crivaledaz/Mattermost-LDAP

### Pre-install
* Install Puppet (Centos 7, RHEL 7 and Fedora) :

```
# On Puppet Client
sudo yum -y --nogpgcheck install puppet
echo "server=SERVER_NAME" >> /etc/puppet/puppet.conf 

# On Puppet Master :
yum install -y --nogpgcheck puppet puppet-server
systemctl start puppetmaster

# On Puppet Client
puppet agent -t

# On Puppet Master
puppet cert sign CLIENT_NAME

```
Change SERVER_NAME and CLIENT_NAME by your settings.

* Install required Puppet modules :

```
# On Puppet Master
puppet module install puppetlabs-stdlib --version 4.17.0
puppet module install puppet-archive --version 1.3.0 
	
```

Your system is ready to use Puppet module Mattermost-LDAP.


## Beginning with Mattermost-LDAP Puppet

Clone (or download and extract) this repository :
```
git clone https://github.com/crivaledaz/Mattermost-LDAP.git
```

Move mattermostldap directory from the Puppet directory to /etc/puppet/modules on your Puppet Master, in order to add this module in Puppet. Make a tar.gz archive with the Oauth directory and it is recommended to put this archive on a http server. Thus, the archive will be reachable from a url.

If you have already a Mattermost server running, and a suitable database configured for the Oauth server, this is the minimum you need to get Mattermost-LDAP working:

```
class { 'mattermostldap':
	  	project_url  => 'http://myserver.com/project.tar.gz',
	  	base_url   => 'http://mattermost.company.org',
	  	install_path => '/var/www/html',
	  	ldap_base    => 'o=Company',
	  	ldap_uri   => 'ldap://company.org',
	  	ldap_port    => 389,
	  	ldap_rdn   => 'ou=People,o=Company',
	  	db_user    => 'oauth',
	  	db_pass    => 'oauth_secure-pass',
	  	db_name    => 'oauth_db',
	  	db_host    => 'localhost',
	  	db_port    => 5432,
	  	db_type    => 'pgsql',
	  	client_secret => "a7da08dc26fa84bf7254563fbd54d33ca22dc434844fa0c57161366852e82bab",
        client_id => "a40e2e4aae54e8eb99b8fc7c0ea42702a6c66ad812d78a82cd9109e40b86c6d9",
        $timezone   = 'Europe/Paris'
	}
```

This will download project.tar.gz from your server, and extract the archive in /var/www/html. After running, this module ensure that PHP, php-pdo, php-ldap, php-pgsql is installed and httpd is installed and running. The oauth database will be configured and an oauth client, for Mattermost, will be created with id and secret provide. Moreover, Oauth server will be configure to interact with the LDAP and the oauth database.  

Below, there is an example of Mattermost-LDAP Puppet module using Mattermost and PostgreSQL puppet module to install and configure all running on the same server (requires puppetlabs/postgresql and liger1978/mattermost):

```
########################---Config Mattermost---########################### 
	class { 'postgresql::server':
	  ipv4acls => ['host all all 127.0.0.1/32 md5'],
	}
	postgresql::server::db { 'mattermost_db':
	   user     => 'mattermost',
	   password => postgresql_password('mattermost', 'mattermost_secure-pass'),
	}
	postgresql::server::database_grant { 'mattermost_db':
	  privilege => 'ALL',
	  db        => 'mattermost_db',
	  role      => 'mattermost',
	} ->
	class { 'mattermost':
	  version  => '4.0.2',
	  override_options => {
	  	'ServiceSettings' => {
        	'SiteURL' => "http://mattermost.company.com:8065",
        },
	    'SqlSettings' => {
	      'DriverName' => 'postgres',
	      'DataSource' => "postgres://mattermost:mattermost_secure-pass@127.0.0.1:5432/mattermost_db?sslmode=disable&connect_timeout=10",
	    },
	    'GitLabSettings' => {
        'Enable' => true,
        'Secret' => "a7da08dc26fa84bf7254563fbd54d33ca22dc434844fa0c57161366852e82bab",
        'Id' => "a40e2e4aae54e8eb99b8fc7c0ea42702a6c66ad812d78a82cd9109e40b86c6d9",
        'Scope' => "api",
        'AuthEndpoint' => "http://oauth.company.com/oauth/authorize.php",
        'TokenEndpoint' => "http://oauth.company.com/oauth/token.php",
        'UserApiEndpoint' => "http://oauth.company.com/oauth/resource.php",
    	},
	  },
	}

	########################---Config Oauth---########################### 
	
	postgresql::server::db { 'oauth_db':
	    user     => 'oauth',
	    password => postgresql_password('oauth', 'oauth_secure-pass'),
	}
	postgresql::server::database_grant { 'oauth_db':
	  	privilege => 'ALL',
	  	db        => 'oauth_db',
	  	role      => 'oauth',
	}

	class { 'mattermostldap':
	  	project_url  => 'http://myserver.com/project.tar.gz',
	  	base_url   => 'http://mattermost.company.com:8065',
	  	install_path => '/var/www/html',
	  	ldap_base    => 'o=Company',
	  	ldap_uri   => 'ldap://company.com',
	  	ldap_port    => 389,
	  	ldap_rdn   => 'ou=People,o=Company',
	  	db_user    => 'oauth',
	  	db_pass    => 'oauth_secure-pass',
	  	db_name    => 'oauth_db',
	  	db_host    => 'localhost',
	  	db_port    => 5432,
	  	db_type    => 'pgsql',
	  	client_secret => "a7da08dc26fa84bf7254563fbd54d33ca22dc434844fa0c57161366852e82bab",
        client_id => "a40e2e4aae54e8eb99b8fc7c0ea42702a6c66ad812d78a82cd9109e40b86c6d9",
        $timezone   = 'Europe/Paris'
	}
```
With the code above, you should be able to access the Mattermost application at http://mattermost.company.com:8065 (with your company address) and sign in with your LDAP credentials using the Gitlab button.

Please refer to ligger1978/mattermost and puppetlabs/postgresql modules in puppet forge for more information about the use of these modules. 


## Usage
If you have succeeded on previous step you only have to go to the login page of your Mattermost server and click on the Gitlab Button. You will be redirected to a form asking for your LDAP credentials. If your credentials are valid, you will be asked to authorize Oauth to give your information to Mattermost. After authorizing you should be redirected on Mattermost connected with your account.

Keep in mind this will create a new account on your Mattermost server with information from LDAP. The process will fail if an existing user already use your LDAP email. To bind a user to the LDAP authentication, sign in mattermost with this user account, go in account settings > security > sign-in method and "switch to using Gitlab SSO".


## References

#### project_url (Required)
The URL or the path of the project archive (which contains the Oauth directory)

#### base_url (Required)
The base URL of your Mattermost server. This is the URL provided in the site URL field in Mattermost admin panel. (ex : http://mattermost.company.com or http://mattermost.company.com:8065)

#### install_path (Optional)
Directory where the Oauth server will be installed, by default /var/www/html/. The directory must be your httpd root directory. 	

#### ldap_base (Required)
The base directory name of your LDAP server. (ex : ou=People,o=Company)	

#### ldap_filter (Optional)
Additional filters for your LDAP, see LDAP.php class for more information (used by resource.php to get user informations)

#### ldap_uri (Required)
Your LDAP hostname or LDAP IP, to connect to the LDAP server.

#### ldap_port (Optional)
Your LDAP port, to connect to the LDAP server. By default : 389.

#### ldap_rdn (Required)
The LDAP Relative Directory Name suffix to identify a user in LDAP, see LDAP.php class for more information (use by authorize.php to check user credentials on LDAP) 	

#### db_user (Optional)
Oauth user in the database. This user must have rights on the Oauth database to store Oauth tokens. By default : oauth	

#### db_pass (Optional)
Oauth user password in the database. By default, oauth_secure-pass

#### db_host (Optional)
Hostname or IP address of the database. By default : localhost 		

#### db_port
The database port to connect. By default : 5432 (postgres) 		

#### db_type (Optional)
Database type to adapt scripts and configurations to your database server. Should be mysql or pqsql. By default : pgsql

#### db_name (Optional)
Database name for oauth server. By default : oauth_db	

#### client_id (Required)	
The application ID shared with mattermost. This ID should be a random token. You can use openssl to generate this token (openssl rand -hex 32). If the ID is not filled, the database will not be initialised and the client will not be created.

#### client_secret (Required)
The application secret shared with mattermost. This secret should be a random token. You can use openssl to generate this token (openssl rand -hex 32). If the secret is not filled, the database will not be initialised and the client will not be created. The secret must be different of the client ID.

#### redirect_uri (Optional)
The callback address where Oauth will send tokens to Mattermost. Normally it should be http://mattermost.company.com/signup/gitlab/complete (and this is the default value).

#### grant_types (Optional)
The type of authentification use by Mattermost. It should be authorization_code (default value).

#### scope (Optional)
The scope of authentification use by Mattermost. It should be api (default value).

#### user_id (Optional)
The username of the user who create the Mattermost client in Oauth. This field has no impact, and could be used as a commentary field. By default this field is empty.

#### timezone (Optional)
The date.timezone parameter for oauth server script. This parameter will set timezone only for this script. This parameter must be set to avoid E.Notice raise by strtotime() (in Pdo.php). Note that if date.timezone is not defined, Mattermost could return a bad token request error. By default Europe/Paris (Because I love my country :D)	


## Limitation
This module has been tested on Centos 7 with PostgreSQL.

Others operating systems has not been tested yet but should work fine. 

MySQL has not really been tested so it is possible there is some bugs with.

## Thanks

I wish to thank my company and my colleagues for their help and support. Also, I thank ligger1978 for his Mattermost Puppet module which inspires me a lot.


## Known issues
 * LDAP authentication failed
 Try to restart httpd service. If this persists verify your LDAP configuration or your credentials.

 * PHP date timezone error
 Edit php.ini to set up date.timezone option and restart httpd service.

 * Token request failed
 Try to add a new rule in your firewall (or use iptables -F on both Mattermost server and Oauth server)

 * .htaccess does not work
 Add following lines to your httpd.conf and restart httpd service.
 ```<Directory "/var/www/html/oauth">
    AllowOverride All
</Directory>
 ```









