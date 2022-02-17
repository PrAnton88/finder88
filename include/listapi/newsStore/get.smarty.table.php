<?php
header('Content-type:text/html');
	
try{
	
	$path = '../';
	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	
	return $smarty->display('tableNewsStore.tpl');
	
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>