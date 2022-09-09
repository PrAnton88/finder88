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
	
	
	if(!isset($dataRecord['message'])){
		throw new ErrorException('arg message is not found');
	}
	$message = $dataRecord['message'];
	

	/* $HeadMessage = 'Здравствуйте!<br />Оповещаем вас как ответственного на заявку<br />'; */
	/*  $HeadMessage = '<span class="italicBoldNote">Ваша заявка принята!</span><br />'; */
	// $sendmess = "<span class='normalText'>  Здравствуйте, ".$mailt['fio']."!</span><br /><span class='normalText'>".$dtstring['fdatestr']."г. в ".$dtstring['ftimestr']." к заявке №".$mess_id." </span><span class='importantString'>поступил новый комментарий</span><br /><br /><span class='italicBoldNote'>Для просмотра заявки пройдите, пожалуйста, <a href='http://info:86/index.php?id=29&tiket=".$mess_id."'>по этой ссылке</a></span>";
	
	// $message = $HeadMessage.$message;

	/* нужно выбрать данные пользователя ответственного к заявке */
	/* в массив $itemMail */
	$queryApplicantOfTicket .= $nrequest;
	$itemMail = $db->fetchFirst($queryApplicantOfTicket,$uid);
	if(is_string($itemMail) && (strpos($itemMail,'error') !== false)){
		throw new ErrorException('SQL Error');
	}
	if(!is_array($itemMail)){
		throw new ErrorException('data of applicant is invalid (than data is not array)');
	}
	
	if(count($itemMail) == 0){
		throw new ErrorException('data of applicant is empty (than array is empty)');
	}
	
	$dataListMail[] = $itemMail;

	if(!isset($itemMail['email'])){
		throw new ErrorException('data email is not found');
		// echo '{"success":1,"description":"data email of itemMail in not found","countMails":"0"}';
	}
	
	/* if(!sendEmail($itemMail,$subject,$message)){ */
	/*1 - оповещени¤ о назначении ответственным */
	/*2 - оповещени¤ о новых за¤вках */
	/*3 - оповещени¤ об изменениях к заявке */
	/* похоже тут либо 3 либо 1 */
	
	if(!sendMessage($db,$itemMail,$subject,3,$message)){
		throw new ErrorException("send.Email.ToApplicant: Message '$subject' to Applicant was not sent. see custom/logs/anyEvent.txt");
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