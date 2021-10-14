<?php

	if(!isset($path)){ $path = ""; }
	require_once $path."mattach.php";

	$mmail = new mime_mail();


	function sendEmail($mailt,$subject,$sendmess){
		
		global $mmail;
		
		/* почему то из php modx-а $mmail "из global" равен null */
		
		try{
			if(!$mmail){
				$mmail = new mime_mail();
			}
			
			$mmail->parts = array();
			/*отправляем на почту*/
			
			$mailt = str_replace("\n","",str_replace("\n\r","",str_replace(" ","",$mailt['email'])));
			$mmail->from = "helpdesk@nkng.vrn.ru";
			$mmail->headers = "Errors-To: [EMAIL=helpdesk@nkng.vrn.ru]helpdesk@nkng.vrn.ru[/EMAIL]";
			$mmail->to = $mailt;
			$mmail->subject = ($subject?$subject:"");
			$mmail->body = $sendmess;
		
			if($mmail->send()){
				/* залогинить в /custom/logs/pastSendedMail.txt */
				$log=fopen("pastSendedMail.txt","a+");
				fwrite($log, "to {$mailt}\r\n-\t".$sendmess."\r\n\r\n");
				fclose($log);
				
				return true;
			}
			
		} catch (Exception $ex){
			throw $ex;
		}
		
	}
	
	/*
		sendEmail($mail,$subject,$message)
		1. должно существовать $mail['email']
		2. в $subject нельзЯ использовать кириллицу (по невыясненной причине)
	*/
	
?>