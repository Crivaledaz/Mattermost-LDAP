#####################################--CONFIGURATION FILE--########################################

#Client configuration
client_id=$(if [ -z $client_id ]; then echo "123456789"; else echo $client_id; fi)
client_secret=$(if [ -z $client_secret ]; then echo "987654321"; else echo $client_secret; fi)
redirect_uri=$(if [ -z $redirect_uri ]; then echo "http://mattermost.company.com/signup/gitlab/complete"; else echo $redirect_uri; fi)
grant_types=$(if [ -z $grant_types ]; then echo "authorization_code"; else echo $grant_types; fi)
scope=$(if [ -z $scope ]; then echo "api"; else echo $client_id; fi)
user_id=$(if [ -z $user_id ]; then echo ""; else echo $user_id; fi)

#Database configuration
oauth_user=$(if [ -z $oauth_user ]; then echo "oauth"; else echo $oauth_user; fi)
oauth_db_name=$(if [ -z $oauth_db_name ]; then echo "oauth_db"; else echo $oauth_db_name; fi)
oauth_pass=$(if [ -z $oauth_pass ]; then echo "oauth_secure-pass"; else echo $oauth_pass; fi)
ip=$(if [ -z $db_host ]; then echo "localhost"; else echo $ip; fi)
port=$(if [ -z $db_port ]; then echo "5432"; else echo $port; fi)
