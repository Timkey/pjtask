<?php
	session_start();

	require('conn.php');

	if (isset($_GET['searchParam']))
	{
		$param = $_GET['searchParam'];
		$directory = new FileDirectoryMap();
		$list = $directory->searchContent($param);
		$arrayName = array('data' => $list, );

		print json_encode($arrayName, true);
		#print "Reached";

		die();
	}

	if (isset($_POST['searchParam']))
	{
		$param = $_POST['searchParam'];
		$directory = new FileDirectoryMap();
		$list = $directory->searchContent($param);
		$arrayName = array('data' => $list, );

		print json_encode($arrayName, true);
		#print "Reached";

		die();
	}


	$arrayName = array('data' => [], );
	print json_encode($arrayName);