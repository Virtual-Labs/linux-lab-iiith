<?php
$register_message = "Registration";

if (isset($_REQUEST['Submit'])) {
  include_once('ldapexec.php');

  try {
    create_user_account($_REQUEST['login'], $_REQUEST['password'], $_REQUEST['cpassword']);
  } catch (Exception $exception) {
    $register_message = $exception->getMessage();
  }

  if($register_message == "Registration")
	echo "Congratulations! You have successfully registered.";
  else
        echo "Oops! Some error occurred. <br/>" . $register_message;
}
else
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Login Form</title>
<link href="loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h2><?php echo $register_message; ?></h2>
<form id="loginForm" name="loginForm" method="post" action="register-form.php">
  <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr>
      <th>First Name </th>
      <td><input name="fname" type="text" class="textfield" id="fname" /></td>
    </tr>
    <tr>
      <th>Last Name </th>
      <td><input name="lname" type="text" class="textfield" id="lname" /></td>
    </tr>
    <tr>
      <th width="124">Login</th>
      <td width="168"><input name="login" type="text" class="textfield" id="login" /></td>
    </tr>
    <tr>
      <th>Password</th>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
    </tr>
    <tr>
      <th>Confirm Password </th>
      <td><input name="cpassword" type="password" class="textfield" id="cpassword" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Register" /></td>
    </tr>
  </table>


<!--  <center> To login, <a href="login-form.php"> click here </a> </center>-->
</form>
</body>
</html>
<?php
}
?>
