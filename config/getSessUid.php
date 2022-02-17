<?php
	if($sess){
		
		$query = "SELECT uid FROM sessions WHERE sid = '$sess'";
		$result = $db->fetchFirst($query,true);
		if((is_string($result)) && (strpos($result,"error") !== false)){
			throw new ErrorException("500 SQL Error");
		}
		
		if(count($result)>0){
			
			// echo $sess;
			
			$uid = $result['uid'];
			/*$uid - роль*/	
		}else{
			$uid = false;
			/* echo "result is not array"; */
		}
		
		
	}else{
		$uid = false;
		echo "sess is not found";
	}
?>