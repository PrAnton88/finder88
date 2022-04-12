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
		
	$admin = ($user["priority"] == 3);
		
	$userv = false;
	if(isset($_POST['userv'])){
		$userv = (int) $_POST['userv'];
		if($userv == 0){ $userv = false; }
	}
	
	if(($userv !== false) && ($userv == $user['uid'])){
		$userv = false;
		/* потому что запрашивают на самого себя, а нам изместно что */
		/* информация об авторизованном и так находится внутри $user */
	}
	
	if($userv !== false){
		//if($admin){
			
			/* в $user поместим данные о сотруднике $userv */
			$user = getUserData($db,$uid,"r.id=$userv");
			
		//}
		/* просматривая чужой профиль не зачем устанавливать настройки */
		$admin = false;
	}
	
	// echo 'count user = '.count($user);
	
	
	if(!$userv){
		// echo 'without userv';
		
		$mop = $user["mop"];
		$uop = $user["uop"];
		$pop = $user["pop"];
		$updateTechnicalSupport = ($user["updateTechnicalPage"] == 1?"checked":"");
		
		$smarty -> assign("updateTechnicalPage",$updateTechnicalSupport);
		$smarty -> assign("uop",$uop);
		$smarty -> assign("mop",$mop);
		$smarty -> assign("pop",$pop);
		
	}else{
		
		// echo 'userv = '.$userv;
		$smarty -> assign("userv",$userv);
	}
	
	
	$smarty -> assign("admin",$admin);
    $smarty -> assign("user",$user);
	
	return $smarty->display('table.userprofile.tpl');
	
}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}catch(Exception $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>