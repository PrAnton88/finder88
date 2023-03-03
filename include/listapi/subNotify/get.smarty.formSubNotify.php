<?php
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{
	
	if(count($user) != 0){ 
		$admin = ($user['priority'] == 3);
		if(!$admin){ $resolution = false; }
	} 
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}

	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
		
	/* типы устройств, в которых есть хотя бы одно устройство этого типа */
	/* $query = 'SELECT id,type as name FROM bronedevicetype 
	WHERE hidden=0 AND display=1 AND (id IN (SELECT e.type as id FROM bronedevicename as e WHERE e.hidden=0	))
	ORDER BY name';
			
	$resultEveryType=$db->fetchAssoc($query,true);
		
	$smarty->assign("messages",$resultEveryType);
	$smarty->assign("messagesJson",json_encode($resultEveryType));
	*/
	
	
	
	/* 1. Список всех сотрудников ОТО */
	
	$listOtoUsers=$db->fetchAssoc($queryUserData.' AND p.dept = 75 AND r.priority=3 ORDER BY u.last_name',$uid);
	if(is_string($listOtoUsers) && (strpos($listOtoUsers,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	/* 2. Список тех, кто подписан на оповещения (сотрудников из frontend) */
	// $query = 'SELECT id FROM frontend';
	$query = $SechenuchQueryCheckedsON;
	$frontend = $db->fetchAssoc($query,$uid);
	
	$idsfrontend = [];
	foreach($frontend as $item){
		
		$idsfrontend[] = $item['uid'];
	}

	foreach($listOtoUsers as &$item){
		$item['signed'] = in_array($item['uid'],$idsfrontend);
	}
	
	$smarty->assign("listOtoUsers",json_encode($listOtoUsers));
	
	
	$smarty->assign("admin",$admin);
	$smarty->assign("user",$user);
	
	return $smarty->display('formSubNotify.tpl');
	
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>