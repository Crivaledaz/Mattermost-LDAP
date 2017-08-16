#!/bin/bash
#This script need right to become postgres user (so root) and to read/write in httpd directory

source config_init.sh

#Creating tables for oauth database (use oauth role)
psql postgres://$oauth_user:$oauth_pass@$ip:$port/$oauth_db_name -c "$create_table_oauth_client"
psql postgres://$oauth_user:$oauth_pass@$ip:$port/$oauth_db_name -c "$create_table_oauth_access_tokens"
psql postgres://$oauth_user:$oauth_pass@$ip:$port/$oauth_db_name -c "$create_table_oauth_authorization_codes"
psql postgres://$oauth_user:$oauth_pass@$ip:$port/$oauth_db_name -c "$create_table_oauth_refresh_tokens"
psql postgres://$oauth_user:$oauth_pass@$ip:$port/$oauth_db_name -c "$create_table_users"
psql postgres://$oauth_user:$oauth_pass@$ip:$port/$oauth_db_name -c "$create_table_oauth_scopes"

#Insert new client in the database
psql postgres://$oauth_user:$oauth_pass@$ip:$port/$oauth_db_name -c "$create_client"
