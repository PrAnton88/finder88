<?php
header('Content-type:application/json;');

	$getUserAuthData = true;
	$sessAccesser = false;
	require_once "../start.php";
	
	require_once "../preDataQuery.php";

try{
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	
	$comp = false;
	if(isset($_POST["comp"])){
		$comp = (int) htmlspecialchars($_POST["comp"]);
		if($comp <= 0){ $comp = false; }
	}
	
	$query = $deptsPreQuery." WHERE p.hidden=0 AND d.hidden=0 ";
	
	$dataRecord = false;
	if(isset($_POST['dataRecord'])){
		$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
		$dataRecord = json_decode($dataRecord,true);
	}
	
	
	$selectAnyDept = false;
	$tmp = false;
	$isEmptyData = false;
	$ncomp = false;
	
	if($comp !== false){
		$query .= " AND p.comp=".$comp;
	}else{
		
		$forAppend = false;
		if(isset($dataRecord["listcomps"])){
			
			$listcomps = $dataRecord["listcomps"];
			foreach($listcomps as $item){
				$tmp = (int) $item;
				if($tmp > 0){
					
					if(!$forAppend){
						$query .= " AND ";
					}else{
						$query .= " OR ";
					}
					
					$query .= "p.comp=".$tmp;
					$selectAnyDept = true;
					
					$forAppend = true;
				}
				
			}
		}
		
		if((!$forAppend) && isset($dataRecord["ncomp"])){
			$tmp = (int) $dataRecord["ncomp"];
			if($tmp > 0){
				$query .= " AND p.comp=".$tmp;
				$selectAnyDept = true;
				$ncomp = $tmp;
				
			}elseif($tmp < 0){
				$isEmptyData = true;
			}
		}
		
		if(isset($dataRecord["sdept"])){
			$tmp = $dataRecord["sdept"];
			$tmp = mb_strtolower(trim($tmp));
			if($tmp != ''){
				$query .= " AND (LOWER(d.name) LIKE '%".$tmp."%') ";
				$selectAnyDept = true;
			}
		}
	}
	if(($tmp === 0) || ($tmp == '')){ 
		$selectAnyDept = false;
	}
	
	if($isEmptyData){
		// $result = array('id'=>'-1','deptname'=>'Нет данных');
		$result = array(array('id'=>'0','deptname'=>'Все отделы'));
	}else{

		$query .= " GROUP BY d.name ORDER BY d.name ASC";
		// echo $query;
		
		$result = $db->fetchAssoc($query,$uid);
		if((is_string($result)) && (strpos($result,"error") !== false)){
			throw new ErrorException("SQL Error"); 
		}
		
	/* if($selectAnyDept === false){
		array_unshift($result,array('id'=>'0','deptname'=>'Все отделы'));
	} */
		
		
		if(count($result) > 0){
			array_unshift($result,array('id'=>'0','deptname'=>'Все отделы'.((($ncomp !== false) && ($ncomp > 0))?' организации':'')));
		}else{
			// array_unshift($result,array('id'=>'-1','deptname'=>'Нет данных'));
			$result = array(array('id'=>'-1','deptname'=>'Нет данных'));
		}
		
	}
		
	echo '{"success":1,"description":"issetData","listData":'.json_encode($result).'}';

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	$ex = exc_handler($ex);
	$ex = iconv("utf-8","windows-1251//IGNORE",$ex);
	
	echo '{"success":0,"description":"'.$ex.'"}';
	
}catch(Exception $ex){
	
	$ex = exc_handler($ex);
	$ex = iconv("utf-8","windows-1251//IGNORE",$ex);
	echo '{"success":0,"description":"'.$ex.'"}';
}
?>