<?php

header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;

try{
	
	/* useExam
		dataReqNext({file:urlServerSide+'helpdesk/getImageItem.php',type:'text',
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
	$modx->smarty = $smarty;


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
	
	/* выбираем номер заявки по номеру изображения */
	$query = "SELECT i.id, i.image, i.path as imagePath,ic.ncontext FROM images_context as ic LEFT JOIN images as i ON i.id = ic.nimage WHERE type=1 AND ic.nimage = $nimage";
	$image = $db->fetchFirst($query,true);
	if((is_string($image)) && (strpos($image,"error") !== false)){
		throw new ErrorException("SQL Error when checking images");
	}
	if(count($image) == 0){
		/*когда не найдена инфа о заявке (к которой прикреплено изображение) */
		throw new ErrorException("data images is empty");
	}
	
	$nticket = (int)$image['ncontext'];

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

	
	$modx->resource = $modx->getObject('modResource',45);
	
	 /* создаие миниатюры (рационально ли распологать тут, как минимум не пересоздаёт миниатюру, надеюсь отрабатывает только один раз) */
	/*$imageFieldCache = $modx->runSnippet('phpthumbof',
		array(
			'input'=>$_SERVER['DOCUMENT_ROOT'].$image['imagePath'].$image['image'],
			'options'=>'&w=50&h=50&zc=1'
		)
	);*/
	
	
	
	
	$pos = strrpos($image['image'],'.');
	$fileExtension = substr($image['image'],$pos+1);
	
	$imageFieldCache = 'assets/images/icons/arbuzova.files/'.$fileExtension.'.tpl';
	if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/images/icons/arbuzova.files/'.$fileExtension.'.tpl')){
		
		$imageFieldCache = 'assets/images/icons/arbuzova.files/Other.tpl';
	}

	/* в файл $imageFieldCache поместить содержимое само же $imageFieldCache */
	$imageFieldCache = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$imageFieldCache);
	if(!$imageFieldCache){
		throw new ErrorException('file_get_contents is empty');
	}
	
	
	$image['imagePath'] = substr($image['imagePath'],1);
	
	
	$name = $image['image'];
	$tmp = explode('.',$name);
	if(count($tmp) > 2){
		$name = $tmp[0].'.'.$fileExtension;
	}
	
	
	
	
	if(!$imageFieldCache){
		echo 'sys:(phpthumbof is empty)';
	}else{
		$image = array('id'=>$image['id'],'icon'=>$imageFieldCache,'type'=>'image','name'=>$name,'link'=>$image['imagePath'].$image['image']);
		
		
		$smarty->assign("admin",$admin);
		$smarty->assign("uid",$uid);
		$smarty->assign("file",$image);
		$smarty->assign("message",array('applicant'=>$message['applicant']));
		
		return $smarty->display('../chunks/tmp.fileItem.tpl');
	}
	
	
	
}catch(ErrorException $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}catch(Error $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}