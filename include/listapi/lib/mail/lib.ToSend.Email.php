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
			
$display = '<html>'.$styleHead.'<body>
<div style="float: left; white-space: pre; line-height: 1; background: #D3DBDE;">';
$display .= $sendmess;
			
$display .= '</div>
<div style="width:100%;padding:1%;background-color:#6699CC;height=35px;">
        <font style="font-size:12px;font-family:Tahoma;color:white;"> � ��� ��� �������-�����-��� '.date("Y").'</font>
    </div>
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
		
		$opts = array(
          'http'=>array(
            'method'=>"POST",
            'header'=>"Accept: */*\r\n".
					  "Accept-Encoding: gzip, deflate\r\n".
					  "Accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7\r\n".
                      "Cookie: modxcsessid=".$_COOKIE['modxcsessid']."\r\n".
                      "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n",
            'content' => $postdata,
            'timeout' => 20
          )
        );
        
        $context = stream_context_create($opts);
        
        // Open the file using the HTTP headers set above
        $file = file_get_contents(
           'http://localhost/xmpp/send.Test.php',
            false, 
            $context
        );
        
        return $file;
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
		return array("default"=>"search to role = $thisIsRole","subCom"=>1,"subMail"=>1);
	}
	
	function replaceHtmlChars($str){
		
		/* ����� ���� ��� */
		
		$str = str_replace("<b>","",$str);
		$str = str_replace("</b>","",$str);
		$str = str_replace("<br /><br /><br />","<br /><br />",$str);
		$str = str_replace("<br />","\n",$str);
		
		
		/* strip_tags($str, "<a>"); */
		
		
		
		$posStartLinkEmail = null;
		if(($posStartLinkEmail = strpos($str,"<a href='mailto:")) && ($posStartLinkEmail !== false)){
			
			
			//$str = str_replace("<a href='mailto:","",$str);
			
			$LinkEmail = substr($str, $posStartLinkEmail, (strpos($str,"'>")+2-$posStartLinkEmail));
			
			$str = str_replace($LinkEmail,"",$str);
			$str = str_replace("</a>","",$str);
			
		}
		$str = str_replace('�� <a href=\'http','�� ������ http',$str);
		$str = str_replace("'>���� ������","",$str);
		
		
		$str = str_replace(array("normalText","italicBoldNote","forNotes","importantString"),"",$str);
		$str = str_replace('href="http://info:86/index.php?id=68"','',$str);
		$str = str_replace('<a class="" >','',$str);
		$str = str_replace('<span class="">','',$str);
		$str = str_replace("</span>","",$str);
		
		return $str;
	}
	
	function sendMessage($db,$mailt,$subject,$n,$sendmess,$sendCom=false){
		/* ���������� ����������� - �� ����� ��� � ����� */
		
		/*$n = 1 ��� 2 ��� 3*/
		/*1 - ��������� � ���������� �������������*/
		/*2 - ��������� � ����� ������*/
		/*3 - ��������� �� ���������� � ������*/
		
		$log=fopen($_SERVER['DOCUMENT_ROOT']."/custom/logs/anyEvent.txt","a+");
		try{
			
			if(!isset($mailt['email'])){ throw new Exception('arg email is not found'); }
			$email = str_replace("\n","",str_replace("\n\r","",str_replace(" ","",$mailt['email'])));
			
			
			fwrite($log, date("d-m-Y H:i:s")."\r\n-\tnew message to {$email}\r\n");
			fwrite($log, "-\t".$sendmess."\r\n");
			
			
			if(!isset($mailt['role'])){ throw new Exception('arg role is not found'); }
			if(!isset($mailt['login'])){ throw new Exception('arg login is not found'); }
			
			$opts = routeBacket($db,$n,$mailt['role']);
			if(!$opts){ throw new Exception('setting route for get method delivery to recipient is empty'); }
			
			fwrite($log, "\topts=".json_encode($opts)."\r\n");
			
			
			// � ��������� ��������� � �������� ���� ������ �� �������������� (���� �� ���� �������)
			// $subject = iconv("utf-8","windows-1251",$subject);
			
			$sendmess = iconv("utf-8","windows-1251",$sendmess);
			
			if($opts['subMail']==1){
				
				fwrite($log, "- Send to Mail....");
				
				if(!sendEmail($email,$subject,$sendmess)){
					throw new Exception(" send to email is invalid ");
				}
				
				fwrite($log, "complete\r\n");
			}
			
			if($opts['subCom']==1){
				
				if(!$sendCom){ 
					$sendCom = $sendmess; 
					/* ������ ���������� � utf-8 � �� ���� ��� ����� �������������� � windows-1251 */
					/* ������������ ������� */
					
					
					$sendCom = replaceHtmlChars($sendCom);
					
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