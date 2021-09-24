<?php
header('Content-type:text/html');
// header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

if((!$resolution) || ($user['priority'] != 3)){
	header("HTTP/1.1 403 Forbidden");
	exit;
}

try{

	require_once "../config.modx.php";
	/* get $modx */
	
	require_once "../config.smarty.php";
	/* get $smarty */
	
	$modx->smarty = $smarty;


	$listDevices = array();
	if(isset($_POST['listdevices'])){
        $listDevices = html_entity_decode(htmlspecialchars($_POST['listdevices']));
		$listDevices = json_decode($listDevices,true);
    }
	
	if(count($listDevices) == 0){
		throw new ErrorException("listDevices пустой");
	}
	
	/* когда free было 2 > 1 - венруть устройства в оборот */
	/* когда free было 0 > 2 - выдать устройства пользователю */
	/* по полю free мы определяем то что будем делать с устройством, если оно сюда опало */
	
	/* пусть без проверки данных об устройствах на реальное существование (ибо сроки) */
	
	$free = null;
	$res = "";
	/* .id, .name,  .type, .idtick, .free, .checked */
	foreach($listDevices as $device){
		
		if($device["free"] == "0"){ 
			$free = "2"; 
		}else{
			$free = "1";
		}
		
		// $result = $db->query("SET @update_id = NULL; update `bronedevicedate` set `free`=$free, id = (SELECT @update_id := id) where iddevice=".$device['id']."; SELECT @update_id;");

		$result = $db->update("bronedevicedate", array('free'=>$free), array(), "iddevice=".$device['id']." AND idtick = ".$device['idtick']);
	
		if(is_string($result) && (strpos($result,"error") !== false)){
			throw new ErrorException($result);
		}
	}
	
	
	
	echo '{"success":1}';
	
}catch(ErrorException $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>