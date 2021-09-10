<?php
header('Content-type:application/json;');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

if($resolution){
	
	$comp = false;
	if(isset($_POST["comp"])){
		$comp = (int) htmlspecialchars($_POST["comp"]);
		if($comp <= 0){ $comp = false; }
	}
	
	
	if($comp){
		$query = "SELECT d.id, d.name FROM dept as d LEFT JOIN place1 as p ON p.dept = d.id ";
		
		$query .= " WHERE p.comp='$comp' ";
		
		$query .= "AND p.hidden=0 AND d.hidden=0 GROUP BY p.dept";
		
	}else{
		$query = "SELECT d.id, d.name FROM dept as d ";
		$query .= "WHERE d.hidden=0 GROUP BY d.name";
		
		
	}
	
	
	$result = $db->fetchAssoc($query,$uid);
	
	if((is_string($result)) && (strpos($result,"error") !== false)){
		header("HTTP/1.1 500 SQL Error: $result");
	}else{
		echo toJson($result,true/*Assoc*/);
	}
	

}else{ header("HTTP/1.1 403 Forbidden"); }
?>