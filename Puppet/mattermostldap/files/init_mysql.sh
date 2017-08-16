#!/bin/bash
#This script need right to become mysql user (so root) and to read/write in httpd directory

source config_init.sh

#Creating tables for ouath database (use oauth role)
info "Creation of tables for database $oauth_db (using $oauth_user)"
mysql -u $oauth_user --password=$oauth_pass $oauth_db_name --execute "$create_table_oauth_client"
mysql -u $oauth_user --password=$oauth_pass $oauth_db_name --execute "$create_table_oauth_access_tokens"
mysql -u $oauth_user --password=$oauth_pass $oauth_db_name --execute "$create_table_oauth_authorization_codes"
mysql -u $oauth_user --password=$oauth_pass $oauth_db_name --execute "$create_table_oauth_refresh_tokens"
mysql -u $oauth_user --password=$oauth_pass $oauth_db_name --execute "$create_table_users"
mysql -u $oauth_user --password=$oauth_pass $oauth_db_name --execute "$create_table_oauth_scopes"

#Insert new client in the database
info "Insert new client in the database"
mysql -u $oauth_user --password=$oauth_pass $oauth_db_name --execute "$create_client"
