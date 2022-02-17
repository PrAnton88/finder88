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
	
	$dataOutput = array();
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	
	if(!isset($dataRecord['name'])){
		throw new ErrorException("arg name is not found");
	}
	$name = $dataRecord['name'];
	if($name == ''){
		throw new ErrorException("arg name is empty");
	}
	
	
	$nRecord = false;
	if(isset($dataRecord['id'])){
		$nRecord = (int)$dataRecord['id'];
	}
	if(($nRecord == 0) || $nRecord == 'false'){
		$nRecord = false;
	}

	
	
	
	if($nRecord === false){
		/* нов */
		
		$insertId = $db->insert("conversationdevice", 
			array(), 
			array('name'=>$name)
		);
			
		if(!$insertId){
			throw new ErrorException("запись не создана");
			exit;
		}
		
		
		$dataOutput["id"] = $insertId;
		$dataOutput["new"] = true;
		
		
	}else{
		if($nRecord <= 0){
			throw new ErrorException("Нет поля nRecord");
		}
		
		$query = "UPDATE conversationdevice set name='".addslashes($name)."' WHERE id=$nRecord";
			
		$resultUpd = $db->query($query, $uid);
		if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
			throw new ErrorException("SQL Error");
			exit;
		}
		
		$dataOutput["id"] = $nRecord;
		$dataOutput["update"] = true;
	}
	
	
	$dataOutput = json_encode($dataOutput);
	
	echo '{"success":1,"data":'.$dataOutput.'}';
	

}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	/* если text/html 
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
}catch(Error $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>