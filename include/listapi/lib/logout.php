<?php
header('Content-type:application/json;');
	/* обрабатываться будет как json - весь вывод в json */

	$getUserAuthData = true;
	/* $sessAccesser = true; */
	
	$path = '../';
	require_once "$path../start.php";

try{


	/* для временного решения */
	/* на проде - убрирать */

	$_SESSION[SSESSID] = '';
	session_destroy();
	$_COOKIE[CSESSID] = '';
	//setcookie('csessid');
	setcookie(CSESSID, "", time() - 3600, '/');
	
	
	if(isset($_SESSION[SSESSID]) and ($_SESSION[SSESSID] != "")){
		
		throw new ErrorException("_SESSION isset");
		
	}elseif(isset($_COOKIE[CSESSID]) and ($_COOKIE[CSESSID] != "")){
		
		throw new ErrorException("_COOKIE isset");
	}
	
	
	echo '{"success":1}';
	
}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>