<?php

header('Content-type:application/json;');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

if($resolution){
	
	$dept = false;
	if(isset($_POST["dept"])){
		$dept = (int) htmlspecialchars($_POST["dept"]);
		if($dept <= 0){ $dept = false; }
	}
	
	
	if($dept){
		$query = "SELECT a.id, concat_ws(' ', a.last_name, a.first_name, a.patronymic) as fio, r.login, r.email ";
		
		$query .= " FROM role as r RIGHT JOIN users as a ON a.role = r.id LEFT JOIN place1 as p ON a.place=p.id ";

		$query .= " WHERE p.dept='$dept' ";
		
		$query .= "AND a.hidden=0 ORDER BY fio";
		
		$result = $db->fetchAssoc($query,$uid);
		
		if((is_string($result)) && (strpos($result,"error") !== false)){
			header("HTTP/1.1 500 SQL Error: $result");
		}else{
			echo toJson($result,true/*Assoc*/);
		}
	}else{
		
		header("HTTP/1.1 400 Bad request");
	}

}else{ header("HTTP/1.1 403 Forbidden"); }
?>