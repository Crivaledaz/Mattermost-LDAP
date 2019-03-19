# ciaas/mattermostldap-postgres
# Create and setup the database for Mattermost-LDAP
# For more information, please refer to the Mattermost-LDAP project.
# > https://github.com/crivaledaz/Mattermost-LDAP

# Start from a minimal PostgreSQL image
FROM postgres:alpine

# Copy init script in the container
ADD ./files /docker-entrypoint-initdb.d/

# Prepare data for persistence
VOLUME /var/lib/postgresql/data
