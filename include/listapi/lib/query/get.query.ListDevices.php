<?php

	/* .free = 2 это затребованные, = 0 это выданные */
	function getQueryToCheckFreeCountListDevice($type='',$editRecord=''){
		if($type != ''){ $type .= ' AND'; }
		return 'SELECT n.id,n.name, n.description FROM bronedevicename as n
	WHERE '.$type.' n.hidden=0 AND ((n.id NOT IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) AND (e.hidden=0))	)) '.$editRecord.') AND count>0 
	ORDER BY n.name';
		
	}
	
	function getQueryToCheckBusyDevices($type=''){
		if($type != ''){ $type .= ' AND'; }
		
		return "SELECT n.id,n.name, n.description, b.idtick, b.datest, b.dateend, c.userid,
concat_ws(' ', u.last_name, u.first_name, u.patronymic) 'fio' 
FROM bronedevicename as n 
LEFT JOIN bronedevicedate as b ON b.iddevice=n.id
LEFT JOIN bronedevicecomplete as c ON c.id=b.idtick
LEFT JOIN users as u ON u.id=c.userid
	WHERE ".$type." n.hidden=0 AND (b.free=0 OR b.free=2) AND count>0 
	ORDER BY n.name";
		
	}
	
	/* 
		return 'SELECT n.id,n.name,COUNT("name") as count, n.description FROM bronedevicename as n
	WHERE '.$type.' n.hidden=0 AND ((n.id NOT IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) AND (e.hidden=0))	)) '.$editRecord.') AND count>0 
	GROUP BY n.name ORDER BY n.name';
	*/

	function getQueryToGetDevicesOfConversation(){
		/* устройства для бронирования переговорной */
		return "SELECT * FROM conversationdevice";
		
	}

	function getQueryToGetDevicesTypesOfBroneDevices(){
		/* типы устройств для бронирования командировочных */
		return "SELECT * FROM bronedevicetype WHERE hidden<>1";
		
	}
	
	function getQueryToGetDevicesOfBroneDevices(){
		/* устройства для бронирования командировочных */
		
		return "SELECT n.id,t.type,n.name,t.id as ntype,n.count,n.description FROM bronedevicename as n 
LEFT JOIN bronedevicetype as t ON t.id=n.type
		WHERE n.hidden<>1 
		GROUP BY n.name ORDER BY n.name 
		";
		
	}

?>