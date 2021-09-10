<?php

/*
dataReqNext({file:'listapi/test.check.data.Time.php',type:'text',
	args:'createRecord='+JSON.stringify({time:data.startTime+'-'+data.endTime})+
	'&oldRecord='+JSON.stringify({time:'09:30-10:40'})
	},
	function(responseTest){
		if(responseTest){
			console.log('responseTest');
			console.log(responseTest);
			
			
		}
	}
);
*/

// header('Content-type:application/json');
header('Content-type:text/html');

	$getUserAuthData = true;
	$sessAccesser = true;
	require_once "../start.php";

if($resolution){
	
	try{
			
		$display = '{"success":1}';
		
		$oldRecord = false;
		if(isset($_POST['oldRecord'])){
			$oldRecord = html_entity_decode(htmlspecialchars($_POST['oldRecord']));
			$oldRecord = json_decode($oldRecord,true);
		}
		
		$newRecord = false;
		if(isset($_POST['newRecord'])){
			$newRecord = html_entity_decode(htmlspecialchars($_POST['newRecord']));
			$newRecord = json_decode($newRecord,true);
		}
			
			
		if(!($oldRecord && $newRecord)){ 
			throw new ErrorException("Недостаточно данных ");
		}
			
		print_r($oldRecord);
		echo '<br />';
		echo '<br />';
		print_r($newRecord);
			
		/* сравним два промежутка времени (часы и минуты) */
		$timeBusy = (explode('-',$oldRecord['time']));
		$timeNew = (explode('-',$newRecord['time']));
		
		echo "сравнение начал диапазонов \n\r";
		echo 'timeNew > timeBusy ?'.(string)($timeNew[0] > $timeBusy[0]); echo " \n\r";
		echo 'timeNew < timeBusy ?'.(string)($timeNew[0] < $timeBusy[0]); echo " \n\r";
		echo 'timeNew == timeBusy ?'.(string)($timeNew[0] == $timeBusy[0]); echo " \n\r \n\r";
		
		
		/* машина сравнивает учитывая минуты, то есть не нужно парсить и разделять на минуты и часы, и не нужно приводить к типу инт */
		
		
		
		
		header("HTTP/1.1 200 Ok");
		
		
		
		echo $display;
			
			
	}catch(ErrorException $ex){
		
		echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	}


}else{ 

	header("HTTP/1.1 403 Forbidden");
}
?>