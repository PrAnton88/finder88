<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{
	
	if($resolution){
		$resolution = checkUserLaws('adminConversation');
	}
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("Нет данных для записи"); exit;
	}
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	$nRecord = false;
	if(isset($dataRecord['id'])){
		$nRecord = (int)$dataRecord['id'];
	}
	if(($nRecord == 0) || $nRecord == 'false'){
		throw new ErrorException("Нет поля nRecord");
	}

	
	$query = "DELETE FROM conversationdevice WHERE id=$nRecord";
	$result=$db->query($query,$uid);

	if((is_string($result)) && (strpos($result,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	echo '{"success":1}';

}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	/* если text/html 
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
}catch(Error $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>