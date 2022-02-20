<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{

	if($resolution){
		$resolution = checkUserLaws('admintoBroneDevice');
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
	
	
	if(!isset($dataRecord['type'])){
		throw new ErrorException("arg type is not found");
	}
	$type = $dataRecord['type'];
	if($type == ''){
		throw new ErrorException("arg type is empty");
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
		
		$insertId = $db->insert("bronedevicetype", 
			array(), 
			array('type'=>$type)
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
		
		$query = "UPDATE bronedevicetype set type='".addslashes($type)."' WHERE id=$nRecord";
			
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
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>