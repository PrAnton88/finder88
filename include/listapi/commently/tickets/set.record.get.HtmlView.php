<?php

/* это брат сниппета multiHandComment (133) */
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{

	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	function checkPrivateParentComment(&$nParent){
		
		global $db;
		
		$comment = $db->fetchFirst("SELECT id, user_link, hidden, parent FROM comments WHERE id=$nParent",true);
		if( is_string($comment) && (strpos($comment,"error") !== false)){
			throw new ErrorException("SQL for get record comment has failed");
		}
		if(count($comment) == 0){ return false; }
		
		
		if((int)$comment["hidden"] == 1){
			return true;
		}
		
		if((int)$comment["parent"] == 0){
			return false;
		}
		
		if((int)$comment["parent"] > 0){
			return checkPrivateParentComment($comment["parent"]);
		}
		
		return false;
	}
	
	
	/* use exam
	
		dataReqNext({file:urlServerSide+'commently/tickets/set.record.get.HtmlView.php',type:'json'},console.log); 
		dataReqNext({file:urlServerSide+'commently/tickets/set.record.get.HtmlView.php',type:'json',args:'dataRecord='+JSON.stringify({title: 'test title',
            	        comment: 'test comment value',
            	        hidden: 0,
            	        nresource: 4579,
            	        nparent: 0})},console.log);
	*/
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("Нет данных для записи"); exit;
	}
	
	$dataOutput = array();
	// $id = $user["uid"];
	
	$description = 'Получено сообщение. ';
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	/*
	title: 
    comment: 
    hidden:
    nresource: 
    nparent:
	*/
	
	$title = '';
	if(isset($dataRecord['title'])){
		$title = str_replace('\'','"',$dataRecord['title']);
		$title = str_replace("\\","/",$title);
	}
	
	$comment = '';
	if(isset($dataRecord['comment'])){
		$comment = str_replace('\'','"',$dataRecord['comment']);
		$comment = str_replace("\\","/",$comment);
	}
	
	$nRecord = false;
	if(isset($dataRecord['nresource'])){
		$nRecord = (int)$dataRecord['nresource'];
		/* if($nRecord == 0){ $nRecord = false; } */
	}
	if(($nRecord == 0) || $nRecord == 'false'){
		$nRecord = false; /* номер ресурса - в данном случае это номер заявки */
	}

	$nParent = 'NULL'; /* комментарий какого комментария, или комментарий не комментария */
	if(isset($dataRecord['nparent'])){
		$nParent = (int)$dataRecord['nparent'];
		/* if($nRecord == 0){ $nRecord = false; } */
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
	
	
	
	if(is_array($message) && (count($message)==0)){
		throw new ErrorException("ticket for create record comment is not found");
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
	
	/* и подумать об оповещении при добавленном комментарии */


	require_once "$path../config.modx.php";
	$modx->resource = $modx->getObject('modResource',45);

	$outputFunc = $modx->runSnippet('func');
	$dateReg = $outputFunc["getDateStr"]("now");
	$dateReg = $dateReg["fdatestr"].' '.$dateReg["ftimestr"];


	/* 3. сразу выведем отображеине */
	$chunk = $modx->getObject('modChunk',array(
		'name' => 'commentItem'
	));
	
	if(!$chunk){ 
		echo '<li class="nothing-here">chunk commentItem is not found!</li>';
	}else{ 
		
		if($comment != ''){
			
			
			if($nParent == 0){
				$nParent = ''; 
			}
			
			// $nRecord, /* номер заявки или номер ресурса */
			
			echo $chunk->process(array(
				'id' => $nRecord,
				'nparent' => ($nParent && ( ((int) $nParent) != 0))?1:0,
				'author' => $user["fio"],
				'datetime' => $dateReg,
				'content' => str_replace("\n","<br/>",$comment),
				'title' => $title,
				'hidden' => $hidden,
				'ncommon' => $nCommon,
				'type' => 'tickets',
				'editable' => 1,
				'repliedly' => 1
			));

			echo '<script>customMess("Комментарий добавлен");';
			
			/* и оповещения */
			
			
			
			/* если родитель комментария был скрытым комментарием */
			/* и мы знаем что как скрытый комментарий, так и его потомков пользователь заявок не должен видеть */
			/* тогда, на комментарий, родитель которого был скрыт - мы не оповещаем инициатора заявки */
			/* поэтому, если комментарий не скрыт, и есть родитель, то
			инеративно выбираем родителей - до корня комментариев, или до ммоента, когда один из предков точно был скрытым
			выбираем данные родителя комментария, что бы узнать был ли он скрыт */
			
			
			$isPrivate = false;
			if(($hidden != 0) || (($hidden == 0) && ((int)$nParent > 0) && checkPrivateParentComment($nParent))){
				$isPrivate = true;
			}
			
			
			echo '</script>';
		}else{
			echo '<li class="nothing-here">arg comment is not found!</li>';
		}
		
		
	}
	
	header("HTTP/1.1 200 Ok");
	exit;
	
}catch(ErrorException $ex){
	/* если application/json 
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	*/
	 
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	
}catch(Error $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>