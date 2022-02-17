<?php
// session_start();
header('Content-type:application/json;');
	/* обрабатываться будет как json - весь вывод в json */

	$getUserAuthData = true;
	/* $sessAccesser = true; */
	
	$path = '../';
	require_once "$path../start.php";

try{

	// define('SSESSID',"modxlocalhsessid");
	// define('CSESSID',"modxlocalcsessid");
	
	// require_once "$path../config.modx.php";
	// $modx->resource = $modx->getObject('modResource');
	
	
	// $sess = '';
	/*$_SESSION[SSESSID] = '';
	session_destroy();
	$_COOKIE[CSESSID] = '';
	//setcookie('csessid');
	setcookie(CSESSID, "", time() - 3600, '/');
*/
	/* возможно если закомментирован sessAccesser - то данная проверка неактуальна */
	//if(!$resolution){
	//	header("HTTP/1.1 403 Forbidden"); exit;
	//}

	if(isset($_SESSION[SSESSID]) and ($_SESSION[SSESSID] != "")){
		$sess = '_SESSION = '.$_SESSION[SSESSID];
		
	}elseif(isset($_COOKIE[CSESSID]) and ($_COOKIE[CSESSID] != "")){
		$sess = '_COOKIE = '.$_COOKIE[CSESSID];
		/*то выбрать sid по $_COOKIE[CSESSID]*/
		
		
		// echo " sess = $sess ";
		//$query = "SELECT sid FROM sessions WHERE id = '$sess'";
		//if($db && ($result = $db->fetchFirst($query))) $sess = $result["sid"];
		//else echo " db not found ";
	}



	echo '{"success":1,"data":"'.$sess.'","sessName":"'.SSESSID.'","coocieName":"'.CSESSID.'"}';
	
}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>