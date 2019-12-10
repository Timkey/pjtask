<?php
	require('conn.php');

	/*
	*	connection to the database
	*/


	$connect = new Bridge();
	$cnt = new FileDirectoryMap();
	$arrayName = array(
		'0' => "flop",
		'1' => "/path/to\here\\file.txt", 
	);

	$arrayName = explode("\n", explode("--", file_get_contents('dirStructure.txt'))[0]);
	#$cnt->logContent($arrayName);
	$ls = $cnt->searchContent('do');
	var_dump($ls);

	die();


	/*
	*	code parts test
	*/

	//open file method
	$f = fopen('dbase.sql', 'r');
	
	while(!feof($f))
	{
		print fgets($f); 
	}

	fclose($f);


	//get_contents method
	$fl = file_get_contents('dbase.sql');
	print $fl;

	$qrs = explode(";\n\n", $fl);
	var_dump($qrs);

	print PHP_EOL;

?>