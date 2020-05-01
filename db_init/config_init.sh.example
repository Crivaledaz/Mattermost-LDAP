#####################################--CONFIGURATION FILE--########################################

#Client configuration
client_id=$(if [ -z $client_id ]; then echo "123456789"; else echo $client_id; fi)
client_secret=$(if [ -z $client_secret ]; then echo "987654321"; else echo $client_secret; fi)
redirect_uri=$(if [ -z $redirect_uri ]; then echo "http://mattermost.company.com/signup/gitlab/complete"; else echo $redirect_uri; fi)
grant_types=$(if [ -z $grant_types ]; then echo "authorization_code"; else echo $grant_types; fi)
scope=$(if [ -z $scope ]; then echo "api"; else echo $client_id; fi)
user_id=$(if [ -z $user_id ]; then echo ""; else echo $user_id; fi)

#Database configuration
db_user=$(if [ -z $db_user ]; then echo "oauth"; else echo $db_user; fi)
db_name=$(if [ -z $db_name ]; then echo "oauth_db"; else echo $db_name; fi)
db_pass=$(if [ -z $db_pass ]; then echo "oauth_secure-pass"; else echo $db_pass; fi)
db_host=$(if [ -z $db_host ]; then echo "localhost"; else echo $db_host; fi)
db_port=$(if [ -z $db_port ]; then echo "5432"; else echo $db_port; fi)
