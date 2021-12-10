Install using containers - Docker/Podman
========================================

The easiest way to setup Mattermost-LDAP is using the docker-compose implementation.

For production use, you must use the [`docker-compose.yaml`](https://github.com/Crivaledaz/Mattermost-LDAP/blob/master/docker-compose.yaml) file available at the root of this repository. Unlike the Demo, this docker-compose file only setup Mattermost-LDAP with an Nginx server linked to a PHP engine and a PostgreSQL database.

This implementation uses the repository Oauth server, which can be configured by environment variables.

## Requirements

To use docker-compose implementation, you need to install Docker and Docker compose. For CentOS 8 and Fedora, it is recommended to use Podman and Podman compose instead of Docker and Docker compose.

For more information about Docker installation, see official guide : https://docs.docker.com/engine/install/

For more information about Podman installation, see official documentation : https://podman.io/getting-started/installation.html

## Preparation

First, you need to clone (or download and extract) this repository on your server with:
```bash
git clone https://github.com/Crivaledaz/Mattermost-LDAP
cd Mattermost-LDAP
```

Then, before running the docker-compose file, you need to adapt LDAP and DB parameters. All parameters are gathered in `environment` sections in the [`docker-compose.yaml`](https://github.com/Crivaledaz/Mattermost-LDAP/blob/master/docker-compose.yaml) file and they are passed to Postgres and Oauth server by environment variables.

You must edit the docker-compose file to adapt parameters with your values.

**Warning** : Postgres root password and database Oauth password must be changed. Client and secret tokens must be generated randomly, using `openssl rand -hex 32`.

For more information about available parameters, refer to the [configuration section](https://github.com/Crivaledaz/Mattermost-LDAP#configuration) of the repository README.

Otherwise, for production, you need to create a directory to store PostgreSQL data. This directory will contain the Oauth database and allows data persistence, even if containers are stopped or restarted. By default, this Mattermost-LDAP implementation uses folder `data/` next to the `docker-compose.yaml` file to store data. This folder need to be created before running Docker compose :

```bash
mkdir data
```

To use Mattermost-LDAP with your own Mattermost server, you need to configure your Mattermost instance as described in section "Configure Mattermost" below.

## Configure Mattermost

Active Gitlab authentication in Mattermost configuration and fill GitLab parameters, with your values. To do this, you need to edit the `config.json` file or change parameters in Mattermost configuration table if you use configuration in the database.

In the `config.json` file, GitLab configuration is gathered in the section `GitLabSettings`. Adapt this section with your values, it should seems like this :

```
    "GitLabSettings": {
        "Enable": true,
        "Secret": "fedcba987654321fedcba987654321",
        "Id": "123456789abcdef123456789abcdef",
        "Scope": "",
        "AuthEndpoint": "https://<HOSTNAME>/oauth/authorize.php",
        "TokenEndpoint": "https://<HOSTNAME>/oauth/token.php",
        "UserApiEndpoint": "https://<HOSTNAME>/oauth/resource.php"
    },
```

Change `<HOSTNAME>` by  the hostname or ip of the server where you have installed Mattermost-LDAP module. The `Secret` and `Id` parameters should contain the tokens generated previously.

**Note** : You need to restart the Mattermost server to take into account the change.

## HTTPS configuration

Since Mattermost-LDAP version 2.1, HTTPS is enable by default to protect sensitive data exchanged between users and Mattermost-LDAP (LDAP username and password).

You need to provide a pair of TLS certificates and to store these in a directory named `certs`. To generate self-signed certificates you can use the following command :

```bash
mkdir certs
openssl req -x509 -newkey rsa:4096 -sha256 -days 364 -nodes -keyout certs/key.pem -out certs/cert.pem -subj '/CN=<HOSTNAME>' -extensions san -config <(   echo '[req]';   echo 'distinguished_name=req';   echo '[san]';   echo 'subjectAltName=DNS:localhost,<HOSTNAME>')
```

Replace `<HOSTNAME>` by the hostname serving the Oauth server (ie: the server where you have installed Mattermost-LDAP).

**Remark** : By default, Mattermost does not trust self-signed certificate. To remediate you need to add the certificate to the Mattermost server certificate bundle or change the parameter `EnableInsecureOutgoingConnection` to true in the Mattermost configuration (`config.json`).

Alternatively, you can use your own certificates and place them in the `certs` directory. This directory will be consumed by the Nginx container as a volume.

*Note* : Your certificates should be in PEM format and must be named `cert.pem` and `key.pem`, to match the Nginx configuration.

## Usage

Once you have adapted environement paramters in the docker-compose file, you can run Mattermost-LDAP with the following commands (from the root of the repository) :
```bash
# With Docker
docker-compose up -d

# With Podman
podman-compose up -d
```
The previous command starts all services described in the Docker compose file. The `-d` argument allows to start all container in background, in a detached mode.

Once all services are started, go to Mattermost server and click on GitLab button to login with LDAP credential on Mattermost-LDAP. Then, if you login successfully and authorize Mattermost-LDAP to transmit your data to Mattermost, you should be log on Mattermost.

To stop Mattermost-LDAP, use the following command :
```bash
# With Docker
docker-compose down

# With Podman
podman-compose down
```
