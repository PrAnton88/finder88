<?php

/* это брат сниппета multiHandComment (133) */
// header('Content-type:text/html');
header('Content-type:text/html; charset=UTF-8');

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
		
		$comment = $db->fetchFirst("SELECT id, user_link, hidden, parent FROM comments WHERE id=$nParent",true);
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
	
	
	
	function getHtmlPreviewComment($message,$prior){
		
		if($prior == 1){ $color = '#b38c16'; 
		}elseif($prior == 2){ $color = '#dc3545'; 
		}else{ $color = 'gray'; }
		/* ВЕРСТКУ НЕ ИЗМЕНЯТЬ, а если поменяете то измените и в lib.ToSend.Email.php */
		
		return '"\<div style=\"border: 2px solid '.$color.'; width: 120px; display: block; margin:10px 30px; background-color: white; color: '.$color.';\" \>\<text\>'.$message.'\<\/text\>\<\/div\>";';
	}
	
	/* use exam
	
		dataReqNext({file:urlServerSide+'commently/tickets/set.record.get.HtmlView.php',type:'json'},console.log); 
		dataReqNext({file:urlServerSide+'commently/tickets/set.record.get.HtmlView.php',type:'json',args:'dataRecord='+JSON.stringify({title: 'test title',
            	        comment: 'test comment value',
            	        hidden: 0,
            	        nresource: 4579,
            	        nparent: 0})},console.log);
	*/
	
	if(!isset($_POST['dataRecord'])){
		throw new ErrorException("Нет данных для записи"); exit;
	}
	
	$dataOutput = array();
	// $id = $user["uid"];
	
	$description = 'Получено сообщение. ';
	
	$dataRecord = html_entity_decode(htmlspecialchars($_POST['dataRecord']));
	$dataRecord = json_decode($dataRecord,true);
	
	/*
	title: 
    comment: 
    hidden:
    nresource: 
    nparent:
	*/
	
	$title = '';
	if(isset($dataRecord['title'])){
		$title = str_replace('\'','"',$dataRecord['title']);
		$title = str_replace("\\","/",$title);
	}
	
	$comment = '';
	if(isset($dataRecord['comment'])){
		$comment = str_replace('\'','"',$dataRecord['comment']);
		$comment = str_replace("\\","/",$comment);
	}
	
	$nRecord = false;
	if(isset($dataRecord['nresource'])){
		$nRecord = (int)$dataRecord['nresource'];
		/* if($nRecord == 0){ $nRecord = false; } */
	}
	if(($nRecord == 0) || $nRecord == 'false'){
		$nRecord = false; /* номер ресурса - в данном случае это номер заявки */
	}

	$nParent = 'NULL'; /* комментарий какого комментария, или комментарий не комментария */
	if(isset($dataRecord['nparent'])){
		$nParent = (int)$dataRecord['nparent'];
		/* if($nRecord == 0){ $nRecord = false; } */
	}

	$hidden = (int)$dataRecord['hidden'];
	
	
	/* тест отказа */
	/*$checkedDevicesTypes = array();
	if(count($checkedDevicesTypes) == 0){
		throw new ErrorException("Данные о комментарии не были переданы");
		exit;
	}*/
	
	
	

	/* новая запись */
	require_once $path."lib/query/get.query.ListTickets.php";
	$query = getQueryToGetTickets();
		
	$query .= " WHERE a.id = $nRecord";
		
		/* комментарий может делать только заявитель заявки или ответственный, или любой админ */
		/* поэтому */
	$message = $db->fetchFirst($query,true);
	if( is_string($message) && (strpos($message,"error") !== false)){
		throw new ErrorException("SQL for get record ticket has failed");
	}
	
	if(is_array($message) && (count($message)==0)){
		throw new ErrorException("ticket for create record comment is not found");
	}
	
	if($message['hidden'] == 1){
		throw new ErrorException("Нельзя комментировать удаленную заявку");
	}
	
	
	$admin = ($user['priority'] == 3);
	
	if(($message['applicant']!=$user['uid']) && ($admin == 0)){
		header("HTTP/1.1 403 Forbidden"); exit; /* доступ запрещен */
	}
	
	/* комментарии hidden должны приходить только от админов */
	if(($hidden != 0) && ($admin == 0)){
		header("HTTP/1.1 403 Forbidden"); exit; /* доступ запрещен */
	}
	
	if($comment == ''){
		throw new ErrorException("arg comment is empty!");
	}
	
	
	$dateReg = date("Y-m-d H:i:s");
	

	$nCommon = $db->insert("comments", array(), array('request'=>$nRecord,'user_link'=>$user["id"],'text'=>$comment,'date_reg'=>$dateReg,'hidden'=>$hidden,'head'=>$title, 'parent'=>$nParent));
	if( is_string($nCommon) && (strpos($nCommon,"error") !== false)){
		throw new ErrorException("SQL Error. comments push has failed.");
	}
	
	/* и подумать об оповещении при добавленном комментарии */


	require_once "$path../config.modx.php";
	$modx->resource = $modx->getObject('modResource',45);

	$outputFunc = $modx->runSnippet('func');
	$dateReg = $outputFunc["getDateStr"]("now");
	$dateReg = $dateReg["fdatestr"].' '.$dateReg["ftimestr"];


	/* 3. сразу выведем отображеине */
	$chunk = $modx->getObject('modChunk',array(
		'name' => 'commentItem'
	));
	
	$display = '';
	
	if(!$chunk){
		throw new ErrorException("chunk commentItem is not found!");
	}
	
	if($nParent == 0){
		$nParent = '';
	}
	
	
	
	// $nRecord, /* номер заявки или номер ресурса */
	/* к сожалению частично повторяет снипет getCommentItems */
	
	$comment = str_replace("\n","<br/>",$comment);
	
	$ashtml = $chunk->process(array(
		'id' => $nRecord,
		'nparent' => ($nParent && ( ((int) $nParent) != 0))?1:0,
		'author' => $user["fio"],
		'datetime' => $dateReg,
		'content' => $comment,
		'title' => $title,
		'hidden' => $hidden,
		'ncommon' => $nCommon,
		'type' => 'tickets',
		'editable' => 1,
		'repliedly' => 1,
		'avatar' => $user['avatar']
	));

	if($nParent && ( ((int) $nParent) != 0)){
		$display .= '<ol class="children tr">';
	}



	$display .= '<li class="comment odd alt thread-even depth-1 tr">';
	$display .= $ashtml;
	$display .= '</li>';
	if($nParent && ( ((int) $nParent) != 0)){
		echo '</ol>';
	}
	
	
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
		$displayHeader .= 'dataMessage.measure = "'.$dateReg.' добавлен новый комментарий к заявке";';/* первая строка в письме - заголовок (тема) */
		$displayHeader .= 'dataMessage.description = "'.$commonMessageTicket.'";';
		
		
		
		$displayFooter = '(dataMessage,function(){ completeSend(note); });})('.$nRecord.');';
		
		
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