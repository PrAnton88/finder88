<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;

	$path = '../../../';
	require_once "$path../start.php";

try{
	
	/*
		dataReqNext({file:'listapi/lib/modx/auth/setCookie.redirect.php',type:'json'},
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
	/* time()+60*60*24*30 установит, что срок действия cookie истекает через 30 дней */
	
	$cookexp = time()+10;/* 10 секунд должно хватить что бы во время перехода на страницу менеджера куки были использованы */
	
	setcookie('login', 'mgr_'.$user['login'], $cookexp, '/', $_SERVER['SERVER_NAME'], false, false);
	setcookie('password', 'mgr_'.$user['login'], $cookexp, '/', $_SERVER['SERVER_NAME'], false, false);
	
	// echo '{"success":1}';
	
	header('Location: /manager/');
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