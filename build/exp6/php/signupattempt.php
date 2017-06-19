<?php 
$pusername = $_POST["preferredusername"];
$ppassword = $_POST["preferredpassword"];
$yourname = $_POST["yourname"];
$email = $_POST["email"];

$encryptedpassword =  crypt($ppassword,"$1\$q/df5XMV");
$data="";
$err="";
if (!function_exists("ssh2_connect")) die("function ssh2_connect doesn't exist");
// log in at server1.example.com on port 22
if(!($con = ssh2_connect("10.2.48.34", 22))){
    echo "fail: unable to establish connection\n";
} else {
    // try to authenticate with username root, password secretpassword
    if(!ssh2_auth_password($con, "root", "patriot")) {
        echo "fail: unable to authenticate\n";
    } else {
        if (!($stdout_stream = ssh2_exec($con, "useradd -p '''".$encryptedpassword."''' -m ".$pusername))) {
            echo "fail: unable to execute command\n";
        } else {
            // collect returning data from command
                        stream_set_blocking($stdout_stream, true);
                        $data = "";
                        while ($buf = fread($stdout_stream,4096)) {
                                $data .= $buf;

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
echo $data." ".$err;
}


?>
