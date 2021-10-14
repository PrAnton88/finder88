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
	
	/* пока по невы¤сненной причине в $subject нельз¤ использовать кириллицу */
	$subject = iconv("utf-8","windows-1251",$subject);
	
	
	if(!isset($dataRecord['message'])){
		$message = 'this message кириллица';
	}
	$message = $dataRecord['message'];
	$message = iconv("utf-8","windows-1251",$message);


	
	$dataListMail = [$user];
	
	
	foreach($dataListMail as $itemMail){
		if(!isset($itemMail['email'])){
			throw new ErrorException('data email of itemMail in not found');
		}
		
		if(!sendEmail($itemMail,$subject,$message)){
			throw new ErrorException("—ообщение '$subject' не отправлено");
		}
	}

	echo '{"success":1}';

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Error $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>