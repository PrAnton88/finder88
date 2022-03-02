<?php

/* это брат сниппета multiHandComment (133) */
header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";
	
try{

	/* use exam
	
		dataReqNext({file:urlServerSide+'helpdesk/get.State.php',args:'nrequest=4434',type:'json'},
		function(resultJson){
			resultJson = success.check(resultJson);
				
			console.log(resultJson);
		});
	
	тест отказа 
	
		throw new ErrorException("Данные о комментарии не были переданы");
	*/
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	if(!isset($_POST['nrequest'])){
		throw new ErrorException("arg nrequest is not found");
	}
	
	$nrequest = false;
	$nrequest = (int)$_POST['nrequest'];
	
	if($nrequest == 0){
		throw new ErrorException("arg nrequest is invalid");
	}
	
	$query = "SELECT state,hidden FROM request WHERE id=$nrequest";
	$existmess = $db->fetchFirst($query);
	if((is_string($existmess)) && (strpos($existmess,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	
	
	echo '{"success":1,"data":{"state":"'.$existmess['state'].'","hidden":"'.$existmess['hidden'].'"}}';
	
	
	header("HTTP/1.1 200 Ok");
	exit;
	
}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	/* если text/html 
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>