<?php
header('Content-type:text/html');
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

	if(!isset($_POST['dataRecord'])){
		throw new ErrorException('dataRecord is empty');
	}
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	if(!isset($dataRecord['law'])){
		throw new ErrorException('dataRecord.law is empty');
	}
	
	
	$listLaw = [];
	$query = 'SELECT id,nameField,nameLaw,messHelp FROM listlaw';
	$resplistLaw = $db->fetchAssoc($query,$uid);
	if(is_string($resplistLaw) && (strpos($resplistLaw,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	foreach($resplistLaw as &$item){
		$listLaw[] = $item['nameField'];
	}
	/* in_array($field,$listLaw) */
	
	if(!in_array($dataRecord['law'],$listLaw)){
		throw new ErrorException("this dataRecord.law '{$dataRecord['law']}' is unregistered");
	}

	
	// require_once "query/get.query.UserLaws.php";
	$query = getQueryToGetUserLaws('l.'.$dataRecord['law']);
	
	$query .= " WHERE l.".$dataRecord['law']."=1";
	

	$usersOfLaw = $db->fetchAssoc($query,$uid);
	if(is_string($usersOfLaw) && (strpos($usersOfLaw,'error')!== false)){
		throw new ErrorException('SQL Error');
	}

	echo '{"success":1,"data":'.json_encode($usersOfLaw).'}';
	
}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>