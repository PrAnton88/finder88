<?php

header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";
	
try{

	/* use exam
	
		dataReqNext({file:urlServerSide+'helpdesk/get.Html.row.item.OfRecordTableRequest.php',type:'text',
		args:'dataRecord='+JSON.stringify(
			{nrequest:4388}
		)},
		console.log
		);
	
		
		throw new ErrorException("тест отказа ");
	*/
	
	include "$path../headerBase.php";
	/* availabla for use $dataRecord */
	
	
	if(!isset($dataRecord['nrequest'])){
		throw new ErrorException("arg nrequest is not found");
	}
	
	$nrequest = (int)$dataRecord['nrequest'];
	if($nrequest == 0){
		throw new ErrorException("arg nrequest is empty");
	}
	
	
	// $query = "SELECT * FROM request WHERE id=$nrequest";
	
	require_once $path."/lib/query/get.query.ListTickets.php";
    $query = getQueryToGetTickets()." WHERE a.id=".$nrequest;
	
	
	$message = $db->fetchFirst($query,true);
	if((is_string($message)) && (strpos($message,"error") !== false)){
		throw new ErrorException("SQL Error"); exit;
	}
	
	if(count($message) == 0){
		
		throw new ErrorException('Информация о заявке не найдена');
	}
	
	$admin = ($user['priority'] == 3);
	
	
	
	require_once "../lib/commently/fn.getCount.php";
	
	$commentsHand = new oCommentsHand($db);
	
    $message['ccomment'] = ($commentsHand->getCount($nrequest,$admin));

	
	require_once "$path../config.modx.php";
	require_once "$path../config.smarty.php";
	$modx->smarty = $smarty;
	
	
	
	
	// echo 'assd = '.$message['assd'];
	// print_r($admins);
	// echo $query;
	
	// print_r($message);
	
	
	$smarty->assign("uid",$user['id']);
	$smarty->assign("admin",$admin);
	$smarty->assign("message",$message);
	$smarty->assign("nres",$nrequest);
	
	
	header("HTTP/1.1 200 Ok");
	return $smarty->display('../chunks/components/tmp.row.item.OfRecordTableRequest.tpl');
	
	
}catch(ErrorException $ex){
	/* если application/json 
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	*/
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	
}catch(Exception $ex){
	
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
}
?>