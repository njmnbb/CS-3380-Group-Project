<?php
	
	session_start();
	
	//connecting to database
	include("../../secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
	
	$uName = $_SESSION['username'];
	
	//log users actions in log table
	$q = "INSERT INTO final_proj.log(username, ip_address, action) VALUES ($1, $2, $3)";
	$pre = pg_prepare($conn, "logout", $q) or die("not able" . pg_last_error($conn));
	//sends query to database
	$ans = pg_execute($conn, "logout", array($uName, $_SERVER['REMOTE_ADDR'], "logged out"));
	//if database doesnt return results print this
	if(!$ans) {
		die("Unable to execute: " . pg_last_error($conn));
	}
	
	//destroys session
	session_destroy();
	//navigates to login.php
	header('Location: index.php');
	
?>