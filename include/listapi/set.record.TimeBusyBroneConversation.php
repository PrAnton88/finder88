<?php

header('Content-type:application/json');
// header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";


	/* параметр который заполняется на true после вычислений из функции checkBusyTime */
	$editRecord = array();/* не отдаем пользователю его заполнять */

function checkBusyTime($listBusyRecord,$checkDataRecord,$timeNew,$lastConcurrenceUserId=0){
	
	global $user;
	global $editRecord;
	
	$busyInterval = false;
	
	$timeBusy = null;
	foreach($listBusyRecord as $itemOwner){
		
		if($itemOwner['date'] != $checkDataRecord['date']){ continue; }
			
			
		if($itemOwner['date'] == $checkDataRecord['date']){
			
			/* блин. как узнать занято ли время, если оно может быть записано в виде интервалов */
			$timeBusy = explode('-',$itemOwner['time']);
			
			
			if($timeNew[0] < $timeBusy[0]){
				/* то можно сразу сравнить окончания, и 
					если окончания тоже меньше, то break;
				*/
				
				if($timeNew[1] < $timeBusy[1]){
					
					/* конец нового интервала больше начала старого ? */
					if($timeNew[1] <= $timeBusy[0]){
						
						continue;
					}else{
						/* да, больше или равно началу старого */
						if(($lastConcurrenceUserId != 0) && ($itemOwner["userId"] != $lastConcurrenceUserId)){
							/* еслиредактируется запись бронирующего НЕ такая же как и в прошлом вызове */
							/* и сейчас попалась запись, с другим бронирующим но на этот же промежуток, что незаконно */
							return $itemOwner;
						}elseif($lastConcurrenceUserId == 0){
							$busyInterval = $itemOwner; break;
						}else{
							/* в этом случае запись бронирующего такая же как и в прошлом вызове */
							/* что уже было разрешено, и отправлено на дальнейшую проверку, поэтому */
							continue;
						}
					}
					
					
					
					
				}elseif($timeNew[1] >= $timeBusy[1]){
					
					/* иначе
						если окончание вдруг больше
						то пытаемся занять занятый интервал
						тогда return false
					*/
					if(($lastConcurrenceUserId != 0) && ($itemOwner["userId"] != $lastConcurrenceUserId)){
						/* еслиредактируется запись бронирующего НЕ такая же как и в прошлом вызове */
						/* и сейчас попалась запись, с другим бронирующим но на этот же промежуток, что незаконно */
						return $itemOwner;
					}elseif($lastConcurrenceUserId == 0){
						$busyInterval = $itemOwner; break;
					}else{
						continue;
					}
					
				}
				
				
				
			}
			
			if($timeNew[0] > $timeBusy[0]){
				/* то можно сразу сравнить окончания, и 
					если окончания тоже больше, то continue;
				*/
				
				if($timeNew[1] > $timeBusy[1]){
					
					
					if($timeNew[0] < $timeBusy[1]){
						if(($lastConcurrenceUserId != 0) && ($itemOwner["userId"] != $lastConcurrenceUserId)){
							/* еслиредактируется запись бронирующего НЕ такая же как и в прошлом вызове */
							/* и сейчас попалась запись, с другим бронирующим но на этот же промежуток, что незаконно */
							return $itemOwner;
						}elseif($lastConcurrenceUserId == 0){
							$busyInterval = $itemOwner; break;
						}else{
							continue;
						}
						
					}else{
						
						continue;
					}
					
					
					
				}elseif($timeNew[1] <= $timeBusy[1]){
					
					if(($lastConcurrenceUserId != 0) && ($itemOwner["userId"] != $lastConcurrenceUserId)){
						/* еслиредактируется запись бронирующего НЕ такая же как и в прошлом вызове */
						/* и сейчас попалась запись, с другим бронирующим но на этот же промежуток, что незаконно */
						return $itemOwner;
					}elseif($lastConcurrenceUserId == 0){
						$busyInterval = $itemOwner; break;
					}else{
						continue;
					}
				}
				
			}
			
			
			if($timeNew[0] == $timeBusy[0]){
				/* если начала равны */
				
				if(($lastConcurrenceUserId != 0) && ($itemOwner["userId"] != $lastConcurrenceUserId)){
					/* еслиредактируется запись бронирующего НЕ такая же как и в прошлом вызове */
					/* и сейчас попалась запись, с другим бронирующим но на этот же промежуток, что незаконно */
					return $itemOwner;
				}elseif($lastConcurrenceUserId == 0){
					$busyInterval = $itemOwner; break;
				}else{
					continue;
				}
			}
			
			
			
		}
	}
	
	
	if($busyInterval !== false){
		/* данные на занятом участке интервала (но это допустимо при редактировании) */
		
		/* если в изменяемой записи userId такой же, как и у авторизованного (или авторизованный админ) - то позволять, 
		ибо редактирование */
		/* ['time'] */
		if( ($user['uid'] == $busyInterval["userId"]) || ($user['priority'] == 3)){
		
			/* что бы узнать не занял ли интервал ещё какой нибудь промежуток */
			/* нужно извлечь из $listBusyRecord запись о $busyInterval */
			
		
		
			$resultBasyTakeNewData = array();
			foreach($listBusyRecord as $itemOwner){
				if($itemOwner["id"] != $busyInterval["id"]){
					$resultBasyTakeNewData[] = $itemOwner;
				}
			}
			
			/* имеем право на редактирование текущей записи */
			$editRecord = $busyInterval;
			
			/* провести проверку заново, на занятость на участках для других пользователей */
			
			return checkBusyTime($resultBasyTakeNewData,$checkDataRecord,$timeNew,$busyInterval["userId"]);
			
			
			
		}else{
			/* userId у авторизованного другой, и авторизованный не админ */
			/* throw new ErrorException("Интервал уже занят "); */
		}
		
		
		
	}
	
	return $busyInterval;
}


if($resolution){
	
	try{
		$today = date("Y")."-".date("m")."-".date("d");
		$valDeleteInterval = (string) date('Y-m-d');
				
		$d = (date("n"));
				
		if(date("m")<=2){//для того что бы всегда выводились только последние 3 месяца
			$query = "SELECT * FROM conversationcompleted WHERE hidd<>1 AND date>='$valDeleteInterval' AND (date LIKE '".(date("Y")-1)."-11%' OR date LIKE '".(date("Y")-1)."-12%' OR date LIKE '".(date("Y"))."%') ORDER BY date, time";
		}else{
			$query = "SELECT * FROM conversationcompleted WHERE hidd<>1 AND date>='$valDeleteInterval' AND (date LIKE '".(date("Y"))."-".(date("m"))."%' OR date LIKE '".(date("Y"))."-".(($d<9)?"0":"").($d+1)."%' OR date LIKE '".(date("Y"))."-".(($d<8)?"0":"").($d+2)."%' OR date LIKE '".(date("Y")+1)."-01%' OR date LIKE '".(date("Y")+1)."-02%') ORDER BY date, time";
		}
			
		$result=$db->fetchAssoc($query,$uid);
		
		if((is_string($result)) && (strpos($result,"error") !== false)){
			header("HTTP/1.1 500 SQL Error: $result");
		}else{
			
			
			$dataRecord = false;
			require_once "../headerBase.php";
			
			//$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
			//$dataRecord = json_decode($dataRecord,true);
				
				
				/* необходимо узнать не пересекается ли запись с датой и временем из уже имеющихся записей */
				
				
				if(!isset($dataRecord['time'])){
					$timeNew = array($dataRecord['startTime'],$dataRecord['endTime']);
					
				}else{
					$timeNew = (explode('-',$dataRecord['time']));
				}
				
				
				
				if($timeNew[1] <= $timeNew[0]){
					/* окончание должно быть больше начала */
					throw new ErrorException("Неверно установлен интервал ");
				}
				
				/* см test.check.data.Time.php */
				$busyInterval = false;
				if(count($result)>0){
					$busyInterval = checkBusyTime($result,$dataRecord,$timeNew);
				}
				$description = "";
				$dataOutput = array();
				
				if($busyInterval !== false){
					/* интервал занят и занят либо другим полльзователем либо  */
					throw new ErrorException("Интервал уже занят ");
				}else{
					
					/* выбираем устройства из БД по переданным идентификаторам */
					$getImport = true; /* говорит о том что бы не выводилась информация внутри подключаемого файла */
					/* заголовок header type должен быть таким же */
					require_once('get.data.listDevicesConversation.php');
					
					
					// print_r($dataRecord);
					$Devices = $dataRecord['listDevices'];
					
					$listDevices = array();
					//print_r($listDevicesConversation);
					
					foreach($listDevicesConversation as $item){
						// if($item['id'] == )
						if(in_array($item['id'],$Devices)){
							
							$listDevices[] = $item['name'];
						}
					}
					
					$dataRecord['listDevices'] = implode("; ", $listDevices);
					
					/* .id .userId .date .time .devices .note .measure .hidd .fio */
					
					
					
					$description = "Интервал НЕ занят другими.";
					if(count($editRecord)>0){
						/* Интервал НЕ занят другими. Редактируем. Редактируем смело */
						/* Так как узнали о редактировании не от пользователя - вопроса доверия нет */
						/* редактируем чеерз update */
						$description .= " Вычисляемо редактируем запись ".$editRecord["id"];
						
						$time = $timeNew[0].'-'.$timeNew[1];
						
$query = "UPDATE conversationcompleted set time='$time', note='".str_replace('\'','"',$dataRecord['note'])."', measure='".str_replace('\'','"',$dataRecord['measure'])."', date='".$dataRecord['date']."', devices='".$dataRecord['listDevices']."' WHERE id=".$editRecord["id"];
						
						//$description = $query;
						$result = $db->query($query, $uid);
						if( is_string($result) && (strpos($result,"error") !== false)){
							$description .= $result;
							throw new ErrorException($description);
						}else{
							$description = 'Запись обновлена';
						}
						
					}else{
						
						/* запись возможно была открыта на редактирование, но, время сместили так, 
						что оно полностью не пересекается с предыдушей (с открытой на редактирование) записью */
						/* то мы могли оказаться здесь */
						
						/* и только теперь сориентируемся на параметр - редактирование или нет
							flagEditRecordId
						*/
						if((isset($_POST['flagEditRecordId'])) && ($_POST['flagEditRecordId'] != 'false')){
							
							/* идентификатор редактируемой записи */
							$editRecordId = (int) htmlspecialchars($_POST['flagEditRecordId']);
							/* в этом случае, мы приняли от пользователя этот параметр, а значит он может быть подделан */
							/* поэтмоу от прежней записи не избавляемся, а обозначаем её как hidden=1 */
							/* и создаем новую запись */
							$description .= " Пользовательский параметр на редактирование id= $editRecordId. ";
							
							/* 1. из записи $editRecordId */
							/* необходимо вытащить значения в $dataOutput["id"] $dataOutput["fio"] $dataOutput["userId"]*/
							
							$query = "SELECT id, fio, userId, devices, nrequest FROM conversationcompleted WHERE id=$editRecordId";
							$editRecord = $db->fetchFirst($query, $uid);
							if( is_string($editRecord) && (strpos($editRecord,"error") !== false)){
								$description .= $editRecord;
								throw new ErrorException($description);
							}else{
								$description .= ' fio, userId получены. ';
								
								/* 2. имеет ли пользователь право на изменени записи */
								if( ($user['uid'] == $editRecord["userId"]) || ($user['priority'] == 3)){
									
									/* 3. у записи $editRecordId */
									/* необходимо обновить hidd = 1 */
								
									
									$query = "UPDATE conversationcompleted set hidd=1 WHERE id=$editRecordId";
							
									//$description = $query;
									$resultUpd = $db->query($query, $uid);
									if( is_string($resultUpd) && (strpos($resultUpd,"error") !== false)){
										$description .= $resultUpd;
										throw new ErrorException($description);
									}else{
										$description .= ' Поле hidd обновлено. ';
									}
								
									/* 4. создать новую запись */
									
									
									/*if($dataRecord["listDevices"] != $dataRecord["devices"]){ 
										// нужны строго отсортированные данные 
										$description .= ' Переданые устройства '.$dataRecord["devices"].' не соответствуют устройствам базы данных '.$dataRecord["listDevices"];
										throw new ErrorException($description); 
									}else{*/
										/* пригодится потом посмотреть */
										$devices = $dataRecord["listDevices"];
										unset($dataRecord["listDevices"]);
										
									/*}*/
									
									
									$dataRecord["fio"] = $editRecord["fio"];
									$dataRecord["userId"] = $editRecord["userId"];
									$dataRecord["nrequest"] = $editRecord["nrequest"];
									
									
									$result = $db->insert('conversationcompleted', array(), $dataRecord, $uid);
									/* в result - идентификатор записи */
									
									if( is_string($result) && (strpos($result,"error") !== false)){
										$description .= $result;
										throw new ErrorException($description);
									}else{	/* ' Новая запись вставлена вместо прежней. ' */
										
										$description = ' Запись обновлена ';
										
										$editRecord["id"] = $result;
										
										$dataRecord["listDevices"] = $devices;
									}
									
									
									
								}else{
									
									throw new ErrorException($description." Пользователь не имеет право на изменение записи .");
								}
							}
							
							
						}else{
							$description .= " Создаем запись";
							
							
							$dataRecord["fio"] = $user["fio"];
							$dataRecord["userId"] = $user["uid"];
							
							unset($dataRecord["listDevices"]);
							
							$result = $db->insert('conversationcompleted', array(), $dataRecord, $uid);
							/* в result - идентификатор записи */
							
							if( is_string($result) && (strpos($result,"error") !== false)){
								$description .= $result;
								throw new ErrorException($description);
							}else{	/* ' Новая запись вставлена вместо прежней. ' */
								
								$description = ' Запись создана ';
								
								$dataRecord["id"] = $result;
								
							}
							
							/* необходимо создать запись в техподдержке */
							
							
							
							/* оповестить подписанных админов */
							
							
							
						}
						
					}
					
					
					if(count($editRecord)>0){
						
						$dataOutput["id"] = $editRecord["id"];
						$dataOutput["fio"] = $editRecord["fio"];
						$dataOutput["userId"] = $editRecord["userId"];
						
						/* что бы видеть разницу в устройствах при редактировании */
						$dataOutput["devices"] = $editRecord["devices"];
						/* новые устройства в $dataRecord, но они нам дальше на выходе не будут нужны */
						
						
						if(isset($editRecord["nrequest"])){
							$dataOutput["nrequest"] = $editRecord["nrequest"];
						}
						
					}else{
						/* когда запись только создали, в ней не будет nrequest так как заявка ещё не была создана */
						$dataOutput = $dataRecord;
					}
					
					
				}
				
				$dataOutput = json_encode($dataOutput);
				
				
				
				
				$display = '{"success":1,"description":"'.$description.'","data":'.$dataOutput.'}';
				echo $display;
			
				header("HTTP/1.1 200 Ok");
			
			
		}

	}catch(ErrorException $ex){
		
		echo '{"success":0,"description":"'.exc_handler($ex).'"}';
		
		/* если text/html 
		echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
		*/
	}


}else{ 

	header("HTTP/1.1 403 Forbidden");
}
?>