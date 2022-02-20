<?php

/* это брат сниппета multiHandComment (133) */
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{

	/* use exam
	
		dataReqNext({file:urlServerSide+'commently/tickets/get.HtmlView.php',type:'text',args:'dataRecord='+JSON.stringify(
			{nresource: 2})},console.log);
			
		dataReqNext({file:urlServerSide+'commently/tickets/get.HtmlView.php',type:'text',args:'dataRecord='+JSON.stringify(
			{nresource: 2})},function(resultHtml){
				resultHtml = convertResponseToObject(resultHtml); 
				document.body.appendChild(resultHtml[0]); 
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
		$nRecord = false; /* номер ресурса - в данном случае это номер заявки */
	}

	
	// $id = $user["uid"];
	$admin = ($user['priority'] == 3);
	
	/* id,request,user_link,text,date_reg,hidden,head,parent */
	$queryComment = "SELECT * FROM comments WHERE id = $nRecord";
	
	
	/* id,request,user_link,text,date_reg,hidden,head,parent */
	$queryComment = "SELECT * FROM comments";
	$recordComment = $db->fetchFirst($queryComment,true);
	if( is_string($recordComment) && (strpos($recordComment,"error") !== false)){
		throw new ErrorException("SQL Error");
	}

	if(count($recordComment) == 0){
		throw new ErrorException("Ответ не найден");
	}

	if(($recordComment['user_link']!=$user['uid']) && ($admin == 0)){
		header("HTTP/1.1 403 Forbidden"); exit; /* доступ запрещен */
	}

	$dateReg = $recordComment["date_reg"];
	$nParent = (int)$recordComment["parent"];
	$comment = $recordComment["text"];
	$title = $recordComment["head"];
	$hidden = $recordComment["hidden"];
	$nCommon = $recordComment["id"];

	$dataOutput = array();

	require_once "$path../config.modx.php";
	$modx->resource = $modx->getObject('modResource',45);

	

	/* 3. сразу выведем отображеине */
	$chunk = $modx->getObject('modChunk',array(
		'name' => 'commentItem'
	));
	
	$display = '';
	
	if(!$chunk){ 
		$display .= '<li class="nothing-here">chunk commentItem is not found!</li>';
	}else{ 
		
		if($nParent == 0){
			$nParent = ''; 
		}
		
		// $nRecord, /* номер заявки или номер ресурса */
		
		$comment = str_replace("\n","<br/>",$comment);
		
		$display .= $chunk->process(array(
			'id' => $nRecord,
			'nparent' => ($nParent && ( ((int) $nParent) != 0))?1:0,
			'author' => $user["fio"],
			'datetime' => $dateReg,
			'content' => $comment,
			'title' => $title,
			'hidden' => $hidden,
			'ncommon' => $nCommon,
			'type' => 'tickets',
			'editable' => 1,
			'repliedly' => 1
		));
	}
	
	echo $display;
	
	header("HTTP/1.1 200 Ok");
	exit;
	
}catch(ErrorException $ex){
	/* если application/json 
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	*/

	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	
}catch(Exception $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>