<?php

header('Content-type:application/json');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Expose-Headers:Connection,Content-Length,Date,Expires,Keep-Alive');

function checkFreeCountListDevice($type){
	/* свободные для бронирования устройства */
	/* тут count - это количество свободных устройств (так как количество строк устройств свободных) */
	
	global $editRecord;
	global $db;
	/*
	$query = 'SELECT id,name,COUNT("name") as count, description FROM bronedevicename 
	WHERE type='.$type.' AND hidden=0  AND (id NOT IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) AND (e.hidden=0))	))  AND count>0 
	GROUP BY name ORDER BY name';
	*/
	
	$query = 'SELECT id,name,COUNT("name") as count, description FROM bronedevicename 
	WHERE type='.$type.' AND hidden=0  AND (id NOT IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) '.$editRecord.' AND (e.hidden=0))	))  AND count>0 
	GROUP BY name ORDER BY name';
	
	/*
	$query = 'SELECT n.id,n.name,COUNT("name") as count, n.description 
	FROM bronedevicename as n WHERE n.type='.$type.' AND n.hidden=0 
	AND (n.id NOT IN (
		SELECT e.id FROM bronedevicedate as b, bronedevicename as e 
			WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) '.$editRecord.' AND (e.hidden=0))
		)) 
	AND n.count>0 GROUP BY n.name ORDER BY n.name';
	*/
	
	
	/*
	$query = 'SELECT n.id,n.name,count, n.description 
	FROM bronedevicename as n WHERE n.type='.$type.' AND n.hidden=0 
	AND (n.id NOT IN (
		SELECT e.id FROM bronedevicedate as b, bronedevicename as e 
			WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) '.$editRecord.' AND (e.hidden=0))
		)) 
	AND n.count>0 ORDER BY n.name';
	*/
		
		
		
	/*
	SELECT n.id,n.name,COUNT("name") as count, n.description 
	FROM bronedevicename as n WHERE n.type=73 AND n.hidden=0 AND 
	(n.id NOT IN ( 
		SELECT e.id FROM bronedevicedate as b, bronedevicename as e 
		WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) AND b.idtick<>298 AND (e.hidden=0)) 
		)) 
	AND n.count>0 GROUP BY n.name ORDER BY n.name
	*/
		
	return $db->fetchAssoc($query,true);
}


try{
	
	require_once "../../error.reporting.php";
	require_once("../../../config/connDB.php");/* get $db */

	if(!isset($getImport)){
		if(!isset($_POST['type'])){
			throw new ErrorException('type arg is empty');
		}
		
		$type = (int) htmlspecialchars($_POST['type']);
		if($type === 0){ throw new ErrorException('type arg is invalid'); }
	}
	
	$editRecord = "";
	if(isset($_POST['editRecord'])){
		$editRecord = (int) htmlspecialchars($_POST['editRecord']);
		
		if($editRecord == 0){ 
			$editRecord = ''; 
		}else{
			$editRecord = ' AND b.idtick<>'.$editRecord;
		}
		
	}
	
	if(!isset($getImport)){
		$result = checkFreeCountListDevice($type);
		
		if((is_string($result)) && (strpos($result,"error") !== false)){
			header("HTTP/1.1 500 SQL Error: $result"); exit;
		}
		
		echo '{"success":1,"listData":'.json_encode($result).'}';
	}

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>