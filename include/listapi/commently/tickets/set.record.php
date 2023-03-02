<?php

/* это брат сниппета multiHandComment (133) */
header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{
	
	if(!$resolution){
		if(count($user) == 0){
			throw new ErrorException('401 Unauthorized');
		}
		
		// header("HTTP/1.1 403 Forbidden"); exit;
		throw new ErrorException("HTTP/1.1 403 Forbidden");
	}
	
	/* use exam
	
		dataReqNext({file:urlServerSide+'commently/tickets/set.record.php',type:'json'},console.log); 
		dataReqNext({file:urlServerSide+'commently/tickets/set.record.php',type:'json',args:'dataRecord='+JSON.stringify({title: 'test title',
            	        comment: 'test comment value',
            	        hidden: 0,
            	        nresource: 4432,
            	        nparent: 0})},console.log);
	*/
	
	$dataOutput = array();
	// $id = $user["uid"];
	$description = 'Получено сообщение. ';
	

	include "$path../headerBase.php";
	/* availabla for use $dataRecord */
	
	
	//echo is_string($dataRecord);
	
	// print_r(json_decode(addslashes($str)));
	//echo $str;
	//throw new ErrorException('test Error');
	
	/*
	title: 
    comment: 
    hidden:
    nresource: 
    nparent:
	*/
	
	$title = '';
	if(isset($dataRecord['title'])){
		$title = $dataRecord['title'];
	}
	
	if(!isset($dataRecord['comment'])){
		throw new ErrorException("недостатчно данных для записи"); exit;
	}
	$comment = $dataRecord['comment'];
	
	
	
	$nRecord = false;
	if(isset($dataRecord['nresource'])){
		$nRecord = (int)$dataRecord['nresource'];
		/* if($nRecord == 0){ $nRecord = false; } */
	}
	/* номер ресурса - в данном случае это номер заявки */
	if((!$nRecord) || ($nRecord == 0) || ($nRecord == 'false')){
		throw new ErrorException("Номер заявки не обнаружен"); exit;
	}

	$nParent = 'NULL'; /* комментарий какого комментария, или комментарий не комментария */
	if(isset($dataRecord['nparent'])){
		$nParent = (int)$dataRecord['nparent'];
	}

	$hidden = (int)$dataRecord['hidden'];
	
	
	/* тест отказа */
	/*$checkedDevicesTypes = array();
	if(count($checkedDevicesTypes) == 0){
		throw new ErrorException("Данные о комментарии не были переданы");
		exit;
	}*/
	
	
	

	/* новая запись */


	require_once $path."lib/query/get.query.ListTickets.php";
	$query = getQueryToGetTickets();
		
	$query .= " WHERE a.id = $nRecord";
		
		/* комментарий может делать только заявитель заявки или ответственный, или любой админ */
		/* поэтому */
	$message = $db->fetchFirst($query,true);
	if( is_string($message) && (strpos($message,"error") !== false)){
		throw new ErrorException("SQL for get record ticket has failed");
	}
	
	if(count($message)==0){
		throw new ErrorException("ticket for create record comment is not found");
	}
	
	if($message['hidden'] == 1){
		throw new ErrorException("Нельзя комментировать удаленную заявку");
	}
	
	if($message['state'] == 2){
		throw new ErrorException("Нельзя комментировать выполненную заявку");
	}
	
	$admin = ($user['priority'] == 3);
	
	if(($message['applicant']!=$user['uid']) && ($admin == 0)){
		header("HTTP/1.1 403 Forbidden"); exit; /* доступ запрещен */
	}
	
	/* комментарии hidden должны приходить только от админов */
	if(($hidden != 0) && ($admin == 0)){
		header("HTTP/1.1 403 Forbidden"); exit; /* доступ запрещен */
	}
	
	
	$dateReg = date("Y-m-d H:i:s");
	

	$nCommon = $db->insert("comments", array(), array('request'=>$nRecord,'user_link'=>$user["id"],'text'=>$comment,'date_reg'=>$dateReg,'hidden'=>$hidden,'head'=>$title, 'parent'=>$nParent));
	if( is_string($nCommon) && (strpos($nCommon,"error") !== false)){
		throw new ErrorException("SQL Error. comments push has failed.");
	}

	echo '{"success":1,"description":"комментарий вставлен","data":{"ncomment":"'.$nCommon.'"}}';

	header("HTTP/1.1 200 Ok");
	exit;

}catch(ErrorException $ex){
	/* если text/html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>