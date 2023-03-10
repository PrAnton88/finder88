<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{

	$dataRecord = false;
	require_once "$path../headerBase.php";
	
	$dataOutput = array();
	// $id = $user["uid"];
	$description =	'Получено сообщение. ';
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
	
	/* вероятно диспетчер может забронировать или изменять запись на период граница которого ранее сегодняшнего дня */
	if(($datest < date("Y-m-d")) || ($dateend < date("Y-m-d"))){
		
		$resolution = checkUserLaws('adminBroneDevice');
		if(!$resolution){
			$resolution = checkUserLaws('dispatchToBroneDevice');
		}
		
		if(!$resolution){
			// throw new ErrorException('test error');
			
			header("HTTP/1.1 403 Forbidden"); exit;
		}
	}
	
	$checkedDevicesTypes = $dataRecord['checkedDevicesTypes'];

	$deviceName = '';
	$deviceCount = '';


	function bulidListDeviceChecked(){
		// собрать выбранные устройства в строку
		
		global $checkedDevicesTypes;
		$checkedDevicesAllTypes = array();
		
		$listdevice = '';
		$currentType = '';
		
		foreach($checkedDevicesTypes as $itemType){
			$currentType = '('.$itemType['name'].') ';
			
			// $itemDevices [id,name]
			$itemDevices = $itemType['checkedDevices'];
			// $checkedDevicesAllTypes[] = $itemDevices;
			
			
			$issetDevice = array();
			$checkedDevices = array();
			
			
			foreach($itemDevices as $device){
				
				$checkedDevicesAllTypes[] = $device;
				foreach($checkedDevices as $item){
				
					if($item['name'] == $device['name']){
						$issetDevice = $item;
						break;
					}
				}
			
				
				if(count($issetDevice) == 0){

					$checkedDevices[] = $device;
				}
				
				
				$issetDevice = array();
			}
			
			foreach($checkedDevices as $item){
				
				$listdevice .= $currentType;
				$listdevice .= $item['name'].' -->'.$item['count'].' шт; ';
			}	
		}
		
		return array('strListdevice'=>'Запрашиваемые устройства: '.$listdevice,'checkedDevices'=>$checkedDevicesAllTypes);
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
		$existRecord = $db->fetchFirst($query,true);
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
			
			
			$query = "UPDATE bronedevicecomplete set listdevice='".addslashes($listdevice)."' WHERE id=$nRecord";
			
			if($note !== ''){
			
				$insertId = $db->insert("bronedevicenotes", 
					array(), 
					array('note'=>$note)
				);
				
				/* !!! НО, в $listdevice только текст */
				$query = "UPDATE bronedevicecomplete set listdevice='".addslashes($listdevice)."', note=$insertId WHERE id=$nRecord";
				
			}
			
			
			
			
			$resultUpd = $db->query($query, $uid);
			if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
				throw new ErrorException("SQL Error");
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
		
		
		if($update){
			/* в режиме редактирования, часть устройств переданная в массиве может быть уже выдана */
			/* сначала нужно узнать какие именно устройства уже выданы, 
			что бы не перезаписать поле bronedevicedate.free = 0 - будто снова только запрошены */
			
			
			$query = "SELECT free FROM bronedevicedate WHERE idtick = $nRecord";
			$query2 = "";
			foreach($checkedDevices as &$device){
				
				$query2 = $query." AND iddevice = ".$device['id'];
				
				$existRecord = $db->fetchFirst($query2,true);
				if((is_string($existRecord)) && (strpos($existRecord,"error") !== false)){
					throw new ErrorException("SQL Error. get request data has failed. query = ".$query2); exit;
				}
				
				if(!is_array($existRecord)){
					throw new ErrorException("data is nit array. query = ".$query2); exit;
				}
				
				if(count($existRecord) > 0 ){
				
				
					if(!isset($existRecord['free'])){
						throw new ErrorException("data .free is not exist. query = ".$query2); exit;
					}
					
					$device['free'] = $existRecord['free'];
					
				}else{
					/* просто не было ранее забронировано */
					
				}
				
					
				// print_r($device);
				// echo '<br />';
					
			}
		}
		
		
		
		
		
		$query = "DELETE FROM bronedevicedate WHERE idtick=".$nRecord;
		$result=$db->query($query,$uid);
	
		if((is_string($result)) && (strpos($result,"error") !== false)){
			header("HTTP/1.1 500 SQL Error: $result"); exit;
		}
		
		$description .= '. дата начала '.$datest.', дата окончания '.$dateend;
		
		
		// print_r($checkedDevices);
		
		
		foreach($checkedDevices as &$device){
			
			// вставляем запись о новом устройстве $device
			
			
			// print_r($device);
			// echo '<br />';
			
			
			/* ранее возвращенные устройства разрешено бронировать снова */
			/* тогда признак возврата .free = 1 заменяем на 0 */
			/* ведь должно юыть можно забронировать то что уже снова в обороте */
			
			
			$insertId = $db->insert("bronedevicedate", 
				array('iddevice'=>$device['id'],'datest'=>"'$datest'",'dateend'=>"'$dateend'",'idtick'=>$nRecord,
				'free'=>( (( isset($device['free']) ) && (((int)($device['free'])) != 0))?( ($device['free'] == 1)?0:$device['free'] ):0 )
				), 
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
	
	$dataOutput["listdevice"] = $listdevice;
	
	
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