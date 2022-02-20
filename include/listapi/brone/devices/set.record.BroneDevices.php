<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{

	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("Нет данных для записи"); exit;
	}
	
	$dataOutput = array();
	// $id = $user["uid"];
	
	$description = 'Получено сообщение. ';
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	
	$note = '';
	if(isset($dataRecord['note'])){
		$note = str_replace('\'','"',$dataRecord['note']);
		$note = str_replace("\\","/",$note);
	}
	
	$nRecord = false;
	if(isset($dataRecord['id'])){
		$nRecord = (int)$dataRecord['id'];
		/* if($nRecord == 0){ $nRecord = false; } */
	}
	if(($nRecord == 0) || $nRecord == 'false'){
		$nRecord = false;
	}

	$datest = $dataRecord['datest'];
	$dateend = $dataRecord['dateend'];
	$checkedDevicesTypes = $dataRecord['checkedDevicesTypes'];

	$deviceName = '';
	$deviceCount = '';

	function bulidListDeviceChecked(){
		// собрать выбранные устройства в строку
		
		global $checkedDevicesTypes;
		
		$checkedDevices = array();
		$listdevice = 'Запрашиваемые устройства: ';
		
		foreach($checkedDevicesTypes as $itemType){
			$listdevice .= '('.$itemType['name'].') ';
			
			$deviceName = '';
			$deviceCount = 1;
			// $itemDevices [id,name]
			
			$itemDevices = $itemType['checkedDevices'];
			
			$deviceName = $itemDevices[0]['name'];
			$listdevice .= $deviceName.'  -->';
			
			$checkedDevices[] = $itemDevices[0];
			
			for($i = 1; $i<count($itemDevices); $i++){
			
				if($itemDevices[$i]['name'] != $deviceName){
					
					$listdevice .= $deviceCount.' шт; ';
					$deviceCount = 1;
					
					$deviceName = $itemDevices[$i]['name'];
					
					$listdevice .= '('.$itemType['name'].') ';
					$listdevice .= $deviceName.' -->';
				}else{
					
					$deviceCount++;
				}
				
				$checkedDevices[] = $itemDevices[$i];
			}
			$listdevice .= $deviceCount.' шт; ';
			
		}
		
		return array('strListdevice'=>$listdevice,'checkedDevices'=>$checkedDevices);
	}

	
	if(count($checkedDevicesTypes) == 0){
		throw new ErrorException("Данные о бронируемых устройствах не были переданы");
		exit;
	}
	

	if($nRecord === false){
		/* новая заявка */
		
		/* не заняты ли эти устройства на этот же период времени КАКИМ ЛИБО ЛЮБЫМ пользователем */
		/* bronedevicedate 'iddevice','datest','dateend','idtick','free' */
		
		require_once $path."lib/query/get.query.ListDevices.php";
		
		foreach($checkedDevicesTypes as $itemType){
		
			$querytCountListDevices = getQueryToCheckFreeCountListDevice('type='.$itemType['id']);
			$resultCountListDevices = $db->fetchAssoc($querytCountListDevices);
			
			
			// массив устройств данного типа, которые возможно забронировать [ ['id', 'name'] ]
			
			if((is_string($resultCountListDevices)) && (strpos($resultCountListDevices,"error") !== false)){
				header("HTTP/1.1 500 SQL Error: $resultCountListDevices"); exit;
			}
			
			// массив устройств которые были затребованы
			foreach($itemType['checkedDevices'] as $itemCheckedDevice){
				// в $itemCheckedDevice ['id', 'name', 'count']
				
				$much = false;
				// пойдём по массиву доступных устройств дянного типа
				foreach($resultCountListDevices as $countableDevice){
					
					if($itemCheckedDevice['id'] == $countableDevice['id']){
						
						$much = true;
					}
				}
				
				if($much === false){
					
					throw new ErrorException("Затребовано устройство которое не было свободно");
				}
			}
		}
		
		
	
		$listdevice = bulidListDeviceChecked();
		// print_r($listdevice);
		
		
		$checkedDevices = $listdevice['checkedDevices'];
		$listdevice = $listdevice['strListdevice'];
		
		// echo ($listdevice);
		
		
		$insertId = 0;
		if($note !== ''){
			
			$insertId = $db->insert("bronedevicenotes", 
				array(), 
				array('note'=>$note)
			);
			
			if(!$insertId){
				throw new ErrorException("запись не создана");
				exit;
			}
		}
		
		

		
		$insertIdtick = $db->insert("bronedevicecomplete", 
			array('userid'=>$user["uid"]/*$uid*/,'note'=>$insertId), 
			array('listdevice'=>$listdevice)
		);
			
		if(!$insertIdtick){
			throw new ErrorException("запись не создана");
			exit;
		}
		
		
		foreach($checkedDevices as $device){
			$insertId = $db->insert("bronedevicedate", 
				array('iddevice'=>$device['id'],'datest'=>"'$datest'",'dateend'=>"'$dateend'",'idtick'=>$insertIdtick,'free'=>0), 
				array()
			);
			
			if(!$insertId){
				throw new ErrorException("запись не создана");
				exit;
			}
			
			$description .= '. запись вставлена '.$insertId;
		}
		
		$dataOutput["id"] = $insertIdtick;
		$dataOutput["new"] = true;
		
		
	}else{
		if($nRecord <= 0){
			throw new ErrorException("Нет поля nRecord");
		}
		
		/* передан $nRecord значит редактирование */
		
		/* редактирование  */
		/* обновление $title и $body */
		$query = "SELECT * FROM bronedevicecomplete WHERE id=$nRecord";
		$existRecord = $db->fetchFirst($query);
		if((is_string($existRecord)) && (strpos($existRecord,"error") !== false)){
			throw new ErrorException("SQL Error. get request data has failed."); exit;
		}
		
		if(! (($existRecord["userid"] == $user["uid"]) || ($user['priority'] == 3))){
			throw new ErrorException("Вы не имете право на изменение записи ."); exit;
		}
		
		/* Запрашиваемые устройства  -- SIM-карта  -  89155455380  -->1 шт;Телефон  - Nokia 130 Dual SIM  -->1 шт;Фотоаппарат  - Canon IXUS 190  -->1 шт; */

		$listdevice = bulidListDeviceChecked();
		/* array('strListdevice'=>$listdevice,'checkedDevices'=>$checkedDevices); */
		
		$checkedDevices = $listdevice['checkedDevices'];
		$listdevice = $listdevice['strListdevice'];
		
		if(count($checkedDevices) > 0){
			
			
			if($note !== ''){
			
				$insertId = $db->insert("bronedevicenotes", 
					array(), 
					array('note'=>$note)
				);
				
			}
			
			
			/* !!! НО, в $listdevice только текст */
			$query = "UPDATE bronedevicecomplete set listdevice='".addslashes($listdevice)."', note=$insertId WHERE id=$nRecord";
			
			$resultUpd = $db->query($query, $uid);
			if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
				throw new ErrorException("SQL Error. updating request data has failed. $resultUpd");
				exit;
			}
			
			$update = true;
			
		}else{
			
			$query = "DELETE FROM bronedevicecomplete WHERE id=".$nRecord;
			$result=$db->query($query,$uid);
		
			if((is_string($result)) && (strpos($result,"error") !== false)){
				header("HTTP/1.1 500 SQL Error: $result"); exit;
			}
			
			$update = false;
		}
		
		
		
		$query = "DELETE FROM bronedevicedate WHERE idtick=".$nRecord;
		$result=$db->query($query,$uid);
	
		if((is_string($result)) && (strpos($result,"error") !== false)){
			header("HTTP/1.1 500 SQL Error: $result"); exit;
		}
		
		$description .= '. дата начала '.$datest.', дата окончания '.$dateend;
		
		foreach($checkedDevices as $device){
			
			// вставляем запись о новом устройстве $device
			
			$insertId = $db->insert("bronedevicedate", 
				array('iddevice'=>$device['id'],'datest'=>"'$datest'",'dateend'=>"'$dateend'",'idtick'=>$nRecord,'free'=>0), 
				array()
			);
			
			if(!$insertId){
				throw new ErrorException("запись не создана");
			}
			
			$description .= '. запись вставлена '.$insertId;
			
		}
		
		$dataOutput["id"] = $nRecord;
		
		/* если $update = false, то пересохранение без данных, в результате чего произошло удаление записи */
		
		$dataOutput["update"] = $update;
	}
	
	
	
	
	$dataOutput = json_encode($dataOutput);
	
	$display = '{"success":1,"description":"'.$description.'","data":'.$dataOutput.'}';
	echo $display;
	
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