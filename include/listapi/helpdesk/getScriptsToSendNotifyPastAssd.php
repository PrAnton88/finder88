<?php

/* это брат сниппета multiHandComment (133) */
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";
	
try{
	
	/* use exam
	
		dataReqNext({file:urlServerSide+'helpdesk/get.New.HtmlViewState.php',type:'text',
		args:'dataRecord='+JSON.stringify(
			{nstate:1,nassd:811,nrequest:4434}
		)},
		console.log
		
		);
	
	тест отказа 
	
		throw new ErrorException("Данные о комментарии не были переданы");
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
	
	
	
	$query = "SELECT * FROM request WHERE id=$nrequest";
	$existmess = $db->fetchFirst($query);
	if((is_string($existmess)) && (strpos($existmess,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	
	$display = '';
	if(($existmess["user_link"] == $user["uid"]) || (($existmess["user_link"] == '') && ($user['priority'] == 3))){
	
		$smarty->assign('nres',$nrequest);
	
		$display .= $smarty->display('../default/components/tmp.htmlListState-min.tpl');
	}else{
		
		$smarty->assign("state",$existmess['state']);
		$display .= $smarty->display('../default/components/tmp.infoState.tpl');
	}
	
	
	/* Внимание!!! только при изменении статуса проводить оповещения 1. Ответственного, 2. Нового Ответственного !! */
	
	
	$display .= '<script>(function(){';
	$display .= 'let completeSend = function (){ customMess("Сообщение отправлено"); };';
	$display .= 'let oSender = oBaseAPI.message.email;';
	$display .= 'let dataMessage = {};';
	
	$display .= 'dataMessage.nrequest = '.$nrequest.';';
	$display .= 'dataMessage.subject = "ticket state";';
	
	
	$commonMessage = ('<br /> №'.$existmess["id"].' \"'.$existmess["header"].'\"<br />');
	/* $display .= 'customMess("'.$nassd.' '.$existmess["user_link"].'");'; */
	

			
	/* 3. */
	$display .= 'dataMessage.note = "Оповещение ответственному - о снятии с заявки";';
	$display .= 'dataMessage.description = "Вы больше НЕ назначемы ответственным на выполнение заявки '.$commonMessage.'";';
	$queryUserData .= ' AND u.id='.$nassd;

	$userData = $db->fetchFirst($queryUserData,$uid);
	if(is_string($userData) && (strpos($userData,'error')!== false)){
		throw new ErrorException('SQL Error');
	}
	$display .= 'oSender.sendToAnyUser(dataMessage,[{email:"'.$userData['email'].'"}],completeSend);';
	

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