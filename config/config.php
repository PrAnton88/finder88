<?php
ini_set('display_errors','On');

	// print_r($_SERVER);

	$useRole = false;
	//$printInfo = true;
	

    /* 
    $db_host = "127.0.0.1:3306";
	$db_login = 'p10977_coot';
	$db_pass = "03pranton88";  
	$db_name = "p10977_infokngtest";
	*/
	
	/*
	$db_host = "127.0.0.1";
	$db_login = "root";
	$db_pass = "";  
	$db_name = "modx-text";
	
	$deptToPortal = 75;
	*/
	
	/*
	define("DB_HOST","127.0.0.1");
	define("DB_LOGIN","root");
	define("DB_PASS","");
	define("DB_NAME","modx-text");
	*/
	
	
	// use local database
	define("DB_HOST","localhost");
	define("DB_LOGIN","root");
	define("DB_PASS","");
	
	
	/*define("DB_HOST","192.168.7.254:3333");
	define("DB_LOGIN","root");
	define("DB_PASS","newroot"); */
	
	
	define('SSESSID',"modxlocalhsessid");
	define('CSESSID',"modxlocalcsessid");
?>