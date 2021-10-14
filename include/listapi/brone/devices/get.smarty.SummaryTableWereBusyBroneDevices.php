<?php
header('Content-type:text/html');

try{
	
	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
	if(!$resolution){
		
		header("HTTP/1.1 403 Forbidden");
		exit;
	}
	
	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	
	$getImport = true;
	require_once "get.data.WereBusyBroneDevices.php";
	
	$resultTimeWasBusy = $result;
	
	$arrDate = array();
	foreach($resultTimeWasBusy as &$record){
		
		
		$arrDate = explode("-", $record['datest']);
		$record['datestViewility'] = $arrDate[2]."-".$arrDate[1]."-".$arrDate[0];
		
		$arrDate = explode("-", $record['dateend']);
		$record['dateendViewility'] = $arrDate[2]."-".$arrDate[1]."-".$arrDate[0];
		
		if(strpos($record['listdevice'],"Запрашиваемые устройства  --") !== false){
			$listdevice = explode("Запрашиваемые устройства  --", $record['listdevice']);
			$record['listdevice'] = $listdevice[1];
		}
		
		$whereAnd = " u.id=".$record["userid"];
		$result = getUserDataWithoutOptions($db,$uid,$whereAnd);
		
		if((is_string($result)) && (strpos($result,"error") !== false)){
			header("HTTP/1.1 500 SQL Error: $result"); exit;
			
		}elseif(is_array($result)){
			
			$record["tooltip"] = $result["dept"].
			"<br /> Комната: ".$result["nroom"].
			"<br /> внутренний телефон ".
			$result["int_phone"].
			"<br /> внешний телефон ".
			$result["ext_phone"];
			
		}else{
			
			// throw new ErrorException("Данные о местоположении ".$record["userid"]." не найдены ");
		}
	}
	
	// print_r($resultTimeWasBusy);


	$smarty->assign("admin",($user['priority'] == 3));
	$smarty->assign("user",$user);
	
	$smarty->assign("messages",$resultTimeWasBusy);
	$smarty->assign("messagesJson",json_encode($resultTimeWasBusy));

	// print_r($smarty);
	return $smarty->display('tableSummaryWereBusyBroneDevices.tpl');
	

}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>