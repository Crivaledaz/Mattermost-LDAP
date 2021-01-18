Install using containers - Docker/Podman
========================================

The easiest way to setup Mattermost-LDAP is using the docker-compose implementation.

For production use, you must use the `docker-copose.yaml` file available at the root of this repository. Unlike the Demo, this docker-compose file only setup Mattermost-LDAP with an Apache server and a PostgreSQL database.

This implementation uses an embedded Oauth server, which can be configured by environment variables.

## Requirements

To use docker-compose implementation, you need to install Docker and Docker compose. For CentOS 8 and Fedora, it is recommended to use Podman and Podman compose instead of Docker and Docker compose.

For more information about Docker installation, see official guide : https://docs.docker.com/engine/install/

For more information about Podman installation, see official documentation : https://podman.io/getting-started/installation.html

## Preparation

First, you need to clone (or download and extract) this repository on your server :
```bash
git clone https://github.com/Crivaledaz/Mattermost-LDAP
cd Mattermost-LDAP
```

Then, before running the docker-compose file, you need to adapt LDAP and DB parameters. All parameters are gathered in the `env.example` file and they are passed to Postgres and Oauth server by environment variables.

Copy the `env.example` file to `.env` and edit it to change with your values.

**Warning** : Postgres root password and database Oauth password must be changed. Client and secret tokens must be generated randomly, using `openssl rand -hex 32`.

For more information about available parameters, refer to the [configuration section](https://github.com/Crivaledaz/Mattermost-LDAP#configuration) of the repository README.

Otherwise, for production, you need to create a directory to store PostgreSQL data. This directory will contain the Oauth database and allows data persistence, even if containers are stopped or restarted. By default, this Mattermost-LDAP implementation uses folder `data/` next to the `docker-compose.yaml` file to store data. This folder need to be created before running Docker compose :
```bash
mkdir data
```

To use Mattermost-LDAP with your own Mattermost server, you need to configure your Mattermost instance as described in section "Configure Mattermost".

## Configure Mattermost

Active Gitlab authentication in `System Console > Gitlab` (or `config.json`) and fill application id and secret with the two tokens got during install section. For the next fields use this :

```
User API Endpoint : http://HOSTNAME/oauth/resource.php
Auth Endpoint: http://HOSTNAME/oauth/authorize.php
Token Endpoint: http://HOSTNAME/oauth/token.php
```

Change `HOSTNAME` by hostname or ip of the server where you have installed Mattermost-LDAP module.

Since Mattermost 4.9, these fields are disabled in admin panel, so you need to edit directly section `GitLabSettings` in the Mattermost configuration file `config.json`.

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

Once the `.env` file have been adapted, you can run the docker-compose file with the following commands :
```bash
# With Docker
docker-compose build
docker-compose up -d

# With Podman
podman-compose build
podman-compose up -d
```

The build command allows Docker compose to build necessary image. Images use are available in the [Docker/](Docker) directory of this repository. The up command starts all services described in the Docker compose file.

Once all services are started, go to Mattermost server and click on GitLab button to login with LDAP credential on Mattermost-LDAP. Then, if you login successfully and authorize Mattermost-LDAP to transmit your data to Mattermost, you should be log on Mattermost.

To stop Mattermost server and Mattermost-LDAP, use the following command :
```bash
# With Docker
docker-compose down

# With Podman
podman-compose down
```

## Extension

### Additional information for usage with nginx-proxy, nginx-proxy-letsencrypt

In case you want to use `nginx-proxy`, `nginx-proxy-letsencrypt`, and (for example) `openldap`, it is possible to use subdomains for your services. Following this approach you could have mattermost running on on `https://chat.example.com` and authenticate via this container from `https://oauth.example.com`. This container will then have its own letsencypt certificate.

You can add the following settings to your configuration files for this type of setup.

In `docker-compose.yaml` :
```yaml
version: '3'

[...]

services:
    mattermost-ldap:

        [...]

        expose:
            - 80
            - 443

        environment:
            [...]
            - VIRTUAL_HOST=oauth.example.com,www.oauth.example.com
            - LETSENCRYPT_HOST=oauth.example.com,www.oauth.example.com

[...]
```

In `.env`:
```bash
[...]

redirect_uri = "https://chat.example.com/signup/gitlab/complete"

ldap_filter = "(&(objectClass=inetOrgPerson)(memberof=cn=chat,ou=groups,dc=example,dc=com))"

[...]
```

This filter will additionally allow you to filter based on group affiliation within your LDAP server.

Finally, add the following to your mattermost `config.json` to ensure the correct redirect.

```json
    "GitLabSettings": {
        "Enable": true,
        "Secret": "XXX",
        "Id": "YYY",
        "Scope": "",
        "AuthEndpoint": "https://oauth.example.com/oauth/authorize.php",
        "TokenEndpoint": "https://oauth.example.com/oauth/token.php",
        "UserApiEndpoint": "https://oauth.example.com/oauth/resource.php"
    },
```
