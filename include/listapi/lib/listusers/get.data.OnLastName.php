<?php
header('Content-type:application/json');
/* но обрабатываться будет как json - поэтому весь вывод как json */

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";

try{

	/* useExam 
	
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
	$tmp = null;
	$isEmptyData = false;
	
	
	$forAppend = false;
	if(isset($dataRecord["listdepts"])){
		
		$listcomps = $dataRecord["listdepts"];
		$query .= " AND (";
		
		foreach($listcomps as $item){
			$tmp = (int) $item;
			if($tmp > 0){
				
				if($forAppend){
					$query .= " OR ";
				}
				
				$query .= "p.dept=".$tmp;
				$selectAnyDept = true;
				
				$forAppend = true;
			}
			
		}
		
		$query .= " )";
	}
		
	
	if((!$forAppend) && isset($dataRecord["ndept"])){
		$tmp = (int) $dataRecord["ndept"];
		if($tmp > 0){
			$query .= " AND p.dept=".$tmp;
		}elseif($tmp < 0){
			$isEmptyData = true;
		}
	}
		
	if(isset($dataRecord["sfio"])){ /* поиск по фамилии */
		$tmp = $dataRecord["sfio"];
		$tmp = mb_strtolower(trim($tmp));
		
		$query .= " AND (LOWER(u.last_name) LIKE '%".$tmp."%') ";
	}
		
		
	if($isEmptyData){
		
		$listUsers = array('id'=>'-1','deptname'=>'Нет данных');
		
		echo '{"success":1,"data":{"listusers":'.json_encode($listUsers).'}}';
		
	}else{
		
		$query .= " ORDER BY u.last_name ASC";
		// echo $query;
		
		/* 1. Список сотрудников */
		
		$listUsers=$db->fetchAssoc($query,$uid);
		if(is_string($listUsers) && (strpos($listUsers,'error')!== false)){
			throw new ErrorException('SQL Error');
		}
		
		echo '{"success":1,"data":{"listusers":'.json_encode($listUsers).'}}';
		
	}
	
	
}catch(ErrorException $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>