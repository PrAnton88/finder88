<?php
header('Content-type:text/html');

try{

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

	if($resolution){
		
		require_once "../config.modx.php";
		/* get $modx */
		
		require_once "../config.smarty.php";
		/* get $smarty */
		
		$modx->smarty = $smarty;
		
		
		$smarty->assign("admin",($user['priority'] == 3));
		$smarty->assign("user",array('id'=>$user['id'],'uid'=>$user['uid']));
		
		$smarty->assign("messages",$resultTimeBusy);

		// print_r($smarty);
		return $smarty->display('selectorDiapazoneDate.tpl');
		
		
		
	}else{
		header("HTTP/1.1 403 Forbidden");
	}

}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>