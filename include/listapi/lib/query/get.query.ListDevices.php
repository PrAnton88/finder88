<?php

	/* .free = 2 это затребованные, = 0 это выданные */
	function getQueryToCheckFreeCountListDevice($type='',$editRecord=''){
		if($type != ''){ $type .= ' AND'; }
		return 'SELECT n.id,n.name, n.description FROM bronedevicename as n
	WHERE '.$type.' n.hidden=0 AND ((n.id NOT IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) AND (e.hidden=0))	)) '.$editRecord.') AND count>0 
	ORDER BY n.name';
		
	}
	
	/* 
		return 'SELECT n.id,n.name,COUNT("name") as count, n.description FROM bronedevicename as n
	WHERE '.$type.' n.hidden=0 AND ((n.id NOT IN (SELECT e.id FROM bronedevicedate as b, bronedevicename as e WHERE ((b.iddevice=e.id) AND (b.free=0 OR b.free=2) AND (e.hidden=0))	)) '.$editRecord.') AND count>0 
	GROUP BY n.name ORDER BY n.name';
	*/

?>