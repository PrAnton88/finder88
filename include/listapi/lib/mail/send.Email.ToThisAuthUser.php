<?php

header('Content-type:application/json; charset=windows-1251');


try{
	
	require_once "lib.ToSend.Email.php";
	
	$getUserAuthData = true;
	$sessAccesser = true;
	$path = '../../';
	require_once "$path../start.php";
	
	//$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	//$dataRecord = json_decode($dataRecord,true);
	
	require_once "$path../headerBase.php";

	if(!isset($dataRecord['subject'])){
		$subject = 'this subject кириллица';
	}
	$subject = $dataRecord['subject'];
	
	if(!isset($dataRecord['message'])){
		$message = 'this message кириллица';
	}
	$message = $dataRecord['message'];
	
	$dataListMail = [$user];
	
	
	foreach($dataListMail as $itemMail){
		if(!isset($itemMail['email'])){
			throw new ErrorException('data email of itemMail in not found');
		}
		
		/* if(!sendEmail($itemMail,$subject,$message)){ */
		if(!sendMessage($db,$itemMail,$subject,false /*$n*/,$message/*,$sendCom=false*/)){
			throw new ErrorException("—ообщение '$subject' не отправлено");
		}
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