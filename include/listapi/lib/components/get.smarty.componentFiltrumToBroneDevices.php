<?php
header('Content-type:text/html');

	$getUserAuthData = true;
	/* $sessAccesser = true; */
	
	$path = '../../';
	require_once "$path../start.php";

try{
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}

	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	
	/* список ФИО всех сотрудников */
	$getImport = true;
	require_once "../../brone/devices/get.data.WereBusyBroneDevices.php";
	
	foreach($result as &$record){
		
		$record['id'] = $record['userid'];
		unset($record['userid']);
		unset($record['listdevice']);
		unset($record['datest']);
		unset($record['dateend']);
	}
	
	$smarty->assign("clientsOrder",json_encode($result));
	
	return $smarty->display('components/FiltrumToBroneDevices.tpl');
	
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>