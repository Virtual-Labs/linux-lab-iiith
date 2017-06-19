<?php
	//Start session
	session_start();
	
	//Check whether the session variable SESS_MEMBER_ID is present or not
	if(!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
		header("Location: login-form.php");
		exit();
	}
?>