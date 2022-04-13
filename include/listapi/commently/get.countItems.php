<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";
	
try{

	/* use exam
	
		dataReqNext({file:urlServerSide+'commently/get.countItems.php',type:'json',args:'dataRecord='+JSON.stringify(
			{nresource: 2, type: "tickets" || "posts"})},function(resultJson){
				 
			}
		);
		
		dataReqNext({file:urlServerSide+'commently/get.countItems.php',type:'json',args:'dataRecord='+JSON.stringify(
			{nresource: 4434, type: "tickets"})},function(resultJson){
				resultJson = success.check(resultJson);
				
				// resultJson.count - содержит найденное количество комментариев
			}
		);
		
	*/
	
	/* тест отказа */
	/*$checkedDevicesTypes = array();
	if(count($checkedDevicesTypes) == 0){
		throw new ErrorException("Данные о комментарии не были переданы");
		exit;
	}*/
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	/* expected .nresource: */
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("Нет данных"); exit;
	}
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	
	$nRecord = false;
	if(isset($dataRecord['nresource'])){
		$nRecord = (int)$dataRecord['nresource'];
		/* if($nRecord == 0){ $nRecord = false; } */
	}
	if(($nRecord == 0) || $nRecord == 'false'){
		$nRecord = false; /* номер ресурса - в данном случае это номер заявки ЛИБО номер ресурса в постах, в новостях например */
	}

	$type = '';
	if(isset($dataRecord['type'])){
		$type = $dataRecord['type'];
	}
	
	if($type == ''){
		throw new ErrorException("Не установлен тип");
	}
	
	
	// $id = $user["uid"];
	$admin = ($user['priority'] == 3);
	
	/* id,request,user_link,text,date_reg,hidden,head,parent */
	
	$countComment = 0;
	
	if($type == 'tickets'){
		
		require_once "../lib/commently/fn.getCount.php";
		
		$countComment = getCount($nRecord,$admin);
		
	}elseif($type == 'posts'){
		$queryComment = "SELECT count(c.id) as count FROM `commentscommon` as c LEFT JOIN `commentsposts` as p ON p.ncommon = c.id WHERE c.parent=0 AND p.nresource=$nRecord";
		$comments = $db->fetchFirst($queryComment,true);
		if( is_string($comments) && (strpos($comments,"error") !== false)){
			throw new ErrorException("SQL Error");
		}
		
		$countComment = $comments['count'];
	}
	
	echo '{"success":1,"count":"'.$countComment.'"}';
	
	header("HTTP/1.1 200 Ok");
	exit;
	
}catch(ErrorException $ex){

	echo '{"success":0,"description":"'.exc_handler($ex).'"}';

	/* если text/html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>