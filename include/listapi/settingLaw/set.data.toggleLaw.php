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
	
	
	
	
	if(!isset($dataRecord['field'])){
		throw new ErrorException('dataRecord.field is empty');
	}
	
	if(!isset($dataRecord['val'])){
		throw new ErrorException('dataRecord.val is empty');
	}
	
	
	
	$arrUsersUid = [];
	$query = "SELECT * FROM law";
	$usersLaws = $db->fetchAssoc($query,$uid);
	if(is_string($usersLaws) && (strpos($usersLaws,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	foreach($usersLaws as $item){
		$arrUsersUid[] = $item['uid'];
	}
	

	/* лишь .uid и .field и .val */
	
	
	
	
	/* есть ли пользователь среди $usersLaws или нет */
	if(in_array($dataRecord['uid'],$arrUsersUid)){
		/* значит пользоаптель уже есть в этой таблице */
		
		/* но вдруг новое значение такое же как прежнее */
		foreach($usersLaws as $item){
			if($item['uid'] == $dataRecord['uid']){
				if($item[$dataRecord['field']] == $dataRecord['val']){
					throw new ErrorException('Новое значение такое же как прежнее');
				}
				
			}
		}
		
		
		$checking = $db->update('law', array($dataRecord['field']=>$dataRecord['val']), array(), " uid=".$dataRecord['uid']);
		
		if(is_string($checking) && (strpos($checking,'error')!== false)){
			throw new ErrorException('SQL '.$query.' Error  ');
		}
		
		$description = 'права изменены';
		
	}else{
		throw new ErrorException(' Пользователь не зарегистрирован как сотрудник отдела ТО');
	}
	
	
	echo '{"success":1,"description":"'.$description.'"}';
	
	

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Error $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>