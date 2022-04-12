<?php

header('Content-type:application/json');

	$getUserAuthData = true;
	$sessAccesser = true;
	
	$path = '../';
	require_once "$path../start.php";

try{
	
	/* useExam
		// Число пользователей: uop
		// Число заявок: mop
		
		
		dataReqNext({file:urlServerSide+'userprofile/set.data.php',type:'json',
			args:'dataRecord='+JSON.stringify({ mop: 5})},
			console.log
		);
		
	*/

	include "$path../headerBase.php";
	/* availabla for use $dataRecord */
		
	
	$mop = $user["mop"];
	$uop = $user["uop"];
    $pop = $user["pop"];
    $updpage = $user["updateTechnicalPage"];
	
		
	if(isset($dataRecord['mop'])){
		$mop = (int)$dataRecord['mop'];
		if($mop == 0){
			$mop ++;
		}elseif($mop > 50){
			$mop = 50;
		}
	}
	if(isset($dataRecord['uop'])){
		$uop = (int)$dataRecord['uop'];
		if($uop == 0){
			$uop ++;
		}elseif($uop > 50){
			$uop = 50;
		}
	}
	if(isset($dataRecord['pop'])){
		$pop = (int)$dataRecord['pop'];
		if($pop == 0){
			$pop ++;
		}elseif($pop > 50){
			$pop = 50;
		}
	}
	if(isset($dataRecord['updpage'])){
		$updpage = (int)$dataRecord['updpage'];
		if($updpage > 1){
			$updpage = 1;
		}
	}
	
	
	
	$res = $db->query("UPDATE user_options SET mop=$mop, uop=$uop, pop=$pop, updateTechnicalSupport=$updpage WHERE id=".$user['uid'],true); 
	if( is_string($res) && (strpos($res,"error") !== false)){
		throw new ErrorException("SQL error");
	}
	
	$dataOutput = [];
	$dataOutput['updpage'] = $updpage;
	$dataOutput['pop'] = $pop;
	$dataOutput['mop'] = $mop;
	$dataOutput['uop'] = $uop;
	
	$dataOutput = json_encode($dataOutput);
	
	echo '{"success":1,"data":'.$dataOutput.'}';
	

}catch(ErrorException $ex){
	/* - когда html
	echo '<script>new UserException("'.exc_handler($ex).'").log();</script>';
	*/
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}catch(Exception $ex){
	
	echo '{"success":0,"description":"'.exc_handler($ex).'"}';
}
?>