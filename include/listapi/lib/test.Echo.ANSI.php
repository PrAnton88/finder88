<?php
header('Content-type:application/json;');
	/* обрабатываться будет как json - весь вывод в json */

	$getUserAuthData = true;
	/* $sessAccesser = true; */
	
	$path = '../';
	require_once "$path../start.php";

try{

	/*
	dataReqNext({file:'listapi/lib/test.Echo.ANSI.php',type:'json'},
		console.log
	);
	*/
	
	/* возможно если закомментирован sessAccesser - то данная проверка неактуальна */
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}


	$data = "Р—Р°СЏРІРєР° РѕС‚РјРµРЅРµРЅР°";

	// fwriteLog('Тест: была выдана информация опользователе '.json_encode($userData));

	echo '{"success":1,"data":"'.$data.'"}';
	
}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}/*catch(Warning $ex){ -- бесполезно
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}*/
?>