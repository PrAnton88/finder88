<?php

header('Content-type:application/json');
// header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
try{
	
	$path = '../';
	require_once "$path../start.php";
	
	/* use exam
		
		dataReqNext({file:urlServerSide+'helpdesk/change.state.record.php',type:'json',args:'dataRecord='+JSON.stringify(
		{state:1,priority:2,assd:811,nrequest:4434})},
		function(resultJson){
			resultJson = success.check(resultJson);
				
			console.log(resultJson);
		});
		
		
		.state == 0 Не назначена
		.state == 1 Принята
		.state == 2 Закрыта
		
		.priority == 0 Низкий
		.priority == 1 Средний
		.priority == 2 Высокий
		
	*/
	
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("Нет данных для записи");
	}
	
	$dataOutput = array();
	$id = $user["uid"];
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	$state = false;
	if(isset($dataRecord['state'])){
		$state = (int)$dataRecord['state'];
	}
	$priority = false;
	if(isset($dataRecord['priority'])){
		$priority = (int)$dataRecord['priority'];
	}
	$assd = false;
	if(isset($dataRecord['assd'])){
		$assd = (int)$dataRecord['assd'];
	}
	
	$nrequest = false;
	if(isset($dataRecord['nrequest'])){
		$nrequest = (int)$dataRecord['nrequest'];
	}
	
	if($nrequest === false){
		throw new ErrorException("аргумент nrequest не найден");
	}
	if($nrequest <= 0){
		throw new ErrorException("аргумент nrequest не найден установлен неверно");
	}
	
	$query = "SELECT * FROM request WHERE id=$nrequest";
	$existmess = $db->fetchFirst($query);
	if((is_string($existmess)) && (strpos($existmess,"error") !== false)){
		throw new ErrorException("SQL Error. get request data has failed.");
	}
	
	if(! (($existmess["applicant"] == $user["uid"]) || ($user['priority'] == 3))){
		throw new ErrorException("Вы не имете право на изменение записи .");
	}
	
	if((int)$existmess["user_link"] != $assd){
		$existmess["user_link"] = $assd;
		$dataOutput["assd"] = $assd;
		
		if($assd != 0){
			$state = 1;
		}else{
			$state = 0;
		}
	}
	if((int)$existmess["priority"] != $priority){
		$existmess["priority"] = $priority;
		$dataOutput["priority"] = $priority;
	}
	
	if($state === false){
		$state = (int)$existmess["state"];
	}
	
	if((int)$existmess["state"] != $state){
		$existmess["state"] = $state;
	}
	
	$dataOutput["state"] = $state;
	
	$query = "UPDATE request set ";
	
	if($assd == 0){ 
		$query .= "user_link=NULL,";
	}else{
		$query .= "user_link='".$existmess["user_link"]."',";
	}
	$query .= " priority='".$existmess["priority"]."', state='".$existmess["state"]."' WHERE id=$nrequest";
	
	
	
	$resultUpd = $db->query($query, $uid);
	if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	$dataOutput["nrequest"] = $nrequest;
	
	/*foreach($dataOutput as $item => $key){
		echo $item;
		
	}*/
	
	$dataOutput = json_encode($dataOutput);
	
	$display = '{"success":1,"data":'.$dataOutput.'}';
	echo $display;
	
	/* throw new ErrorException("Тестовая ошибка вместо успеха"); */
	
	header("HTTP/1.1 200 Ok");
	
	
}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	/* если text/html 
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>