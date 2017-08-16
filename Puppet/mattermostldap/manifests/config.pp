class mattermostldap::config inherits mattermostldap {
  #Path to configuration files
  $conf_ldap = "${mattermostldap::install_path}/oauth/LDAP/config_ldap.php"
  $conf_db = "${mattermostldap::install_path}/oauth/config_db.php"
  $conf_init = "${mattermostldap::install_path}/oauth/config_init.sh"

  #Apply template on configuration files
  file{ $conf_ldap:
    path    => $conf_ldap,
    ensure  => file,
    content => template('mattermostldap/config_ldap.php.erb'),
  }

  file{ $conf_db:
    path    => $conf_db,
    ensure  => file,
    content => template('mattermostldap/config_db.php.erb'),
  }

  #If client_id and client_secret are provided, set up the oauth server
  if $mattermostldap::client_id and $mattermostldap::client_secret
  {

    #Get init file depending on database type choose 
    if $mattermostldap::db_type == 'mysql'
    {
      file{ "${mattermostldap::install_path}/oauth/init_mysql_puppet.sh":
      source  => 'puppet:///modules/mattermostldap/init_mysql.sh',
      ensure  => file,
      owner   =>  'root',
      group   =>  'root',
      mode    =>  '740',
      }
      $init_script="${mattermostldap::install_path}/oauth/init_mysql_puppet.sh"
    }
    
    if $mattermostldap::db_type == 'pgsql'
    {  
      file{ "${mattermostldap::install_path}/oauth/init_postgres_puppet.sh":
        source  => 'puppet:///modules/mattermostldap/init_postgres.sh',
        ensure  => file,
        owner   =>  'root',
        group   =>  'root',
        mode    =>  '740',
      }
      $init_script="${mattermostldap::install_path}/oauth/init_postgres_puppet.sh"
    }

    #Apply template on configuration files
    file { $conf_init:
      path    => $conf_init,
      ensure  => file,
      content => template('mattermostldap/config_init.sh.erb'),
    } ->

    #If init configuration file has been modified, the init script is executed
    #/!\ If a client with the same client_id is already in the database, the script will failed

    exec {'init oauth_db tables':
      command   => $init_script,
      cwd       => "${mattermostldap::install_path}/oauth/",
      path      => '/usr/bin:/bin',
      user      => 'root',
      subscribe   => File[$conf_init],
      refreshonly => true,
    }
  }
}
