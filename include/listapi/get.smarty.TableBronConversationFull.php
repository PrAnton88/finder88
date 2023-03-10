<?php
header('Content-type:text/html;');

try{

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

	if($resolution){
		
		$nLuminate = false;
		if(isset($_POST['nLuminate'])){
			
			$nLuminate = (int)$_POST['nLuminate'];
			if($nLuminate == 0){ $nLuminate = false; }
			
		}
		
		require_once "../config.modx.php";
		/* get $modx */
		
		require_once "../config.smarty.php";
		/* get $smarty */
		
		$modx->smarty = $smarty;
		
		
		$today = date("Y")."-".date("m")."-".date("d");
					
		$valDeleteInterval = (string) date('Y-m-d');
				
		$d = (date("n"));
				
		if(date("m")<=2){//для того что бы всегда выводились только последние 3 месяца
			$query = "SELECT * FROM conversationcompleted WHERE hidd<>1 AND date>='$valDeleteInterval' AND (date LIKE '".(date("Y")-1)."-11%' OR date LIKE '".(date("Y")-1)."-12%' OR date LIKE '".(date("Y"))."%') ORDER BY date, time";
		}else{
			$query = "SELECT * FROM conversationcompleted WHERE hidd<>1 AND date>='$valDeleteInterval' AND (date LIKE '".(date("Y"))."-".(date("m"))."%' OR date LIKE '".(date("Y"))."-".(($d<9)?"0":"").($d+1)."%' OR date LIKE '".(date("Y"))."-".(($d<8)?"0":"").($d+2)."%' OR date LIKE '".(date("Y")+1)."-01%' OR date LIKE '".(date("Y")+1)."-02%') ORDER BY date, time";
		}
			
		$resultConversation=$db->fetchAssoc($query,$uid);
			
			
		/*
			Array ( [id] => 402 [userId] => 2073 [date] => 2021-05-29 [time] => 15:00-16:00 [devices] => Видеоконференц связь; [note] => ВКС на платформе webinar.ru [measure] => ВКС с участием А.П. Шевцова [hidd] => 0 [fio] => Петросов Эдуард Николаевич )
		*/

				// $resultConversation = json_decode($resultConversation,true);
				
				
		/* так как fio записано в таблице conversationcompleted.fio
			а нужно иногда только fi то возьмём его винзу из getUserData,
			тем более что к нему все равно обращаемся что бы взять информацию для 
			вмплывающей подсказки
		*/
				
		try{
			
			$tmp = null;
			foreach($resultConversation as &$record){
				
				$whereAnd = " u.id=".$record["userId"];
				$result = getUserData($db,$uid,$whereAnd);
				
				
				$tmp = explode("-", $record['time']);
				$record['time'] = $tmp[0]." - ".$tmp[1];
				
				
				
				if((is_string($result)) && (strpos($result,"error") !== false)){
					header("HTTP/1.1 500 SQL Error: $result");
					break;
				}elseif(is_array($result)){
					/*
						[dept]
						[nroom]
						[int_phone]
						[ext_phone]
					*/
					$record["fi"] = $result["fi"];
					
					$record["tooltip"] = $result["dept"].
					"<br /> Комната: ".$result["nroom"].
					"<br /> внутренний телефон ".
					$result["int_phone"].
					"<br /> внешний телефон ".
					$result["ext_phone"];
					
				}else{
					throw new ErrorException("Данные о местоположении ".$record["fio"]." не найдены ");
				}
			}
			

			$smarty->assign("admin",($user['priority'] == 3));
			$smarty->assign("user",array('id'=>$user['id'],'uid'=>$user['uid']));
			
			/*
			print_r($resultConversation);
			if(is_string($resultConversation)){
				echo "str = ".$resultConversation;
			}
			*/
			
			
			
			/* нужно взять информацию о правах авторизованного пользователя */
			// require_once "lib/query/get.query.UserLaws.php";
			$queryLawsThisUser = getQueryToGetUserLaws("l.adminConversation,l.dispatchConversation");
			$queryLawsThisUser .= " WHERE u.role = ".$uid;

			$dataLaw = $db->fetchFirst($queryLawsThisUser,$uid);
			if(is_string($dataLaw) && (strpos($dataLaw,'error')!== false)){
				throw new ErrorException('SQL Error ');
			}
			
			/* $smarty->assign("dataLaw",json_encode($dataLaw)); */
			$smarty->assign("dataLaw",$dataLaw);
			
			
			
			
			
			$smarty->assign("nLuminate",$nLuminate);
			$smarty->assign("messages",$resultConversation);

			// print_r($smarty);
			return $smarty->display('tableBroneConversation.tpl');
		}catch(ErrorException $ex){
			
			print "error";
			$smarty->assign("message",exc_handler($ex));
			return $smarty->display('error.formDataIsNotFound.tpl');
			
		}
		
		
	}else{
		header("HTTP/1.1 403 Forbidden");
	}

}catch(ErrorException $ex){
	$description = exc_handler_report($ex);
	print $description;
}
?>