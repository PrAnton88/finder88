<?php

header('Content-type:application/json');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Expose-Headers:Connection,Content-Length,Date,Expires,Keep-Alive');

	$getUserAuthData = true;
	$sessAccesser = true;
	
try{
	
/* как из set.record.helpdesk.php */
class SpliterTime{
	var $uTime = false;
	var $date = false;
	var $time = false;
	var $list = false;
	/* "Y-m-d H:i:s" */
	function SpliterTime($t){
		
		if(strpos($t," ") !== false){
			
			$t = explode(" ",$t);
			$this->date = explode("-",$t[0]);
			$this->time = explode(":",$t[1]);
		}else{
			$this->date = explode("-",$t);
			$this->time = explode(":",'00:00:00');
		}
		$this->uTime = Array ("date" => $this->date, "time" => $this->time); 
	}
	function split(){
		$this->list = Array("Y"=>$this->date[0],"m"=>$this->date[1],"d"=>$this->date[2],"H"=>$this->time[0],"i"=>$this->time[1],"s"=>$this->time[2]);
	}
	function get($par = false){
		if(!$this->list) $this->split();
		if($par && $this->list[$par]) return $this->list[$par];
		// return as array(Y,m,d,H,i,s)
		return $this->list;
	}
	/*
	function tomktime(){
		// return as array(h,i,s,m,d,y)
		return Array("H"=>$this->time[0],"i"=>$this->time[1],"s"=>$this->time[2],"m"=>$this->date[1],"d"=>$this->date[2],"Y"=>$this->date[0]);
	}*/
	function mktime(){
							/* так как принимает только в формате (h,i,s,m,d,y) */
		return mktime(/*"H"=>*/$this->time[0],/*"i"=>*/$this->time[1],/*"s"=>*/$this->time[2],
					   /*"m"=>*/$this->date[1],/*"d"=>*/$this->date[2],/*"Y"=>*/$this->date[0]);
	}
}
	
	
	
	
	
	
	/* require_once "../../start.php"; */
	
	require_once "../../error.reporting.php";
	require_once("../../../config/connDB.php");/* get $db */
	
	$user = array("uid"=>811,'priority'=>3);
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

	if($nRecord === false){
		/* новая заявка */
		
		/* необходимо проверить этот период времени, этого пользователя,
		и не заняты ли эти устройства на этот же период времени */
	
	
		/* в заявках поле .applicant это users.id, а в $userid хранится users.role */
		/* 
		$query = "SELECT * FROM request WHERE header='".$title."' AND message='".$body."' AND (state is null OR state=0 OR state=1) AND applicant=$id";
		$existmess = $db->fetchFirst($query);
		
		if((is_string($existmess)) && (strpos($existmess,"error") !== false)){
			throw new ErrorException("500 SQL Error: $existmess");
		}
		
		$dtime = false;
		if($existmess){
			
			//Вид 2021-06-08 16:39:21
			$dtime = $existmess['opened'];
		
			// конвертировать в формат, пригодный для конветрации в юникс формат, а именно в h,i,s,m,d,y
			$dtime = new SpliterTime($dtime);
			
			
		}
		
		// если не дубирует созданные
		if (!(($existmess = $db->fetchFirst($query)) and (($dtime !== false) && (($dtime->mktime()) > (time() - 600)) ))){
			
			$date = date("Y-m-d");
			
			$respInsert = $db->insert("request", array('user_link'=>'NULL', 'applicant'=>$id, 'state'=>0, 'priority'=>0, 'opened'=>"'$date'", 'user_apdate'=>$id), array('header'=>$title, 'message'=>$body));
			
			if(!$respInsert){
				$description .= " По невыясненной причине заявка от пользователя $id - $title - $body, не создана. ";
				
				
			}else{
				$description = "Заявка создана";
				
				$dataOutput["id"] = $respInsert;
				$dataOutput["new"] = true;
			}
			
			
		}else{
			
			$description = 'Ранее вы уже создали точно эту же заявку. См заявку '.$existmess['id'];
			
			$dataOutput["id"] = $existmess['id'];
			$dataOutput["new"] = false;
		}
		*/
		
		
		$dataOutput["id"] = $existmess['id'];
		$dataOutput["new"] = false;
		
		
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

		$listdevice = 'Запрашиваемые устройства  -- ';
		$checkedDevicesTypes = $dataRecord['checkedDevicesTypes'];
		
		$checkedDevices = array();
		
		// print_r($checkedDevicesTypes);
		foreach($checkedDevicesTypes as $itemType){
			/* $itemType [id, name] */
			$listdevice .= $itemType['name'].'  -  ';
			foreach($itemType['checkedDevices'] as $itemDevices){
				/* $itemDevices [name, count] */
				$listdevice .= $itemDevices['name'].'  -->'.$itemDevices['count'].' шт;';
				$checkedDevices[] = $itemDevices;
				
			}
		}
		
		
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
		
		
		
		
		
		
		$query = "SELECT * FROM bronedevicedate WHERE idtick=$nRecord";
		$existRecords = $db->fetchAssoc($query);
		if( is_string($existRecords) && (strpos($existRecords,"error") !== false)){
			throw new ErrorException("SQL Error. $existRecords");
			exit;
		}
		/* $existRecords[iddevice,datest,dateend,idtick,free] */
		
		/* рассмотрим совпадение с устройствами из $checkedDevices[] */
		$much = false;


		/*
		$datest = new SpliterTime($dataRecord['datest']);
		$datest = date("Y-m-d", $datest->mktime());
		$dateend = new SpliterTime($dataRecord['dateend']);
		$dateend = date("Y-m-d", $dateend->mktime());
		*/
		
		$datest = $dataRecord['datest'];
		$dateend = $dataRecord['dateend'];
		
		
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
		$dataOutput["update"] = true;
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
}
?>