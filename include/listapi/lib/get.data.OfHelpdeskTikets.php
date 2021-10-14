<?php
header('Content-type:text/html');
/* но обрабатываться будет как json - поэтому весь вывод как json */

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{

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
	
	$query = "SELECT * FROM request WHERE hidden<1 AND id = ".$nrequest;

	$dataTickets = $db->fetchAssoc($query,$uid);
	if(is_string($dataTickets) && (strpos($dataTickets,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	echo '{"success":1,"data":'.json_encode($dataTickets).'}';
	
}catch(ErrorException $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>