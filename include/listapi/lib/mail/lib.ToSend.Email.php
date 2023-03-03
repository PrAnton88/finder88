<?php

	if(!isset($path)){ $path = ""; }
	require_once $path."mattach.php";

	$mmail = new mime_mail();


	function sendEmail($mailt,$subject,$sendmess){
		
		global $mmail;
		
		/* ������ �� �� php modx-� $mmail "�� global" ����� null */
		
		try{
			if(!$mmail){
				$mmail = new mime_mail();
			}
			
			
			$mmail->parts = array();
			/*���������� �� �����*/
			
			
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
.iconv('UTF-8','Windows-1251//IGNORE','<font> � ��� ��� �������-�����-��� '.date("Y").'</font>').
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
		1. ������ ������������ $mail['email']
		2. � $subject ������ ������������ ��������� (�� ������������ �������)
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
        
		
		try{
			
			$context = stream_context_create($opts);
			
			// Open the file using the HTTP headers set above
			// throw new Exception('test Error');
			
			$stream = fopen( /* instead file_get_contents */
			   'http://192.168.7.254:86/xmpp/send.Test.php',
			   'r',
				false, 
				$context
			);
			
			// ���������� � ����������, � �����
			// ���������� � ������
			// echo Debug::d(stream_get_meta_data($stream),'stream_get_meta_data($stream)');

			// ���������� ���������� �� ������ $url
			// echo Debug::d(stream_get_contents($stream),'stream_get_contents($stream)');
			// fclose($stream);
			
		}catch(Exception $ex){
			
			$stream = $ex->getMessage();
		}finally{
			return $stream;
		}
    }
	
	/* �� ��� � convretDataSendOptio � routeBacket ���� �� ������ */
	/* ������ ����� ����� ���� ��� ��� ��������� ������� ������� ��� ������������ � ������� � ������ ��� ������������ */
	
	/* update 2019.06.05 - �� ����� ������������� ������� ��� ���, ���, �������� ������� ������� */
	function convretDataSendOption($valToMail,$valToCom,$direction = false){
		$OO = $valToMail;
		$ON = $valToCom;
		/* ��� ��� ������ ����� ����� �������� ������� - � ��� ����� ��������� "�������" */
		/*  ���� 		<=> �����
	valToMail | valToCom
			1 | 1   	<=> 1 | 0 = ������ �����
			1 | 2   	<=> 1 | 1 = � ���� � ����
			2 | 2   	<=> 0 | 1 = ������ ��������
			2 | 1   	<=> 0 | 0 = ������
			
			"������� = {(2->0) | -1}";
		
			������� ������� <=> ���
		*/
		
		
		/* "�������" */
		if($direction){
			/* ������� ������� � ������ ���� ��� � $valToCom - $valToMail � �������� */
			if($valToMail == 2) $valToMail = 0;
			$valToCom --;
		}else{
			/*����� ����� ������������� � ���� �������, ��� �� �������� � ��� �������*/
			if($valToMail == 0) $valToMail = 2;
			$valToCom ++;
		}
		return array("subCom"=>$valToCom,"subMail"=>$valToMail,"from"=>"$OO | $ON");
	}
	
	function routeBacket($db,$nomb,$thisIsRole){
	
		/* ��������� �������. */ 
		/*�����!!! (�� ���� �������) send_options.User_id = role.id */
		$msg = '';
		$def = false;
		switch($nomb){
			case 1:{$msg .='� ���������� ������������� ';
				$fieldSt = 'SendOO_1';
				$fieldEnd = 'SendOO_2';
				break;
			}
			case 2:{$msg .='� ����� ������� ';
				$fieldSt = 'SendON_1';
				$fieldEnd = 'SendON_2';
				break;
			}
			case 3:{$msg .='�� ���������� � ������ ';
				$fieldSt = 'SendOC_1';
				$fieldEnd = 'SendOC_2';
				break;
			}
			default:{
				/* ��� �������� ����� � ����� ������ ��� ������������ */
				$def = true;
				break;
			}
		}
		
		
		if(!$def){
			/* �� ������ ������� ������, ������� �� ������������ �� ������ ���� */
			$result = $db->fetchFirst("SELECT $fieldSt, $fieldEnd FROM send_options WHERE User_id=$thisIsRole",true);
			if(!$result){ return false; }
			
			
			return convretDataSendOption($result["$fieldSt"],$result["$fieldEnd"],true);
		}
		
		/* �� ���������, ����� default  */
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
	
	function replaceLink(&$str,$taglink = '<a href="mailto:'){
		
		$posStartLinkEmail = null;
		$LinkEmail = false;
		
		//throw new ErrorException($str);
		
		if(($posStartLinkEmail = strpos($str,$taglink)) && ($posStartLinkEmail !== false)){
			
			//throw new ErrorException(strpos($str,'">')+2);
			//$str = str_replace("<a href='mailto:","",$str);
			
			//$LinkEmail = substr($str, $posStartLinkEmail, (strpos($str,'">')+2-$posStartLinkEmail));
			//$LinkEmail = substr($str, $posStartLinkEmail, (strpos($str,'">')+2));
			$LinkEmail = substr($str, $posStartLinkEmail);
			
			
			$LinkEmail = substr($LinkEmail,0,(strpos($LinkEmail,'</a>')+4));
			
			if(strpos($LinkEmail,'http') === false){
				$completeLink = substr($LinkEmail,strlen($taglink),strpos($LinkEmail,'">')-strlen($taglink));
			}else{
				$completeLink = substr($LinkEmail,strlen($taglink)-strlen('http://'),strpos($LinkEmail,'">')-strlen($taglink)+strlen('http://'));
			}
			
			
			// throw new ErrorException($completeLink);
			
			// $LinkEmail = substr($str,(count($LinkEmail) + (strpos($str,'<')+1)));
			
			$str = str_replace($LinkEmail,$completeLink,$str);
			
			// $LinkEmail = substr($LinkEmail, $posStartLinkEmail, );
			
			
			// $str = str_replace("</a>","",$str);
			
			return array('str'=>$str,'tag'=>$completeLink);
		}
		
		/* if(!$LinkEmail){
			throw new Exception($taglink."  >>>  ".$str);
		} */
		// return array('str'=>$str,'tag'=>$LinkEmail);
		return false;
	}
	
	function replaceHtmlChars2($str){
		
		/* ����� ���� ��� */
		
		$str = str_replace("<b>","",$str);
		$str = str_replace("</b>","",$str);
		$str = str_replace("<br /><br /><br />","<br /><br />",$str);
		$str = str_replace("<br />","\n",$str);
		$str = str_replace("<br/>","\n",$str);
		
		
		$replacer = replaceLink($str);
		if($replacer){
			
			
			// throw new ErrorException($str);
			
			$str = $replacer['str'];
			
			
			
			
			
			$replacer = replaceLink($str, '<a href="http://');
			if($replacer){
				
				// $str .= ' '.$replacer['tag'];
				$str = $replacer['str'];
			
				if(($posStartLinkEmail = strpos($replacer['tag'],'http://')) && ($posStartLinkEmail !== false)){
					
					$replacer = replaceLink($replacer['tag'], 'http://'); 
					
				}
				
			}
		}else{
			$str = '';
		}
		
		
		
		
		
		
		$str = str_replace(array("normalText","italicBoldNote","forNotes","importantString"),"",$str);
		$str = str_replace('href="http://'.$_SERVER['HTTP_HOST'].'/index.php?id=158"','',$str);
		
		$str = str_replace("������������ �� ����������", '������������ �� ���������� http://'.$_SERVER['HTTP_HOST'].'/index.php?id=158',$str);
		// $str = str_replace('href="http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER['REMOTE_PORT'].'/index.php?id=158"','',$str);
		$str = str_replace('<a class="" >','',$str);
		$str = str_replace('<span class="">','',$str);
		$str = str_replace('<span class="','',$str);
		$str = str_replace("</span>","",$str);
		
		$str = str_replace("<text>","",$str);
		$str = str_replace("</text>","",$str);
		
		$str = str_replace('<div style="border: 2px solid #b38c16; width: 120px; display: block; margin:10px 30px; background-color: white; color: #b38c16;" >','',$str);
		$str = str_replace('<div style="border: 2px solid #dc3545; width: 120px; display: block; margin:10px 30px; background-color: white; color: #dc3545;" >','',$str);
		$str = str_replace('<div style="border: 2px solid gray; width: 120px; display: block; margin:10px 30px; background-color: white; color: gray;" >','',$str);

		$str = str_replace('</div>',"\r\n",$str);
		$str = str_replace("</a>","",$str);
		$str = str_replace("/*","\r\n/*",$str);
		
		return $str;
	}
	
	function sendMessage($db,$mailt,$subject,$n,$sendmess,$sendCom=false){
		/* ���������� ����������� - �� ����� ��� � ����� */
		global $user;
		/*$n = 1 ��� 2 ��� 3*/
		/*1 - ��������� � ���������� �������������*/
		/*2 - ��������� � ����� ������*/
		/*3 - ��������� �� ���������� � ������*/
		
		$log=fopen($_SERVER['DOCUMENT_ROOT']."/custom/logs/anyEvent.txt","a+");
		$stream=false;
		try{
			
			$email = false;
			if(isset($mailt['email']) && ($mailt['email'] != '')){
				$email = $mailt['email'];
				$email = str_replace("\n","",str_replace("\n\r","",str_replace(" ","",$email)));
				
				fwrite($log, date("d-m-Y H:i:s")."\r\n-\tnew message to {$email}\r\n");
			}
			
			if($email === false){
				// throw new Exception('arg email is not found'); 
				fwrite($log, date("d-m-Y H:i:s")."\r\n-\t arg email is not found \r\n");
			}
			
			fwrite($log, "-\t".$sendmess."\r\n\r\n");
			if($email === false){
				return true;
			}
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
			
			
			
			$sendmess = str_replace("amp;","&",$sendmess);
			$sendmess = iconv("utf-8","windows-1251",$sendmess);
			
			$opts['subMail'] = 0;
			$opts['subCom'] = 1;
			
			if($opts['subMail']==1){
				
				fwrite($log, $subject." - Send to Mail....");
				
				if(!sendEmail($email,$subject,$sendmess)){
					if($opts['subCom']!=1){
						throw new Exception(" send to email is invalid ");
					}else{
						fwrite($log, "send to email is invalid\r\n");
					}
				}
				
				fwrite($log, "complete\r\n");
			}
			
			if($opts['subCom']==1){
				
				if(!$sendCom){ 
					$sendCom = $sendmess; 
					/* ������ ���������� � utf-8 � �� ���� ��� ����� �������������� � windows-1251 */
					/* ������������ ������� */
					
					
					$sendCom = replaceHtmlChars2($sendCom);
					$sendCom .= "\r\n";
					
					$sendCom = iconv("windows-1251","utf-8",$sendCom);
				}
				
				fwrite($log, "- Send to Spark....");
				
				$i = 0;
				while(($i < 5) && (($stream === false) || is_string($stream) || ($stream == null))){
					$stream = sendMessAsXmpp($mailt["login"],$sendCom);
					
					if(($stream !== false) && (!is_string($stream)) && ($stream != null)){
						break; /* ������ ��� �� �� ����������� ������� ���� ��������� ��� � ��������� �� ���� �������� */
					}
					
					$i++;
				}
				
				if($i == 5){
					/* ���� ��� ��������� �������� ���������� �������� ���������, ������ "stream is invalid " */
					
					if(is_string($stream)){
						fwrite($log, "$stream\r\n");
					}
					throw new Exception("stream is invalid ");	
				}
				
				
				if(($stream !== false) && (!is_string($stream))){
					fwrite($log, "complete\r\n");
				}
			}
			
			fwrite($log, "\r\n\r\n");
			return true;
			
		}catch(Exception $ex){
		
			$ex = exc_handler($ex);
			/* $ex = iconv("utf-8","windows-1251//IGNORE",$ex); */
			
			fwrite($log, "-\tException: ".$ex."\r\n\r\n");
			
		}finally{
			fclose($log);
			if(($stream !== false) && (!is_string($stream))){
				if($stream != null){
					fclose($stream);
				}
			}
		}
		
	}
?>