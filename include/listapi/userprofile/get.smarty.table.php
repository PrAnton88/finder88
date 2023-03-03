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
			$user = getUserData($db,$uid,"u.id=$userv");
			
			if(count($user) == 0){
				$query = queryDisabledUser;
				$query .= " AND u.id = $userv";
				
				$user = $db->fetchFirst($query,true);
				
				if( is_string($user) && (strpos($user,"error") !== false)){
					throw new ErrorException("SQL error");
				}
				
				
			}
			
		//}
		/* просматривая чужой профиль не зачем устанавливать настройки */
		// $admin = false;
	}
	
	// echo 'count user = '.count($user);
	
	
	if(!$userv){
		// echo 'without userv';
		
		/* выбор аватара */
		require_once $path."/lib/getFiles.php";
		$files = getFiles($modx,$db,$user['uid']);
		
		if(count($files) > 0){
			$file = $files[0];
		}else{
			$file = array('link'=>'/assets/images/avatars/avatar.png');
		}
		
		// это для "$pathAppChunk/tmp.avatar.tpl"}
		$smarty->assign("file", $file);


		$mop = $user["mop"];
		$uop = $user["uop"];
		$pop = $user["pop"];
		
		/* $updateTechnicalSupport = ($user["updateTechnicalPage"] == 1?"checked":"");
		$smarty -> assign("updateTechnicalPage",$updateTechnicalSupport); */
		
		
		$smarty -> assign("updateTechnicalPage",$user["updateTechnicalPage"]);
		
		
		
		$smarty -> assign("uop",$uop);
		$smarty -> assign("mop",$mop);
		$smarty -> assign("pop",$pop);
		
	}else{
		
		// echo 'userv = '.$userv;
		$smarty -> assign("userv",$userv);
	}
	
	$query = "SELECT ip FROM pc WHERE role=".$user["id"]." AND hidden=0";
	$listip = $db->fetchAssoc($query,$uid);
	if( is_string($listip) && (strpos($listip,"error") !== false)){
		throw new ErrorException("SQL error");
	}
	$user["listip"] = $listip;
	$smarty -> assign("isprofile",true);
	
	
	$smarty -> assign("admin",$admin);
    $smarty -> assign("user",$user);
	
	if(isset($_COOKIE['dbtype'])){
		if($_COOKIE['dbtype'] == "prod"){
			$dbtype = "prod";
		}else{
			$dbtype = "develop";
		}
	}else{
		$dbtype = "develop";
	}
	$smarty -> assign("dbtype",$dbtype);
	
	
	
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