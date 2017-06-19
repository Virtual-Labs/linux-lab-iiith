<?php
session_start();
if($_SESSION["captcha"]==$_POST["captcha"])
{
    echo "true";
}
else
{
    echo "Incorrect Captcha text!";
}
?>
