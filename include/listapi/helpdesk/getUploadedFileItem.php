<?php

header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;

try{
	
	/* useExam
		dataReqNext({file:urlServerSide+'helpdesk/getUploadedFileItem.php',type:'text',
		args:'dataRecord='+JSON.stringify({nimage:46})},
			function(){}
		); 
	*/
	
	$path = '../';
	require_once "$path../start.php";
	

	if(!$resolution){
		header("HTTP/1.1 403 Forbidden");
		exit;
	}


	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	// $modx->smarty = $smarty;


	include "$path../headerBase.php";
	/* availabla for use $dataRecord */
	
	$nimage = false;
	if(!isset($dataRecord['nimage'])){
		throw new ErrorException("arg nimage is not found");
	}
	$nimage = (int) $dataRecord['nimage'];
	if($nimage <= 0){
		throw new ErrorException("arg nimage is invalid");
	}
	
	$mimetype = false;
	if(!isset($dataRecord['mimetype'])){
		throw new ErrorException("arg mimetype is not found");
	}
	$mimetype = $dataRecord['mimetype'];
	if($mimetype == ''){
		throw new ErrorException("arg mimetype is invalid");
	}
	
	/* выбираем номер заявки по номеру изображения */
	$query = "SELECT i.id, i.image as fileName, i.path as filePath,ic.ncontext FROM images_context as ic LEFT JOIN images as i ON i.id = ic.nimage WHERE type=1 AND ic.nimage = $nimage";
	$uploadedFile = $db->fetchFirst($query,true);
	if((is_string($uploadedFile)) && (strpos($uploadedFile,"error") !== false)){
		throw new ErrorException("SQL Error when checking images");
	}
	if(count($uploadedFile) == 0){
		/*когда не найдена инфа о заявке (к которой прикреплено изображение) */
		throw new ErrorException("data uploadedFile is empty");
	}
	
	
	
	
	
	
	
	$nticket = (int)$uploadedFile['ncontext'];

	$admin = ($user['priority'] == 3);
	require_once $path."/lib/query/get.query.ListTickets.php";
	
	
	$query = getQueryToGetTickets()." WHERE a.id=$nticket";
	$message = $db->fetchFirst($query,true);
	if((is_string($message)) && (strpos($message,"error") !== false)){
		throw new ErrorException("SQL Error");
	}
	
	if(count($message) == 0){
		/*когда не найдена инфа о заявке (к которой прикреплено изображение) */
		throw new ErrorException("data ticket is empty");
	}
	
	
	if((!$admin) && ($message['applicant'] != $uid)){
		
		header("HTTP/1.1 403 Forbidden");
		exit;
	}


	$pos = strrpos($uploadedFile['fileName'],'.');
	$fileExtension = substr($uploadedFile['fileName'],$pos+1);
	
	/*
	$icon = 'assets/images/icons/files/'.$fileExtension.'.png';
	if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/images/icons/files/'.$fileExtension.'.png')){
		
		$icon = 'assets/images/icons/files/Other.png';
	}
	*/
	
	$icon = 'assets/images/icons/arbuzova.files/'.$fileExtension.'.tpl';
	if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/images/icons/arbuzova.files/'.$fileExtension.'.tpl')){
		
		$icon = 'assets/images/icons/arbuzova.files/Other.tpl';
	}

	/* в файл $icon поместить содержимое само же $icon */
	$icon = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$icon);
	if(!$icon){
		throw new ErrorException('file_get_contents is empty');
	}
	

	$uploadedFile['filePath'] = substr($uploadedFile['filePath'],1);
	
	$name = $uploadedFile['fileName'];
	$tmp = explode('.',$name);
	if(count($tmp) > 2){
		$name = $tmp[0].'.'.$fileExtension;
	}
	
	$uploadedFile = array('id'=>$uploadedFile['id'],'icon'=>$icon,'type'=>'file','name'=>$name,'link'=>$uploadedFile['filePath'].$uploadedFile['fileName']);
	
	
	$smarty->assign("admin",$admin);
	$smarty->assign("uid",$uid);
	$smarty->assign("file",$uploadedFile);
	$smarty->assign("message",array('applicant'=>$message['applicant']));
	
	return $smarty->display('../chunks/tmp.fileItem.tpl');
	
	
	
	
}catch(ErrorException $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}catch(Error $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}