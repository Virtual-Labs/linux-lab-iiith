<?php

// ldaphost
$ldap_host = "10.4.14.151";
$ldap_port = 389;

//Start session
//session_start();

// using ldap bind 
$ldap_admin_dn  = 'cn=admin,dc=virtual-labs,dc=ac,dc=in';     // ldap rdn or dn 
$ldap_admin_pass = 'VenEucAf0';  // associated password 
$ldap_base_user_dn = 'ou=People,dc=virtual-labs,dc=ac,dc=in';

function check_user_account($username, $password) {
  assert_valid_username($username);

  $ldap_bind_rdn  = "uid=$username,ou=People,dc=virtual-labs,dc=ac,dc=in";     // ldap rdn or dn 
  $ldap_pass = $password;  // associated password 

  $ldap_conn = connect();

  // binding to ldap server 
  $ldap_bind = ldap_bind($ldap_conn, $ldap_bind_rdn, $ldap_pass);

  return $ldap_bind;
}	

function create_user_account($username, $password, $confirm_password) {
  if ($password != $confirm_password) {
    throw new Exception("Passwords don't match");
  }

  assert_valid_username($username);

  $output = NULL;
  $return_value = NULL;
  $command = "/usr/sbin/ldapadduser " . escapeshellarg($username) . " vlusers 2>&1";
  exec($command, $output, $return_value);
  if ($return_value != 0) {
    throw new Exception("Failed to create user: " . implode($output));
  }

  $password_hash = "{SHA}" . base64_encode(sha1($password, TRUE));
  exec("/usr/sbin/ldapsetpasswd " . escapeshellarg($username) . " $password_hash");

  //logging into the 12 server to create the home directory
  $conn = ssh2_connect('ssh.cse09.virtual-labs.ac.in', 22);
  ssh2_auth_password($conn, $username, $password);
  ssh2_exec($conn, 'su -u ls -la');

}

function assert_valid_username($username) {
  if (!preg_match('/[a-zA-Z0-9_]{4,30}/', $username)) {
    throw new Exception("Invalid username");
  }
}

function connect() {
  global $ldap_host;

  // connect to ldap server 
  $ldap_conn = ldap_connect($ldap_host);
  if (!$ldap_conn) {
    throw Exception("Could not connect to LDAP server.");
  }

  ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

  echo "User Registered. Please proceed to login.";
  return $ldap_conn;
}
