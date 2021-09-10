<?php

header('Content-type:application/json');
// header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

if($resolution){
	
	try{
		
		$dataRecord = false;
		if(!isset($_POST['dataRecord'])){
			
			throw new ErrorException("request data is invalid. record for new data is not found ");
		}
		
		$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
		$dataRecord = json_decode($dataRecord,true);
		
		
		if(!(isset($dataRecord['id']) && isset($dataRecord['nrequest']))){
			throw new ErrorException("Недостаточно данных");
		}
		
		$id = (int) $dataRecord['id'];
		$nrequest = (int) $dataRecord['nrequest'];
		
		if(($id <=0) || ($nrequest<=0)){
			throw new ErrorException("Недостаточно данных");
		}
		
		
		$query = "SELECT userId FROM conversationcompleted WHERE id=$id";
		$editRecord = $db->fetchFirst($query, $uid);
		if( is_string($editRecord) && (strpos($editRecord,"error") !== false)){
			throw new ErrorException($editRecord);
		}
		
		
		/* имеет ли пользователь право на изменение записи */
		if(! (($user['uid'] == $editRecord["userId"]) || ($user['priority'] == 3))){
			throw new ErrorException($description." Пользователь не имеет право на изменение записи .");
		}
		
		
		$query = "UPDATE conversationcompleted set nrequest=$nrequest WHERE id=$id";

		
		$resultUpd = $db->query($query, $uid);
		if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
			throw new ErrorException($resultUpd);
		}else{
			$description = 'Запись обновлена';
		}
	
	
		$display = '{"success":1,"description":"'.$description.'"}';
		echo $display;
	
		header("HTTP/1.1 200 Ok");
		
		
	}catch(ErrorException $ex){
		
		echo '{"success":0,"description":"'.exc_handler($ex).'"}';
		
		/* если text/html 
		echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
		*/
	}


}else{ 

	header("HTTP/1.1 403 Forbidden");
}
?>