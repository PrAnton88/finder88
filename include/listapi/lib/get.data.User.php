<?php
header('Content-type:application/json;');
	/* обрабатываться будет как json - весь вывод в json */

	$getUserAuthData = true;
	/* $sessAccesser = true; */
	
	$path = '../';
	require_once "$path../start.php";

try{

	/* возможно если закомментирован sessAccesser - то данная проверка неактуальна */
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
	
	/* разрешать пользователю юыть неавторизованным, но только если он не зпарвшивает чьи то (чужие) данные */
	if($whereable){
		
		if(count($user) == 0){
			/* пользователь не авторизован но хочет получить чьи то данные */
			header("HTTP/1.1 403 Forbidden"); exit;
		}
		
		$queryUserData .= $whereable;
		
		$userData = $db->fetchAssoc($queryUserData,$uid);
		if(is_string($userData) && (strpos($userData,'error')!== false)){
			throw new ErrorException('SQL Error');
		}
		
	}else{
		$userData = $user;
	}
	
	// fwriteLog('Тест: была выдана информация опользователе '.json_encode($userData));

	echo '{"success":1,"data":'.json_encode($userData).'}';
	
}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Error $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>