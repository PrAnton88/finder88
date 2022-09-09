<?php

header('Content-type:text/html');

	$path = '../../../';
	require_once "$path../start.php";

try{
	
	/*
		dataReqNext({file:'listapi/modx/auth/setCookie.php',type:'json'},
			console.log
		);
	*/
	
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}
	
	setcookie('PHPSESSID', '41nqei41etrg0i60ijmjl6dp8q', /*(time() + (10 * 365 * 24 * 60 * 60))*/ 'Session' /* $cookexp*/, '/', 'localhost', false, true);
	
	
	echo '{"success":1}';

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>