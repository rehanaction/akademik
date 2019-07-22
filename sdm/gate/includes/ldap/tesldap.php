<?php 

error_reporting(E_ALL & ~E_NOTICE);

// Application specific LDAP login
$app_user = 'uid=authenticate,ou=system,dc=ueu,dc=ac,dc=id';
$app_pass = 'mysecret4system';
 
// User-provided info (either from _POST or any way else)
// You should LDAP-escape $username here since it will be
//    used as a parameter for searches, but it's not a
//    subject of this article. That one will follow soon. :-)
$username = 'suyudi';
$password = 'dragonfly';
 
// Here we'll put user's DN
$userdn = '';
 
// Connect to LDAP service
$conn_status = ldap_connect('ldap.esaunggul.ac.id', 389);
if ($conn_status === FALSE) {
    die("Couldn't connect to LDAP service");
}

ldap_set_option($conn_status, LDAP_OPT_PROTOCOL_VERSION, 3);

// Bind as application
$bind_status = ldap_bind($conn_status, $app_user, $app_pass);
if ($bind_status === FALSE) {
    die("Couldn't bind to LDAP as application user");
}
 
// Find the user's DN
// See the note above about the need to LDAP-escape $username!
$query = "(&(uid=" . $username . "))";
$search_base = "dc=ueu,dc=ac,dc=id";
$search_status = ldap_search(
    $conn_status, $search_base, $query, array('dn')
);

if ($search_status === FALSE) {
   die("Search on LDAP failed");
}
 
// Pull the search results
$result = ldap_get_entries($conn_status, $search_status);
if ($result === FALSE) {
    die("Couldn't pull search results from LDAP");
}

//print_r($result);
 
if ((int) @$result['count'] > 0) {
    // Definitely pulled something, we don't check here
    //     for this example if it's more results than 1,
    //     although you should.
    $userdn = $result[0]['dn'];
}
 
if (trim((string) $userdn) == '') {
    die("Empty DN. Something is wrong.");
}
 
// Authenticate with the newly found DN and user-provided password
$auth_status = ldap_bind($conn_status, $userdn, $password);
if ($auth_status === FALSE) {
    die("Couldn't bind to LDAP as user!");
}
 
print "Authentication against LDAP succesful. Valid username and password provid";

?>

