# mattermostldap-oauth
# Create and configure a Docker image to setup an Oauth Server (Mattermost-LDAP)
# For more information, please refer to the Mattermost-LDAP project.
# > https://github.com/crivaledaz/Mattermost-LDAP

# Start from a CentOS 7 image
FROM centos:latest

# Update packages and install dependencies
RUN yum update -y && yum -y install httpd php postgresql php-ldap php-pdo php-pgsql git

# Retrieve Mattermost-LDAP from git repository
RUN git clone https://github.com/crivaledaz/Mattermost-LDAP.git Mattermost-LDAP/

# Change workdir
WORKDIR Mattermost-LDAP/

# Install server Oauth
RUN cp -r oauth/ /var/www/html/

# Get config files with custom parameters
ADD ./files .

# Copy config files in Oauth server
RUN cp config_ldap.php /var/www/html/oauth/LDAP/ && cp config_db.php /var/www/html/oauth/

# Open and expose port 80 for Apache server
EXPOSE 80

# Start Apache server 
CMD ["/usr/sbin/httpd", "-DFOREGROUND"]
