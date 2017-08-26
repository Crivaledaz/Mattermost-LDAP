class mattermostldap (
	$project_url	= $mattermostldap::params::project_url,
	$base_url		= $mattermostldap::params::base_url,
	$install_path	= $mattermostldap::params::install_path,
	$ldap_base		= $mattermostldap::params::ldap_base,
	$ldap_filter	= $mattermostldap::params::ldap_filter,
	$ldap_uri		= $mattermostldap::params::ldap_uri,
	$ldap_port		= $mattermostldap::params::ldap_port,	
	$ldap_attribute	= $mattermostldap::params::ldap_attribute,
	$db_user		= $mattermostldap::params::db_user,
	$db_pass		= $mattermostldap::params::db_pass,
	$db_host		= $mattermostldap::params::db_host,
	$db_port		= $mattermostldap::params::db_port,
	$db_type		= $mattermostldap::params::db_type,
	$db_name		= $mattermostldap::params::db_name,
	$client_id		= $mattermostldap::params::client_id,
	$client_secret	= $mattermostldap::params::client_secret,
	$redirect_uri	= "\"${base_url}/signup/gitlab/complete\"",
	$grant_types	= $mattermostldap::params::grant_types,
	$scope			= $mattermostldap::params::scope,
	$user_id		= $mattermostldap::params::user_id,
	$timezone		= $mattermostldap::params::timezone,
	$ldap_bind_dn	= $mattermostldap::params::ldap_bind_dn,
	$ldap_bind_pass	= $mattermostldap::params::ldap_bind_pass,

) inherits mattermostldap::params {
	
	validate_string($project_url)
	validate_string($base_url)
	validate_absolute_path($install_path)
	validate_string($ldap_base)
	validate_string($ldap_filter)
	validate_string($ldap_uri)
	validate_integer($ldap_port)
	validate_string($ldap_attribute)
	validate_string($db_user)
	validate_string($db_pass)
	validate_string($db_host)
	validate_integer($db_port)
	validate_re($db_type,['^mysql$','^pgsql$'])
	validate_string($db_type)
	validate_string($db_name)
	validate_string($client_id)
	validate_string($client_secret)
	validate_string($redirect_uri)
	validate_string($grant_types)
	validate_string($scope)
	validate_string($user_id)
	validate_string($timezone)
	validate_string($ldap_bind_dn)
	validate_string($ldap_bind_pass)		



  anchor { 'mattermostldap::begin': } ->
  class { '::mattermostldap::install': } ->
  class { '::mattermostldap::config': } ->
  anchor { 'mattermostldap::end': }
}
