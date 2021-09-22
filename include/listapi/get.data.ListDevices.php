<?php

header('Content-type:application/json');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Expose-Headers:Connection,Content-Length,Date,Expires,Keep-Alive');

try{
	
	require_once "../error.reporting.php";
	require_once("../../config/connDB.php");/* get $db */

	if(!isset($_POST['type'])){
		throw new ErrorException('type arg is empty');
	}
	
	$type = (int) htmlspecialchars($_POST['type']);
	if($type === 0){ throw new ErrorException('type arg is invalid'); }
	
	
	$editRecord = '';
	if(isset($_POST['editRecord'])){
		
		$editRecord = (int) htmlspecialchars($_POST['editRecord']);
		if($editRecord !== 0){
		
			$editRecord = ' OR (id IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND b.idtick='.$editRecord.' AND (e.hidden=0)) )) ';
		}else{
			$editRecord = '';
		}
	}
	
	
	/* свободные для бронирования устройства */
	/* тут count - это количество свободных устройств (так как количество строк устройств свободных) */
	
	/* .free = 2 это затребованные, = 0 это выданные */
	
	$query = 'SELECT id,name,COUNT("name") as count, description FROM bronedevicename 
	WHERE type='.$type.' AND hidden=0 AND ((id NOT IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) AND (e.hidden=0))	)) '.$editRecord.') AND count>0 
	GROUP BY name ORDER BY name';
	
	
	/*
		SELECT id,name,COUNT("name") as count, description FROM bronedevicename 
		WHERE type=4 AND hidden=0 AND (id NOT IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) 
		AND (e.hidden=0)) )) 
		OR (id IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND b.idtick=305 AND (e.hidden=0)) )) AND count>0 
		GROUP BY name ORDER BY name
	*/
			
	
	/*	
	$query = 'SELECT id,name,count, description FROM bronedevicename 
	WHERE type='.$type.' AND hidden=0 AND (id NOT IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) AND (e.hidden=0))	))  AND count>0 
	ORDER BY name';
	*/	
		
	$result=$db->fetchAssoc($query,true);

	if((is_string($result)) && (strpos($result,"error") !== false)){
		header("HTTP/1.1 500 SQL Error: $result"); exit;
	}
	
	
	
	

	if(!isset($getImport)){
		echo '{"success":1,"listData":'.json_encode($result).'}';
	}
	

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>