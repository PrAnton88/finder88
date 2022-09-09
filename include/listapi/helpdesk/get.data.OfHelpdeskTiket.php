<?php
header('Content-type:text/html');
/* но обрабатываться будет как json - поэтому весь вывод как json */

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{

	/* useExam

		1. oBaseAPI.helpdesk.getDataTiket(4519,console.log);

		2. oBaseAPI.helpdesk.getDataTiket(nrequest,function(oDataTiket){
			// oDataTiket have json format
			
		});
	*/

	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}

	$dataRecord = false;
	if($_POST['dataRecord']){
		$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
		$dataRecord = json_decode($dataRecord,true);
	}
	
	if(!isset($dataRecord['nrequest'])){
		throw new ErrorException('arg nrequest is not found');
	}

	$nrequest = (int) $dataRecord['nrequest'];
	if($nrequest == 0){
		throw new ErrorException('arg nrequest is empty');
	}
	
	/* ранее $query = "SELECT * FROM request WHERE id = ".$nrequest; */
	require_once $path."lib/query/get.query.ListTickets.php";


	$query = getQueryToGetTickets()." WHERE a.id = ".$nrequest;
	
	


	$dataTickets = $db->fetchFirst($query,$uid);
	if(is_string($dataTickets) && (strpos($dataTickets,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	
	/* $dataTickets['u'] = $user; */
	
	/* условие выдачи = пользователь админ, или заявка не удалена и заявка из отдела пользователя */
	if(!(($user['priority'] == 3) || ( ($dataTickets['hidden'] == 0) && ($dataTickets['ndept'] == $user['otdel']) ))){
		
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	echo '{"success":1,"data":'.json_encode($dataTickets).'}';
	
	
	
}catch(ErrorException $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>