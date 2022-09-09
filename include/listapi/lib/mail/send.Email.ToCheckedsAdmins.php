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
	
	
	if(!isset($dataRecord['message'])){
		throw new ErrorException('arg message is not found');
	}
	$message = $dataRecord['message'];
	
	
	$HeadMessage = '<span class="italicBoldNote">Поступила новая заявка</span><br /><span class="italicBoldNote">Оповещаем вас как </span><a class="importantString" href="http://info:86/index.php?id=158">подписанного на оповещения</a><span class="italicBoldNote">.</span><br />';
	$message = $HeadMessage.$message;
	

	/* выбираем в массив $dataListMail подписаных на оповещения */
	/* $queryAdminsCkeckeds < queryUserData.php < ../../../start.php */
	$listAdminSigned = $db->fetchAssoc($queryAdminsCkeckeds,$uid);
	if(is_string($listAdminSigned) && (strpos('error',$listAdminSigned) !== false)){
		throw new ErrorException("SQL Error when selecting emalis admins signed");
	}
	
	$dataListMail = $listAdminSigned;
	
	
	
	// print_r($dataListMail);
	
	if(count($dataListMail) == 0){
		throw new ErrorException('Получатели не определены. count(dataListMail) = 0');
	}
	
	foreach($dataListMail as $itemMail){
		if(!isset($itemMail['email'])){
			throw new ErrorException('data email of itemMail in not found');
		}
		
		/* if(!sendEmail($itemMail,$subject,$message)){ */
		if(!sendMessage($db,$itemMail,$subject,2 /*$n*/,$message/*,$sendCom=false*/)){
			throw new ErrorException("this process send message '$subject' as failed. see logs/anyEvent.txt");
		}
	}

	echo '{"success":1,"countMails":"'.count($dataListMail).'"}';

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'","countMails":"'.count($dataListMail).'"}';
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'","countMails":"null"}';
}
?>