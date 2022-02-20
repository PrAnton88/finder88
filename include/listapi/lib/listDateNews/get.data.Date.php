<?php

header('Content-type:application/json');

	$path = '../../';
	require_once "$path../start.php";

try{
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}
	
	$db2 = new DB("infokng-copy");
	
	
	
	$query = "SELECT publishedon as id,pagetitle as name,class_key,parent FROM modx_site_content ";
	$query .= " WHERE parent=6 AND publishedby<>0 AND deleted=0 ORDER BY publishedon DESC";
	
	/*
	$query = "SELECT id,pagetitle as name,class_key,parent,publishedon,publishedby,deleted FROM modx_site_content ";
	$query .= " WHERE parent=6";
	*/
	
	$result = $db2->fetchAssoc($query,$uid);
	if((is_string($result)) && (strpos($result,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	/*
	$result = array(
		array('id'=>1,'name'=>'Июнь 2021'),
		array('id'=>2,'name'=>'Июль 2021'),
		array('id'=>3,'name'=>'Август 2021'),
		array('id'=>4,'name'=>'Сентябрь 2021'),
		array('id'=>5,'name'=>'Октябрь 2021'),
	);
	*/
	
	echo '{"success":1,"listData":'.json_encode($result).'}';


}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>