<?php

	$sess = null;
	if(isset($_SESSION) && isset($_SESSION['modxhsessid'])){
		$sess = $_SESSION['modxhsessid'];
		
		// echo " sess = $sess ";
		
	}elseif(isset($_COOKIE['modxcsessid'])){
		$sess = $_COOKIE['modxcsessid'];
		/*то выбрать sid по $_COOKIE['modxcsessid']*/
		
		
		// echo " sess = $sess ";
		$query = "SELECT sid FROM sessions WHERE id = '$sess'";
		if($db && ($result = $db->fetchFirst($query))) $sess = $result["sid"];
		//else echo " db not found ";
	}else{
		// echo " modxhsessid is not found, modxcsessid is not found ";
		
	}

	// echo " DOCUMENT_ROOT = ".$_SERVER['DOCUMENT_ROOT'];
	// print_r($_COOKIE);
	// print_r($_SESSION);

?>