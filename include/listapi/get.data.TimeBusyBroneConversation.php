<?php

header('Content-type:application/json;');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

if($resolution){
	
	/*
	$query = "SELECT * FROM conversationcompleted WHERE date > ";
	$bottomDate = (date('Y').'-'.date('m').'-'.(date('d')-1));
	$query .= "'".$bottomDate."'";
	$result = $db->fetchAssoc($query,$uid);
	*/
	
	$today = date("Y")."-".date("m")."-".date("d");
	$valDeleteInterval = (string) date('Y-m-d');
			
	$d = (date("n"));
			
	if(date("m")<=2){//для того что бы всегда выводились только последние 3 месяца
		$query = "SELECT * FROM conversationcompleted WHERE hidd<>1 AND date>='$valDeleteInterval' AND (date LIKE '".(date("Y")-1)."-11%' OR date LIKE '".(date("Y")-1)."-12%' OR date LIKE '".(date("Y"))."%') ORDER BY date, time";
	}else{
		$query = "SELECT * FROM conversationcompleted WHERE hidd<>1 AND date>='$valDeleteInterval' AND (date LIKE '".(date("Y"))."-".(date("m"))."%' OR date LIKE '".(date("Y"))."-".(($d<9)?"0":"").($d+1)."%' OR date LIKE '".(date("Y"))."-".(($d<8)?"0":"").($d+2)."%' OR date LIKE '".(date("Y")+1)."-01%' OR date LIKE '".(date("Y")+1)."-02%') ORDER BY date, time";
	}
		
	$result=$db->fetchAssoc($query,$uid);
	
	
	
	$display = "";
	
	
	if((is_string($result)) && (strpos($result,"error") !== false)){
		header("HTTP/1.1 500 SQL Error: $result");
	}else{
		
		// echo toJson($result,true/*Assoc*/);
		if(is_array($result)){
			
			
			$display .= toJson($result,true/*Assoc*/);
		}else{
			
			/* не массив или пустой результат но нужно что бы ответ был не "success:"0 и "description" */
			$display .= toJson(array(),true/*Assoc*/);
		}
		/* в случае если в toJson() будет передан не массив, то результатом будет такая строка json, где {"success:"0,"description":"Данные не найдены"}; */
		
		header("HTTP/1.1 200 Ok");
		echo $display;
	}

}else{ 

	header("HTTP/1.1 403 Forbidden");
}
?>