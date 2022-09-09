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
	
	
	
	$display = $smarty->display('components/AnyListDataWp.tpl');
	$display .= $smarty->display('components/AnyListDataWp.preview.tpl');
	
	return $display;
	
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>