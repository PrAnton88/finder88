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
 


	$smarty->assign("admin",($user['priority'] & 2));


	$smarty -> assign("applicantFI",$user["fi"]);
    $smarty -> assign("adminSettingLaw",checkUserLaws('adminSettingLaw'));
	$smarty -> assign("adminConversation",checkUserLaws('adminConversation'));
	$smarty -> assign("adminBroneDevice",checkUserLaws('adminBroneDevice'));
	$smarty -> assign("editorNews",checkUserLaws('editorNews'));
	$smarty -> assign("editorDocsWithoutGOPB",checkUserLaws('editorDocsWithoutGOPB'));
	$smarty -> assign("editorDocsGOPB",checkUserLaws('editorDocsGOPB'));
	$smarty -> assign("editorCategories",checkUserLaws('editorCategories'));
	
	return $smarty->display('../chunks/profilePoint.tpl');
	
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>