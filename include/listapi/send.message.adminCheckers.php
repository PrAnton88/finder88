<?php

header('Content-type:application/json');
// header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

	require_once "../mattach.php";
	// require_once "../xmpp/funcToSendMessAsXMPP.php";

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

function toComOrMail($db,$nomb,$thisIsRole){
	
	$table = 'send_options';
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
			/* тип когда о новой заявке вне техподдержки */
			$def = true;
			break;
		}
	}
	
	//echo "$nomb) $msg <br />";
	
	
	if((!$def) && ($result = $db->fetchFirst("SELECT $fieldSt, $fieldEnd FROM $table WHERE User_id=$thisIsRole"))){
		return convretDataSendOption($result["$fieldSt"],$result["$fieldEnd"],true);
	}
	
	/* по умолчанию, когда default например  */
	return array("default"=>"search to role = $thisIsRole","subCom"=>1,"subMail"=>1);
}

function replaceCom($str){
	$str = str_replace(array("<br />","<br/>","<br>","<tr>","</p>"),"\n",str_replace(array("этой ссылке","ссылке","'>","'/>","\"/>","\">","<html>","</html>","</ html>","<head>","</head>","</ head>","<body>","</body>","</ body>","<b>","</b>","</ b>","<a href= '","<a href= \"","<a href ='","<a href =\"","<a href='","<a href=\"","<a href = '","<a href = \"","</a>","</ a>","< /a>"),"",$str));
	$str = str_replace(array("<span>","</span>","<text>","</text>","onclick","<table>","</table>","<thead>","</thead>","<tbody>","</tbody>","<td>","</td></tr><p>")," ",$str);
	return $str;
}

function replaseCom($str){
	return replaceCom($str);
}

class Loger{
	
	var $arrToSerialize = array();
	var $arrEl = array();
	var $strComplete = "\n log ";
	var $fileToWriteLog = "../logs/logDefault.txt";
	
	function Loger($nameFile,$fileToLoger = false){
		$this->strComplete .= $nameFile." ".date("d-m-Y H:i:s")."\r\n ";
		if($fileToLoger !== false) $this->fileToWriteLog = $fileToLoger;
	}
	function pushS($el){
		$this->arrToSerialize[] = $el;
	}
	function push($el){
		$this->arrEl[] = $el."\r\n";
	}
	
	/* ошибка если файл не существует */
	function openFile($filestr){
		$log=fopen($filestr,"a+");
		chmod($filestr,755);
		return $log;
	}
	
	function writeLog(){
		$filestr = $this->fileToWriteLog;
		
		try{
			if(!file_exists($filestr)) $filestr = "../".$filestr;
			if(file_exists($filestr)){
				
				$log = $this->openFile($filestr);
				
				fwrite($log, $this->strComplete);
				
				fclose($log);
				return true;
			}else return false;
		} catch (Exception $e){
			//throw new Exception($e->getMessage());
			return false;
		}
	}
	function buildMess(){
		if(count($this->arrToSerialize)>0){
			foreach($this->arrToSerialize as $item){
				$this->strComplete .= serialize($item)."\r\n";
			}
		}
		if(count($this->arrEl)>0){
			foreach($this->arrEl as $item){
				$this->strComplete .= $item;
			}
		}
		$this->strComplete .= "\r\n";
	}
	function run(){
		$this->buildMess();
		return $this->writeLog();
	}
	
	/*use 
		//$Loger = new Loger("funcToMail.php","../logs/logmailTest.txt");
		$Loger = new Loger("funcToMail.php");
		$Loger->pushS($mailt);
		
		$Loger->push($completeSendToSpark);
		$Loger->push($sendmess);
		
		return $Loger->run();
	*/
	
}

function buildMessLog($mailt,$sendmess,$completeSendToSpark,$n){
	
	$Loger = new Loger("funcToMail.php","../logs/logmail.txt");
	
	$Loger->pushS($mailt);
	$Loger->push($completeSendToSpark);
	$Loger->push($sendmess);
	
	return $Loger->run();
}

function useLoger($mailt,$sendmess,$completeSendToSpark){
	
	$Loger = new Loger("funcToMail.php","../logs/logmail.txt");
	
	$Loger->pushS($mailt);
	$Loger->push($completeSendToSpark);
	$Loger->push($sendmess);
	
	//return $Loger->run();
	return $Loger;
}

$mmail = new mime_mail();

function sendOnlyEmail($mailt,$subject,$sendmess){
	
	global $mmail;
	try{
		
		$mmail->parts = array();
		/*отправляем на почту*/
		
		$mailt = str_replace("\n","",str_replace("\n\r","",str_replace(" ","",$mailt['email'])));
		$mmail->from = "helpdesk@nkng.vrn.ru";
		$mmail->headers = "Errors-To: [EMAIL=helpdesk@nkng.vrn.ru]helpdesk@nkng.vrn.ru[/EMAIL]";
		$mmail->to = $mailt;
		$mmail->subject = ($subject?$subject:"");
		$mmail->body = $sendmess;
	
	
		$mmail->send();
		
	} catch (Exception $e){
		
		
		throw new ErrorException($e->getMessage());
	}

}

function fsendEmail($mailt,$n,$subject,$sendmess,$sendCom=false){

	//echo " -- $sendmess <br /> ";
	//if(($n < 1) || ($n > 3)) $n = 2;
	
	//print_br("n = $n");
	/*$n = 1 или 2 или 3*/
	/*1 - оповещени¤ о назначении ответственным*/
	/*2 - оповещени¤ о новых за¤вках*/
	/*3 - оповещени¤ об изменениях к заявке*/
	//toComOrMail($db,$n,811);
	
	global $user;
	global $db;
	
	$completeSendToSpark = " sendCom is no much found. ";
	if($sendCom === false) $sendCom = replaceCom($sendmess);
	else $completeSendToSpark = " sendCom is found. ";
		
	$role = (int)(isset($mailt['nrole'])?$mailt['nrole']:(isset($mailt['role'])?$mailt['role']:(isset($mailt['userid'])?$mailt['userid']:$mailt['id'])));


	$opts = toComOrMail($db,$n,$role);
	

	if($opts['subMail'] == 1){
		sendOnlyEmail($mailt,$subject,$sendmess);
	}
	
	
	
	/* запись лога - случай успеха, не возникло ни одной ошибки */
	//return  buildMessLog($mailt,$sendmess,$completeSendToSpark,$n);
	$useLoger = useLoger($mailt,($subject." -- ".$sendmess),$completeSendToSpark);
	$useLoger->pushS($opts);
	$useLoger->push(" n =$n ");
	return $useLoger->run();
	
}


if($resolution){
	
	try{
		
		if(!isset($_POST['dataRecord'])){
			throw new ErrorException("Нет даннх для записи");
		}
		
		
		$dataOutput = array();
		$dataOutput["fio"] = $user["fio"];
		
		$id = $user["uid"];
		
		
		
		$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
		$dataRecord = json_decode($dataRecord,true);
		
		
		$subject = str_replace('\'','"',$dataRecord['title']);
		$message = str_replace('\'','"',$dataRecord['mess']);
		
		$subject = str_replace("\\","/",$subject);
		$message = str_replace("\\","/",$message);
		
		$description = 'Получено сообщение. ';
		
		$mailt = array('login'=>'oto016','fio'=>"Pr. Anton",'email'=>'prudnikov@kng.vrn.ru','userid'=>811);
		
		fsendEmail($mailt,2,$subject,$message);
			
			
			
			
			
			
			
			
		$dataOutput = json_encode($dataOutput);
		
		$display = '{"success":1,"description":"'.$description.'","data":'.$dataOutput.'}';
		echo $display;
		
		header("HTTP/1.1 200 Ok");
		
		
	}catch(ErrorException $ex){
		
		echo '{"success":0,"description":"'.exc_handler($ex).'"}';
		
		/* если text/html 
		echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
		*/
	}


}else{ 

	header("HTTP/1.1 403 Forbidden");
}
?>