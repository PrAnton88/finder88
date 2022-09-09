<?php
header('Content-type:application/json;');

try{

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

// header("Cache-Control: max-age=3600, must-revalidate");
header("Cache-Control: max-age=3600");/* странно но запро всё равно долго обрабатывается */
header("Expires: Thu, 28 Nov 2021 18:00:00 GMT");/* не должен ли он если из кеша - приходить прилично быстрее */


	if($resolution){
		
		/* get conversationDevice */
		
		$listDevicesConversation=$db->fetchAssoc("Select * From `conversationdevice` ORDER BY name",$uid);
		
		if((is_string($listDevicesConversation)) && (strpos($listDevicesConversation,"error") !== false)){
			header("HTTP/1.1 500 SQL Error: $listDevicesConversation"); exit;
		}
		
		
		if(!isset($getImport)){
			echo toJson($listDevicesConversation,true);
		}
		
		/*
		echo '{"success":1,"listData":[{"id":"32","name":"Проектор"},{"id":"32","name":"Проектор"},{"id":"32","name":"Проектор"},{"id":"56","name":"Ноутбук"},{"id":"58","name":"Видеоконференц связь"},{"id":"59","name":"Презентер"},{"id":"60","name":"Телефонная связь"},{"id":"61","name":"Телевизор"}]}';
		*/
		
	}else{
		header("HTTP/1.1 403 Forbidden");
	}

}catch(ErrorException $ex){
	if(isset($getImport)){
		throw $ex;
	}
	
	$description = exc_handler($ex);
	echo '{"success":0,"description":"'.$description.'"}';
}

?>