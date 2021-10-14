<?php

header('Content-type:application/json; charset=windows-1251');


try{
	$dataListMail = array();
	require_once "lib.ToSend.Email.php";
	
	$getUserAuthData = true;
	$sessAccesser = true;
	$path = '../../';
	require_once "$path../start.php";
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);

	if(!isset($dataRecord['subject'])){
		throw new ErrorException('arg subject is not found');
	}
	$subject = $dataRecord['subject'];
	
	/* пока по невы¤сненной причине в $subject нельз¤ использовать кириллицу */
	$subject = iconv("utf-8","windows-1251",$subject);
	
	
	if(!isset($dataRecord['message'])){
		throw new ErrorException('arg message is not found');
	}
	$message = $dataRecord['message'];
	
	
	$message = iconv("utf-8","windows-1251",$message);
	$HeadMessage = iconv("utf-8","windows-1251","Здравствуйте!"); $HeadMessage .= '<br />';
	$HeadMessage .= iconv("utf-8","windows-1251","Оповещаем вас как подписанного на оповещения о новых заявках."); $HeadMessage .= '<br />';
	
	/*
	$HeadMessage = "Здравствуйте!<br />Оповещаем вас как подписанного на оповещения о новых заявках.<br />";
	*/
	$message = $HeadMessage.$message;
	


	/* выбираем в массив $dataListMail подписаных на оповещения */
	/* $queryAdminsCkeckeds < queryUserData.php < ../../../start.php */
	$listAdminSigned = $db->fetchAssoc($queryAdminsCkeckeds,$uid);
	if(is_string($listAdminSigned) && (strpos('error',$listAdminSigned) !== false)){
		throw new ErrorException("SQL Error when selecting emalis admins signed");
	}
	$dataListMail = $listAdminSigned;
	
	/*
	if(count($dataListMail) == 0){
		throw new ErrorException('Получатели не определены. count(dataListMail) = 0');
	}
	*/
	foreach($dataListMail as $itemMail){
		if(!isset($itemMail['email'])){
			throw new ErrorException('data email of itemMail in not found');
		}
		
		if(!sendEmail($itemMail,$subject,$message)){
			throw new ErrorException("Сообщение '$subject' не отправлено");
		}
	}

	echo '{"success":1,"countMails":"'.count($dataListMail).'"}';

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'","countMails":"'.count($dataListMail).'"}';
}catch(Error $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'","countMails":"null"}';
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'","countMails":"null"}';
}
?>