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
			
			
			$mmail->from = "helpdesk@nkng.vrn.ru";
			$mmail->headers = "Errors-To: [EMAIL=helpdesk@nkng.vrn.ru]helpdesk@nkng.vrn.ru[/EMAIL]";
			$mmail->to = $mailt;
			$mmail->subject = ($subject?$subject:"");
			
			
			
			$styleHead = '<head>
<META http-equiv=Content-Type content="text/html; charset=WINDOWS-1251">
<title>Exported from Notepad++</title>
<style type="text/css">
span {
	font-family: "Courier New";
	font-size: 11pt;
	color: #000000;
}
.importantString {
	font-weight: bold;
	color: #8000FF;
	background: #FFFFFF;
}
a.importantString {
	font-weight: bold;
	color: #8000FF;
	background: #F2F4FF;
	text-decoration: none;
}
.br {
	background: #F2F4FF;
}
.forNotes {
	color: #008000;
	background: #F2F4FF;
}
.italicBoldNote {
	font-weight: bold;
	font-style: italic;
	color: #000080;
	background: #F2F4FF;
}
.thisString {
	color: #808080;
	background: #F2F4FF;
}
.normalText {
	font-weight: bold;
	background: #F2F4FF;
}
</style>
</head>';
			
$display = '<html>'.$styleHead.'<body style="background: #D3DBDE; margin: 0px;"><div style="line-height: 1; 
width:100%; ">';
$display .= $sendmess;
			
$display .= '</div>
<div style="background-color:#6699CC; 
height=35px; 
color:white; 
font-family:Tahoma; 
width:100%; 
padding:1%; 
font-size:12px;
display:block; ">'
.iconv('UTF-8','Windows-1251','<font> © ООО ФПК «Космос-Нефть-Газ» '.date("Y").'</font>').
'</div>
</body></html>';
			
			$mmail->body = $display;
		
			if($mmail->send()){
				return true;
			}
			
		} catch (Exception $ex){
			throw $ex;
		}
	}
	
	/*
		sendEmail($email,$subject,$message)
		1. должно существовать $mail['email']
		2. в $subject нельзЯ использовать кириллицу (по невыясненной причине)
	*/
	
	function sendMessAsXmpp($login,$message="test message"){
        
        $postdata = http_build_query(
		array(
			"dataRecord"=>json_encode(array("login"=>$login,"message"=>$message))
		));
		
		$headers = "Accept: */*\r\n".
					  "Accept-Encoding: gzip, deflate\r\n".
					  "Accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7\r\n".
                      "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n";
		
		if(isset($_COOKIE[CSESSID])){
			$headers .=  "Cookie: ".CSESSID."=".$_COOKIE[CSESSID]."\r\n";
		}
		
		
		$opts = array(
          'http'=>array(
            'method'=>"POST",
            'header'=>$headers,
            'content' => $postdata,
            'timeout' => 20
          )
        );
        
        $context = stream_context_create($opts);
        
        // Open the file using the HTTP headers set above
        $file = file_get_contents(
           'http://192.168.7.254/xmpp/send.Test.php',
            false, 
            $context
        );
        
        return $file;
    }
	
	/* за код в convretDataSendOptio и routeBacket меня не ругать */
	/* только такой может быть код под структуру таблицы которую мне предоставили и которую я должен был использовать */
	
	/* update 2019.06.05 - до этого конвертировал неверно кое что, или, возможно изменял Алексей */
	function convretDataSendOption($valToMail,$valToCom,$direction = false){
		$OO = $valToMail;
		$ON = $valToCom;
		/* так как только после смены значений местами - к ним можно применять "формулы" */
		/*  вход 		<=> выход
	valToMail | valToCom
			1 | 1   	<=> 1 | 0 = только почта
			1 | 2   	<=> 1 | 1 = и туда и туда
			2 | 2   	<=> 0 | 1 = только комагент
			2 | 1   	<=> 0 | 0 = никуда
			
			"формула = {(2->0) | -1}";
		
			вариант Алексея <=> мой
		*/
		
		
		/* "формулы" */
		if($direction){
			/* формулу изменим с учетом того что в $valToCom - $valToMail и наоборот */
			if($valToMail == 2) $valToMail = 0;
			$valToCom --;
		}else{
			/*когда нужно преобразовать к виду Алексея, что бы записать в ЕГО ТАБЛИЦУ*/
			if($valToMail == 0) $valToMail = 2;
			$valToCom ++;
		}
		return array("subCom"=>$valToCom,"subMail"=>$valToMail,"from"=>"$OO | $ON");
	}
	
	function routeBacket($db,$nomb,$thisIsRole){
	
		/* Алексеева таблица. */ 
		/*Важно!!! (Со слов Алексея) send_options.User_id = role.id */
		$msg = '';
		$def = false;
		switch($nomb){
			case 1:{$msg .='о назначении ответственным ';
				$fieldSt = 'SendOO_1';
				$fieldEnd = 'SendOO_2';
				break;
			}
			case 2:{$msg .='о новых заявках ';
				$fieldSt = 'SendON_1';
				$fieldEnd = 'SendON_2';
				break;
			}
			case 3:{$msg .='об изменениях к заявке ';
				$fieldSt = 'SendOC_1';
				$fieldEnd = 'SendOC_2';
				break;
			}
			default:{
				/* тип например когда о новой заявке вне техподдержки */
				$def = true;
				break;
			}
		}
		
		
		if(!$def){
			/* то неясна причина заявки, поэтому не распределять на случаи выше */
			$result = $db->fetchFirst("SELECT $fieldSt, $fieldEnd FROM send_options WHERE User_id=$thisIsRole",true);
			if(!$result){ return false; }
			
			
			return convretDataSendOption($result["$fieldSt"],$result["$fieldEnd"],true);
		}
		
		/* по умолчанию, когда default  */
		return array("default"=>"search to role = $thisIsRole","subCom"=>1,"subMail"=>0);
	}
	
	function replaceHtmlChars($str){
		
		$str = str_replace("<b>","",$str);
		$str = str_replace("</b>","",$str);
		$str = str_replace("<br /><br /><br />","<br /><br />",$str);
		$str = str_replace("<br />","\n",$str);
		$str = str_replace("<br/>","\n",$str);
		
		return strip_tags($str, "<a>");
	}
	
	function replaceLink(&$str,$taglink = "<a href='mailto:"){
		
		$posStartLinkEmail = null;
		if(($posStartLinkEmail = strpos($str,$taglink)) && ($posStartLinkEmail !== false)){
			
			
			//$str = str_replace("<a href='mailto:","",$str);
			
			$LinkEmail = substr($str, $posStartLinkEmail, (strpos($str,"'>")+2-$posStartLinkEmail));
			
			$str = str_replace($LinkEmail,"",$str);
			$str = str_replace("</a>","",$str);
			
		}
		return array('str'=>$str,'tag'=>$LinkEmail);
	}
	
	function replaceHtmlChars2($str){
		
		/* пусть пока так */
		
		$str = str_replace("<b>","",$str);
		$str = str_replace("</b>","",$str);
		$str = str_replace("<br /><br /><br />","<br /><br />",$str);
		$str = str_replace("<br />","\n",$str);
		$str = str_replace("<br/>","\n",$str);
		
		/* strip_tags($str, "<a>"); */
		
		$replacer = replaceLink($str);
		$str = $replacer['str'];
		
		$replacer = replaceLink($str, "<a href='http:");
		$str = $replacer['str'];
		
		
		if(($posStartLinkEmail = strpos($replacer['tag'],'http://')) && ($posStartLinkEmail !== false)){
			
			$replacer = replaceLink($replacer['tag'], 'http://'); 
			
			$str .= ' '.$replacer['tag'];
			$str = str_replace("'>","",$str);
			
		}
		
		$str = str_replace(array("normalText","italicBoldNote","forNotes","importantString"),"",$str);
		$str = str_replace('href="http://info:86/index.php?id=158"','',$str);
		$str = str_replace('<a class="" >','',$str);
		$str = str_replace('<span class="">','',$str);
		$str = str_replace("</span>","",$str);
		
		
		$str = str_replace('<div style="border: 2px solid #b38c16; width: 120px; display: block; margin:10px 30px; background-color: white; color: #b38c16;" >','',$str);
		$str = str_replace('<div style="border: 2px solid #dc3545; width: 120px; display: block; margin:10px 30px; background-color: white; color: #dc3545;" >','',$str);
		$str = str_replace('<div style="border: 2px solid gray; width: 120px; display: block; margin:10px 30px; background-color: white; color: gray;" >','',$str);

		$str = str_replace('</div>',"\r\n",$str);
		
		
		return $str;
	}
	
	function sendMessage($db,$mailt,$subject,$n,$sendmess,$sendCom=false){
		/* определять направление - на почту или в спарк */
		global $user;
		/*$n = 1 или 2 или 3*/
		/*1 - оповещени¤ о назначении ответственным*/
		/*2 - оповещени¤ о новых за¤вках*/
		/*3 - оповещени¤ об изменениях к заявке*/
		
		$log=fopen($_SERVER['DOCUMENT_ROOT']."/custom/logs/anyEvent.txt","a+");
		try{
			
			
			if(!isset($mailt['email'])){ throw new Exception('arg email is not found'); }
			$email = str_replace("\n","",str_replace("\n\r","",str_replace(" ","",$mailt['email'])));
			
			
			fwrite($log, date("d-m-Y H:i:s")."\r\n-\tnew message to {$email}\r\n");
			fwrite($log, "-\t".$sendmess."\r\n");
			
			/*
			if(isset($mailt['uid']) && ($mailt['uid'] == $user['uid'])){
				// fwrite($log, "\tthis send message for self\r\n\r\n");
				// fclose($log);
				return true;
			}
			*/
			
			
			
			if(!isset($mailt['role'])){ throw new Exception('arg role is not found'); }
			if(!isset($mailt['login'])){ throw new Exception('arg login is not found'); }
			
			$opts = routeBacket($db,$n,$mailt['role']);
			if(!$opts){ throw new Exception('setting route for get method delivery to recipient is empty'); }
			
			fwrite($log, "\topts=".json_encode($opts)."\r\n");
			
			
			$subject = iconv("utf-8","cp1251//IGNORE",$subject);
			
			$sendmess = iconv("utf-8","windows-1251",$sendmess);
			
			if($opts['subMail']==1){
				
				fwrite($log, $subject." - Send to Mail....");
				
				if(!sendEmail($email,$subject,$sendmess)){
					throw new Exception(" send to email is invalid ");
				}
				
				fwrite($log, "complete\r\n");
			}
			
			if($opts['subCom']==1){
				
				if(!$sendCom){ 
					$sendCom = $sendmess; 
					/* должны отправлять в utf-8 а мы выше дял почты конвертировали в windows-1251 */
					/* конвертируем обратно */
					
					
					$sendCom = replaceHtmlChars2($sendCom);
					$sendCom .= "\r\n";
					
					$sendCom = iconv("windows-1251","utf-8",$sendCom);
				}
				
				fwrite($log, "- Send to Spark....");
				
				if(!sendMessAsXmpp($mailt["login"],$sendCom)){
					throw new Exception("is invalid ");
				}
				
				fwrite($log, "complete\r\n");
			}
			
			
			
			fwrite($log, "\r\n\r\n");
			return true;
			
		}catch(Exception $ex){
			
			fwrite($log, "-\tException: ".$ex->getMessage()."\r\n\r\n");
			
		}finally{
			fclose($log);
		}
		
	}
?>