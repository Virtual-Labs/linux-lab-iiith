<?php

$cmd="";
$cmd=$_POST["cmd"];
        
$cmd = trim($cmd);

$tid = $_POST["tid"];
$tid = trim($tid);

$ll = exec("ls ../data/tasks | wc -l",$output);

$nooftasks = (int)$output[0];

if($cmd=="start")
{

	$contents = file_get_contents("../data/tasks/task1",true);
	echo $contents;

}
else if($cmd == "next")
{

	if((int)$tid<=$nooftasks){
	$contents = file_get_contents("../data/tasks/task".$tid,true);
	echo $contents;
	}
	else{
		echo "End of tasks! Thank you.";
	}

}
else if($cmd == "prev"){

	if((int)$tid >= 1){

	$contents = file_get_contents("../data/tasks/task".$tid,true);
	echo $contents;
	}
	else
	{	
		echo "'prev' doesn't have any affect here. You are at the first task!\n\n";	
		$contents = file_get_contents("../data/tasks/task"."1",true);
		echo $contents;
	}
}


?>
