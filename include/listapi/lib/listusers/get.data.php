<?php
header('Content-type:application/json');
/* но обрабатываться будет как json - поэтому весь вывод как json */

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";

try{

	/* useExam 
	
		let dataRecord = null;
		dataRecord = {datasearch:"Прудников"}; 
		dataRecord = {datasearch:"иков"};
	
		dataRecord = {datasearch:"иков",ncomp:5};
	
		dataReqNext({file:'listapi/lib/listusers/get.data.php',type:'json',
			args:'dataRecord='+JSON.stringify(dataRecord)},
			function(responseJson){
				console.log(responseJson);
				
			}
		);


		dataReqNext({file:'listapi/lib/listusers/get.data.php',type:'json',
			args:'dataRecord='+JSON.stringify({datasearch:"иков",ncomp:5})},
			function(responseJson){
				console.log(responseJson);
				
			}
		);
		
		
		dataReqNext({file:'listapi/lib/listusers/get.data.php',type:'json',
			args:'dataRecord='+JSON.stringify({ndept:5})},
			function(responseJson){
				console.log(responseJson);
				
			}
		);
		

	*/

	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}


	$tmp = false;
	// $filter = '';

	$dataRecord = false;
	if(isset($_POST['dataRecord'])){
		$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
		$dataRecord = json_decode($dataRecord,true);
	}elseif(isset($_GET['dataRecord'])){
		$dataRecord = html_entity_decode(htmlspecialchars($_GET['dataRecord']));
		$dataRecord = json_decode($dataRecord,true);
	}
	
	
	$query = $queryUserData;
	
	if(isset($dataRecord["ndept"])){
		$tmp = (int) $dataRecord["ndept"];
		$query .= " AND p.dept=".$tmp;
	}
	
	if(isset($dataRecord["ncomp"])){
		$tmp = (int) $dataRecord["ncomp"];
		$query .= " AND p.comp=".$tmp;
	}
	
	
	if(isset($dataRecord["ncab"])){
		$tmp = (int) $dataRecord["ncab"];
		$query .= " AND k.name = '%".$tmp."%'";
	}
	
	if(isset($dataRecord["sfio"])){ /* поиск по фамилии */
		$tmp = $dataRecord["sfio"];
		$tmp = mb_strtolower(trim($tmp));
		
		if(strpos($tmp," ") === false){
		
			$query .= " AND ((LOWER(u.last_name) LIKE '%".$tmp."%') OR (LOWER(u.first_name) LIKE '%".$tmp."%') OR (LOWER(u.patronymic) LIKE '%".$tmp."%')) ";
		}else{
			$tmp = explode(" ", $tmp);
			
			/* немного режим комбинаторики */
			if(count($tmp) == 2){
				
				$query .= " AND (((LOWER(u.last_name) LIKE '%".$tmp[0]."%') AND (LOWER(u.first_name) LIKE '%".$tmp[1]."%')) ";
				$query .= " OR ((LOWER(u.last_name) LIKE '%".$tmp[1]."%') AND (LOWER(u.first_name) LIKE '%".$tmp[0]."%')) ";
				$query .= " OR ((LOWER(u.first_name) LIKE '%".$tmp[0]."%') AND (LOWER(u.patronymic) LIKE '%".$tmp[1]."%')) ";
				$query .= " OR ((LOWER(u.first_name) LIKE '%".$tmp[1]."%') AND (LOWER(u.patronymic) LIKE '%".$tmp[0]."%')) ";
				$query .= " OR ((LOWER(u.last_name) LIKE '%".$tmp[1]."%') AND (LOWER(u.patronymic) LIKE '%".$tmp[0]."%')) ";
				$query .= " OR ((LOWER(u.last_name) LIKE '%".$tmp[1]."%') AND (LOWER(u.patronymic) LIKE '%".$tmp[0]."%')) )";
				
			}else{
				/* тогда 3 или больлше, но если больше просто отринем лишнее */
				
				$query .= " AND (((LOWER(u.last_name) LIKE '%".$tmp[0]."%') AND (LOWER(u.first_name) LIKE '%".$tmp[1]."%') AND (LOWER(u.patronymic) LIKE '%".$tmp[2]."%')) ";
				$query .= " OR ((LOWER(u.last_name) LIKE '%".$tmp[0]."%') AND (LOWER(u.first_name) LIKE '%".$tmp[2]."%') AND (LOWER(u.patronymic) LIKE '%".$tmp[1]."%')) ";
				$query .= " OR ((LOWER(u.last_name) LIKE '%".$tmp[1]."%') AND (LOWER(u.first_name) LIKE '%".$tmp[0]."%') AND (LOWER(u.patronymic) LIKE '%".$tmp[2]."%')) ";
				$query .= " OR ((LOWER(u.last_name) LIKE '%".$tmp[2]."%') AND (LOWER(u.first_name) LIKE '%".$tmp[1]."%') AND (LOWER(u.patronymic) LIKE '%".$tmp[0]."%')) ";
				$query .= " OR ((LOWER(u.last_name) LIKE '%".$tmp[2]."%') AND (LOWER(u.first_name) LIKE '%".$tmp[0]."%') AND (LOWER(u.patronymic) LIKE '%".$tmp[1]."%')) )";
			}
		}
	}
	
	
	if(isset($dataRecord["sintphone"])){
		$tmp = $dataRecord["sintphone"];
		$tmp = mb_strtolower(trim($tmp));
		$query .= " AND u.int_phone LIKE '%".$tmp."%'";
	}
	
	if(isset($dataRecord["sextphone"])){
		$tmp = $dataRecord["sextphone"];
		$tmp = mb_strtolower(trim($tmp));
		$query .= " AND u.ext_phone LIKE '%".$tmp."%'";
	}
	
	if(isset($dataRecord["slogin"])){
		$tmp = $dataRecord["slogin"];
		$tmp = mb_strtolower(trim($tmp));
		$query .= " AND r.login LIKE '%".$tmp."%'";
	}
	
	if(isset($dataRecord["semail"])){
		$tmp = $dataRecord["semail"];
		$tmp = mb_strtolower(trim($tmp));
		$query .= " AND r.email LIKE '%".$tmp."%'";
	}
	
	$query .= " ORDER BY u.last_name ";
	
	
	
	$page = 0;
	if(isset($dataRecord["page"])){
		$page = (int) $dataRecord["page"];
	}
	
	
	$limit = false;
	if(isset($dataRecord["limit"])){
		$limit = (int) $dataRecord["limit"];
		
	}elseif($page > 0){
		$limit = $user["uop"];
	}
	
	
	
	if($limit != false){
		$p = (int) $page;
		$p *= $limit;
		if($p > 0){
			$p -= $limit;
		}
		
		/* получить общее количество */
		$totalcount = $db->fetchAssoc($query,true);
		if((is_string($totalcount)) && (strpos($totalcount,"error") !== false)){
			throw new Error('SQL Error');
		} 
		$totalcount = count($totalcount);
		$query .= " LIMIT $p,$limit ";
	}
	
	/* 1. Список сотрудников */
	$listUsers=$db->fetchAssoc($query,$uid);
	if(is_string($listUsers) && (strpos($listUsers,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	// echo '{"success":1,"data":'.json_encode($listUsers).',"query":"'.$query.'"}';
	// echo '{"success":1,"data":{"listusers":'.json_encode($listUsers).',"filter":'.$filter.'}}';
	echo '{"success":1,"data":{"listusers":'.json_encode($listUsers).(($limit != false)?',"totalcount":'.$totalcount:'').'}}';
	
	
}catch(ErrorException $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>