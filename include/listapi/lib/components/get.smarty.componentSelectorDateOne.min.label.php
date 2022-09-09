<?php
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";

try{
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	$htmlId = 'SelectorDateOne';
	if(isset($_POST['dataConfig'])){
		$dataConfig = html_entity_decode(htmlspecialchars($_POST['dataConfig']));
		$dataConfig = json_decode($dataConfig,true);
		
		if(isset($dataConfig['id']) && ($dataConfig['id'] != '')){
			$htmlId = $dataConfig['id'];
		}
	}
	


	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	
	$smarty->assign('htmlId',$htmlId);
	
	return $smarty->display('components/SelectorDateOne.min.label.tpl');
	
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>