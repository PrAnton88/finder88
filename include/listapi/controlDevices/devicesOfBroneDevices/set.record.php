<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{

	if($resolution){
		$resolution = checkUserLaws('adminBroneDevice');
	}
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("Нет данных для записи"); exit;
	}
	
	$dataOutput = array();
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	
	
	$type = false;
	if(isset($dataRecord['type'])){
		$type = (int) $dataRecord['type'];
	}else{
		throw new ErrorException("arg type is not found");
	}
	
	if((!$type) || ($type == 0)){
		throw new ErrorException("arg type is invalid");
	}
	
	
	$nRecord = false;
	if(isset($dataRecord['id'])){
		$nRecord = (int)$dataRecord['id'];
	}
	if(($nRecord == 0) || $nRecord == 'false'){
		$nRecord = false;
	}

	
	/* если количество увеличилось - то добавляем записи */
	
	
	$newType = false;
	
	if($nRecord === false){
		// нов
		
		
		/* мы должны проверить нет ли в этом типе записи с таким же именем */
		$query = "SELECT type,name FROM bronedevicename WHERE hidden = 0 AND type = $type AND name = '".$dataRecord['name']."'";
		$res = $db->fetchFirst($query,$uid);
		if( is_string($res) && (strpos($res,"error") !== false)){
			throw new ErrorException("SQL Error $res");
		}
		if(count($res) > 0){
			throw new ErrorException("Запись с таким именем уже существует");
		}
		
		
		
		
		$firstInsert = null;
		
		for($i = 0; $i < $dataRecord['count']; $i++){
			/* количество увеличилось */
			$insertId = $db->insert("bronedevicename", 
				array(), 
				$dataRecord
			);
				
			
			if(!$insertId){
				throw new ErrorException("запись не создана");
			}
			
			if(!$firstInsert){ $firstInsert = $insertId; }
		}
		
		
		/*
		$insertId = $db->insert("bronedevicename", 
			array(),
			$dataRecord
		);
			
		if(!$insertId){
			throw new ErrorException("запись не создана");
			exit;
		}
		*/
		
		$dataOutput["id"] = $firstInsert;
		$dataOutput["new"] = true;
		
		
	}else{
		
		if($nRecord <= 0){
			throw new ErrorException("Нет поля nRecord");
		}
		
		
		$editRecord = $db->fetchFirst("SELECT * FROM bronedevicename WHERE id = $nRecord",$uid);
		if(is_string($editRecord) && (strpos($editRecord,'error') !== false)){
			throw new ErrorException('sql error');
		}
		
		/* узнать изменился ли тип !!!! */
		if($editRecord['type'] != $type){
			$newType = true;
		}
		
		if(count($editRecord) == 0){
			throw new ErrorException('Редактируемый элемент не найден');
		}
		
		
		
		/* проверки */
		/* 1. в этом типе есть ли устройства с таким же именем ? */
		if(trim($dataRecord['name']) != trim($editRecord['name'])){
		
			$query = "SELECT type,name FROM bronedevicename WHERE hidden = 0 AND type = $type AND name = '".trim($dataRecord['name'])."'";
			$res = $db->fetchFirst($query,$uid);
			if( is_string($res) && (strpos($res,"error") !== false)){
				throw new ErrorException("SQL Error $res");
			}
			if(count($res) > 0){
				/* а что если мы просто изменяли количество этого утсройства в этом типе */
				if(($newType === false) || ($editRecord['count'] == $dataRecord['count'])){
					
					throw new ErrorException("Запись с таким именем уже существует");
				}else{
					/* вариант умысла - изменили и название на название существующего устройства ещё и количество */
					/* так как обновление, в этом случае просто обновится запись устройства - теперь будет в нужном количестве */
					
				}
			}
		}
		
		
		
		// print_r($editRecord);
		
		if($editRecord['count'] != $dataRecord['count']){
			
			/* Прим.1 ну нужно что бы НЕ обновлялось id в других записях */
			unset($dataRecord['id']);
			
			/* Прим.2 после каждой операции unset интексы массива теперь идут не по порядку !! 
			если обращаться по номеру индекса, которого не существует - будет ошибка */
			
			$resultOfBusy = array();
			$resultOfFree = array();
			
			require_once $path."lib/query/get.query.ListDevices.php";
			/* если количество уменьшилось - то проверяем на то заняты ли устройства 
			и если нет - удаляем это количество незанятых устройств */
			$query = getQueryToCheckBusyDevices('n.type = '.$editRecord['type']);
			$result = $db->fetchAssoc($query,$uid);
			if(is_string($result) && (strpos($result,'error') !== false)){
				throw new ErrorException('sql error');
			}
			
			/* занятые устройства этого типа */
			// print_r($result);
			/* если массив не пустой, то */
			/* пройтись по нему и убрать устройства которые не относятся к этому же наименованию */
			$c = 0;
			foreach($result as &$device){
				if($device['name'] != $editRecord['name']){
					$device = null;
					
					unset($result[$c]);
				}
				$c ++;
			}
			// print_r($result);
			$resultOfBusy = $result;
			
			
			$query = getQueryToCheckFreeCountListDevice('n.type = '.$editRecord['type']);
			$result = $db->fetchAssoc($query,$uid);
			if(is_string($result) && (strpos($result,'error') !== false)){
				throw new ErrorException('sql error');
			}
			
			/* свободные устройства этого типа */
			// print_r($result);
			/* пройтись по нему и убрать устройства которые не относятся к этому же наименованию */
			$c = 0;
			$result2 = array(); /* нужно будет убрать "дыры" с отсутствующими индексами */
			foreach($result as &$device){
				if($device['name'] !== $editRecord['name']){
					$device = null;
					
					unset($result[$c]);
				}else{
					/* о причине этого - смотри в Прим.2 */
					$result2[] = $result[$c];
				}
				
				$c ++;
			}
			
			$result = $result2;
			/*
			echo 'только эти устройства можно удалять - если количество указано меньшее';
			print_r($result);
			*/
			$resultOfFree = $result;
			if($dataRecord['count'] < $editRecord['count']){
				// echo 'уменьшаем количество';
				/* позволительно уменьшить только до количества устройств, которые заняты пользователями */
				
			
				if($dataRecord['count'] < (count($resultOfBusy))){
					throw new ErrorException('Количество можно уменьшить только до '.count($resultOfBusy).' шт');
				}
				
				/* когда уменьшено до позволительного числа до >= count($resultOfBusy) */
				/* 1. удаляем из $resultOfFree до $dataRecord['count'] */
				/* 2. обновляем все поля оставшихся записей  */
				
				/* 1 - в dataRecord['count'] принято новое количество */
				for($i = 0; $i < ($dataRecord['count'] - count($resultOfBusy)); $i++){
					
					
					/*$resultUpd = $db->update('bronedevicename', array(), $dataRecord, "id=".$resultOfFree[$i]['id']);
					if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false) ){
						throw new ErrorException("SQL Error");
					}*/
					
					
					unset($resultOfFree[$i]);
				}
				
				/* 2 - */
				/* в $resultOfFree теперь остались записи только на удаление ';*/
				// print_r($resultOfFree); 
				
				foreach($resultOfFree as $item){
					$query = "DELETE FROM bronedevicename WHERE id=".$item['id'];
					$result=$db->query($query,$uid);

					if((is_string($result)) && (strpos($result,"error") !== false)){
						throw new ErrorException("SQL Error");
					}
				}
				
				/* а теперь обновить информацию в устройствах которые заняты */
				/*foreach($resultOfBusy as $item){
					$resultUpd = $db->update('bronedevicename', array(), $dataRecord, "id=".$item['id']);
					if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
						throw new ErrorException("SQL Error");
					}
				}*/
				
			}else{
				/* count >= */
				
				for($i = ($dataRecord['count'] - $editRecord['count']); $i > 0; $i--){
					/* количество увеличилось */
					$insertId = $db->insert("bronedevicename", 
						array(), 
						$dataRecord
					);
						
					if(!$insertId){
						throw new ErrorException("запись не создана");
					}
				}
				
				/* как на счёт того что бы обновить информацию в уже имеющихся записях */
				/* по $resultOfBusy и по $resultOfFree */
				/*foreach(array_merge($resultOfFree,$resultOfBusy) as $item){
					
					$resultUpd = $db->update('bronedevicename', array(), $dataRecord, "id=".$item['id']);
					if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
						throw new ErrorException("SQL Error");
					}
					
				}*/
				
				
				
			}
			
			
			
			
			
		}
		
		/* только если тип не изменился !!! */
		
		if($newType){
			$type = $editRecord['type'];
		}
			
		$dataRecord['id'] = null;
		unset($dataRecord['id']);
		
		$resultUpd = $db->update('bronedevicename', array(), $dataRecord, "type=$type AND name='".$editRecord['name']."'");
		if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
			throw new ErrorException("SQL Error $resultUpd");
		}
		
		
		$dataOutput["id"] = $nRecord;
		$dataOutput["update"] = true;
		/* $dataOutput["dataRecord"] = $dataRecord; */
	}
	
	
	$dataOutput = json_encode($dataOutput);
	echo '{"success":1,"data":'.$dataOutput.'}';
	

}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	/* если text/html 
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>