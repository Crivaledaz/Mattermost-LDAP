node 'bepo'
{
	########################---Config de Mattermost---########################### 
	class { 'postgresql::server':
	  ipv4acls => ['host all all 127.0.0.1/32 md5'],
	}
	postgresql::server::db { 'mattermost_db':
	   user     => 'mattermost',
	   password => postgresql_password('mattermost', 'mattermost_secure-pass'),
	}
	postgresql::server::database_grant { 'mattermost_db':
	  privilege => 'ALL',
	  db        => 'mattermost_db',
	  role      => 'mattermost',
	} ->
	class { 'mattermost':
	  version  => '4.0.2',
	  override_options => {
	  	'ServiceSettings' => {
        	'SiteURL' => "http://mattermost.company.com:8065",
        	'ListenAddress' => ":8065",
        },
	    'SqlSettings' => {
	      'DriverName' => 'postgres',
	      'DataSource' => "postgres://mattermost:mattermost_secure-pass@127.0.0.1:5432/mattermost_db?sslmode=disable&connect_timeout=10",
	    },
	    'GitLabSettings' => {
        'Enable' => true,
        'Secret' => "a7da08dc26fa84bf7254563fbd54d33ca22dc434844fa0c57161366852e82bab",
        'Id' => "a40e2e4aae54e8eb99b8fc7c0ea42702a6c66ad812d78a82cd9109e40b86c6d9",
        'Scope' => "api",
        'AuthEndpoint' => "http://oauth.company.com/oauth/authorize.php",
        'TokenEndpoint' => "http://oauth.company.com/oauth/token.php",
        'UserApiEndpoint' => "http://oauth.company.com/oauth/resource.php",
    	},
	  },
	}

	########################---Config de Oauth---########################### 
	
	postgresql::server::db { 'oauth_db':
	    user     => 'oauth',
	    password => postgresql_password('oauth', 'oauth_secure-pass'),
	}
	postgresql::server::database_grant { 'oauth_db':
	  	privilege => 'ALL',
	  	db        => 'oauth_db',
	  	role      => 'oauth',
	}

	class { 'mattermostldap':
	  	project_url  => 'http://repo.company.com/project.tar.gz',
	  	base_url   => 'http://mattermost.company.com:8065',
	  	install_path => '/var/www/html',
	  	ldap_base    => 'ou=People,o=Company',
	  	ldap_filter  => '',
	  	ldap_uri   => 'ldap://company.com',
	  	ldap_port    => 389,
	  	ldap_attribute   => 'uid',
	  	db_user    => 'oauth',
	  	db_pass    => 'oauth_secure-pass',
	  	db_name    => 'oauth_db',
	  	db_host    => 'localhost',
	  	db_port    => 5432,
	  	db_type    => 'pgsql',
	  	client_secret => "a7da08dc26fa84bf7254563fbd54d33ca22dc434844fa0c57161366852e82bab",
        client_id => "a40e2e4aae54e8eb99b8fc7c0ea42702a6c66ad812d78a82cd9109e40b86c6d9",
	}
}
