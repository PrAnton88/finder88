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
	
		dataReqNext({file:urlServerSide+'commently/tickets/get.record.get.HtmlView.php',type:'text',
			args:'dataRecord='+JSON.stringify({ncomment: 2063})},
			console.log
		);
	*/
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("Нет данных для записи"); exit;
	}
	
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	/*
    ncomment: 
	*/
	
	
	$ncomment = '';
	if(isset($dataRecord['ncomment'])){
		$ncomment = (int) $dataRecord['ncomment'];
	}
	
	/* тест отказа */
	/*$checkedDevicesTypes = array();
	if(count($checkedDevicesTypes) == 0){
		throw new ErrorException("Данные о комментарии не были переданы");
		exit;
	}*/

	/* получить комментарий по $ncomment */
	include $path."lib/query/get.query.Comments.php";
	$query = $queryGetComment." WHERE c.id = ".$ncomment;
	
	$comment = $db->fetchFirst($query ,true);
	if( is_string($comment) && (strpos($comment,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	$admin = ($user['priority'] == 3);
	
	
	$id = $comment["id"];
	
	if(($admin == 0) && checkPrivateParentComment($id)){
		/* то нельзя выдавать пользователю информацию об этой заявке */
	}
	
	
	
	$nRecord = $comment["request"];
	$nParent = $comment["parent"];
	$author = $comment["fio"];
	$hidden = $comment["hidden"];
	$avatar = $comment["avatar"];
	
	
	
	$dateReg = date("Y-m-d H:i:s");
	


	require_once "$path../config.modx.php";
	$modx->resource = $modx->getObject('modResource',45);

	$outputFunc = $modx->runSnippet('func');
	$dateReg = $outputFunc["getDateStr"]("now");
	$dateReg = $dateReg["fdatestr"].' '.$dateReg["ftimestr"];


	/* 3. сразу выведем отображеине */
	$chunk = $modx->getObject('modChunk',array(
		'name' => 'commentItem'
	));
	
	$display = '';
	
	if(!$chunk){
		throw new ErrorException("chunk commentItem is not found!");
	}
	
	if($nParent == 0){
		$nParent = '';
	}
	
	
	
	// $nRecord, /* номер заявки или номер ресурса */
	/* к сожалению частично повторяет снипет getCommentItems */
	
	$content = str_replace("\n","<br/>",$comment["text"]);
	
	$ashtml = $chunk->process(array(
		'id' => $nRecord,
		'nparent' => ($nParent && ( ((int) $nParent) != 0))?1:0,
		'author' => $author,
		'datetime' => $dateReg,
		'content' => $content,
		'title' => '',
		'hidden' => $hidden,
		'ncommon' => $id,
		'type' => 'tickets',
		'editable' => 1,
		'repliedly' => 1,
		'avatar' => $avatar
	));

	if($nParent && ( ((int) $nParent) != 0)){
		$display .= '<ol class="children tr">';
	}



	$display .= '<li class="comment odd alt thread-even depth-1 tr">';
	$display .= $ashtml;
	$display .= '</li>';
	if($nParent && ( ((int) $nParent) != 0)){
		echo '</ol>';
	}
	
	
	
	$isPrivate = false;
	if(($hidden != 0) || (($hidden == 0) && ((int)$nParent > 0) && checkPrivateParentComment($nParent))){
		$isPrivate = true;
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