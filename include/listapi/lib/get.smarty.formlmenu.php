<?php
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}

	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;


	/* права пользователя */
   	$query = getQueryUserLaws($db);
	$query .= " WHERE u.role = ".$uid;
	$dataLawsOfUser = $db->fetchFirst($query);
   	$smarty->assign("dataLawsOfUser",$dataLawsOfUser);


	$smarty->assign("admin",($user['priority'] & 2));

	$smarty->assign("tpl_name",'settinglaw');


	
	
	
	return $smarty->display('../default/lmenu.tpl');
	
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>