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

	if(!isset($dataRecord['nrequest'])){
		throw new ErrorException('arg nrequest is not found');
	}

	$nrequest = (int) $dataRecord['nrequest'];
	if($nrequest == 0){
		throw new ErrorException('arg nrequest is empty');
	}

	if(!isset($dataRecord['ncomment'])){
		throw new ErrorException('arg ncomment is not found');
	}
	
	$ncomment = (int) $dataRecord['ncomment'];
	if($ncomment == 0){
		throw new ErrorException('arg ncomment is empty');
	}


	if(!isset($dataRecord['subject'])){
		throw new ErrorException('arg subject is not found');
	}
	$subject = $dataRecord['subject'];
	
	if(!isset($dataRecord['message'])){
		throw new ErrorException('arg message is not found');
	}
	$message = $dataRecord['message'];
	

	
	
	
	/* нужно выбрать данные пользователя некоторого автора комментария в заявке */
	
	/* $queryApplicantOfTicket .= $nrequest;
	$itemMail = $db->fetchFirst($queryApplicantOfTicket,$uid);
	if(is_string($itemMail) && (strpos($itemMail,'error') !== false)){
		throw new ErrorException('SQL Error');
	}
	*/ 
	$itemMail = array('fio'=>"Pr. Anton",'email'=>'prudnikov@kng.vrn.ru','role'=>811,'login'=>'oto016');

	
	if(!is_array($itemMail)){
		throw new ErrorException('data of applicant is invalid (than data is not array)');
	}
	
	if(count($itemMail) == 0){
		throw new ErrorException('data of applicant is empty (than array is empty)');
	}
	

	if(!isset($itemMail['email'])){
		throw new ErrorException('data email is not found');
	}
	
	/* if(!sendEmail($itemMail,$subject,$message)){ */
	/*1 - оповещени¤ о назначении ответственным */
	/*2 - оповещени¤ о новых за¤вках */
	/*3 - оповещени¤ об изменениях к заявке */
	/* похоже тут 3 */
	
	if(!sendMessage($db,$itemMail,$subject,3,$message)){
		throw new ErrorException("send.Email.ToOtherAuthorComment: Message '$subject' to OtherAnyAuthorAnyComment was not sent. see custom/logs/anyEvent.txt");
	}
	

	echo '{"success":1}';

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>