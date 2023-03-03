<?php

header('Content-type:application/json; charset=windows-1251');


try{
	
	/* useExam
	
		let dataMessage = {message:'изменение в вашей заявке',subject:'изменение в заявке',nrequest:4745};
		
		oBaseAPI.message.email.sendToApplicantTickets(dataMessage,function(respJson){
			
			console.log(respJson);
		});
	*/
	
	
	$dataListMail = array();
	require_once "lib.ToSend.Email.php";
	
	$getUserAuthData = true;
	$sessAccesser = true;
	$path = '../../';
	require_once "$path../start.php";
	
	//$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	//$dataRecord = json_decode($dataRecord,true);


	require_once "$path../headerBase.php";


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
	

	/* $HeadMessage = 'Здравствуйте!<br />Оповещаем вас как заявителя на заявку<br />'; */
	/*  $HeadMessage = '<span class="italicBoldNote">Ваша заявка принята!</span><br />'; */
	// $sendmess = "<span class='normalText'>  Здравствуйте, ".$mailt['fio']."!</span><br /><span class='normalText'>".$dtstring['fdatestr']."г. в ".$dtstring['ftimestr']." к заявке №".$mess_id." </span><span class='importantString'>поступил новый комментарий</span><br /><br /><span class='italicBoldNote'>Для просмотра заявки пройдите, пожалуйста, <a href='http://info:86/index.php?id=29&tiket=".$mess_id."'>по этой ссылке</a></span>";
	
	// $message = $HeadMessage.$message;

	/* нужно выбрать данные заявителя заявки */
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
	
	if($itemMail['ctype'] == 1){
		
		$display = iconv("utf-8","windows-1251","Cообщение заявителю не отправлено");
		/* throw new ErrorException($display); */
		
		echo '{"success":1,"description":"'.$display.'","countMails":"0"}';
		exit;
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
	
	$display = iconv("utf-8","windows-1251","Cообщение заявителю отправлено");
	echo '{"success":1,"description":"'.$display.'","countMails":"'.count($dataListMail).'"}';

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'","countMails":"'.count($dataListMail).'"}';
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'","countMails":"null"}';
}
?>