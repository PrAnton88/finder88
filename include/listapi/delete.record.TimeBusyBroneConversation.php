<?php

header('Content-type:application/json');
// header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";


if($resolution){
	
	try{
		
		$editRecordId = false;
		if((isset($_POST['flagEditRecordId'])) && ($_POST['flagEditRecordId'] != 'false')){
			
			/* идентификатор редактируемой записи */
			$editRecordId = (int) htmlspecialchars($_POST['flagEditRecordId']);
			if($editRecordId <= 0){
				throw new ErrorException("не установлена запись для удаления");
			}
			
		}else{
			throw new ErrorException("не установлена запись для удаления");
		}
		
		$description = "";
		/* выбрать запись */
		$query = "SELECT id, userId, date, time, devices, hidd, nrequest FROM conversationcompleted WHERE id=$editRecordId";
		$editRecord = $db->fetchFirst($query, $uid);
		if( is_string($editRecord) && (strpos($editRecord,"error") !== false)){
			$description .= $editRecord;
			throw new ErrorException($description);
		}
		
		
		if(! (($user['uid'] == $editRecord["userId"]) || ($user['priority'] == 3)) ){
			throw new ErrorException("недостаточно прав для удаления");
		}
		
		if($editRecord["hidd"] == 1){
			$description = "Запись удалена";
		}else{
			
			$query = "DELETE FROM conversationcompleted WHERE id=$editRecordId";
			$result=$db->query($query,$uid);
		
			if((is_string($result)) && (strpos($result,"error") !== false)){
				header("HTTP/1.1 500 SQL Error: $result");
			}else{
				$description = "Запись удалена";
			}
			
			if($user['uid'] == $editRecord["userId"]){
				/* удаляет сам заявитель */
				$editRecord['admin'] = false;
			}elseif($user['priority'] == 3){
				/* удаляет админ */
				$editRecord['admin'] = $user['fio'];
			}
			$editRecord['applicant'] = $user['fio'];
			
			
		}
		
		
		
		$display = '{"success":1,"description":"'.$description.'","data":'.json_encode($editRecord).'}';
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