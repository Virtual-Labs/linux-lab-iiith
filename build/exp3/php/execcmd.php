<?php

$q="";
$q=$_POST["q"];
        
$cmd = trim($q);
$maincmd = $cmd;
$mcmd = "";

session_start();
$userpath =$_POST["path"];
$userid = $_POST["userid"];
$username = $_SESSION["username"];
$password= $_SESSION["password"];
if($username=="root"){

$userpath =str_replace("~/","/".$userid."/",$userpath);

}
else{
$userpath =str_replace("~/","/home/".$userid."/",$userpath);
}
//echo $userpath;

if(substr_count($cmd," ")>0)
{
	$maincmd = split(" ",$cmd);
	$maincmd = $maincmd[0];
//	echo $maincmd.":".$cmd;
}

if(substr_count($cmd,";")>0){

	echo "No. Not Allowed!!";
}
else if($maincmd=="ls"||$cmd=="pwd"||$maincmd=="ps"||$maincmd=="mkdir"||$maincmd=="kill"||$maincmd=="nohup"||$maincmd=="renice"||$maincmd=="rm"||$maincmd=="rmdir"||$maincmd=="du"||$maincmd=="df"||$maincmd=="cat"||$maincmd=="echo"||$maincmd=="svn"||$maincmd=="ifconfig"){

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
#       echo "okay: logged in...\n";

                // execute a command
                if (!($stdout_stream = ssh2_exec($con, "cd ".$userpath.";". $cmd .";"))) {
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
$err = trim($err);
echo $err.$data;
}
else{

echo "No Not Allowed!";
}


?>
