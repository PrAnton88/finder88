<?php

header('Content-type:application/json');
// header('Content-type:application/json;charset=WINDOW-1251');

	$getUserAuthData = true;
	$sessAccesser = true;
	
try{
	require_once "../start.php";
	if($resolution){
		
		/* b.free: 0 - не выдали, 2 - выдали, 1 - вернули */
		/*выбрать все заявки на командировочное оборудование, где есть хотя бы одно устройство которое не вернули*/
		$query = "SELECT a.id, a.userid as userId, a.listdevice,
	 b.datest, b.dateend, b.free, 
	 c.id as 'nnote', c.note,
	 concat_ws(' ', f.last_name, f.first_name, f.patronymic) as 'fio'
	FROM
	bronedevicecomplete as a LEFT JOIN bronedevicedate as b ON b.idtick=a.id
		LEFT JOIN bronedevicename as n ON n.id = b.iddevice
		LEFT JOIN bronedevicenotes as c ON c.id = a.note
		LEFT JOIN users as f ON a.userid = f.id
		WHERE (b.free = 0 OR b.free = 2)
		GROUP BY a.id ORDER BY b.datest,b.dateend";
		
			
		$result=$db->fetchAssoc($query,$uid);
		
		
		if((is_string($result)) && (strpos($result,"error") !== false)){
			header("HTTP/1.1 500 SQL Error: $result"); exit;
		}
		
		
		if(!isset($getImport)){
			header("HTTP/1.1 200 Ok");
			//echo '{"success","data":'.json_encode($result).'}';
		
			echo toJson($result,true);
		}
		
	}else{
		header("HTTP/1.1 403 Forbidden");
	}
	
}catch(ErrorException $ex){
	if(isset($getImport)){
		throw $ex;
	}
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	/* если text/html 
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
}
?>