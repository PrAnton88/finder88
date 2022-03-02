<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";
	
try{

	/*	use Exam
	
		dataReqNext({file:urlServerSide+'helpdesk/remove.record.php',type:'json',
		args:'dataRecord='+JSON.stringify({id:4434})},
		function(resultJson){
			resultJson = success.check(resultJson);
			
			console.log(resultJson);
		}); 
		
		
		throw new ErrorException("тест отказа ");
	*/


	if($user['priority'] != 3){ $resolution = false; }

	include "$path../headerBase.php";
	/* availabla for use $dataRecord */
	
	
	$id = false;
	if(!isset($dataRecord['id'])){
		throw new ErrorException("Не достаточно данных");
	}
	$id = (int)$dataRecord['id'];
	
	
	if(($id == 0) || $id == 'false'){
		throw new ErrorException("Data id is invalid");
	}



	$result = $db->fetchFirst("SELECT hidden FROM request where id=$id",true); 
	if( is_string($result) && (strpos($result,"error") !== false)){
		throw new ErrorException("SQL error");
	}
	
	$toogle = 1;
	if($result['hidden'] == 1){
		$toogle = 0;
	}
	
	$result = $db->query("UPDATE request SET hidden=$toogle where id=$id",true); 
	if( is_string($result) && (strpos($result,"error") !== false)){
		throw new ErrorException("SQL error");
	}
	

	header("HTTP/1.1 200 Ok");
	$display .= '{"success":1}';
	
	
	echo $display;
	
	
}catch(ErrorException $ex){
	/* если application/json  */
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
	
	/* если application/json 
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
}catch(Exception $ex){
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>