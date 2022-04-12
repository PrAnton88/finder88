<?php

/* это брат сниппета multiHandComment (133) */
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";
	
try{

	/* use exam
	
		dataReqNext({file:urlServerSide+'helpdesk/call.modx.Ticket-InitSnippet.php',type:'text',
		args:'dataRecord='+JSON.stringify(
			{nrequest:4434}
		)},
		console.log
		);
	
		
		throw new ErrorException("тест отказа ");
	*/
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	// include "$path../headerBase.php";
	/* availabla for use $dataRecord */
	
	
	
	
	require_once "$path../config.modx.php";

	
	$outputStart = $modx->runSnippet('helpdeskTiket-init',
		array(
			
		)
	);
	

	header("HTTP/1.1 200 Ok");
	echo $outputStart;
	
	
	
}catch(ErrorException $ex){
	/* если application/json 
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	
}catch(Exception $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>