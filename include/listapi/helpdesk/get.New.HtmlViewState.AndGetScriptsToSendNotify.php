<?php

/* это брат сниппета multiHandComment (133) */
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";
	
try{
	
	/* use exam
	
		let dataRecord = {nstate:1,nassd:811,nrequest:4434};
	
		dataReqNext({file:urlServerSide+'helpdesk/get.New.HtmlViewState.php',type:'text',
		args:'dataRecord='+JSON.stringify(dataRecord)},
			console.log
		);
	
		тест отказа 
		throw new ErrorException("Данные о комментарии не были переданы");
	
	
		// необходимые варианты тестирования
		1. В только что созданной заявке назначается Ответственный
		1.2. Ответственный меняется, принимает  значение - Не заполнено
		1.3. Ответственный меняется, принимает значение Не Не заполнено. Дважды
		2.  Статус принимает занчение - Открыта.
		2.1 Статус принимает значение - Принята.
		2.2 Статус - снова Открыта.
		(3. Изменение приоритета.)
		4. Статус - Выполнена.
	
	*/

	
	include "$path../headerBase.php";
	/* next use $dataRecord */
	
	
	if(!isset($dataRecord['nrequest'])){
		throw new ErrorException("arg nrequest is not found");
	}
	
	$nrequest = false;
	$nrequest = (int)$dataRecord['nrequest'];
	
	if($nrequest == 0){
		throw new ErrorException("arg nrequest is invalid");
	}
	
	if(!isset($dataRecord['nassd'])){
		throw new ErrorException("arg nassd is not found");
	}
	
	$nassd = false;
	$nassd = (int)$dataRecord['nassd'];
	
	if(!isset($dataRecord['nstate'])){
		throw new ErrorException("arg nstate is not found");
	}
	
	$nstate = false;
	$nstate = (int)$dataRecord['nstate'];
	
	$npriority = false;
	$npriority = (int)$dataRecord['npriority'];
	
	/* (данные в заявке уже изменены) */
	require_once $path."/lib/query/get.query.ListTickets.php";
    $query = getQueryToGetTickets()." WHERE a.id=".$nrequest;
	
	
	$existmess = $db->fetchFirst($query,true);
	if((is_string($existmess)) && (strpos($existmess,"error") !== false)){
		throw new ErrorException("SQL Error"); exit;
	}
	
	if(count($existmess) == 0){
		throw new ErrorException('Информация о заявке не найдена');
	}
	
	
	
	
	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	
	$display = '';
	if(($existmess["assd"] == $user["uid"]) || (($existmess["assd"] == '') && ($user['priority'] == 3))){
	
		$smarty->assign('nres',$nrequest);
		$smarty->assign('nassd',$existmess["assd"]);/* теперь ответственный */
	
		$display .= $smarty->display('../chunks/components/tmp.htmlListState-min.tpl');
	}else{
		
		$smarty->assign("state",$existmess['state']);
		$display .= $smarty->display('../chunks/components/tmp.infoState.tpl');
	}
	
	
	/* Внимание!!! только при изменении статуса проводить оповещения 1. Ответственного, 2. Нового Ответственного !! */
	
	
	$display .= '<script>(function(){thrower(function(){';
	/* $display .= 'let dataMessage = {};'; */
	$display .= 'let progressSendly = new oProgress("notify about Change State"); progressSendly.begin();';
	$display .= 'let itemSetNotifyEvent = function(){
		if(listNotifyEvent.length > 0){
			let item = listNotifyEvent.shift();
			oTimeExecuter.push(function(){
				item('.$nrequest.');
			});
		}
		if(oTimeExecuter.isEmpty()){
			progressSendly.end();
		}
	};';
	$display .= 'let listNotifyEvent = [];';
	$display .= 'let completeSend = function (mess){ 
		if(mess){
			message.print(mess); 
		}
		
		itemSetNotifyEvent();
		
	};';
	$display .= 'let oSender = oBaseAPI.message.email;';
	
	/* $display .= 'dataMessage.nrequest = '.$nrequest.';';
	$display .= 'dataMessage.subject = "ticket change state";'; */
	
	$header = $existmess["header"];
	$header = str_replace("\\","\\\\",$header);
	$header = str_replace('"','\"',$header);
	
	$commonMessage = html_entity_decode(htmlspecialchars('<br />№ '.$existmess["id"].' \"'.$header.'\"<br />'));
	/* $display .= 'message.print("'.$nassd.' '.$existmess["assd"].'");'; */
	
	
	/* $nassd - предыдущий ответственный */
	/* $nstate - предыдущий статус */
	/* $npriority - предыдущий приоритет */
	
	$passAssd = false;
	if($nassd != 0){
		
		/* информация о предыдущем ответственном по $nassd */
		$queryUserData .= " AND u.id = $nassd";

		$passAssd = $db->fetchFirst($queryUserData,true);
		if((is_string($passAssd)) && (strpos($passAssd,"error") !== false)){
			throw new ErrorException("SQL Error"); exit;
		}

		if(!is_array($passAssd)){
			throw new ErrorException('passAssd is not array');
		}

		if(count($passAssd) == 0){
			throw new ErrorException('passAssd is not found');
		}
	}
	
	
	
	if(($npriority != $existmess['prior']) && ((int)$existmess["assd"] != 0)){
		
		/* значит установлен ответственный, и  после этого изменили приоритет */
		/* если этот приотритет изменил не тот кто  ответственный */
		
		if($user["uid"] != $existmess["assd"]){
		
			$listPrior = ["Низкий","Средний","Высокий"];
			/* то отовещаем Ответственного об изменении приоритета заявки */
		
			$display .= 'listNotifyEvent.push(
				(function(nrequest){
					let dataMessage = {};
					dataMessage.nrequest = nrequest;
					dataMessage.subject = "Новый приоритет заявки";
					let note = "Оповещение ответственному";
					dataMessage.description = "'.$existmess["assdShort"].', для заявки '.$commonMessage.'Новый приоритет - '.$listPrior[$existmess['prior']].'";
					oSender.sendToAssdTickets(dataMessage,function(){ completeSend(note); });
				})
			);';
			$display .= '
				itemSetNotifyEvent();
			});})();';
			$display .= '</script>';
			
			echo $display;
		}else{
			/* $display .= 'message.print("Сам Ответственный изменил приоритет");';
			
			$display .= '});})();';
			$display .= '</script>';
			
			echo $display; */
		}
		
		header("HTTP/1.1 200 Ok");
		exit;
	}
	
	/* предыдущий равен существующему ? */
	if($nstate == $existmess["state"]){
		/* статус не изменен */
		
		if($nassd != $existmess["assd"]){
			/* изменен Ответственный */
			
			/* 1. заявителю о том что был назначен НОВЫЙ ответственный */
			/* 2. ответственному о том что он теперь ответственный на эту заявку */
			/* 3. бывшему ответственному о том что он теперь НЕ ответственный на эту заявку */
			
			
			
			if((int)$existmess["assd"] != 0){
				/* неважно то какой был ответственный - но если сейчас 0, то уже нет ответственно */
				if((int)$existmess["applicant"] != 0){
					
					$display .= 'listNotifyEvent.push(
						(function(nrequest){
						let dataMessage = {};
						dataMessage.nrequest = nrequest;';
					if($nassd != 0){
						
						/* 1. отменен предыдущий ответственный и назначен новый */
						/* $display .= 'dataMessage.subject = "Change in your ticket";';*/
						$display .= 'dataMessage.subject = "Изменения в заявке";
							dataMessage.measure = "Изменен ответственный на вашу заявку";
							let note = "('.$passAssd["fi"].' больше не ответственный)";
							dataMessage.description = "На вашу заявку '.$commonMessage.'Назначен ДРУГОЙ ответственный - '.$existmess["assdShort"].'";';
						
					}else{
						/* 1. - назначен ответственный на заявку */
						/* $display .= 'dataMessage.subject = "Your ticket is in progress";'; */
						$display .= 'dataMessage.subject = "Ваша заявка принята";
									dataMessage.measure = "Ваша заявка принята!";
									let note = "сообщение заявителю";
									dataMessage.description = "'.$existmess["assdShort"].' назначен ответственным на вашу заявку '.$commonMessage.'";';
					}
					$display .= 'oSender.sendToApplicantTickets(dataMessage,function(){ completeSend(false); });
					}));';
				}
				/* 2. */
				$display .= 'listNotifyEvent.push(';
				$display .= '(function(nrequest){';
				$display .= 'let dataMessage = {};';
				$display .= 'dataMessage.nrequest = nrequest;';
				/* $display .= 'dataMessage.subject = "New ticket";'; */
				$display .= 'dataMessage.subject = "О назначении ответственным";';
				/* $display .= 'dataMessage.measure = "Новая заявка!";'; */
				$display .= 'let note = "Оповещение ответственному";';
				$display .= 'dataMessage.description = "'.$existmess["assdShort"].', Вам назначена заявка '.$commonMessage.'";';
				$display .= 'oSender.sendToAssdTickets(dataMessage,function(){ completeSend(note); });';
				$display .= '})';
				$display .= ');';
				
				/* 3. оповещение предыдущему ответственному */
				if($passAssd){
					$display .= 'listNotifyEvent.push(';
					$display .= '(function(nrequest){';
					$display .= 'let dataMessage = {};';
					$display .= 'dataMessage.nrequest = nrequest;';
					/* $display .= 'dataMessage.subject = "Exempt from doing";'; */
					$display .= 'dataMessage.subject = "О переназначении заявки ";';
					/* $display .= 'dataMessage.measure = "О переназначении заявки ";'; */
					/* $display .= 'dataMessage.note = "Оповещение предыдущему ответственному";'; */
					$display .= 'let note = "Оповещение предыдущему ответственному";';
					$display .= 'dataMessage.description = "'.$passAssd["fi"].', выполнение заявки '.$commonMessage.'переназначено другому ответственному";';
					$display .= 'oSender.sendToAnyUser(dataMessage,[{email:"'.$passAssd["email"].'",login:"'.$passAssd["login"].'",role:"'.$passAssd["role"].'"}],function(){ completeSend(note); });';
					$display .= '})';
					$display .= ');';
				}
				
				
			}else{
				/* невозможная ветка - статус не изменен, ответственный изменен, но новый ответственный равен 0 */
				$display .= 'message.print("невозможная ветка");';
				
			}
			
		}/*else{
			/* невозможная ветка - статус не изменен, ответственный не изменен
		}*/
	}else{
	
		$display .= 'message.print("Статус изменен");';
	
		if($existmess["state"] == 2){
			$display .= 'message.print("Заявка выполнена");';
			/* закрыли заявку - установили статус Выполнена */
			/* 1. заявителю */
			if((int)$existmess["applicant"] != 0){
				$display .= 'listNotifyEvent.push(';
				$display .= '(function(nrequest){';
				$display .= 'let dataMessage = {};';
				$display .= 'dataMessage.nrequest = nrequest;';
				/* $display .= 'dataMessage.subject = "Your ticket has been completed";'; */
				$display .= 'dataMessage.subject = "Ваша заявка выполнена";';
				$display .= 'let note = "Оповещение заявителю";';
				$display .= 'dataMessage.description = "Ваша заявка '.$commonMessage.' ВЫПОЛНЕНА";';
				$display .= 'oSender.sendToApplicantTickets(dataMessage,function(){ completeSend(false);  });';
				$display .= '})';
				$display .= ');';
			}
		}else{
			
			/* оповещения */
			/* 1. заявителю о том что был назначен ответственный */
			/* 2. ответственному о том что он теперь ответственный на эту заявку */
			
			if($existmess["assd"] != ''){
				
				/* 1. заявителю */
				if((int)$existmess["applicant"] != 0){
					$display .= 'listNotifyEvent.push(';
					$display .= '(function(nrequest){';
					$display .= 'let dataMessage = {};';
					$display .= 'dataMessage.nrequest = nrequest;';
					/* $display .= 'dataMessage.subject = "Your ticket is in progress";'; */
					$display .= 'dataMessage.subject = "Ваша заявка принята";';
					$display .= 'dataMessage.measure = "Ваша заявка принята";';
					$display .= 'let note = "Оповещение заявителю";';
					$display .= 'dataMessage.description = "На вашу заявку '.$commonMessage.'назначен ответственным '.$existmess["assdShort"].'";';
					$display .= 'oSender.sendToApplicantTickets(dataMessage,function(){ completeSend(false); });';
					$display .= '})';
					$display .= ');';
				}
				/* 2. ответственному */
				$display .= 'listNotifyEvent.push(';
				$display .= '(function(nrequest){';
				$display .= 'let dataMessage = {};';
				$display .= 'dataMessage.nrequest = nrequest;';
				/* $display .= 'dataMessage.subject = "New ticket";'; */
				$display .= 'dataMessage.subject = "О назначении ответственным";';
				/* $display .= 'dataMessage.measure = "Новая заявка!";'; */
				$display .= 'let note = "Оповещение ответственному";';
				$display .= 'dataMessage.description = "'.$existmess["assdShort"].', Вам назначена заявка '.$commonMessage.'";';
				$display .= 'oSender.sendToAssdTickets(dataMessage,function(){ completeSend(note); });';
				$display .= '})';
				$display .= ');';
			}elseif($passAssd){
				/* $passAssd скорее всего есть, потому что  */
				
				
				/* 1. заявителю */
				if((int)$existmess["applicant"] != 0){
					$display .= 'listNotifyEvent.push(';
					$display .= '(function(nrequest){';
					$display .= 'let dataMessage = {};';
					$display .= 'dataMessage.nrequest = nrequest;';
					/* $display .= 'dataMessage.subject = "Change in your ticket";'; */
					$display .= 'dataMessage.subject = "Изменения в заявке";';
					$display .= 'dataMessage.measure = "Отменен ответственный";';
					$display .= 'let note = "Оповещение заявителю";';
					$display .= 'dataMessage.description = "'.$passAssd["fi"].' больше не ответственный на вашу заявку '.$commonMessage.'";';
					$display .= 'oSender.sendToApplicantTickets(dataMessage,function(){ completeSend(false); });';
					$display .= '})';
					$display .= ');';
				}
				
				/* 2. бывшему ответственному */
				$display .= 'listNotifyEvent.push(';
				$display .= '(function(nrequest){';
				$display .= 'let dataMessage = {};';
				$display .= 'dataMessage.nrequest = nrequest;';
				/* $display .= 'dataMessage.subject = "Exempt from doing";'; */
				$display .= 'dataMessage.subject = "О переназначении заявки ";';
				/* $display .= 'dataMessage.measure = "О переназначении заявки ";'; */
				/* $display .= 'dataMessage.note = "Оповещение предыдущему ответственному";'; */
				$display .= 'let note = "Оповещение предыдущему ответственному";';
				$display .= 'dataMessage.description = "'.$passAssd["fi"].', отменено выполнение заявки '.$commonMessage.' ";';
				$display .= 'oSender.sendToAnyUser(dataMessage,[{email:"'.$passAssd["email"].'",login:"'.$passAssd["login"].'",role:"'.$passAssd["role"].'"}],function(){ completeSend(note); });';
				$display .= '})';
				$display .= ');';
			}else{
				/* 1. изменен статус и статус не Выполнена; 2. нет ответственый на заявку; 3. и раньше ответственного на заявку не было */
				/* поэтому это невозможная ветка */
				
			}
		}
	}
	
	$display .= '
		itemSetNotifyEvent();
	});})();';
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