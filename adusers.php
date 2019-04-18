<?php

	include (dirname(__FILE__) . "/adLDAP/adLDAP.php");
	
	try {
		$adldap = new adLDAP();
	}
	catch (adLDAPException $e) {
		echo $e; 
		exit();   
	}
	// Perform the search and grab all their details
	$includeDescription = false;
	$search = "*";
	$sorted = true;
	$filter = "(&(objectClass=user)(samaccounttype=" . adLDAP::ADLDAP_NORMAL_ACCOUNT .")(objectCategory=person)(cn=" . $search . "))";
	$fields = array("samaccountname","displayname");
	$sr = ldap_search($adldap->getLdapConnection(), $adldap->getBaseDn(), $filter, $fields);
	$entries = ldap_get_entries($adldap->getLdapConnection(), $sr);


?>
<?php 	//var_dump($entries); 	

	$users=array();
	foreach($entries as $i) {
		$user=array();
		$info=$adldap->user()->info($i["samaccountname"][0], array("physicalDeliveryOfficeName","mail","displayname","distinguishedname"));
		
		if (strpos($info[0]["distinguishedname"][0],"OU=DisabledUsers")==0) {
			$user["username"]=$i["samaccountname"][0];
			if (isset($info[0]["displayname"][0])) { 
				$user["name"]=$info[0]["displayname"][0];
			}
			else {
				$user["name"]="";
			}
			if (isset($info[0]["mail"][0])) { 
				$user["email"]=$info[0]["mail"][0];
			}
			else {
				$user["email"]="";
			}
				
			$users[]=$user;
		}

	}
?>
<pre>
<?php 
	$user = $adldap->user()->infoCollection('bmace', array('*'));
	var_dump($user);
	
	echo $user->givenName;
	echo $user->sn;
	echo $user->displayName."<br";

	$groupArray = $user->memberOf; 
	foreach ($groupArray as $group) {
	   echo $group."<br>";
	}

	if ($user->enabled) { 
		print "enabled";
	}
	else { 
		print "disabled";
	}
?>
</pre> 
<?php echo array_search("bmace", $users); ?>





