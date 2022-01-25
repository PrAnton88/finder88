<?php
header('Content-type:application/json');
/* но обрабатываться будет как json - поэтому весь вывод как json */

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{

	$admin = ($user['priority'] == 3);
	if(!$admin){ $resolution = false; }

	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}

	$dataRecord = false;
	if($_POST['dataRecord']){
		$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
		$dataRecord = json_decode($dataRecord,true);
	}
	
	/* 1. Список всех сотрудников ОТО */
	$listOtoUsers=$db->fetchAssoc($queryUserData.' p.dept = 75 AND r.priority=3',$uid);
	if(is_string($listOtoUsers) && (strpos($listOtoUsers,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	
	
	
	/*
	$listLaw = ["l.id as lawid","l.uid"];
	$query = 'SELECT id,nameField,nameLaw,messHelp FROM listlaw';
	$resplistLaw = $db->fetchAssoc($query,$uid);
	if(is_string($resplistLaw) && (strpos($resplistLaw,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	foreach($resplistLaw as &$item){
		$listLaw[] = "l.".$item['nameField'];
	}
	$listLaw = implode(", ", $listLaw);
	
	
	$query = getQueryToGetUserLaws($listLaw);
	*/
	
	$query = getQueryUserLaws($db);
	
	/*
	$query = "SELECT $listLaw, u.id, concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio', u.post 'prof' FROM law as l LEFT JOIN users as u ON u.role = l.uid";
	*/
	
	if($dataRecord && (isset($dataRecord['uid']))){
		
		$query .= " WHERE u.role = ".$dataRecord['uid'];
	}

	$usersLaws = $db->fetchAssoc($query,$uid);
	if(is_string($usersLaws) && (strpos($usersLaws,'error')!== false)){
		throw new ErrorException('SQL Error ');
	}
	

	
	echo '{"success":1,"data":'.json_encode($usersLaws).'}';
	
	
	
	
}catch(ErrorException $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>