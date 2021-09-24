<?php
header('Content-type:text/html');
// header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

if((!$resolution) || ($user['priority'] != 3)){
	header("HTTP/1.1 403 Forbidden");
	exit;
}

try{

	require_once "../config.modx.php";
	/* get $modx */
	
	require_once "../config.smarty.php";
	/* get $smarty */
	
	$modx->smarty = $smarty;


	$nRecord = 0;
	if(isset($_POST['nrecord'])){
        $nRecord = (int) html_entity_decode(htmlspecialchars($_POST['nrecord']));
    }
	$free = '0';
	if(isset($_POST['free'])){
        $free = html_entity_decode(htmlspecialchars($_POST['free']));
    }
	
	if($nRecord == 0){
		throw new ErrorException("nrecord не передан");
	}
	
	if(($free != '0') && ($free != '2')){
		throw new ErrorException("free value is invalid");
	}
	
	$query = "SELECT n.id, n.name,  t.type, d.idtick, d.free
	FROM
	bronedevicename as n LEFT JOIN bronedevicedate as d ON d.iddevice = n.id
	RIGHT OUTER JOIN bronedevicetype as t ON t.id = n.type
		WHERE d.free = $free AND d.idtick = $nRecord
		GROUP BY n.id ORDER BY d.datest,d.dateend";
		
	
	/* listRecordsRequestingDevices */
	$listRecordsDevices = $db->fetchAssoc($query,$uid);
	
	
	if((is_string($listRecordsDevices)) && (strpos($listRecordsDevices,"error") !== false)){
		
		throw new ErrorException($listRecordsDevices);
	}
	/*
	echo '{"success":1,"nrecord":"'.$nRecord.'","listRecordsDevices":'.json_encode($listRecordsDevices).'}';
	*/
	
	$smarty->assign("listRecordsDevices",json_encode($listRecordsDevices));
	
	$smarty->assign("admin",($user['priority'] == 3));
	
	return $smarty->display('formActionItemRequestingDevices.tpl');
	
	
	
}catch(ErrorException $ex){
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	// echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>