<?php
header('Content-type:text/html');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Expose-Headers:Connection,Content-Length,Date,Expires,Keep-Alive');

try{

		require_once "../../error.reporting.php";


		require_once("../../../config/connDB.php");/* get $db */
		
		/* getUserData(&$db,&$uid,$where) */
		/* getUserDataWithoutOptions(&$db,&$uid,$where) */
		require_once "../../queryUserData.php";
		
		$uid = "842";
		$user = getUserData($db,$uid,"u.id=842");

		
		require_once "../../config.modx.php";
		/* get $modx */
		
		require_once "../../config.smarty.php";
		/* get $smarty */
		
		$modx->smarty = $smarty;
		
		
		/* типы устройств, в которых есть хотя бы одно устройство этого типа */
		$query = 'SELECT id,type as name FROM bronedevicetype 
	WHERE hidden=0 AND display=1 AND (id IN (SELECT e.type as id FROM bronedevicename as e WHERE e.hidden=0	))
	ORDER BY name';
			
	$resultEveryType=$db->fetchAssoc($query,true);
		
		
		
		$smarty->assign("messages",$resultEveryType);
		$smarty->assign("messagesJson",json_encode($resultEveryType));
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$smarty->assign("admin",($user['priority'] == 3));
		$smarty->assign("user",$user);
		
		// $smarty->assign("messages",$resultTimeBusy);

		// print_r($smarty);
		return $smarty->display('formBronDevices.tpl');
		
	

}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>