<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";

try{
	
	if($resolution){
		$resolution = checkUserLaws('admintoBroneDevice');
	}
	
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}

	/* устройства для бронирования переговорной */
	
	require_once $path."lib/query/get.query.ListDevices.php";
	$result = $db->fetchAssoc(
		getQueryToGetDevicesTypesOfBroneDevices(),
		$uid
	);
	
	if((is_string($result)) && (strpos($result,"error") !== false)){
		throw new ErrorException('SQL Error');
	}
	
	echo '{"success":1,"listData":'.json_encode($result).'}';


}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Error $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>