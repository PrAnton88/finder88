<?php

	$sess = null;
	if(isset($_SESSION) && isset($_SESSION['hsessid'])){
		$sess = $_SESSION['hsessid'];
		
		// echo " sess = $sess ";
		
	}elseif(isset($_COOKIE['csessid'])){
		$sess = $_COOKIE['csessid'];
		/*то выбрать sid по $_COOKIE['csessid']*/
		
		
		// echo " sess = $sess ";
		$query = "SELECT sid FROM sessions WHERE id = '$sess'";
		if($db && ($result = $db->fetchFirst($query))) $sess = $result["sid"];
		//else echo " db not found ";
	}else{
		// echo " hsessid is not found, csessid is not found ";
		
	}

	// echo " DOCUMENT_ROOT = ".$_SERVER['DOCUMENT_ROOT'];
	// print_r($_COOKIE);
	// print_r($_SESSION);

?>