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
	
	
	/*	useExam
	
		let dataRecord = null;
		dataRecord = {ncomp:1}; 
		// или dataRecord = {scomp:"ФПК"}; 
	
		dataReqNext({file:'listapi/getCompany.php',type:'json',
			args:'dataRecord='+JSON.stringify(dataRecord)},
			function(responseJson){
				console.log(responseJson);
				
			}
		);
	*/
	
	
	$query = $companisePreQuery;
	
	$dataRecord = false;
	if(isset($_POST['dataRecord'])){
		$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
		$dataRecord = json_decode($dataRecord,true);
	}
	
	$tmp = false;
	if(isset($dataRecord["ncomp"])){
		$tmp = (int) $dataRecord["ncomp"];
		if($tmp > 0){
			$query .= " AND id=".$tmp;
		}
	}
	
	if(isset($dataRecord["scomp"])){
		$tmp = $dataRecord["scomp"];
		$tmp = mb_strtolower(trim($tmp));
		if($tmp != ''){
			$query .= " AND (LOWER(name) LIKE '%".$tmp."%') ";
		}
	}
	
	$query .= " ORDER By name";
	// echo $query;
	
	$result = $db->fetchAssoc($query,$uid);
	if((is_string($result)) && (strpos($result,"error") !== false)){
		throw new ErrorException("SQL Error $result"); 
	}

	array_unshift($result,array('id'=>'0','name'=>'Все организации'));
	
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