<?php
header('Content-type:text/html');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Expose-Headers:Connection,Content-Length,Date,Expires,Keep-Alive');

try{

	require_once "../../error.reporting.php";

	require_once("../../../config/connDB.php");/* get $db */
	
	require_once "../../config.modx.php";
	/* get $modx */
	
	require_once "../../config.smarty.php";
	/* get $smarty */
	
	$modx->smarty = $smarty;

	return $smarty->display('oTester.tpl');
		
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>