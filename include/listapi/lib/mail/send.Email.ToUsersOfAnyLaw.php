<?php

header('Content-type:application/json; charset=windows-1251');


try{
	$dataListMail = array();
	require_once "lib.ToSend.Email.php";
	
	$getUserAuthData = true;
	$sessAccesser = true;
	$path = '../../';
	require_once "$path../start.php";
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException('dataRecord is not found');
	}
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);

	if(!isset($dataRecord['law'])){
		throw new ErrorException('dataRecord.law is not found');
	}
	
	if(strlen($dataRecord['law']) == 0){
		throw new ErrorException('dataRecord.law is empty string');
	}
	
	
	if(!isset($dataRecord['subject'])){
		$subject = 'this subject кириллица';
	}
	$subject = $dataRecord['subject'];
	
	if(!isset($dataRecord['message'])){
		$message = 'this message кириллица';
	}
	$message = $dataRecord['message'];
	

	/* сначала необходимо выбрать список всех существующих прав */
	/* и сравнить с переданным $dataRecord['law'] */
	$query = "SELECT id,nameField,nameLaw,messHelp FROM listlaw WHERE nameField = '".$dataRecord['law']."'";
	$resplistLaw = $db->fetchFirst($query,$uid);
	if(is_string($resplistLaw) && (strpos($resplistLaw,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	if(count($resplistLaw) == 0){
		/* throw new ErrorException('Права '.$dataRecord['law'].' не существует'); */
		
		echo '{"success":1,"description":"'.iconv("utf-8","windows-1251",("Права {$dataRecord['law']} не существует")).'","countMails":"'.count($dataListMail).'"}';
		exit;
	}

	
	
	$thisNameLaw = $resplistLaw['nameLaw'];
	$thisNameLaw = trim($thisNameLaw);
	if(strpos($thisNameLaw,' ') !== false){
		
		$sNameLaw = substr($thisNameLaw,0,strpos($thisNameLaw,' ')).'а ';
		$sNameLaw .= substr($thisNameLaw,strpos($thisNameLaw,' '));
		$thisNameLaw = $sNameLaw;
	}
	
	/* $HeadMessage = 'Здравствуйте!<br />Оповещаем вам как '.$thisNameLaw.'<br />'; */
	$HeadMessage = '<span class="normalText">  Здравствуйте!</span><br /><span class="normalText">Оповещаем вас как </span><span class="importantString">'.$thisNameLaw.'</span><br />';
	
	$message = $HeadMessage.$message;



	// require_once "../query/get.query.UserLaws.php";
	$query = getQueryToGetUserLaws('l.'.$dataRecord['law']);
	$query .= " WHERE l.".$dataRecord['law']."=1";
	

	$usersOfLaw = $db->fetchAssoc($query,$uid);
	if(is_string($usersOfLaw) && (strpos($usersOfLaw,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	
	$dataListMail = $usersOfLaw;
	
	/*
	if(count($dataListMail) == 0){
		throw new ErrorException('Получатели не определены. count(dataListMail) = 0');
	}
	*/
	foreach($dataListMail as $itemMail){
		if(!isset($itemMail['email'])){
			throw new ErrorException('data email of itemMail in not found');
		}
		
		/* if(!sendEmail($itemMail,$subject,$message)){ */
		if(!sendMessage($db,$itemMail,$subject,false /*$n*/,$message/*,$sendCom=false*/)){
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