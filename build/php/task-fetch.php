<html>
<head>
<title> Experiment-1</title>
</head>
<?php
$expid=$_GET['expid'];
$filename="../exp" . $expid . "/tasks.html";
$tasks=file_get_contents($filename);
$convert = explode("\n", $tasks); 
for ($i=0;$i<count($convert);$i++) 
{
    print $convert[$i]; //write value by index
}
?>
</html>
