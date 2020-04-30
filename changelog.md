Change Log
==========

## Mattermost-LDAP 2.0

This new version of Matermost-LDAP brings many changes and some new features.

- Update Oauth server : Integrate Oauth server from bshaffer : https://github.com/bshaffer/oauth2-server-php/releases/tag/v1.11.1
- Compatibility with PHP 7 (issue #41)
- Add possibility to change user in the authorization page (issue #44)
- Authorization is now required only the first time (issue #45)
- New lightweight Docker image
- Docker compose implementation for easy setup
- Add a Docker-Compose demo, to allow testing Mattermost and Mattermost-LDAP in a PoC.
- Force username in lowercase (PR #40)
- Allow complex LDAP filter
- Cleaning repository. Database scripts have moved to folder `db_init`

Breaking changes :
- LDAP filter parameter syntax has changed. Now `ldap_filter` value must use the LDAP syntax and need to be included into parenthesis.
- Some variable names have been changed, mainly parameters for database in init scripts.

If you find a bug or a regression in this new version, let me know using issue tracker on Github : https://github.com/Crivaledaz/Mattermost-LDAP/issues
