<?php

/**
 * Class to interact with LDAP
 *
 * @author Denis CLAVIER <clavierd at gmail dot com> 
 */
interface LDAPInterface
{
    /**
     * Check user credentials
     *
     * @param string @rdn
     * A ldap user relative directory name 
     * @param string @password
     * An optional password linked to the specified rdn account, if not provided an anonymous bind is attempted
     * 
     * @return 
     * TRUE if the user is identified and can access to the LDAP server
     * and FALSE if it isn't  
     */
    public function checkLogin($rdn,$password = null);

    /**
     * Return only necessary data for Mattermost 
     *
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
    public function getDataForMattermost($base_dn, $filter,$bind_dn,$bind_pass);
}
