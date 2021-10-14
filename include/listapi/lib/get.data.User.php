<?php
header('Content-type:application/json;');
/* но обрабатываться будет как json - поэтому весь вывод как json */

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{

	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}


	$whereable = false;
	if(isset($_POST['dataRecord'])){
		
		$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
		$dataRecord = json_decode($dataRecord,true);
		
		/* насколько разумно выдавать такую информацию любым пользователям */
		if(isset($dataRecord['id'])){
			
			$whereable = ' u.id = '.$dataRecord['id'];
		}elseif($dataRecord['login']){
			
			$whereAnd = " r.login='$login' ";
		}elseif($dataRecord['userid']){
			
			$whereAnd = " u.id=$userid ";
		}
	}
	
	
	if($whereable){
		$queryUserData .= $whereable;
		
		$userData = $db->fetchAssoc($queryUserData,$uid);
		if(is_string($userData) && (strpos($userData,'error')!== false)){
			throw new ErrorException('SQL Error');
		}
		
	}else{
		$userData = $user;
	}
	

	echo '{"success":1,"data":'.json_encode($userData).'}';
	
}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>