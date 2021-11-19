Install on Bare Metal
=====================

This file describes the process to install Mattermost-LDAP on a Bare Metal server.

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
sudo yum -y --nogpgcheck install httpd php postgresql-server postgresql php-ldap php-pdo php-xml php-pgsql git

#For MySQL
sudo yum -y --nogpgcheck install httpd php mariadb-server mariadb php-ldap php-pdo php-xml php-mysql git
```

* For Debian, ubuntu, Mint :
```bash
#For PostgreSQL
sudo apt-get -y install httpd php postgresql-server postgresql php-ldap php-pdo php-dom php-pgsql git

#For MySQL
sudo apt-get -y install httpd php mariadb-server mariadb php-ldap php-pdo php-dom php-mysql git
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

You need to create a database for the Oauth server. For this purpose, you can use the script `init_postgres.sh` or `init_mysql.sh`, available in `db_init` directory.

These scripts try to configure your database automatically, by creating a new user and a new database associated for the Oauth server. Scripts also create all tables necessary for the module. If the script fail, please report here, and try to configure manually your database by adapting the commands in scripts.

Before running the script you can change the default settings by editing the `db_init/config_init.sh` file and modifying configuration variables.

For more information about available parameters, refer to the [configuration section](https://github.com/Crivaledaz/Mattermost-LDAP#configuration) of the repository README.

For PostgreSQL, you can copy and paste following lines :

```bash
cd db_init
vim config_init.sh
./init_postgres.sh
```

This script will automatically create and add a new client in the Oauth server, returning a client id and a client secret. You need to keep these two token to configure Mattermost. Please be sure the client secret remained secret.

The redirect uri in the script must comply with the hostname of your Mattermost server, or else Mattermost will not be able to get data from the Oauth server. If you update your hostname, you will need to update this value.  Here is an example query:

```sql
UPDATE oauth_clients SET redirect_uri = 'https://mattermost.company.com/signup/gitlab/complete' WHERE client_id = '1234567890';
```

**Warning** : The `redirect_uri` parameter should be strictly the same as the one given by Mattermost to Oauth server during authentication. If your Mattermost server uses HTTPS, make sure the `redirect_uri` begin with `https`.

*Note* : Mattermost build the `redirect_url` from the parameter `SiteURL` in `config.json`. Thus, if you set this parameter to `https://mattermost.company.com`, Mattermost will use the following redirect URL : http**s**://mattermost.company.com/signup/gitlab/complete (`SiteURL` + '/signup/gitlab/complete').

### Configuration

To complete the installation process you need to configure the Oauth server, by editing the following files :

- `oauth/LDAP/config_ldap.php` : LDAP configuration to allow user authentication on the LDAP server.
- `oauth/config_db.php` : Database configuration to allow Oauth server to store data.

For more information about available parameters, refer to the [configuration section](https://github.com/Crivaledaz/Mattermost-LDAP#configuration) of the repository README.

### Configure Mattermost

Active Gitlab authentication in `System Console > Gitlab` (or `config.json`) and fill application id and secret with the two tokens got during install section. For the next fields use this :

```
User API Endpoint : http://HOSTNAME/oauth/resource.php
Auth Endpoint: http://HOSTNAME/oauth/authorize.php
Token Endpoint: http://HOSTNAME/oauth/token.php
```

Change `HOSTNAME` by hostname or ip of the server where you have installed Mattermost-LDAP module.

Since Mattermost 4.9, these fields are disabled in admin panel, so you need to edit directly section `GitLabSettings` in the Mattermost configuration file `config.json`.

Since Mattermost 6.x the configuration needs to be changed in the `Configurations` SQL table. After setting the `Gitlab Site URL`, `Application ID` & `Application Secret Key` via the System Console, the following SQL commands can fix the URLs:
- `UPDATE Configurations SET Value=REPLACE(Value, 'https://mm-ldap.example.com/oauth/authorize', 'https://mm-ldap.example.com/oauth/authorize.php');`
- `UPDATE Configurations SET Value=REPLACE(Value, 'https://mm-ldap.example.com/oauth/token', 'https://mm-ldap.example.com/oauth/token.php');`
- `UPDATE Configurations SET Value=REPLACE(Value, 'https://mm-ldap.example.com/api/v4/user', 'https://mm-ldap.example.com/oauth/resource.php');`

In the `config.json` file, GitLab configuration is gathered in the section `GitLabSettings`. You have to enable it and to fill parameters with your values. Once completed, the section should look like :

```
    "GitLabSettings": {
        "Enable": true,
        "Secret": "fedcba987654321fedcba987654321",
        "Id": "123456789abcdef123456789abcdef",
        "Scope": "",
        "AuthEndpoint": "http://localhost/oauth/authorize.php",
        "TokenEndpoint": "http://localhost/oauth/token.php",
        "UserApiEndpoint": "http://localhost/oauth/resource.php"
    },
```

*Note* : You need to restart the Mattermost server to take into account the change.

## Usage

If you have succeeded the previous step you only have to go to the login page of your Mattermost server and click on the Gitlab Button. You will be redirected to a form asking for your LDAP credentials. If your credentials are valid, you will be asked to authorize Oauth to give your information to Mattermost. After authorizing you should be redirected on Mattermost connected with your account.

Keep in mind this will create a new account on your Mattermost server with information from LDAP. The process will fail if an existing user already use your LDAP email. To bind an existing user to the LDAP authentication, sign in Mattermost with this user account, go in `account settings > security > sign-in method and "switch to using Gitlab SSO"`.
