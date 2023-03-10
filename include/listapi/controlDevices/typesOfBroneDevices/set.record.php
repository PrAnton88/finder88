<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{

	if($resolution){
		$resolution = checkUserLaws('adminBroneDevice');
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

	$display = 0;
	if(isset($dataRecord['display'])){
		$display = (int)$dataRecord['display'];
	}
	
	$hidden = 0;
	if(isset($dataRecord['hidden'])){
		$hidden = (int)$dataRecord['hidden'];
	}
	
	
	
	/* проверить, вероятно запись с таким же именем уже существует */
	/* актуально как и для создания записи так и для обновления существующей */
	$query = "SELECT * FROM bronedevicetype WHERE type = '$type' AND hidden = 0";
	$res = $db->fetchFirst($query,$uid);
	if( is_string($res) && (strpos($res,"error") !== false)){
		throw new ErrorException("SQL Error $res");
	}
	if(count($res) > 0){
		
		if( ((int) $res['display']) == $display){
			throw new ErrorException("Запись с таким именем уже существует");
		}
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
		
		$query = "UPDATE bronedevicetype set type='".addslashes($type)."', display=$display, hidden=$hidden WHERE id=$nRecord";
			
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