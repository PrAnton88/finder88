<?php

/* это брат сниппета multiHandComment (133) */
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";
	
try{

	/* use exam
	
		dataReqNext({file:urlServerSide+'helpdesk/get.HtmlForCompleteCreateTicket.php',type:'text',
		args:'dataRecord='+JSON.stringify(
			{nrequest:4434}
		)},
		console.log
		);
	
		
		throw new ErrorException("тест отказа ");
	*/
	
	include "$path../headerBase.php";
	/* availabla for use $dataRecord */
	
	
	if(!isset($dataRecord['nrequest'])){
		throw new ErrorException("arg nrequest is not found");
	}
	
	$nrequest = (int)$dataRecord['nrequest'];
	if($nrequest == 0){
		throw new ErrorException("arg nrequest is empty");
	}
	
	
	// $query = "SELECT * FROM request WHERE id=$nrequest";
	
	require_once $path."/lib/query/get.query.ListTickets.php";
    $query = getQueryToGetTickets()." WHERE a.id=".$nrequest;
	
	
	$message = $db->fetchFirst($query,true);
	if((is_string($message)) && (strpos($message,"error") !== false)){
		throw new ErrorException("SQL Error"); exit;
	}
	
	if(count($message) == 0){
		
		throw new ErrorException('Информация о заявке не найдена');
	}
	
	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	
	$smarty->assign("nrequest",$nrequest);
	$smarty->assign("theme",$message['header']);
	/* $smarty->assign("user",json_encode($user)); - возьмем сразу из браузера
		используя checkUser.get(console.log)
	*/
	
	header("HTTP/1.1 200 Ok");
	return $smarty->display('viewForCompleteCreateTicket.AndSendsNotifyes.tpl');
	
	
}catch(ErrorException $ex){
	/* если application/json 
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	
}catch(Exception $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>