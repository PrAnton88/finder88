<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{
	
	function removeRecord(&$db,&$id){
		
		$query = "UPDATE bronedevicename set hidden='1' WHERE id=".$id;
		
		$resultUpd = $db->query($query, true);
		if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
			throw new ErrorException("SQL Error");
		}
		
	}
	
	if($resolution){
		$resolution = checkUserLaws('admintoBroneDevice');
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
	
	
	/* 1. как минимум что бы узнать текущее количество */
	$editRecord = $db->fetchFirst("SELECT * FROM bronedevicename WHERE id = $nRecord",$uid);
	if(is_string($editRecord) && (strpos($editRecord,'error') !== false)){
		throw new ErrorException('sql error');
	}
	
	
	/* 2. есть ли занятые устройства */
	require_once $path."lib/query/get.query.ListDevices.php";
	$query = getQueryToCheckBusyDevices('n.type = '.$editRecord['type']);
	$result = $db->fetchAssoc($query,$uid);
	if(is_string($result) && (strpos($result,'error') !== false)){
		throw new ErrorException('sql error');
	}
	
	/* пройтись по нему и убрать устройства которые не относятся к этому же наименованию */
	$c = 0;
	foreach($result as &$device){
		if($device['name'] != $editRecord['name']){
			$device = null;
			
			unset($result[$c]);
		}
		$c ++;
	}
	
	if(count($result) > 0){
		throw new ErrorException('Удалить запись нельзя. (Забронированны '.count($result).'шт)');
	}
	
	
	/* выбрать все свободные устройства */
	$query = getQueryToCheckFreeCountListDevice('n.type = '.$editRecord['type']);
	$result = $db->fetchAssoc($query,$uid);
	if(is_string($result) && (strpos($result,'error') !== false)){
		throw new ErrorException('sql error');
	}
	$c = 0;
	foreach($result as &$device){
		if($device['name'] != $editRecord['name']){
			$device = null;
			
			unset($result[$c]);
		}
		$c ++;
	}
	
	
	/* идти по записям и ставить hidden = 1 */
	foreach($result as $device){
		removeRecord($db,$device['id']);
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