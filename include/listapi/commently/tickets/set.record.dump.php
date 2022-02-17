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
	
	/* use exam
	
		dataReqNext({file:urlServerSide+'commently/tickets/set.record.php',type:'json'},console.log); 
		dataReqNext({file:urlServerSide+'commently/tickets/set.record.php',type:'json',args:'dataRecord='+JSON.stringify({title: 'test title',
            	        comment: 'test comment value',
            	        hidden: 0,
            	        nresource: 0,
            	        nparent: 0})},console.log);
	*/
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("Нет данных для записи"); exit;
	}
	
	$dataOutput = array();
	// $id = $user["uid"];
	
	$description = 'Получено сообщение. ';
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	/*
	title: 
    comment: 
    hidden:
    nresource: 
    nparent:
	*/
	
	$title = '';
	if(isset($dataRecord['title'])){
		$title = str_replace('\'','"',$dataRecord['title']);
		$title = str_replace("\\","/",$title);
	}
	
	$comment = '';
	if(isset($dataRecord['comment'])){
		$comment = str_replace('\'','"',$dataRecord['comment']);
		$comment = str_replace("\\","/",$comment);
	}
	
	$nRecord = false;
	if(isset($dataRecord['nresource'])){
		$nRecord = (int)$dataRecord['nresource'];
		/* if($nRecord == 0){ $nRecord = false; } */
	}
	if(($nRecord == 0) || $nRecord == 'false'){
		$nRecord = false;
	}

	$nParent = 'NULL';
	if(isset($dataRecord['nparent'])){
		$nParent = (int)$dataRecord['nparent'];
		/* if($nRecord == 0){ $nRecord = false; } */
	}

	$hidden = $dataRecord['hidden'];
	
	
	
	/* тест отказа */
	$checkedDevicesTypes = array();
	
	
	if(count($checkedDevicesTypes) == 0){
		throw new ErrorException("Данные о комментарии не были переданы");
		exit;
	}
	

		/* новая запись */
		
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