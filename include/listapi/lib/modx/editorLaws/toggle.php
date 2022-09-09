<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;

	$path = '../../../';
	require_once "$path../start.php";

try{
	
	/*
		dataReqNext({file:'listapi/modx/editorLaws/toggle.php',type:'json',args:'dataRecord={"uid":811}'},
			console.log
		);
	*/
	
	$admin = ($user['priority'] == 3);
	if(!$admin){ $resolution = false; }
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}

	if(!isset($_POST['dataRecord'])){
		throw new ErrorException('dataRecord is empty');
		
	}

	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	// setcookie('PHPSESSID', '41nqei41etrg0i60ijmjl6dp8q', /*(time() + (10 * 365 * 24 * 60 * 60))*/ 'Session' /* $cookexp*/, '/', 'localhost', false, true);
	
	
	if(empty($dataRecord['uid'])){
		throw new ErrorException("missing arg 'id' user for set access");
	}
	$userid = false;
	
	
	if(isset($dataRecord['uid'])){
		$userid = (int)$dataRecord['uid'];
		if($userid <= 0){ $userid = false; }
	}
	
	if(!$userid){
		throw new ErrorException("arg 'id' user for set access is invalid");
	}
	
	
	$field = false;
	if(isset($dataRecord['field'])){
		$field = $dataRecord['field'];
		if(strlen($field) == 0){ $field = false; }
	}
	if($field=== false){
		throw new ErrorException("arg 'field' for groupName is invalid");
	}
	
	// throw new ErrorException("test error about any invalid");
	
	
	
	$query = $queryUserData." AND u.id = ".$userid;
	$dataUser = $db->fetchFirst($query,$uid);
	if(is_string($dataUser) && (strpos($dataUser,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	if(count($dataUser) == 0){
		throw new ErrorException('User is not found');
	}
	// print_r($dataUser);
	require_once "$path../config.modx.php";
	
	/* префикс 'mgr_'. обязателен (или любой другой), важно что бы логин в modx не был равен логин с инфо */
	$login = 'mgr_'.$dataUser['login'];
	
	$count = $modx->getCount('modUser', array('username' => $login));
	
	
	include "createUser.php";
	
	$groupName = $field;
	
	$description = '';
	
	include "toggleGroup.php";
	
	
	
	echo '{"success":1,"description":"'.$description.'"}';

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>