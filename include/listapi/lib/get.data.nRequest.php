<?php

/* что бы обновлять заявки нужно знать номер request.id, принимает в качестве аргумента link (request.link) */
header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";
	
try{
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	$nLink = 0;
	if(!isset($_POST['link'])){
		throw new ErrorException("arg link is not found");
	}
	
	if(isset($_POST['link'])){
		$nLink = (int) $_POST['link'];
	}
	
	if($nLink == 0){ throw new ErrorException("arg link is unconrrect"); } 
	
	
	$query = "SELECT * FROM request WHERE link = ".$nLink;
	
	$result=$db->fetchFirst($query,$uid);
	if((is_string($result)) && (strpos($result,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	if((!$result) || (count($result)==0)){ 
		throw new ErrorException("request ticket is not found"); 
		/* throw new WarnException("request ticket is not found"); 
		echo '{"success":0,"description":"request ticket is not found","type":"warn"}'; */
	}
	
	echo '{"success":1,"id":"'.$result['id'].'"}';
	
}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}/*catch(WarnException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}*/
?>