<?php
header('Content-type:application/json;');
	/* �������������� ����� ��� json - ���� ����� � json */

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
	
	/* �������� ���� ��������������� sessAccesser - �� ������ �������� ����������� */
	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}


	$data = "Заявка отменена";

	// fwriteLog('����: ���� ������ ���������� ������������� '.json_encode($userData));

	echo '{"success":1,"data":"'.$data.'"}';
	
}catch(ErrorException $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}/*catch(Warning $ex){ -- ����������
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}*/
?>