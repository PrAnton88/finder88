<?php

/* это брат сниппета get.commentForm (150) */
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../../';
	require_once "$path../start.php";
	
try{

	/* use exam
		
		dataReqNext({file:urlServerSide+'commently/tickets/get.commentForm.php',type:'text',
			args:'dataRecord='+JSON.stringify({ nresource: 4579})},
			console.log
		);
		
		тест отказа
		throw new ErrorException("Данные о комментарии не были переданы");
	*/
	
	
	include "$path../headerBase.php";
	/* availabla for use $dataRecord */
	
	/*
    nresource: 
	*/
	
	$nRecord = false;
	if(isset($dataRecord['nresource'])){
		$nRecord = (int)$dataRecord['nresource'];
		/* if($nRecord == 0){ $nRecord = false; } */
	}
	if(($nRecord == 0) || $nRecord == 'false'){
		$nRecord = false; /* номер ресурса - в данном случае это номер заявки */
	}


	
	$admin = ($user['priority'] == 3);
	
	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	
	$admin = ($admin>0?1:0);

	$smarty->assign("recovery",1);
	$smarty->assign("nres",$nRecord);
	$smarty->assign("admin",$admin);

	header("HTTP/1.1 200 Ok");
	return $smarty->display('../chunks/commentForm.tpl');
	
	
}catch(ErrorException $ex){
	/* если application/json 
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	*/

	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	
}catch(Exception $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>