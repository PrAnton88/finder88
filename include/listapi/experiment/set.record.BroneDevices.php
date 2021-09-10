<?php

header('Content-type:application/json');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Expose-Headers:Connection,Content-Length,Date,Expires,Keep-Alive');

	$getUserAuthData = true;
	$sessAccesser = true;
	
try{

	/* require_once "../../start.php"; */
	
	require_once "../../error.reporting.php";
	require_once("../../../config/connDB.php");/* get $db */
	
	$user = array("id"=>842,"uid"=>811,'priority'=>3);
	$uid = $user['uid'];
	
	$resolution = true;
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

	function bulidListDeviceChecked(){
		// собрать выбранные устройства в строку
		
		global $checkedDevicesTypes;
		
		$checkedDevices = array();
		$listdevice = 'Запрашиваемые устройства  -- ';
		
		foreach($checkedDevicesTypes as $itemType){
			// $itemType [id, name] 
			$listdevice .= $itemType['name'].'  -  ';
			foreach($itemType['checkedDevices'] as $itemDevices){
				// $itemDevices [name, count]
				$listdevice .= $itemDevices['name'].'  -->'.$itemDevices['count'].' шт;';
				$checkedDevices[] = $itemDevices;
				
			}
		}
		
		return array('strListdevice'=>$listdevice,'checkedDevices'=>$checkedDevices);
	}

	

	if($nRecord === false){
		/* новая заявка */
		
		/* не заняты ли эти устройства на этот же период времени КАКИМ ЛИБО ЛЮБЫМ пользователем */
		/* bronedevicedate 'iddevice','datest','dateend','idtick','free' */
		
		$getImport = true;
		
		
		
		require_once "get.data.ListDevices.php";
		
		foreach($checkedDevicesTypes as $itemType){
		
			$resultCountListDevices = checkFreeCountListDevice($itemType['id']);
			// массив устройств данного типа, которые возможно забронировать [ ['id', 'name', 'count'] ]
			
			if((is_string($resultCountListDevices)) && (strpos($resultCountListDevices,"error") !== false)){
				header("HTTP/1.1 500 SQL Error: $resultCountListDevices"); exit;
			}
			
			// массив устройств которые были затребованы
			foreach($itemType['checkedDevices'] as $itemCheckedDevice){
				// в $itemCheckedDevice ['id', 'name', 'count']
				
				// пойдём по массиву доступных устройств дянного типа
				foreach($resultCountListDevices as $countableDevice){
					// в $countableDevice ['id', 'name', 'count']
					if($itemCheckedDevice['id'] == $countableDevice['id']){
						
						
						if($itemCheckedDevice['count'] > $countableDevice['count']){
							// требуют количество устройств больше чем доступно устройств этого типа и имени
							throw new ErrorException("недопустимое значение Count"); exit;
						}
						
					}
				}
			}
		}
		
		
	
		$listdevice = bulidListDeviceChecked();
		
		$checkedDevices = $listdevice['checkedDevices'];
		$listdevice = $listdevice['strListdevice'];
		
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
			array('userid'=>$user['id'],'note'=>$insertId), 
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
			/* !!! НО, в $listdevice только текст */
			$query = "UPDATE bronedevicecomplete set listdevice='".addslashes($listdevice)."' WHERE id=$nRecord";
			
			$resultUpd = $db->query($query, $uid);
			if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
				throw new ErrorException("SQL Error. updating request data has failed. $resultUpd");
				exit;
			}
			
			$query = "UPDATE bronedevicenotes set note='".addslashes($note)."' WHERE id=".$existRecord['note'];
			
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
		
		
		
		
		
		$query = "SELECT * FROM bronedevicedate WHERE idtick=$nRecord";
		$existRecords = $db->fetchAssoc($query);
		if( is_string($existRecords) && (strpos($existRecords,"error") !== false)){
			throw new ErrorException("SQL Error. $existRecords");
			exit;
		}
		/* $existRecords[iddevice,datest,dateend,idtick,free] */
		
		/* рассмотрим совпадение с устройствами из $checkedDevices[] */
		$much = false;

		$description .= '. дата начала '.$datest.', дата окончания '.$dateend;
		
		foreach($checkedDevices as $device){
			$much = false;
			
			foreach($existRecords as $item){
			
				if($item['iddevice'] == $device['id']){
					// обновить datest,dateend у $item['id']
					$query = "UPDATE bronedevicedate set datest = '".$datest."',dateend = '".$dateend."' WHERE iddevice=".$device['id'];
		
					$resultUpd = $db->query($query, $uid);
					if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
						throw new ErrorException("SQL Error. updating request data has failed. $resultUpd");
						exit;
					}
					
					$description .= '. запись обновлена '.$resultUpd;
					$much = true;
					break;
				}
				
			}
			
			if($much === false){
				// вставляем запись о новом устройстве $device
				
				$insertId = $db->insert("bronedevicedate", 
					array('iddevice'=>$device['id'],'datest'=>"'$datest'",'dateend'=>"'$dateend'",'idtick'=>$nRecord,'free'=>0), 
					array()
				);
				
				if(!$insertId){
					throw new ErrorException("запись не создана");
					exit;
				}
				
				$description .= '. запись вставлена '.$insertId;
			}
		}
		
		// потом об удалении устройств из $existRecords, которых нет в $checkedDevices
		$much = false;
		
		foreach($existRecords as $item){
			$much = false;
			
			foreach($checkedDevices as $device){
			
				if($device['id'] == $item['iddevice']){
					
					$much = true;
					break;
				}
			}
			
			if($much == false){
				// удаляем $item['iddevice']
				$query = "DELETE FROM bronedevicedate WHERE iddevice=".$item['iddevice']." AND idtick=".$nRecord;
				$result=$db->query($query,$uid);
			
				if((is_string($result)) && (strpos($result,"error") !== false)){
					header("HTTP/1.1 500 SQL Error: $result"); exit;
				}
				
				$description .= '. запись удалена iddevice='.$item['iddevice'].' AND idtick='.$nRecord;
			}
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
}catch(Error $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>