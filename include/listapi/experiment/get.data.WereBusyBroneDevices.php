<?php

header('Content-type:application/json');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Expose-Headers:Connection,Content-Length,Date,Expires,Keep-Alive');

try{
	require_once("../../../config/connDB.php");/* get $db */

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
			
				
	$result=$db->fetchAssoc($query,true);

/*
	echo '{"success":1,"listData":[
		{"id": "114", "userid": "811", "listdevice": "Запрашиваемые устройства  -- Аккумулятор - Типоразмер АА  -- 2 шт;", "datest": "2017-09-28", "dateend": "2017-09-28"},
		{"id": "115", "userid": "811", "listdevice": "Запрашиваемые устройства  -- Аккумулятор - Типораз…нов  -- 1 шт;Телефон - Nokia Model: 105  -- 2 шт;", "datest": "2017-09-28", "dateend": "2017-10-09"},
		{"id": "116", "userid": "572", "listdevice": "Запрашиваемые устройства  -- Аккумулятор - Типоразмер ААА  -- 1 шт;", "datest": "2017-09-29", "dateend": "2017-10-03"}
	]}';
*/
	// $getImport = true;
	if(!isset($getImport)){
		echo '{"success":1,"listData":'.json_encode($result).'}';
	}

}catch(ErrorException $ex){
	/*
	$smarty->assign("message",exc_handler($ex));
	return $smarty->display('error.formDataIsNotFound.tpl');
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>