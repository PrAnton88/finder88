<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{
	
	if($resolution){
		$resolution = checkUserLaws('adminOrderDocs');
	}
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}

	
	/* для выборки документов необходимо подключаться к другой БД */
	/*
	define("DB_HOST","localhost:3333");
	define("DB_LOGIN","root");
	define("DB_PASS","newroot");
	define("DB_NAME","infokng-copy");
	*/
	
	$db2 = new DB("infokng-copy");
	
	/*
	require_once $path."lib/query/get.query.ListDevices.php";
	$result = $db->fetchAssoc(
		getQueryToGetDevicesOfBroneDevices(),
		$uid
	);
	
	*/
	
	/*
	$result = array(
		array('id'=>12,'name'=>'name 12'),
		array('id'=>13,'name'=>'name 13'),
		array('id'=>14,'name'=>'name 14'),
		array('id'=>15,'name'=>'name 15'),
		array('id'=>16,'name'=>'name 16'),
	);
	*/
	
	$query = "SELECT id,pagetitle as name,class_key,parent,publishedon FROM modx_site_content ";
	$query .= " WHERE parent<>6 AND id <> 6 AND id <> 28 AND id <> 29 AND parent <> 110 AND parent <> 111";
	$query .= " AND publishedby<>0 AND deleted=0 ";
	// $query .= " AND (class_key='modDocument' AND parent = 0) ";
	
	
	$result = $db2->fetchAssoc($query,$uid);
	if((is_string($result)) && (strpos($result,"error") !== false)){
		throw new ErrorException("SQL Error, $result");
	}
	
	
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