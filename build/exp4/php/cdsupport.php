<?php

session_start();
$q="";
$q=$_POST["q"];

$cmd = trim($q);
$maincmd = $cmd;

$username=$_SESSION["username"];
$password=$_SESSION["password"];

$currpath = $_POST["path"];
$userid = $_POST["userid"];
$useraccountsfolder = "./";
$locationofvlabaccounts=  "/home/";
if($username=="root"){

$userpath = "/".$userid;
$currpath=str_replace("~","/".$userid,$currpath);
}
else{
$userpath = $locationofvlabaccounts.$useraccountsfolder.$userid;
$currpath=str_replace("~",$locationofvlabaccounts.$useraccountsfolder.$userid,$currpath);
}
$err = "";
$data = "";

if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
// log in at server1.example.com on port 22
if(!($con = ssh2_connect("10.2.48.34", 22))){
	echo "fail: unable to establish connection\n";
} else {
	// try to authenticate with username root, password secretpassword
	if(!ssh2_auth_password($con, $userid, $password)) {
		echo "fail: unable to authenticate\n";
	} else {
		// allright, we're in!
#	echo "okay: logged in...\n";

		// execute a command
		if (!($stdout_stream = ssh2_exec($con, "cd ".$userpath.";cd ".$currpath.";". $cmd .";"."pwd;" ))) {
                        echo "fail: unable to execute command\n";
                } else {
                // collect returning data from command
                        stream_set_blocking($stdout_stream, true);
                        $data = "";
                        while ($buf = fread($stdout_stream,4096)) {
                                $data .= $buf;

                        }
                }
//                sleep(1);
                $stderr_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDERR);
                while($line = fgets($stderr_stream))
                {
                        flush();
                        $err .= $line;
                }
                fclose($stdout_stream);
                fclose($stderr_stream);
                }
}
$data=trim($data);
if($data=="")
	$data="_";
if($err=="")
	$err="-";
$err = trim($err);
echo $locationofvlabaccounts.":::".$userid.":::".$data.":::".$err.":::".$currpath;
?>
