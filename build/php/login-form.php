<?php


$login_message = "Login";
$filename="../exp" . $_REQUEST["expid"] . "/interaction-frame.html";
//	  echo $filename;
if (isset($_REQUEST['login'])) {
  include_once('ldapexec.php');

  if (check_user_account($_REQUEST['login'], $_REQUEST['password'])) {
	  session_start();
//	  echo $filename;
    $_SESSION['authenticated'] = true;
    $_SESSION['username'] = $_REQUEST['login'];
    $_SESSION['password'] = $_REQUEST['password'];	
    $head="Location: ".$filename;
    header($head);
  } else {
    $login_message = 'Login failed, please try again';
  }
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Login Form</title>
<link href="loginmodule.css" rel="stylesheet" type="text/css" />

 <!-- Google Analytics code starts here -->

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-42255654-1', 'virtual-labs.ac.in');
  ga('send', 'pageview');

</script>

 <!-- Google Analytics code ends here -->

</head>
<body>
<h2><?php echo $login_message;?></h2>
<p>&nbsp;</p>
<form id="loginForm" name="loginForm" method="post" action=<?php echo "login-form.php?expid=" .$_REQUEST['expid'] ?>
  <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr>
      <td width="112"><b>Login</b></td>
      <td width="188"><input name="login" type="text" class="textfield" id="login" /></td>
    </tr>
    <tr>
      <td><b>Password</b></td>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Login" /></td>
    </tr>
  </table>

  <center>For new users, <a href="register-form.php">register here</a>.</center> 
</form>
</body>
</html>
