<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{
	
	$admin = ($user['priority'] == 3);
	if(!$admin){ $resolution = false; }
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}

	if(!isset($_POST['dataRecord'])){
		throw new ErrorException('dataRecord is empty');
		
	}

	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
		
	$description = '';
		
	if(isset($dataRecord['signed']) && ($dataRecord['signed'] == true)){
		/* просто отписываем - удаляем запись из таблицы frontend */
		
		$query = 'DELETE FROM frontend WHERE id = '.$dataRecord['id'];
		$signeding = $db->query($query,$uid);
		
		if(is_string($signeding) && (strpos($signeding,'error')!== false)){
			throw new ErrorException('SQL '.$query.' Error  ');
		}
		
		$description = 'пользователь отписан';
		
	}else{
		/* пользователь не был подписан, подписываем его - вносим в таблицу frontend */
		
		$respInsert = $db->insert("frontend", array('id'=>$dataRecord['id']), array());
		
		if(is_string($respInsert) && (strpos($respInsert,'error')!== false)){
			throw new ErrorException('SQL Error');
		}
		
		if(!$respInsert){
			throw new ErrorException('undefined Error пользователь НЕ подписан');
		}else{
			
			$description = 'пользователь подписан';
		}
		
	}
	
	
	echo '{"success":1,"description":"'.$description.'"}';
	
	

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>