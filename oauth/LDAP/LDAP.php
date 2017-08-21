<?php

/**
 * Simple LDAP object to interact with LDAP 
 *
 * @author Denis CLAVIER <clavierd at gmail dot com>
 */


require_once __DIR__.'/LDAPInterface.php';

class LDAP implements LDAPInterface
{
    protected $ldap_server;

     /**
     * LDAP Resource
     *
     * @param string @hostname
     * Either a hostname or, with OpenLDAP 2.x.x and later, a full LDAP URI
     * @param int @port
     * An optional int to specify ldap server port
     * 
     * Initiate LDAP connection by creating an associated resource  
     */
    public function __construct($hostname, $port = 389)
    {
        if (!is_string($hostname)) 
        {
            throw new InvalidArgumentException('First argument to LDAP must be the hostname of a ldap server (string). Ex: ldap//example.com/ ');
        }
        
        if (!is_int($port)) 
        {
            throw new InvalidArgumentException('Second argument to LDAP must be the ldap server port (int). Ex : 389');
        }

        $ldap = ldap_connect($hostname, $port) 
        	or die("Unable to connect to the ldap server : $ldaphost ! Please check your configuration.");

        $this->ldap_server = $ldap;
    }

     /**
     * @param string @rdn
     * A ldap user relative directory name 
     * @param string @password
     * An optional password linked to the specified rdn account, if not provided an anonymous bind is attempted
     * 
     * @return 
     * TRUE if the user is identified and can access to the LDAP server
     * and FALSE if it isn't  
     */
    public function checkLogin($rdn,$password = null) {
        if (!is_string($rdn)) 
        {
            throw new InvalidArgumentException('First argument to LDAP/checkLogin must be the relative directory name of a ldap user (string). Ex: uid=jdupont,ou=People,o=Company');
        }
        if (!is_string($password) && $password != null) 
        {
            throw new InvalidArgumentException('Second argument to LDAP/checkLogin must be the password associated to the relative directory name (string).');
        }

        return ldap_bind($this->ldap_server,$rdn,$password);
    }

     /**
     * @param string @base_dn
     * The LDAP base DN. 
     * @param string @filter
     * A filter to get relevant data. Often the user id in ldap (uid or sAMAccountName).
     * @param string @bind_dn
     * The directory name of a service user to bind before search. Must be a user with read permission on LDAP. 
     * @param string @bind_pass
     * The password associated to the service user to bind before search. 
     * 
     * @return 
     * An array with the user's mail and complete name.
     */
    public function getDataForMattermost($base_dn, $filter, $bind_dn, $bind_pass) {

    	$attribute=array("cn","mail");

        if (!is_string($base_dn)) 
        {
            throw new InvalidArgumentException('First argument to LDAP/getData must be the ldap base directory name (string). Ex: o=Company');
        }
        if (!is_string($filter)) 
        {
            throw new InvalidArgumentException('Second argument to LDAP/getData must be a filter to get relevant data. Often is the user id in ldap (string). Ex : uid=jdupont');
        }

        // If LDAP service account for search is specified, do an ldap_bind with this account
        if ($bind_dn != '' && $bind_dn != null)
        {
            $bind_result=ldap_bind($this->ldap_server,$bind_dn,$bind_pass);

            // If authentification failed, throw an exception 
            if (!$bind_result)
            {
                throw new Exception('An error has occured during ldap_bind execution. Please check parameter of LDAP/getData, and make sure that user provided have read permission on LDAP.');
            }
        }

        $result = ldap_search($this->ldap_server, $base_dn, $filter, $attribute, 0, 1, 500);

        if (!$result)
        {
        	throw new Exception('An error has occured during ldap_search execution. Please check parameter of LDAP/getData.');
        }

        $data = ldap_first_entry($this->ldap_server, $result);
        if (!$data)
        {
        	throw new Exception('An error has occured during ldap_first_entry execution. Please check parameter of LDAP/getData.');
        }

        $mail = ldap_get_values($this->ldap_server, $data, "mail");
        if (!$mail)
        {
        	throw new Exception('An error has occured during ldap_get_values execution (mail). Please check parameter of LDAP/getData.');
        }

        $cn = ldap_get_values($this->ldap_server, $data, "cn");
        if (!$cn)
        {
        	throw new Exception('An error has occured during ldap_get_values execution (complete name). Please check parameter of LDAP/getData.');
        }

        return array("mail" => $mail[0], "cn" => $cn[0]);
    }

    /*
	 * Destructor to close the LDAP connection 
     */
    public function __destruct() 
    {
    	ldap_close($this->ldap_server);
    }

}
