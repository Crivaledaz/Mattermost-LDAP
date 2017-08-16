class mattermostldap::install inherits mattermostldap {
	

	if $mattermostldap::db_type == 'mysql'
    {
		$packets = ['httpd','php','php-pdo','php-mysql','php-ldap']	
    }
    
    if $mattermostldap::db_type == 'pgsql'
    {  
      	$packets = ['httpd','php','php-pdo','php-pgsql','php-ldap']
    }

    #Extract Mattermost-LDAP project in selected install path
	archive { '/tmp/project.tar.gz':
  		ensure        => present,
  		extract       => true,
  		extract_path  => '/var/www/html',
  		source        => $mattermostldap::project_url,
  		cleanup       => true,
	}

	#Install necessary packets for Mattermost-LDAP
	package { $packets: ensure => latest } ->

	#Start apache server 
	service { 'httpd':
    	ensure => 'running',
  }
}


