#!/bin/bash
#This script need right to become postgres user (so root) and to read/write in httpd directory

source config_init.sh

#######################################--Fonctions--###############################################

ok() { echo -e '\e[32m'$1'\e[m'; }
error() { echo -e '\e[31m'$1'\e[m'; }
info() { echo -e '\e[34m'$1'\e[m'; }
warn() { echo -e '\e[33m'$1'\e[m'; }

#######################################--SQL STATEMENT--###########################################

#Tables creation
create_table_oauth_client="CREATE TABLE oauth_clients (client_id VARCHAR(80) NOT NULL, client_secret VARCHAR(80), redirect_uri VARCHAR(2000) NOT NULL, grant_types VARCHAR(80), scope VARCHAR(100), user_id VARCHAR(80), CONSTRAINT clients_client_id_pk PRIMARY KEY (client_id));"
create_table_oauth_access_tokens="CREATE TABLE oauth_access_tokens (access_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT access_token_pk PRIMARY KEY (access_token));"
create_table_oauth_authorization_codes="CREATE TABLE oauth_authorization_codes (authorization_code VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), redirect_uri VARCHAR(2000), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT auth_code_pk PRIMARY KEY (authorization_code));"
create_table_oauth_refresh_tokens="CREATE TABLE oauth_refresh_tokens (refresh_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT refresh_token_pk PRIMARY KEY (refresh_token));"
create_table_users="CREATE TABLE users (id SERIAL NOT NULL, username VARCHAR(255) NOT NULL, CONSTRAINT id_pk PRIMARY KEY (id));"
create_table_oauth_scopes="CREATE TABLE oauth_scopes (scope TEXT, is_default BOOLEAN);"

#Client creation
create_client="INSERT INTO oauth_clients (client_id,client_secret,redirect_uri,grant_types,scope,user_id) VALUES ('$client_id','$client_secret','$redirect_uri','$grant_types','$scope','$user_id');"

###################################################################################################

#Welcome Message
info "This script will create a new Oauth role and an associated database for Mattermost-LDAP\nTo edit configuration please edit this script before running !\n"
warn "SuperUser right must be ask to create the new role and database in postgres\n"
info "Press ctrl+c to stop the script"

sleep 5

#Creating Oauth role and associated database (need admin account on postgres)
info "Creation of role $db_user and database $db_name ..."
psql -U postgres -c "CREATE DATABASE $db_name;"
psql -U postgres -c "CREATE USER $db_user WITH ENCRYPTED PASSWORD '$db_pass';"
psql -U postgres -c "GRANT ALL PRIVILEGES ON DATABASE $db_name TO $db_user;"

#Creating tables for ouath database (use oauth role)
info "Creation of tables for database $db_name (using $db_user)"
psql -U $db_user -d $db_name -c "$create_table_oauth_client"
psql -U $db_user -d $db_name -c "$create_table_oauth_access_tokens"
psql -U $db_user -d $db_name -c "$create_table_oauth_authorization_codes"
psql -U $db_user -d $db_name -c "$create_table_oauth_refresh_tokens"
psql -U $db_user -d $db_name -c "$create_table_users"
psql -U $db_user -d $db_name -c "$create_table_oauth_scopes"

#Insert new client in the database
info "Insert new client in the database"
psql -U $db_user -d $db_name -c "$create_client"

#Verification
psql -U $db_user -d $db_name -c "SELECT * from oauth_clients WHERE client_id='$client_id';" | grep '(1'

if [ $? ]
then ok "Client has been created ! Oauth Database is configured.\n"
info "Client ID : $client_id"
warn "Client Secret : $client_secret\n"
info "Keep id and secret, you will need them to configure Mattermost"
warn "Beware Client Secret IS PRIVATE and MUST BE KEPT SECRET"
else error "Client has not been created ! Check log below"
fi
