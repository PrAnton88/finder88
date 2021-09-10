<?php
header('Content-type:text/html');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Expose-Headers:Connection,Content-Length,Date,Expires,Keep-Alive');

try{

		require_once "../error.reporting.php";


		require_once("../../config/connDB.php");/* get $db */
		
		/* getUserData(&$db,&$uid,$where) */
		/* getUserDataWithoutOptions(&$db,&$uid,$where) */
		require_once "../queryUserData.php";
		
		$uid = "842";
		$user = getUserData($db,$uid,"u.id=842");

		
		require_once "../config.modx.php";
		/* get $modx */
		
		require_once "../config.smarty.php";
		/* get $smarty */
		
		$modx->smarty = $smarty;
		
		
		$getImport = true;
		require_once "get.data.TimeBusyBroneDevices.php";
		
		$listRecordsBusy = $result;
		
		
		$arrDate = array();
		foreach($listRecordsBusy as &$record){
			
			
			$arrDate = explode("-", $record['datest']);
			$record['datest'] = $arrDate[2]."-".$arrDate[1]."-".$arrDate[0];
			
			$arrDate = explode("-", $record['dateend']);
			$record['dateend'] = $arrDate[2]."-".$arrDate[1]."-".$arrDate[0];
			
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