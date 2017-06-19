<?php

session_start();

$username = $_POST["username"];
$password = $_POST["password"];

if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
// log in at server1.example.com on port 22
if(!($con = ssh2_connect("10.2.48.34", 22))){
	echo "fail: unable to establish connection\n";
} else {
	// try to authenticate with username root, password secretpassword
	if(!ssh2_auth_password($con, $username, $password)) {
		echo "fail: unable to authenticate\n";
	} else {
		echo "Login successful!";

	$_SESSION["username"]=$username;
	$_SESSION["password"]=$password;

	}
}




?>
