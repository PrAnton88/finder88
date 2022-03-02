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

	$mail = array('fio'=>"Pr. Anton",'email'=>'prudnikov@kng.vrn.ru','userid'=>811,'login'=>'oto016');

	if(!isset($dataRecord['subject'])){
		$subject = 'this subject кириллица';
	}
	$subject = $dataRecord['subject'];
	
	
	if(!isset($dataRecord['message'])){
		$message = 'this message кириллица';
	}
	$message = $dataRecord['message'];
	

	$HeadMessage = 'Здравствуйте!<br />Оповещаем вас как единственного получателя<br />';
	$HeadMessage = '<span class="normalText">  Здравствуйте!</span><br /><span class="normalText">Оповещаем вас как </span><span class="importantString" >единственного получателя</span><br />';
	
	$message = $HeadMessage.$message;

	if(isset($_POST['dataListMail'])){
		$dataListMail = html_entity_decode(htmlspecialchars($_POST['dataListMail']));
		$dataListMail = json_decode($dataListMail,true);
		
	}else{
		
		$dataListMail = [$mail];
	}
	
	$sending = false;
	foreach($dataListMail as $itemMail){
		if(!isset($itemMail['email'])){
			/* throw new ErrorException('data email of itemMail in not found'); */
			echo '{"success":1,"description":"data email of itemMail in not found","countMails":"0"}';
		}
		
		/* if(!sendEmail($itemMail,$subject,$message)){ */
		if(!sendMessage($db,$itemMail,$subject,false/*$n*/,$message/*,$sendCom=false*/)){
			throw new ErrorException("Сообщение '$subject' не отправлено");
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