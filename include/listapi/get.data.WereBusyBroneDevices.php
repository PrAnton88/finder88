<?php

header('Content-type:application/json');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Expose-Headers:Connection,Content-Length,Date,Expires,Keep-Alive');

	$getUserAuthData = true;
	$sessAccesser = true;
	
try{
	require_once "../start.php";
	if($resolution){
		
		/*выбрать все заявки на командировочное оборудование, где нет ни одного одно устройства которое не вернули*/
		/*для сводной таблицы диспетчера*/
		$query = "SELECT a.id, a.userid, a.listdevice,
 b.datest, b.dateend,
 concat_ws(' ', f.last_name, f.first_name, f.patronymic, f.description) as 'fio'
FROM
bronedevicecomplete as a LEFT JOIN bronedevicedate as b ON b.idtick=a.id
	LEFT JOIN bronedevicename as n ON n.id = b.iddevice
        LEFT JOIN users as f ON a.userid = f.id
	WHERE b.free = 1 AND a.id NOT IN (
SELECT a.id
FROM
bronedevicecomplete as a LEFT JOIN bronedevicedate as b ON b.idtick=a.id
	LEFT JOIN bronedevicename as n ON n.id = b.iddevice
        WHERE b.free = 0
	GROUP BY a.id ORDER BY b.datest,b.dateend
)
	GROUP BY a.id ORDER BY ".(isset($_POST["sort"])?$_POST["sort"]:"b.datest,b.dateend");
		
			
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