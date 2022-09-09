<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{
	
	if($resolution){
		/* добавлять пользователей в таблицу для раздачи прав может только Администратор Настройки Ролей */
		$resolution = checkUserLaws('adminSettingLaw');
	}
	
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
	
	
	
	$arrUsersUid = [];
	$query = "SELECT * FROM law";
	$usersLaws = $db->fetchAssoc($query,$uid);
	if(is_string($usersLaws) && (strpos($usersLaws,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	foreach($usersLaws as $item){
		$arrUsersUid[] = $item['uid'];
	}
	

	/* id - role.id = user.role 
	   uid - user.id 
	adminConversation] => 0 
    [dispatchConversation] => 0 
    [adminBroneDevice] => 0 
    [dispatchToBroneDevice] => 0 
    [adminSectionDocs] */
	
	
	/* теперь будет интересовать есть ли пользователь среди $usersLaws или нет */
	
	
	if(in_array($dataRecord['id'],$arrUsersUid)){
		/* значит пользоаптель уже есть в этой таблице */
		
		$query = 'DELETE FROM law WHERE uid = '.$dataRecord['id'];
		$signeding = $db->query($query,$uid);
		
		if(is_string($signeding) && (strpos($signeding,'error')!== false)){
			throw new ErrorException('SQL '.$query.' Error  ');
		}
		
		$description = 'пользователь отписан';
		
	}else{
		
		/* всё что не относится к полям в listlaw - то нужно убрать из $dataRecord */
		$listLaw = array('uid'=>$dataRecord['id'],
			'adminConversation'=>(isset($dataRecord['adminConversation'])?$dataRecord['adminConversation']:0),
			'dispatchConversation'=>(isset($dataRecord['dispatchConversation'])?$dataRecord['dispatchConversation']:0),
			'dispatchToBroneDevice'=>(isset($dataRecord['dispatchToBroneDevice'])?$dataRecord['dispatchToBroneDevice']:0),
			'adminSettingLaw'=>(isset($dataRecord['adminSettingLaw'])?$dataRecord['adminSettingLaw']:0)
		);
		
		
		/* список прав пользователя выбираем из $query = getQueryUserLaws($db);
		$query .= " WHERE u.role = ".$uid;
		$dataLawsOfUser = $db->fetchFirst($query); */
		
		$respInsert = $db->insert("law", $listLaw, array());
		
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