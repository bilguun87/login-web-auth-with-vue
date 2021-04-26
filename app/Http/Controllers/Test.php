<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Test extends Controller
{
    //
    public function ldap_test(){
    	$ldaprdn  = 'test@statebank.mn';     // ldap rdn or dn
		$ldappass = 'Asdf##123';  // associated password

		// connect to ldap server
		$ldapconn = ldap_connect("ldap://statedc1.statebank.mn")
		    or die("Could not connect to LDAP server.");

		if ($ldapconn) {

		    // binding to ldap server
		    $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

		    // verify binding
		    if ($ldapbind) {
		        echo "LDAP bind successful...";
		    } else {
		        echo "LDAP bind failed...";
		    }

		}
    }
}
