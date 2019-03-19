<?php
// LDAP parameters
$hostname = getenv('ldap_host') ?: "ldap://ldap.company.com/";
$port = intval(getenv('ldap_port')) ?: 389;
$ldap_version = intval(getenv('ldap_version')) ?: 3;

// Attribute use to identify user on LDAP - ex : uid, mail, sAMAccountName
$search_attribute = getenv('ldap_search_attribute') ?: "uid";

// variable use in resource.php
$base = getenv('ldap_base_dn') ?: "ou=People,o=Company";
$filter = getenv('ldap_filter') ?: "objectClass=*";

// ldap service user to allow search in ldap
$bind_dn = getenv('ldap_bind_dn') ?: "";
$bind_pass = getenv('ldap_bind_pass') ?: "";