<?php
header('Content-type:text/html');

try{

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";

	function getCount($arr,$field,$value){
		
		$count = 0;
		foreach($arr as $item){
			
			if($item[$field] == $value){
				$count++;
			}
		}
		return $count;
	}
	
	if(!$resolution){
		
		header("HTTP/1.1 403 Forbidden");
		exit;
	}
	
	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	$getImport = true;
	require_once "get.data.TimeBusyBroneDevices.php";
	
	$listRecordsBusy = $result;
	
	$arrDate = array();
	$queryGetType = "SELECT t.id, t.type as name
	FROM bronedevicetype as t 
	RIGHT JOIN bronedevicename as n on n.type=t.id
	RIGHT JOIN bronedevicedate as d on d.iddevice=n.id
	WHERE d.idtick =";
	
	
	$queryGetCheckedDevice = "SELECT n.id, n.name, d.free 
	FROM bronedevicename as n 
	RIGHT JOIN bronedevicedate as d on d.iddevice=n.id
	WHERE "; /* n.type = 2 AND d.idtick = 290 */
	
	$otherDevice = false;
	$count = 0;
	$arrayGroupName = array();
	
	
	/* нужно взять информацию о правах авторизованного пользователя */
	// require_once "../../lib/query/get.query.UserLaws.php";
	$queryLawsThisUser = getQueryToGetUserLaws("l.adminBroneDevice,l.dispatchToBroneDevice");
	$queryLawsThisUser .= " WHERE u.role = ".$uid;

	$dataLaw = $db->fetchFirst($queryLawsThisUser,$uid);
	if(is_string($dataLaw) && (strpos($dataLaw,'error')!== false)){
		throw new ErrorException('SQL Error ');
	}
	
	$smarty->assign("dataLawJson",json_encode($dataLaw));
	$smarty->assign("dataLaw",$dataLaw);
	
	
	/* код тут пожалуй ужасен */
	foreach($listRecordsBusy as &$record){
		
		$arrDate = explode("-", $record['datest']);
		$record['datest'] = $arrDate[2]."-".$arrDate[1]."-".$arrDate[0];
		
		$arrDate = explode("-", $record['dateend']);
		$record['dateend'] = $arrDate[2]."-".$arrDate[1]."-".$arrDate[0];
		
		
		$result = $db->fetchAssoc(($queryGetType." ".$record['id']." GROUP BY id"),true);
		if((is_string($result)) && (strpos($result,"error") !== false)){
			throw new ErrorException("Данные о ".$record["id"]." не найдены ");
			exit;
		}
		
		foreach($result as &$itemType){
			$arrayGroupName = array();
			$checkedDevices = $db->fetchAssoc(
				($queryGetCheckedDevice."n.type=".$itemType['id']." AND d.idtick=".$record["id"]."")
			,true);

			if((is_string($checkedDevices)) && (strpos($checkedDevices,"error") !== false)){
				throw new ErrorException("Данные о checkedDevices не найдены ");
				exit;
			}
			
			
			$otherDevice = $checkedDevices[0]['name'];
			
			
			$checkedDevices[0]['count'] = getCount($checkedDevices,'name',$otherDevice);
			
			$arrayGroupName[] = $checkedDevices[0];
			
			foreach($checkedDevices as &$itemDevice){
				
				if($otherDevice != $itemDevice['name']){
					
				
					$otherDevice = $itemDevice['name'];
					
					$itemDevice['count'] = getCount($checkedDevices,'name',$otherDevice);
					
					$arrayGroupName[] = $itemDevice;
				}
				
			}
			
			$itemType['checkedDevices'] = $arrayGroupName;
		}
		
		
		$record['listTypes'] = $result;
		
		$whereAnd = " u.id=".$record["userId"];
		$result = getUserDataWithoutOptions($db,$uid,$whereAnd);
		
		if((is_string($result)) && (strpos($result,"error") !== false)){
			header("HTTP/1.1 500 SQL Error: $result");
			break;
		}elseif(is_array($result)){
			/*
				[dept]
				[nroom]
				[int_phone]
				[ext_phone]
			*/
			
			$record["tooltip"] = $result["dept"].
			"<br /> Комната: ".$result["nroom"].
			"<br /> внутренний телефон ".
			$result["int_phone"].
			"<br /> внешний телефон ".
			$result["ext_phone"];
			
		}else{
			throw new ErrorException("Данные о местоположении ".$record["fio"]." не найдены ");
		}
	}
	
	// print_r($listRecordsBusy);
	
	$smarty->assign("admin",($user['priority'] == 3));
	$smarty->assign("user",$user);
	
	$smarty->assign("listRecordsBusy",$listRecordsBusy);
	$smarty->assign("listJsonRecordsBusy",json_encode($listRecordsBusy));


	// print_r($smarty);
	return $smarty->display('tableBroneDevices.tpl');
		
	

}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>