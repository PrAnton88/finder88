<?php
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";

try{

	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
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
	return $smarty->display('formBroneDevices.tpl');
	
	

}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>