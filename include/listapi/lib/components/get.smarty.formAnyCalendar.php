<?php
header('Content-type:text/html');

	$getUserAuthData = false;
	$sessAccesser = false;
	
	$path = '../../';
	require_once "$path../start.php";

try{
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}

	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	
	$display = $smarty->display('components/FormAnyCalendar.tpl');
	$display .= $smarty->display('components/FormAnyCalendar.preview.new.tpl');
	
	return $display;
	
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>