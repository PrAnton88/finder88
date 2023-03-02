<?php

/*
	dataReqNext({file:urlServerSide+'commently/tickets/remove.record.php',type:'json'},console.log); 
	- нет переданных данных

	dataReqNext({file:urlServerSide+'commently/tickets/remove.record.php',type:'json',
		args:'dataRecord='+JSON.stringify({
			id:1076
		})
		},console.log
	); 
*/


header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{

	function cascadeUpdate(&$id){
		global $db;
		
		$comment = $db->query("UPDATE comments SET hidden=2 where id=$id",true); 
		if( is_string($comment) && (strpos($comment,"error") !== false)){
			throw new ErrorException("SQL UPDATE record comment has failed");
		}
		
		$comment = $db->fetchAssoc("SELECT id, user_link FROM comments WHERE parent=$id",true);
		if( is_string($comment) && (strpos($comment,"error") !== false)){
			throw new ErrorException("SQL for get record comment has failed");
		}
		if(count($comment) == 0){ return true; }
		
		foreach($comment as $item){
			
			if(cascadeUpdate($item["id"])){ return true; }
		}
		
		return false;
	}
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("Нет переданных данных"); exit;
	}
	
	$dataOutput = array();
	// $id = $user["uid"];
	
	$description = 'Получено сообщение. ';
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	
	$id = false;
	if(!isset($dataRecord['id'])){
		throw new ErrorException("Не достаточно данных"); exit;
	}
	$id = (int)$dataRecord['id'];
	
	
	if(($id == 0) || $id == 'false'){
		throw new ErrorException("Data id of comment is invalid"); exit;
	}

	
	$comment = $db->fetchFirst("SELECT id, user_link, request as nrequest FROM comments WHERE hidden<2 AND id=".$id,true);
	if( is_string($comment) && (strpos($comment,"error") !== false)){
		throw new ErrorException("SQL for get record comment has failed");
	}
	if(count($comment) == 0){
		throw new ErrorException("record comment is not found. (for arg id) "); exit;
	}
	
	
	/* удалять может если пользователь автор ИЛИ если комментарий от гостя И пользователь Админ */
	if(($comment['user_link'] != $user["role"]) && (!(($comment["user_link"] == 0) && ($user['priority'] == 3))) ){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	/* если заявка удалена - то удаление комментария не возможно */
	/* выбираем информацию о заявке по номеру комментария */
	require_once $path."lib/query/get.query.ListTickets.php";
	$query = getQueryToGetTickets()." WHERE a.id=".$comment['nrequest'];
	$request = $db->fetchFirst($query,true);
	if( is_string($request) && (strpos($request,"error") !== false)){
		throw new ErrorException("SQL for get request info");
	}
	
	if($request['hidden'] == 1){
		header("HTTP/1.1 403 Forbidden. This request has been deleted before"); exit;
	}
	
	
	/* каскадное удаление я пока реализовать не могу, поэтому элементарно в цикле */
	if(cascadeUpdate($id)){
		
		header("HTTP/1.1 200 Ok");
		echo '{"success":1}';
	}else{
		throw new ErrorException("undefined error remove of cascade delete"); exit;
	}
		
		
	

}catch(ErrorException $ex){
	/* если application/json  */
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	/* если application/json 
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
}catch(Exception $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>