<?php

header('Content-type:application/json; charset=windows-1251');


try{
	
	require_once "lib.ToSend.Email.php";
	
	$getUserAuthData = true;
	$sessAccesser = true;
	$path = '../../';
	require_once "$path../start.php";
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);

	if(!isset($dataRecord['subject'])){
		$subject = 'this subject кириллица';
	}
	$subject = $dataRecord['subject'];
	
	
	if(!isset($dataRecord['message'])){
		$message = 'this message кириллица';
	}
	$message = $dataRecord['message'];
	

	$HeadMessage = 'Здравствуйте!<br />Оповещаем вас как администратора<br />(Причина: отправка подписанным на оповещения не удалась)<br />';
	$message = $HeadMessage.$message;

	$query = getQueryToGetUserLaws('l.adminRoot');
	$query .= " WHERE l.adminRoot=1";
    $dataListMail = $db->fetchAssoc($query);
	if((is_string($dataListMail)) && (strpos($dataListMail,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	if(!is_array($dataListMail)){
		$dataListMail = array();
	}
	if(count($dataListMail) == 0){
		throw new ErrorException("data of .adminRoot is empty");
	}
	
	
	foreach($dataListMail as $itemMail){
		if(!isset($itemMail['email'])){
			/* throw new ErrorException('data email of itemMail in not found'); */
			echo '{"success":1,"description":"data email of itemMail in not found","countMails":"0"}';
		}
		
		if(!sendMessage($db,$itemMail,$subject,false/*$n*/,$message/*,$sendCom=false*/)){
			throw new ErrorException("sending message '$subject' was dropped, description see in report file anyEvent.txt");
		}
	}

	echo '{"success":1,"countMails":"'.count($dataListMail).'"}';

}catch(ErrorException $ex){
	/* echo '{"success":0,"description":"'.exc_handler($ex).'","countMails":"'.count($dataListMail).'"}'; */
	echo '{"success":0,"description":"'.exc_handler($ex).'"';
	if(isset($dataListMail)){
		echo ',"countMails":"'.count($dataListMail).'"';
	}
	
	echo '}';
	
}catch(Error $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'","countMails":"null"}';
}catch(Exception $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'","countMails":"null"}';
}
?>