<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";

try{
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}

	$type = '';
	if(isset($_POST['type'])){
		$type = (int) htmlspecialchars($_POST['type']);
		if($type === 0){ throw new ErrorException('type arg is invalid'); }
		
		$type = 'type='.$type;
	}
	
	$editRecord = '';
	if(isset($_POST['editRecord'])){
		
		$editRecord = (int) htmlspecialchars($_POST['editRecord']);
		if($editRecord !== 0){
		
			$editRecord = ' OR (n.id IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND b.idtick='.$editRecord.' AND (e.hidden=0)) )) ';
		}else{
			$editRecord = '';
		}
	}
	
	/* свободные для бронирования устройства */
	/* тут count - это количество свободных устройств, или свободных устройств + $editRecord */
	/* .free = 2 это затребованные, = 0 это выданные */
	
	require_once $path."lib/query/get.query.ListDevices.php";
	
	
	if(isset($_POST['checkBusy'])){
		$query = getQueryToCheckBusyDevices($type);
		
		$result=$db->fetchAssoc($query,true);
		if((is_string($result)) && (strpos($result,"error") !== false)){
			throw new ErrorException($result);
		}
		/* n.id, n.name, n.description, b.idtick, b.datest, b.dateend, c.userid, u.fio */
		
		/* если заказ один и тот же, и устройство одно и то же */
		/* то собираем в одну запись */
		
		if(count($result) > 0){
			$n = 0;
			$tmp = false;
			$tikets = array(array('idtick'=>$result[0]['idtick'],'data'=>array()));
			
			
			
			$tmp = $result[0];
			$tmp['count'] = 1;
			$tikets[0]['data'][] = $tmp;
			
			$i = 0;
			foreach($result as &$item){
				
				if($i > 0){
					$tmp = $result[$i];
					$tmp['count'] = 1;
					if(($result[$i]['idtick']) === ($tikets[$n]['idtick'])){
						$tikets[$n]['data'][] = $tmp;
					}else{
						$tikets[] = array('idtick'=>$result[$i]['idtick'],'data'=>array($tmp));
						$n++;
					}
					if($i == count($result)){ break; }
				}
				
				$i++;
			}
			
			$reData = [];
			
			/* теперь идём по массиву устройсив в каждом из заказов */
			/* и если идентификаторы устройств совпадают - увеличиваем count и убираем одну запись */
			$i = 0;
			foreach($tikets as &$tiket){
				$n = 0;
				
				/* перевернуть элементы массива так, что бы следующий цикл - перебирал с элементы с конца,
				в таком случае, удаление элементов безвредно */
				
				$reData = array_reverse($tiket['data']);
				
				foreach($reData as &$device){
				
					if(isset($reData[$n+1]['name'])){
						if($device['name'] === $reData[$n+1]['name']){
						
							$reData[$n+1]['count'] += $device['count'];
							$device = null;
							
							unset($reData[$n]);
						}
					}
						
					$n++;
				}
				
				/* взять последний - потому что все остальные удалены */
				$tiket['data'] = array($reData[$n-1]);
			}
			
			$result = $tikets;
		}
		
	}else{
		$query = getQueryToCheckFreeCountListDevice($type,$editRecord);
		
		$result=$db->fetchAssoc($query,true);
		
		
	}
	
	
	if(!isset($getImport)){
		
		

		if((is_string($result)) && (strpos($result,"error") !== false)){
			throw new ErrorException($result);
		}
		
		echo '{"success":1,"listData":'.json_encode($result).'}';
	}
	

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>