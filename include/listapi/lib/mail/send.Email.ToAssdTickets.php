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

	if(!isset($dataRecord['nrequest'])){
		throw new ErrorException('arg nrequest is not found');
	}

	$nrequest = (int) $dataRecord['nrequest'];
	if($nrequest == 0){
		throw new ErrorException('arg nrequest is empty');
	}


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
	$HeadMessage .= iconv("utf-8","windows-1251","Оповещаем вас как ответственного на заявку"); $HeadMessage .= '<br />';
	$message = $HeadMessage.$message;



	/* нужно выбрать данные пользователя ответственного к заявке */
	/* в массив $itemMail */
	$queryTicketsAssd .= $nrequest;
	$itemMail = $db->fetchFirst($queryTicketsAssd,$uid);
	if(is_string($itemMail) && (strpos($itemMail,'error') !== false)){
		throw new ErrorException('SQL Error');
	}
	$dataListMail[] = $itemMail;

	if(!isset($itemMail['email'])){
		/* throw new ErrorException('data email of itemMail in not found'); */
		echo '{"success":1,"description":"data email of itemMail in not found","countMails":"0"}';
	}
	
	if(!sendEmail($itemMail,$subject,$message)){
		throw new ErrorException("Сообщение '$subject' не отправлено");
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