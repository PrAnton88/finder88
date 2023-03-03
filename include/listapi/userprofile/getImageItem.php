<?php

header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;

try{
	
	/* useExam
		dataReqNext({file:urlServerSide+'userprofile/getImageItem.php',type:'text',
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
	
	/* выбираем изображение по идентификатору пользователя, а не по роли. (зачит не по uid)  */
	$query = "SELECT i.id, i.image, i.path as imagePath,ic.ncontext FROM `images_context` as ic LEFT JOIN `images` as i ON i.id = ic.nimage WHERE ic.type=2 AND ic.nimage = $nimage AND ic.ncontext = ".$user["uid"];
	$image = $db->fetchFirst($query,true);
	if((is_string($image)) && (strpos($image,"error") !== false)){
		throw new ErrorException("SQL Error when checking images");
	}
	if(count($image) == 0){
		/*когда не найдена инфа */
		throw new ErrorException("data image is empty $query");
	}
	
	$modx->resource = $modx->getObject('modResource',45);
	
	 /* создаие миниатюры (рационально ли) */
	$imageFieldCache = $modx->runSnippet('phpthumbof',
		array(
			'input'=>$_SERVER['DOCUMENT_ROOT'].$image['imagePath'].$image['image'],
			'options'=>'&w=50&h=50&zc=1'
		)
	);
	
	if(!$imageFieldCache){
		echo 'sys:(phpthumbof is empty)';
	}else{
		$image = array('id'=>$image['id'],'link'=>$imageFieldCache,'type'=>'image');
		
		
		$smarty->assign("uid",$uid);
		$smarty->assign("file",$image);
		
		$smarty->assign("pathAppChunk","../chunks");
		
		$smarty->assign("message",array('applicant'=>$uid));/* только потому что используется tmp от helpdesk */
		
		return $smarty->display('../chunks/tmp.avatarItem.tpl');
	}
	
	
	
}catch(ErrorException $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}catch(Error $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}