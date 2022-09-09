<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;

	$path = '../../';
	require_once "$path../start.php";

try{
	
	/*
		лучше переходить сюда как по ссылке - что бы не пыталисся читаться json
	
		dataReqNext({file:'listapi/lib/dbtype/toggle.setCookie.redirect.php',type:'json'},
			console.log
		);
	*/
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}
	
	
	/* $time = getdate();
	$time = getdate($time[0]+86400);
	$cookexp = mktime(0,0,0,$time['mon'],$time['mday'],$time['year']); */
	
	$cookexp = (time() + (10 * 365 * 24 * 60 * 60)); /* never expire */
	
	/* prod или develop */
	$dbtype = false;
	
	if(isset($_COOKIE['dbtype'])){
		$dbtype = $_COOKIE['dbtype'];
		if(strlen($dbtype) == 0){ $dbtype = false; }
	}
		
	if(isset($dbtype)){
		if($dbtype == "prod"){	
			$dbtype = "develop";
		}else{
			$dbtype = "prod";
		}
	}else{
		$dbtype = "develop";
	}
	
	
	setcookie('dbtype', $dbtype, $cookexp, '/', $_SERVER['SERVER_NAME'], false, true);


	

	echo '{"success":1}';
	
	header('Location: '.$_SERVER['HTTP_REFERER']);
	exit();
	
	
	
	

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