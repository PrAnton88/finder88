<?php
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

if($resolution){
	
	require_once "../config.modx.php";
	/* get $modx */
	
	require_once "../config.smarty.php";
	/* get $smarty */
	
	$modx->smarty = $smarty;


	$listDevice = false;
	$dataBusy = false;
	$dateApply = false;
	$openRecord = false;
	if(isset($_POST['listDevices'])){
        $listDevices = html_entity_decode(htmlspecialchars($_POST['listDevices']));
    }
	if(isset($_POST['dataBusy'])){
        $dataBusy = html_entity_decode(htmlspecialchars($_POST['dataBusy']));
    }
	if(isset($_POST['dateApply'])){
        $dateApply = htmlspecialchars($_POST['dateApply']);
    }
	if(isset($_POST['openRecord'])){
        $openRecord = html_entity_decode(htmlspecialchars($_POST['openRecord']));
    }
	

	if($listDevices && $dataBusy && $dateApply){
		
		
		
		
		$listDevices = json_decode($listDevices,true);
		$dataBusy = json_decode($dataBusy,true);
		
		
		
		/* отсюда будем брать занятые интервалы для шагателя по времени */
		$stepsTimeBusy = $dataBusy;
		/* но если есть openRecord - то есть редактирование, то необходимо
			убрать из stepsTimeBusy интервал openRecord,
			что бы на интервал openRecord можно было пролистать время
			(пролистать снова на этот интервал при редактировани, если вышли за его пределы)
		*/
		
		$newItem = array();
		if($openRecord){
			
			$openRecord = json_decode($openRecord,true);
			
			/* интересует поле time */
			foreach($dataBusy as $itemBusy){
				if($itemBusy['time'] != $openRecord['time']){
					$newItem[] = $itemBusy['time'];
				}
			}
			
		}else{
			foreach($dataBusy as $itemBusy){
				$newItem[] = $itemBusy['time'];
			}
		}
		
		/* вот, этот интервал нельзя устанавливать */
		// print_r($newItem);
		
		
		$stepsTimeBusy = json_encode($newItem,1);
		/* формат ["16:00-18:00",....] */
		
		$smarty->assign("stepsTimeBusy",$stepsTimeBusy);
		
		
		if($openRecord){
			
			$checkDev = explode('; ',$openRecord["devices"]);
			
			foreach($listDevices as &$item){
				
				if(in_array($item['name'], $checkDev)){
					$item['check'] = 1;
				}else{
					$item['check'] = 0;
				}
			}
			
			$openRecord = json_encode($openRecord,1);
			
			//echo $openRecord;
			
			$smarty->assign("checkDev",$dev);
		}
		
		$smarty->assign("openRecord",$openRecord);
		
		$smarty->assign("listDevices",json_encode($listDevices));
		
		
		/* если есть периоды забронированные - возможность бронирования должна учесть */
/*
date: "2021-06-04"
devices: "Видеоконференц связь; "
fio: "Селин Евгений Александрович"
hidd: "0"
id: "405"
measure: "ВКС с ООО 'Ильский НПЗ'"
note: ""
time: "10:00-11:00"
userId: "108"
*/
		
		
		
		/* добавить информацию о месте положения сотрудника */
		foreach($dataBusy as &$record){
			
			$whereAnd = " u.id=".$record["userId"];
			$result = getUserData($db,$uid,$whereAnd);
		
			if((is_string($result)) && (strpos($result,"error") !== false)){
				header("HTTP/1.1 500 SQL Error: $result");
				break;
			}else{
				/*
					[dept]
					[nroom]
					[int_phone]
					[ext_phone]
				*/
				
				$record["tooltip"] = $result["dept"]."<br /> Комната: ".$result["nroom"]."<br /> внутренний телефон ".$result["int_phone"]."<br /> внешний телефон ".$result["ext_phone"];
				
			}
		}
		
		/* $user .uid (as userId) id. (as role) */
		
		/* .userId */
		// print_r($user);
		
		
		$smarty->assign("dataBusy",$dataBusy);
		
		/* админ ли - для того что бы разрешать тот или иной доступ */
		$smarty->assign("admin",($user['priority'] == 4));
		$smarty->assign("user",array('id'=>$user['id'],'uid'=>$user['uid']));
		
		
		$smarty->assign("dateApply",$dateApply);
		
		
		return $smarty->display('formBronConversation.tpl');
		
		
	}else{
		$description = "";
		if(!$listDevices){
			$description = "smarty.getFormBronConversation.php: listDevice is not found";
		}elseif(!$dataBusy){
			$description = "smarty.getFormBronConversation.php: dataBusy is not found";
		}
		
		$smarty->assign("message",$description);
		return $smarty->display('error.formDataIsNotFound.tpl');
	}
	
	
}else{
	header("HTTP/1.1 403 Forbidden");
}
?>