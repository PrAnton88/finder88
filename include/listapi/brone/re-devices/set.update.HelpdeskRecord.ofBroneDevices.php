<?php
header('Content-type:application/json;');
/* но обрабатываться будет как json - поэтому весь вывод как json */

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";

try{

	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}

	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException('dataRecord is not found');
	}
		
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	if(!isset($dataRecord['ticket'])){
		/* номер заявки */
		throw new ErrorException('ticket is not found');
	}
	
	if(!isset($dataRecord['id'])){
		/* номер в таблице записей о бронировании */
		throw new ErrorException('id record is not found');
	}
	
	
	
	$respUpdate = $db->update('request', array('type'=>5,'link'=>$dataRecord['id']), array(), " id = ".$dataRecord['ticket']);
	if(is_string($respUpdate) && (strpos($respUpdate,'error')!== false)){
		throw new ErrorException('SQL Error');
	}

	echo '{"success":1}';
	
}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>