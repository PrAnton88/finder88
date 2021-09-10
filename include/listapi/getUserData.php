<?php

header('Content-type:application/json;');

try{
	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

	if($resolution){
		
		$login = false;
		if(isset($_POST["login"])){
			$login = htmlspecialchars($_POST["login"]);
		}
		$userid = false;
		if(isset($_POST["userid"])){
			$userid = htmlspecialchars($_POST["userid"]);
		}
		
		if($login){
			$whereAnd = " r.login='$login' ";
		}elseif($userid){
			$whereAnd = " u.id=$userid ";
		}else{
			$whereAnd = " r.id=$uid ";
		}
		
		$result = getUserData($db,$uid,$whereAnd);
		
		if((is_string($result)) && (strpos($result,"error") !== false)){
			header("HTTP/1.1 500 SQL Error: $result");
		}else{
			
			echo toJson($result,false/*Assoc*/);
			
		}

	}else{ 
		header("HTTP/1.1 403 Forbidden");
	}

}catch(ErrorException $ex){
	$description = exc_handler($ex);
	echo '{"success":0,"description":"'.$description.'"}';
}


?>