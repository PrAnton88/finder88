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
	
	
	
	$arrUsersUid = [];
	$query = "SELECT * FROM law";
	$usersLaws = $db->fetchAssoc($query,$uid);
	if(is_string($usersLaws) && (strpos($usersLaws,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	foreach($usersLaws as $item){
		$arrUsersUid[] = $item['uid'];
	}
	

	/* id 
	uid
	adminConversation] => 0
    [dispatchConversation] => 0
    [admintoBroneDevice] => 0
    [dispatchToBroneDevice] => 0
    [adminSectionDocs] */
	
	
	/* теперь будет интересовать есть ли пользователь среди $usersLaws или нет */
	
	
	if(in_array($dataRecord['uid'],$arrUsersUid)){
		/* значит пользоаптель уже есть в этой таблице */
		
		$query = 'DELETE FROM law WHERE uid = '.$dataRecord['uid'];
		$signeding = $db->query($query,$uid);
		
		if(is_string($signeding) && (strpos($signeding,'error')!== false)){
			throw new ErrorException('SQL '.$query.' Error  ');
		}
		
		$description = 'пользователь отписан';
		
	}else{
		
		/* всё что не относится к полям в listlaw - то нужно убрать из $dataRecord */
		$listLaw = array('uid'=>$dataRecord['uid'],
			'adminConversation'=>$dataRecord['adminConversation'],
			'dispatchConversation'=>$dataRecord['dispatchConversation'],
			'dispatchToBroneDevice'=>$dataRecord['dispatchToBroneDevice'],
			'adminSectionDocs'=>$dataRecord['adminSectionDocs']
		);
		
		
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
}catch(Error $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>