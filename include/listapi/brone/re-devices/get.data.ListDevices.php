<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";

try{
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}

	$type = '';
	if(isset($_POST['type'])){
		$type = (int) htmlspecialchars($_POST['type']);
		if($type === 0){ throw new ErrorException('type arg is invalid'); }
		
		$type = 'type='.$type;
	}
	
	$editRecord = '';
	if(isset($_POST['editRecord'])){
		
		$editRecord = (int) htmlspecialchars($_POST['editRecord']);
		if($editRecord !== 0){
		
			$editRecord = ' OR (n.id IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND b.idtick='.$editRecord.' AND (e.hidden=0)) )) ';
		}else{
			$editRecord = '';
		}
	}
	
	/* свободные для бронирования устройства */
	/* тут count - это количество свободных устройств, или свободных устройств + $editRecord */
	/* .free = 2 это затребованные, = 0 это выданные */
	
	require_once $path."lib/query/get.query.ListDevices.php";
	
	
	
	if(isset($_POST['checkBusy'])){
		$query = getQueryToCheckBusyDevices($type);
	}else{
		$query = getQueryToCheckFreeCountListDevice($type,$editRecord);
	}
	
	
	if(!isset($getImport)){
		
		$result=$db->fetchAssoc($query,true);

		if((is_string($result)) && (strpos($result,"error") !== false)){
			throw new ErrorException($result);
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