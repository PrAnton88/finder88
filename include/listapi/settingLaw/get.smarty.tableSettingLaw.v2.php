<?php
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";
	


try{
	if($resolution){
		$resolution = checkUserLaws('adminSettingLaw');
	}

	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}

	require_once "$path../preDataQuery.php";

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
	/* $queryUserData < queryUserData.php < start.php */
	$listOtoUsers=$db->fetchAssoc($queryUserData.' AND p.dept = 75 AND r.priority=3 ORDER BY u.last_name',$uid);
	if(is_string($listOtoUsers) && (strpos($listOtoUsers,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	/* 2. Список тех, кто подписан на оповещения (сотрудников из frontend) */
	$query = 'SELECT id FROM frontend';
	$frontend = $db->fetchAssoc($query,$uid);
	
	$idsfrontend = [];
	foreach($frontend as $item){
		
		$idsfrontend[] = $item['id'];
	}

	foreach($listOtoUsers as &$item){
		$item['signed'] = in_array($item['id'],$idsfrontend);
	}
	
	$smarty->assign("listOtoUsers",json_encode($listOtoUsers));
	
	
	$listLaw = ["l.id as lawid","l.uid"];
	$query = $queryListlaw;
	if(!checkUserLaws('adminRoot')){
		$query .= " WHERE nameField<>'adminRoot'";
	}
	
	$resplistLaw = $db->fetchAssoc($query,$uid);
	if(is_string($resplistLaw) && (strpos($resplistLaw,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	foreach($resplistLaw as &$item){
		$listLaw[] = "l.".$item['nameField'];
	}
	$listLaw = implode(", ", $listLaw);
	
	
	/* для заголовков таблицы */
	$smarty->assign("resplistLaw",json_encode($resplistLaw));
	
	
	$query = "SELECT $listLaw, u.id as uid, u.role as id, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', concat_ws(' ', u.last_name, u.first_name ) 'fi', u.post 'prof' FROM law as l LEFT JOIN users as u ON u.role = l.uid ORDER BY u.last_name";
	$usersLaws = $db->fetchAssoc($query,$uid);
	if(is_string($usersLaws) && (strpos($usersLaws,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	$smarty->assign("usersLaws",json_encode($usersLaws));
	
	$smarty->assign("admin",$admin);
	$smarty->assign("user",$user);
	
	
	if($depts = $db->fetchAssoc($deptsQuery)){
		$smarty->assign("depts",$depts);
	}
	
	/* if($comps = $db->fetchAssoc($companiseQuery)){
		$smarty->assign("comp",$comps);
	} */
	
	return $smarty->display('tableSettingLaw.v2.tpl');
	
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>