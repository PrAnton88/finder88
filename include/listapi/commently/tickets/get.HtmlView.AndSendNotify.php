<?php

/* это брат сниппета multiHandComment (133) */
// header('Content-type:text/html');
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{

	if(!$resolution){
		header("HTTP/1.1 403 Forbidden"); exit;
	}
	
	function checkPrivateParentComment(&$nParent){
		
		global $db;
		
		$comment = $db->fetchFirst("SELECT id, user_link, hidden, parent FROM comments WHERE hidden<2 AND id=$nParent",true);
		if( is_string($comment) && (strpos($comment,"error") !== false)){
			throw new ErrorException("SQL for get record comment has failed");
		}
		if(count($comment) == 0){ return false; }
		
		
		if((int)$comment["hidden"] == 1){
			return true;
		}
		
		if((int)$comment["parent"] == 0){
			return false;
		}
		
		if((int)$comment["parent"] > 0){
			return checkPrivateParentComment($comment["parent"]);
		}
		
		return false;
	}
	
	
	function getDateStr($dateval){
		
    	$months = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
  
    	if (($dateval == "") or ($dateval == "now")){
			$datetime = getdate();
    	}else{
			$datetime = getdate($dateval);
    	}
    	$mp="";
    	$sp="";
    	$dp="";
    	$dp_="";
    	if (intval($datetime['minutes']) < 10 ) $mp = "0";
    	if (intval($datetime['seconds']) < 10 ) $sp = "0";
    	if (intval($datetime['mon']) < 10 ) $dp = "0";
    	if (intval($datetime['mday']) < 10 ) $dp_ = "0";
    	$fdatestr = $datetime['mday']." ".$months[$datetime['mon']-1]." ".$datetime['year'];
    	$ftimestr = $datetime['hours'].":".$mp.$datetime['minutes'].":".$sp.$datetime['seconds'];
    	$fshortdate = $dp_.$datetime['mday'].".".$dp.$datetime['mon'].".".$datetime['year'];
    	$fsshortdate = $dp_.$datetime['mday'].".".$dp.$datetime['mon'].".".substr($datetime['year'],strlen($datetime['year'])-2,2);
    	return array ("fdatestr"=>$fdatestr, "ftimestr"=>$ftimestr, "shortdate"=>$fshortdate, "sshortdate"=>$fsshortdate);
    }
	
	function getHtmlPreviewComment($message,$prior){
		
		if($prior == 1){ $color = '#b38c16'; 
		}elseif($prior == 2){ $color = '#dc3545'; 
		}else{ $color = 'gray'; }
		/* ВЕРСТКУ НЕ ИЗМЕНЯТЬ, а если поменяете то измените и в lib.ToSend.Email.php */
		
		
		$message = str_replace('&','amp;',$message);
		
		return '`\<div style=\"border: 2px solid '.$color.'; width: 120px; display: block; margin:10px 30px; background-color: white; color: '.$color.';\" \>\<text\>'.($message).'\<\/text\>\<\/div\>`;';
		// return '`<div style="border: 2px solid '.$color.'; width: 120px; display: block; margin:10px 30px; background-color: white; color: '.$color.';" ><text>'.($message).'</text></div>`;';
	}
	
	/* use exam
	 
		dataReqNext({file:urlServerSide+'commently/tickets/get.HtmlView.AndSendNotify.php',type:'text',
			args:'dataRecord='+JSON.stringify({ncomment: 2356})},
			console.log);
			
	*/
	
	include "$path../headerBase.php";
	/* availabla for use $dataRecord */

	$ncomment = false;
	if(isset($dataRecord['ncomment'])){ $ncomment = (int)$dataRecord['ncomment']; }
	if($ncomment <= 0){ throw new ErrorException('arg ncomment is invalid'); }
	
	/* вытащить информацию о $nticket по $ncomment */
	
	
	$queryGetComment = "Select c.*, c.hidden as commentHidden From `comments` as c  LEFT JOIN `request` as r ON r.id = c.request ";
	$query = $queryGetComment." WHERE c.hidden<2 AND c.id = $ncomment";
	$message = $db->fetchFirst($query,true);
	if((is_string($message)) && (strpos($message,"error") !== false)){
		throw new ErrorException("SQL Error ");
	}
	if(count($message) == 0){
		throw new ErrorException("data comment is empty");
	}
	
	$hidden = $message['commentHidden'];
	
	
	$nticket = (int) $message['request'];
	if($nticket <= 0){ throw new ErrorException("arg nticket is invalid"); }
	
	$comment = $message['text'];
	$nParent = $message['parent'];
	
	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	// $modx->smarty->assign("pathAppChunk",$_SERVER['DOCUMENT_ROOT'].'/core/Smarty/chunks');
	//$smarty->assign("outside",true);
	
	
	require_once $path."lib/query/get.query.ListTickets.php";
	$query = getQueryToGetTickets();
		
	$query .= " WHERE a.id = $nticket";
		
		/* комментарий может делать только заявитель заявки или ответственный, или любой админ */
		/* поэтому */
	$message = $db->fetchFirst($query,true);
	if( is_string($message) && (strpos($message,"error") !== false)){
		throw new ErrorException("SQL for get record ticket has failed");
	}
	
	$admin = ($user['priority'] == 3);
	
	if(($message['applicant']!=$user['uid']) && ($admin == 0)){
		header("HTTP/1.1 403 Forbidden"); exit; /* доступ запрещен */
	}
	
	$smarty->assign("pathAppChunk",$_SERVER['DOCUMENT_ROOT'].'/core/Smarty/chunks');

	$id = $message['id'];
	
	$repliedly = ( (($admin != 0) or ($user['uid'] == $message['applicant']))?1:0 );
	
	
	$editable = true;/* только о том, выполнена ли заявка */
	
	$nparent = $nParent;
	$newComment = $ncomment;
	include $path.'helpdesk/getCommentItems.php';
	
	
	$display = '';
	$display .= '<script>message.print("Комментарий добавлен");';
	
	/* и оповещения */
	
	
	
	/* если родитель комментария был скрытым комментарием */
	/* и мы знаем что как скрытый комментарий, так и его потомков пользователь заявок не должен видеть */
	/* тогда, на комментарий, родитель которого был скрыт - мы не оповещаем инициатора заявки */
	/* поэтому, если комментарий не скрыт, и есть родитель, то
	инеративно выбираем родителей - до корня комментариев, или до ммоента, когда один из предков точно был скрытым
	выбираем данные родителя комментария, что бы узнать был ли он скрыт */
	
	
	$isPrivate = false;
	if(($hidden != 0) || (($hidden == 0) && ((int)$nParent > 0) && checkPrivateParentComment($nParent))){
		$isPrivate = true;
	}
	
	$display .= '(function(){';
		$display .= 'let completeSend = function (mess){ message.print(mess?mess:"Сообщение отправлено"); };';
		$display .= 'let oSender = oBaseAPI.message.email;';
		
		
		/*  */
		// $title = str_replace('"','\'',$title);
		// $comment = str_replace('"','\'',$comment);
		
		
		if(($title != '') && ($comment != '')){
			$title .= '<br />';
		}
		
		$title = str_replace("\\","\\\\",$title);
		$title = str_replace('"','\"',$title);
		$commonMessageComment = $user['fio'].':<br />- '.html_entity_decode(htmlspecialchars($title));
		
		$comment = str_replace("\\","\\\\",$comment);
		$comment = str_replace('"','\"',$comment);
		$commonMessageComment .= html_entity_decode(htmlspecialchars($comment));
		
		
		$header = $message["header"];
		$header = str_replace("\\","\\\\",$header);
		$header = str_replace('"','\"',$header);
	
		$commonMessageTicket = html_entity_decode(htmlspecialchars('№ '.$message["id"].' \"'.$header.'\"<br />'));
		
		
		
		
		$displayHeader = '(function(nrequest){let dataMessage = {};dataMessage.nrequest = nrequest;';
		$displayHeader .= 'dataMessage.subject = "Новый комментарий";';/* тема письма */
		// $displayHeader .= 'dataMessage.measure = "Новый комментарий";';/* первая строка в письме - заголовок (тема) */
		$displayHeader .= 'dataMessage.measure = `'.$dateReg.' добавлен новый комментарий к заявке`;';/* первая строка в письме - заголовок (тема) */
		$displayHeader .= 'dataMessage.description = `'.$commonMessageTicket.'`;';
		
		
		
		$displayFooter = '(dataMessage,function(){ completeSend(note); });})('.$nticket.');';
		
		
		/* иначе, на выходе из pho будет нечто вроде "строка сообщения "а тут кавычки"  " -
		что на клиенте не сможет быть интерпретирвоано с помощью javascript */
		
		
		
		/*
		$ashtml = (string)$ashtml;
		$ashtml = str_replace("\n","",$ashtml);
		$ashtml = str_replace("//","\/\/",$ashtml);//for url
		$ashtml = str_replace('"',"'",$ashtml);
		$ashtml = str_replace('\t','',$ashtml);
		$ashtml = str_replace('&','ampersand',$ashtml);//for url
		$display .= 'dataMessage.ashtml = "'.$ashtml.'";';
		*/
		
		$passCommentlyAssd = false;
		if($nParent != ''){
			/* заявитель и / или инициатор так и так будут оповещены (см ниже) */
			/* теперь выберем данные о пользователе который сделал родительский комментарий */
			/* и если родительский комментарий не от заявителя и не от инициатора */
			/* то оповестим ещё и его */
			$query = "SELECT user_link FROM comments WHERE id = $nParent";
			
			$messageParent = $db->fetchFirst($query,true);
			if( is_string($messageParent) && (strpos($messageParent,"error") !== false)){
				throw new ErrorException("SQL for get record ticket has failed");
			}
			
			if(
				($messageParent["user_link"] != $user['uid']) && /* если вы отвечаете не на свой комментарий */
				($messageParent["user_link"] != $message['applicant']) && /* если комментарий не комментарий заявителя */
				( ($message['assd'] == '') || ($messageParent["user_link"] != $message['assd']) )
				/* если нет ответственного на заявку или ответственный - не автор комментария */
			){
				
				/* необходимо узнать и права того сотрудника */
				/* отправляем если
				комментарий не скрытый ИЛИ сотрудник админ */
				
				$queryUserData .= " AND u.id=".$messageParent["user_link"];
				$userData = $db->fetchAssoc($queryUserData,true);
					
				if((!$isPrivate) || ($userData["priority"] == 3)){
				
					$display .= $displayHeader;
					/*
					$display .= 'dataMessage.description = "'.$commonMessageTicket.' На ваш комментарий (некоторый конкретный комментарий),";';
					*/

					$display .= 'dataMessage.ashtml = '.getHtmlPreviewComment($commonMessageComment,$message['prior']);
					/* $display .= 'dataMessage.note = "Комментирование вашего комментария";'; */
					$display .= 'dataMessage.ncomment = '.$nParent.';';
					
					/* это может быть как ответственный так и любой другой админ - потенциальный ответственный */
					$display .= 'let note = "Комментирование комментария";';
					$display .= 'oSender.sendToAuthorTicketComment'.$displayFooter;
					
					/* если этот сотрудник - Ответственный заявки, то $passCommentlyAssd = true */
					if(($message["assd"] != '') && ($messageParent["user_link"] == $message["assd"])){
						$passCommentlyAssd = true;
					}
					
				}
			}
		}
		
		if($message['applicant']==$user['uid']){
			/* оставил коммент - заявитель (хоть админ он хоть не админ) */
			/* оповестим Ответственного - если назначен ответстыенный на заявку */
			
			if($message['assd'] != ''){
				/* если был комментарий комментария Ответственного, то ответственный уже оповещен - выше */
				/* это нужно узнать */
				if(!$passCommentlyAssd){
				
					
					$display .= $displayHeader;
					/*
					$display .= 'dataMessage.description = "'.$commonMessageTicket.'";'; */
					
					$display .= 'dataMessage.ashtml = '.getHtmlPreviewComment($commonMessageComment,$message['prior']);
					/* $display .= 'dataMessage.note = "от заявителя заявки";'; */
				
					$display .= 'let note = "Оповещение ответственному";';
					$display .= 'oSender.sendToAssdTickets'.$displayFooter;
					
				}
			}
			
		}elseif($admin != 0){
			/* оставил коммент кто то из админов - возможно ответственный, или любой другой */
			
			
			if($message['assd']==$user['uid']){
				/* Ответственный на заявку */
				
				/* если коммент не приватен и заявитель не админ */
				/* или заявитель админ */
				
				if(($message['applicantPrior'] == 3) || ((!$isPrivate) && ($message['applicantPrior'] != 3))){
					/* оповестим заявителя */
					
					$display .= $displayHeader;
					
					/*
					$display .= 'dataMessage.description = "'.$commonMessageTicket.'";';
					*/
					
					$display .= 'dataMessage.ashtml = '.getHtmlPreviewComment($commonMessageComment,$message['prior']);
					/* $display .= 'dataMessage.note = "от исполнителя заявки";'; */
					
					$display .= 'let note = "Оповещение заявителю";';
					$display .= 'oSender.sendToApplicantTickets'.$displayFooter;
					
				}
			}else{
				/* "Сторонняя помощь" */
				/* 1. оповестим ответственного (как минимум ответственный должен быть с правами админа) */
				/* 2. оповестим заявителя - если коммент не приватен и заявитель не админ или заявитель админ */
				
				/* 1. оповестим ответственного - только если он есть */
				if($message['assd'] != ''){
					
					$display .= $displayHeader;
					
					/*
					$display .= 'dataMessage.description = "'.$commonMessageTicket.'";';
					*/

					$display .= 'dataMessage.ashtml = '.getHtmlPreviewComment($commonMessageComment,$message['prior']);
					/* $display .= 'dataMessage.note = "предположительно помощь/совет";'; */
					
					$display .= 'let note = "Оповещение ответственному";';
					$display .= 'oSender.sendToAssdTickets'.$displayFooter;
					
				}
				
				
				if(($message['applicantPrior'] == 3) || ((!$isPrivate) && ($message['applicantPrior'] != 3))){
					/* 2. оповестим заявителя */
					
					$display .= $displayHeader;
					
					/*
					$display .= 'dataMessage.description = "'.$commonMessageTicket.'";';
					*/
					
					$display .= 'dataMessage.ashtml = '.getHtmlPreviewComment($commonMessageComment,$message['prior']);
					/* $display .= 'dataMessage.note = "предположительно помощь/совет";'; */
					
					$display .= 'let note = "Оповещение заявителю";';
					$display .= 'oSender.sendToApplicantTickets'.$displayFooter;
					
					
				}
				
				
				
				
			}
		}
		
	
	$display .= '})();';
	$display .= '</script>';
	
	
	echo $display;
	
	header("HTTP/1.1 200 Ok");
	exit;
	
}catch(ErrorException $ex){
	/* если application/json 
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	*/

	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	
}catch(Exception $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>