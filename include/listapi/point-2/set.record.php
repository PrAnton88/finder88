<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";
	
try{

	if($resolution){
		$resolution = checkUserLaws('adminOrderDocs');
	}
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("Нет данных для записи"); exit;
	}
	
	$dataOutput = array();
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	
	
	/* id документов которые меняем местами */
	if(!isset($dataRecord['move'])){
		throw new ErrorException("arg move is not found");
	}
	$move = $dataRecord['move'];
	if($move == ''){
		throw new ErrorException("arg move is empty");
	}
	
	
	if(!isset($dataRecord['id'])){
		throw new ErrorException("arg id is not found");
	}
	$id = $dataRecord['id'];
	if($id == ''){
		throw new ErrorException("arg id is empty");
	}
	
	if($id == $move){
		throw new ErrorException("записи совпадают");
	}
	
	

	/*
	$editRecord = $db->fetchFirst("SELECT * FROM bronedevicename WHERE id = $nRecord",$uid);
	if(is_string($editRecord) && (strpos($editRecord,'error') !== false)){
		throw new ErrorException('sql error');
	}
		
		
	$resultUpd = $db->update('bronedevicename', array(), $dataRecord, "id=$nRecord");
	if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	*/
	
	$db2 = new DB("infokng-copy");
	
	/* 1. проверить один ли родитель у документов, если это не так - вывести запрет */
	$query = "SELECT id,pagetitle as name,class_key,parent,publishedon FROM modx_site_content ";
	$query .= " WHERE id = $move";
	// $query .= " AND (class_key='modDocument' AND parent = 0) ";
	
	
	$editRecord = $db2->fetchFirst($query,$uid);
	if((is_string($editRecord)) && (strpos($editRecord,"error") !== false)){
		throw new ErrorException("SQL Error, $editRecord");
	}
	
	$query = "SELECT id,pagetitle as name,class_key,parent,publishedon FROM modx_site_content ";
	$query .= " WHERE id = $id";
	// $query .= " AND (class_key='modDocument' AND parent = 0) ";
	
	
	$record = $db2->fetchFirst($query,$uid);
	if((is_string($record)) && (strpos($record,"error") !== false)){
		throw new ErrorException("SQL Error, $record");
	}
	
	
	/* две записи */
	if($record['parent'] != $editRecord['parent']){
		throw new ErrorException("Перемещать в другой раздел запрещено");
	}
	
	/* обновить поле publishedon у записи с $move сделать равным */
	
	
	
	$newPublishedon = (int)($record['publishedon']);
	
	$k = 10;
	if( ((int)($editRecord['publishedon']) == $newPublishedon+$k) || ((int)($editRecord['publishedon']) == $newPublishedon-$k) ){
		
		$k = rand(11, 50);
	}
	
	$dataOutput = array('update'=>true,'k'=>$k);
	
	/* в первую очередь отображаются документы с более поздним временем создания */
	/* чем меньше id тем ниже этот документ (тем раньше он написан и тем меньше у него publishedon) */
	if((int)($editRecord['publishedon']) > $newPublishedon+$k){/* написан документ позже (но находится он раньше) */
		/* опускаем */
		
		$dataOutput['descr'] = 'downs';
		
		$result = $db2->update('modx_site_content', array('publishedon'=>( $newPublishedon-$k )), array(), "id=$move");
	}else{/* написан документ раньше (но находится он позже) */
		/* поднимаем  */
		$dataOutput['descr'] = 'upps';
		
		$result = $db2->update('modx_site_content', array('publishedon'=>( $newPublishedon+$k )), array(), "id=$move");
	}
	
	if((is_string($result)) && (strpos($result,"error") !== false)){
		throw new ErrorException("SQL Error, $result");
	}
	
	
	
	
	echo '{"success":1,"data":'.json_encode($dataOutput).'}';
	

}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	/* если text/html 
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
}catch(Error $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>